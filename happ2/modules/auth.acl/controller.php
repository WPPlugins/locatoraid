<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Acl_Controller_HC_MVC extends _HC_MVC
{
	public function extend_link_check( $return, $args, $src )
	{
		list( $slug, $params ) = $return;

		$module = 'auth';
		if( substr($slug, 0, strlen($module . '/')) != $module . '/' ){
			return $return;
		}

		$user = $this->make('/auth/model/user')->get();
		if( $user->is_admin() ){
			return $return;
		}

		$allowed_routes = array(
			'auth/forgot',
			'auth/forgot/send',
			'auth/logout',
			'auth/login',
			'auth/login/login',
			'auth/zoom/index' . ':' . $user->id(),
			'auth/zoom/update-password' . ':' . $user->id(),
			);

		if( ! in_array($slug, $allowed_routes) ){
			$return[0] = '';
		}

		return $return;
	}
}