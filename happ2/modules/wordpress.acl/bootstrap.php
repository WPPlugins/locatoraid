<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Wordpress_Acl_Bootstrap_HC_MVC extends _HC_MVC
{
	public function run()
	{
	// init admin roles
		$users_model = $this->make('/users/model');
		$wp_always_admin = $users_model->run('wp-always-admin');

		$app_short_name = $this->app_short_name();
		$admin_cap = $app_short_name . '_' . 'admin';

		reset( $wp_always_admin );
		foreach( $wp_always_admin as $role ){
			$wp_role = get_role( $role );
			if( ! $wp_role ){
				continue;
			}
			if( ! $wp_role->has_cap($admin_cap) ){
				$wp_role->add_cap($admin_cap);
			}
		}
// $data = get_userdata( get_current_user_id() ); 
// if ( is_object( $data) ) {
	// $current_user_caps = $data->allcaps;
	// echo '<pre>' . print_r( $current_user_caps, true ) . '</pre>';
	// exit;
// }
	}
}