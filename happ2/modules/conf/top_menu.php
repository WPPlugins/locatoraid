<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Conf_Top_Menu_HC_MVC extends _HC_MVC
{
	public function before_children( $args, $src )
	{
		$link = $this->make('/html/view/link')
			->to('')
			->add( $this->make('/html/view/icon')->icon('cog') )
			->add( HCM::__('Settings') )
			;
		$src
			->add( 'conf', $link )
			;
		$src
			->set_child_order( 'conf', 100 )
			;
	}
}