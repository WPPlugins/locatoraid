<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Priority_Locations_Presenter_LC_HC_MVC extends _HC_MVC
{
	public function extend_fields( $return, $args, $src )
	{
		$return['priority'] = HCM::__('Priority');
		return $return;
	}

	public function after_present_front( $return, $args, $src )
	{
		if( isset($return['priority']) && (! $return['priority']) ){
			unset($return['priority']);
		}
		return $return;
	}
}