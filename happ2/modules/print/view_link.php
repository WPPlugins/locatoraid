<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Print_View_Link_HC_MVC extends _HC_MVC
{
	public function _init()
	{
		$return = $this->make('/html/view/link')
			->to('-', $this->make('/print/controller')->run('print-view-args'))
			->ajax()
			->new_window()
			->add( $this->make('/html/view/icon')->icon('printer') )
			->add( HCM::__('Print View') )
			->add_attr('class', 'hc-show-sm')
			;
		return $return;
	}
}
