<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Msgbus_Controller_Message_LC_HC_MVC extends _HC_MVC
{
	public function extend_message( $return, $params, $model )
	{
		$msg = NULL;
		$msg_key = NULL;
		$error = NULL;
		$group_msg = FALSE;

		if( $return ){
			if( $model->exists() ){
				$changes = $model->changes();
				if( $changes ){
					if( array_key_exists('id', $changes) ){
						$msg = HCM::__('Location Added');
						// $msg_key = $model->type() . '-add-' . $model->id();
						$msg_key = $model->type() . '-add';
						$group_msg = TRUE;
					}
					else {
						$msg = HCM::__('Location Updated');
						$msg_key = $model->type() . '-update-' . $model->id();
					}
				}
			}
			else {
				$msg = HCM::__('Location Deleted');
				$msg_key = $model->type() . '-delete-' . $model->id();
			}
		}
		else {
			// $error = $model->errors();
		}

		$msgbus = $this->make('/msgbus/lib');
		if( $msg ){
			$msgbus->add('message', $msg, $msg_key, $group_msg);
		}
		if( $error ){
			$msgbus->add('error', $error, $msg_key, $group_msg);
		}
	}
}