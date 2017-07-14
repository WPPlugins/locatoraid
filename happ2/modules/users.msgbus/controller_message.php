<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Msgbus_Controller_Message_HC_MVC extends _HC_MVC
{
	public function extend_message( $return, $params, $model )
	{
		$msg = NULL;
		$error = NULL;

		if( $return ){
			if( $model->exists() ){
				$changes = $model->changes();
				if( array_key_exists('id', $changes) ){
					$msg = HCM::__('User added');
				}
				else {
					$msg = HCM::__('User updated');
				}
			}
			else {
				$msg = HCM::__('User deleted');
			}
		}
		else {
			$error = $model->errors();
		}

		$msgbus = $this->make('/msgbus/lib');
		if( $msg ){
			$msgbus->add('message', $msg);
		}
		if( $error ){
			$msgbus->add('error', $error);
		}
	}
}