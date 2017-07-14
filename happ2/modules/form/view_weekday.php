<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Weekday_HC_MVC extends Form_View_Select_HC_MVC
{
	public function _init()
	{
		$t = $this->make('/app/lib')->run('time');
		$wkds = $t->getWeekdays();

		foreach( $wkds as $wkd => $label ){
			$this->add_option($wkd, $label);
		}
		return $this;
	}
}