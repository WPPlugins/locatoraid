<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
$config['wordpress_users:_label'] = HCM::__('Users');

$options = array(
	'none'	=> HCM::__('No Access'),
	// 'user'	=> HCM::__('User'),
	'admin'	=> HCM::__('Admin')
	);

$wp_roles = new WP_Roles();
$wordpress_roles = $wp_roles->get_names();

foreach( $wordpress_roles as $role_value => $role_name ){
	$default = 1;

	switch( $role_value ){
		case 'administrator':
			$config['wordpress_users:role_' . $role_value ] = 1;
			break;

		default:
			$config['wordpress_users:role_' . $role_value ] = 0;
			break;
	}
}