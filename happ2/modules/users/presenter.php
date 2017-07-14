<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Presenter_HC_MVC extends _HC_MVC_Model_Presenter
{
	public function present_title()
	{
		$return = NULL;
		if( $this->data('id') ){
			$return = '';
			$return .= $this->make('/html/view/icon')->icon('user');
			// $return .= $this->data('display_name');
			// $return .= ' [' . $this->data('email') . ']';
			$return .= $this->data('email');
		}
		else{
			$return = '___';
		}
		return $return;
	}

	public function present_level()
	{
		$is_admin = $this->data('is_admin');
		$return = $is_admin ? HCM::__('Admin') : HCM::__('User');
		return $return;
	}
}