<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Wordpress_Users_View_HC_MVC extends _HC_MVC
{
	public function after_header( $return )
	{
		$app_title = isset($this->app->app_config['nts_app_title']) ? $this->app->app_config['nts_app_title'] : '';

		$return['roles'] = $this->make('/html/view/list')
			->add( 
				$this->make('/html/view/element')->tag('span')
					->add( $app_title )
					->add_attr('class', 'hc-muted2')
					->add_attr('class', 'hc-fs2')
					)
			->add( $return['roles'] )
			;

		$return['roles'] = HCM::__('Plugin Role');

		$return['wp_user'] = 
			$this->make('/html/view/icon')->icon('wordpress') . __('Username') . ' / ' . __('Role');
			;

		unset($return['email']);
		unset($return['roles']);

		$return['roles'] = HCM::__('Plugin Role');

		return $return;
	}

	public function after_row( $return, $args )
	{
		$e = array_shift( $args );

		$p = $this->make('/users/presenter');
		$p->set_data( $e );

		// $return['wp_username'] = $e['username'];

		$wp_roles = $e['_wp_userdata']['roles'];

		$wp_roles_obj = new WP_Roles();
		$wordpress_roles_names = $wp_roles_obj->get_names();

		$wp_roles_view = array();
		reset( $wp_roles );
		foreach( $wp_roles as $wp_role ){
			$wp_role_name = isset($wordpress_roles_names[$wp_role]) ? $wordpress_roles_names[$wp_role] : $wp_role;
			$wp_roles_view[] = $wp_role_name;
		}

		$wp_roles_view = join(', ', $wp_roles_view);
		$wp_roles_view = $this->make('/html/view/element')->tag('span')
			->add( $wp_roles_view )
			->add_attr('class', 'hc-muted2')
			->add_attr('class', 'hc-fs2')
			;

		$return['wp_user'] = $this->make('/html/view/list')
			->add( $e['username'] )
			->add( $wp_roles_view )
			;

		return $return;
	}
}