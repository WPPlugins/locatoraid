<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Conf_Acl_Controller_Acl_HC_MVC extends _HC_MVC
{
	public function extend_link_check( $return, $args, $src )
	{
		list( $slug, $params ) = $return;

		$checks = array(
			array($this, 'check_my_module'),
			array($this, 'check_admin'),
			);

		foreach( $checks as $check ){
			$check_result = call_user_func( $check, $slug, $params );
			if( $check_result === NULL ){
				continue;
			}
			if( ! $check_result ){
				$return[0] = '';
			}
			break;
		}
		return $return;
	}

	public function check_my_module( $slug, $params )
	{
		$return = NULL;

		$module = 'conf';
		if( ($module != $slug) && (substr($slug, 0, strlen($module . '/')) != $module . '/') ){
			$return = TRUE;
		}
		return $return;
	}

	public function check_admin( $slug, $params )
	{
		$return = FALSE;

		$user = $this->make('/auth/model/user')->get();
		if( $user->is_admin() ){
			$return = TRUE;
		}

		return $return;
	}
}