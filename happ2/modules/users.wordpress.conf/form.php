<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_WordPress_Conf_Form_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$wp_roles = new WP_Roles();
		$wordpress_roles = $wp_roles->get_names();

		$inputs = array();
		reset( $wordpress_roles );
		foreach( $wordpress_roles as $role_value => $role_name ){
			$this_field_pname = 'wordpress_users:role_' . $role_value;
			$inputs[ $this_field_pname ] = $this->make('/users/input/roles')
				;
		}

		$can_edit = FALSE;
		$user = $this->make('/auth/model/user')->get();
		if( $user->is_always_admin() ){
			$can_edit = TRUE;
		}

		foreach( $inputs as $k => $v ){
			if( ! $can_edit ){
				$v->set_readonly( TRUE );
			}

			$this
				->set_input( $k, $v )
				;
		}

		if( ! $can_edit ){
			$this
				->set_readonly( TRUE )
				;
		}

		return $this;
	}

	public function render()
	{
		$wp_users_model = $this->make('/users.wordpress/model/user');
		$wp_always_admin = $wp_users_model->run('wp-always-admin');

		$can_edit = FALSE;
		$user = $this->make('/auth/model/user')->get();
		if( $user->is_always_admin() ){
			$can_edit = TRUE;
		}

		$rm = $this->make('/users/roles');
		$our_roles = $rm->roles();

		$out = $this->make('/html/view/table-responsive')
			->set_no_footer(TRUE)
			;

		$app_title = isset($this->app->app_config['nts_app_title']) ? $this->app->app_config['nts_app_title'] : '';

		$header = array();
		$header['wp_role'] = $this->make('/html/view/icon')->icon('wordpress') . __('Role');

		reset( $our_roles );
		foreach( $our_roles as $rk => $rv ){
			$this_rv = $this->make('/html/view/list')
				// ->add_attr('class', 'hc-align-center')
				;

			if( 0 && $app_title ){
				$this_rv
					->add(
						$this->make('/html/view/element')->tag('span')
							->add( $app_title )
							->add_attr('class', 'hc-muted2')
							->add_attr('class', 'hc-fs2')
						)
					;
			}

			$this_rv
				->add( $rv )
				;
			$header[$rk] = $this_rv;
		}

		$wp_roles = new WP_Roles();
		$wordpress_roles = $wp_roles->get_names();
		$wordpress_count_users = count_users();

		$rows = array();
		reset( $wordpress_roles );
		foreach( $wordpress_roles as $role_value => $role_name ){
			$this_row = array();

			$wp_role_view = $role_name;
			$this_role_count = ( isset($wordpress_count_users['avail_roles'][$role_value]) ) ? $wordpress_count_users['avail_roles'][$role_value] : 0;
			$wp_role_view .= ' [' . $this_role_count . ']';

			if( $this_role_count > 0 ){
				$wp_role_view = $this->make('/html/view/element')->tag('span')
					->add( $wp_role_view )
					->add_attr('class', 'hc-bold')
					;
			}
			
			$this_row['wp_role'] = $wp_role_view;
			$this_field_pname = 'wordpress_users:role_' . $role_value;

			reset( $our_roles );
			foreach( $our_roles as $rk => $rv ){
				$this_view = '';
				if( in_array($role_value, $wp_always_admin) && in_array($rk, array('admin')) ){
					$this_view = $this->make('/html/view/icon')->icon('check');
				}
				else {
					$this_view = $this->inputs[$this_field_pname]
						->render_one($rk, FALSE, FALSE)
						;
				}

				$this_row[$rk] = $this_view;
			}

			$rows[] = $this_row;
		}

		$out
			->set_header( $header )
			->set_rows( $rows )
			;

		$help = HCM::__('Set how WordPress users can work with this plugin.');

		$out = $this->make('/html/view/list')
			->set_gutter(2)
			->add( $help )
			->add( $out )
			;

		return $out;
	}
}