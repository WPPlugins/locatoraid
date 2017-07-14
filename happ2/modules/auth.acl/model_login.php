<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Acl_Model_Login_HC_MVC extends _HC_MVC
{
	public function extend_options( $return, $args, $model )
	{
		$user = $this->make('/auth/model/user')->get();
		if( $user->is_admin() ){
			return $return;
		}

		if( $user->id() == $model->id() ){
			$return['username'] = array( $model->get('username') );
		}
		else {
			$return = array();
		}
		return $return;
	}
}