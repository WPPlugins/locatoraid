<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Wordpress_Auth_Lib_Auth_HC_MVC extends _HC_MVC
{
	const USER_LOGIN_HASH = 'hc_user_hash';

	public function check( $username, $password )
	{
		$return = FALSE;
		// $userInfo = $this->getUserByUsername( $username );
		// if( $userInfo ){
			// $storedHash = $userInfo['user_pass'];
			// $return = wp_check_password( $password, $storedHash, $userInfo['id'] );
			// }
		return $return;
	}

	public function logged_in()
	{
		$current_user = wp_get_current_user();
		$return = $current_user->ID;

		$return = $return ? $return : 0;
		return $return;
	}

	public function login( $user_id, $remember = FALSE )
	{
		$credentials = array(
			'user_login'	=> $user_name,
			'user_password'	=> $user_pass,
			'remember'		=> $remember,
			);
		wp_signon( $credentials );
	}

	public function logout()
	{
		wp_clear_auth_cookie();
	}
}