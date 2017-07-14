<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class _HC_ORM_WordPress_User_Storable implements _HC_ORM_Storable_Interface
{
	protected $default_args = array();
	protected $db = NULL;

	public function set_db( $db )
	{
		$this->db = $db;
		return $this;
	}

	public function add_default_arg( $k, $v )
	{
		$this->default_args[$k] = $v;
		return $this;
	}

	public function delete_all()
	{
		return TRUE;
	}
	public function delete( $wheres = array() )
	{
		return TRUE;
	}

	public function fetch( $fields = '*', $wheres = array(), $limit = NULL, $orderby = NULL, $distinct = FALSE )
	{
		$return = array();
// _print_r( $wheres );
		$args = $this->_prepare_args( $wheres );
		$args = array_merge( $this->default_args, $args );
		$wp_users = get_users( $args );

		foreach( $wp_users as $userdata ){
			$array = $this->_from_userdata( $userdata );
			$array['_wp_userdata'] = $userdata;
			$id = $array['id'];
			$return[ $id ] = $array;
		}
		return $return;
	}

	private function _prepare_args( $wheres = array() )
	{
		$return = array();

		foreach( $wheres as $key => $key_wheres ){
			if( $key == 'id' ){
				$return['include'] = array();
			}

			foreach( $key_wheres as $where ){
				list( $how, $value, $escape ) = $where;
				if( $key == 'id' ){
					if( ! is_array($value) ){
						$value = array( $value );
					}
					$return['include'] = array_merge($return['include'], $value);
				}
			}
		}
		return $return;
	}

	private function _from_userdata( $userdata )
	{
		$return = array(
			'id'			=> $userdata->ID,
			'email'			=> $userdata->user_email,
			'display_name'	=> $userdata->display_name,
			'username'		=> $userdata->user_login,
			);
		return $return;
	}

	public function count( $wheres = array() )
	{
	}

	public function insert( $data )
	{
	}

	public function update( $data, $wheres = array() )
	{
	}

	public function fetch_distinct_prop( $field )
	{
		$return = array();
		return $return;
	}
}

// class Wordpress_Users_Model_User_HC_MVC extends _HC_ORM
class Users_Wordpress_Model_User_HC_MVC extends _HC_ORM_WP_Custom_Post
{
	private $_wp_userdata = NULL;
	private $_wp_always_admin = array('administrator', 'developer');

	public function __construct()
	{
		$this->storable = new _HC_ORM_WordPress_User_Storable();
	}

	public function wp_always_admin()
	{
		return $this->_wp_always_admin;
	}

	public function fetch_array( $fields = '*' )
	{
		$return = parent::fetch_array( $fields );

	// set role bits
		$mapping = $this->wp_roles_mapping();

		$keys = array_keys( $return );
		foreach( $keys as $k ){
			$our_roles_bits = 0;

			if( isset($return[$k]['_wp_userdata']) ){
				$userdata = $return[$k]['_wp_userdata'];
				if( isset($userdata->roles) ){
					$wp_roles = $userdata->roles;
					reset( $wp_roles );
					foreach( $wp_roles as $wp_role ){
						if( isset($mapping[$wp_role]) ){
							$this_bits = $mapping[$wp_role];
							$our_roles_bits = $our_roles_bits | $this_bits;
						}
					}
				}
			}

			$return[$k]['roles'] = $our_roles_bits;
		}
		return $return;
	}

	public function my_roles()
	{
	// fetch only our users
		$return = array();

		$mapping = $this->wp_roles_mapping();
		foreach( $mapping as $wprole => $ourbits ){
			if( ! $ourbits ){
				continue;
			}
			$return[] = $wprole;
		}
		$return = array_unique($return);
		return $return;
	}

	public function _init()
	{
	// fetch only those who have a role
		$role_in = $this->my_roles();
		$this->storable
			->add_default_arg('role__in', $role_in)
			;
		return $this;
	}

	public function is_always_admin()
	{
		$return = FALSE;

		$wp_userdata = $this->get('_wp_userdata');
		if( ! $wp_userdata ){
			return $return;
		}

		if( ! isset($wp_userdata->roles) ){
			return $return;
		}

		$wp_roles = $wp_userdata->roles;

		reset( $this->_wp_always_admin );
		foreach( $this->_wp_always_admin as $wp_always_admin ){
			if( in_array($wp_always_admin, $wp_roles) ){
				$return = TRUE;
				return $return;
			}
		}

		return $return;
	}

	public function where_admins()
	{
		$find_roles = array();

		$rm = $this->make('/users/roles');
		$admin_bits = $rm->get_bits( 'admin' );

		$mapping = $this->wp_roles_mapping();
		foreach( $mapping as $wprole => $ourbits ){
			if( ! ($ourbits & $admin_bits) ){
				continue;
			}
			$find_roles[$wprole] = $wprole;
		}

		$this
			->where('role', 'IN', $find_roles)
			;
		// _print_r( $find_roles );
		// exit;
	}

	public function is_admin()
	{
		$return = FALSE;

		$wp_userdata = $this->get('_wp_userdata');
		if( ! $wp_userdata ){
			return $return;
		}

		if( ! isset($wp_userdata->roles) ){
			return $return;
		}

		$wp_roles = $wp_userdata->roles;

		reset( $this->_wp_always_admin );
		foreach( $this->_wp_always_admin as $wp_always_admin ){
			if( in_array($wp_always_admin, $wp_roles) ){
				$return = TRUE;
				return $return;
			}
		}

	// CHECK OUR CONFIG
		$rm = $this->make('/users/roles');
		$admin_bits = $rm->get_bits( 'admin' );

		$mapping = $this->wp_roles_mapping();
		foreach( $mapping as $wprole => $ourbits ){
			if( in_array($wprole, $wp_roles) ){
				if( $ourbits & $admin_bits ){
					$return = TRUE;
					break;
				}
			}
		}
		return $return;
	}

	public function wp_roles_mapping()
	{
		$app_settings = $this->make('/app/lib/settings');
		$prefix = 'wordpress_users:role_';
		$return = array();
		$all_settings = $app_settings->run('get');

		foreach( $all_settings as $k => $v ){
			if( substr($k, 0, strlen($prefix)) == $prefix ){
				$name = substr($k, strlen($prefix));
				$return[ $name ] = $v;
			}
		}

		$rm = $this->make('/users/roles');
		$admin_bits = $rm->get_bits( 'admin' );

		reset( $this->_wp_always_admin );
		foreach( $this->_wp_always_admin as $wp_always_admin ){
			if( ! isset($return[$wp_always_admin]) ){
				$return[$wp_always_admin] = 0;
			}
			$return[$wp_always_admin] = $return[$wp_always_admin] | $admin_bits;
		}

		return $return;
	}
}