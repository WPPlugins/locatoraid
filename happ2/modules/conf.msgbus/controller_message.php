<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Conf_Msgbus_Controller_Message_HC_MVC extends _HC_MVC
{
	public function extend_message( $return, $params, $model )
	{
		$msg = HCM::__('Settings updated');

		$msgbus = $this->make('/msgbus/lib');
		if( $msg ){
			$msgbus->add('message', $msg);
		}
	}
}