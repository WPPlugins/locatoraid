<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Acl_Manager_HC_MVC extends _HC_MVC
{
	public function after_can( $return, $args, $src )
	{
		$what = array_shift( $args );
		$on = array_shift( $args );

		if( $what != 'user@read' ){
			return $return;
		}

		$return = FALSE;
		$user = $src->user();
		if( $user->is_admin() ){
			$return = TRUE;
		}

		return $return;
	}

	public function after_which( $return, $args, $src )
	{
		$what = array_shift( $args );
		$on = array_shift( $args );

		if( $what != 'user@read' ){
			return $return;
		}

		$return = $this->get_allowed_read();

		return $return;
	}

	public function get_allowed_read(){
		$return = array();
		// $return = array(
			// 'id'	=> array('IN', array(1,2))
			// );
		return $return;
	}

	public function after_link_check( $return, $args, $src )
	{
		$slug = array_shift( $args );
		if( $slug != 'users' ){
			return $return;
		}

		$can = $src
			->run('can', 'employee@read')
			;

		if( ! $can ){
			$return = FALSE;
		}

		return $return;
	}
}