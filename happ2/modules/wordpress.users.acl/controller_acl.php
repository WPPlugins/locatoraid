<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Wordpress_Users_Acl_Controller_Acl_HC_MVC extends _HC_MVC
{
	public function extend_link_check( $return, $args, $src )
	{
		list( $slug, $params ) = $return;

		$checks = array(
			array($this, 'check_delete'),
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

	public function check_delete( $slug, $params )
	{
		$return = NULL;

		if( $slug == 'users/index/delete' ){
			$return = FALSE;
		}
		return $return;
	}
}