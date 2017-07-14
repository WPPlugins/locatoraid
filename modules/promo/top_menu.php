<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Promo_Top_Menu_LC_HC_MVC extends _HC_MVC
{
	public function before_children( $args, $src )
	{
		$label = 'Locatoraid Pro';

		$link = $this->make('/html/view/link')
			->to('http://www.locatoraid.com/order/')
			->add( $this->make('/html/view/icon')->icon('star') )
			->add( $label )
			->add_attr( 'target', '_blank' )
			;

		$src
			->add( 'promo', $link )
			->set_child_order( 'promo', 200 )
			;
	}
}