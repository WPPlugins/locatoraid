<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Wordpress_Users_Acl_Model_User_HC_MVC extends _HC_MVC
{
// filters options for properties available for editing
	public function extend_options( $return, $args, $model )
	{
		$user = $this->make('/auth/model/user')->get();

		$return['is_admin'] = array();
		$return['display_name'] = array( $model->get('display_name') );
		$return['email'] = array( $model->get('email') );
		$return['username'] = array( $model->get('username') );

		if( $user->is_admin() ){
			return $return;
		}

		if( $user->id() == $model->id() ){
		}
		else {
			$return['email'] = array();
		}

		return $return;
	}
}