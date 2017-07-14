<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class WordPress_Auth_Notallowed_Controller_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
	}
}