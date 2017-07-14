<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Top_Menu_LC_HC_MVC extends _HC_MVC
{
	public function before_children( $args, $src )
	{
		$link = $this->make('/html/view/link')
			->to('')
			->add( $this->make('/html/view/icon')->icon('home') )
			->add( HCM::__('Locations') )
			;
		$src
			->add( 'location', $link )
			;
	}
}