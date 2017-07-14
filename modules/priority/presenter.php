<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Priority_Presenter_LC_HC_MVC extends _HC_MVC_Model_Presenter
{
	public function present_options()
	{
		$return = array(
			0	=> HCM::__('Normal'),
			1	=> HCM::__('Featured'),
			// 2	=> HCM::__('Always Show'),
			);
		return $return;
	}
}
