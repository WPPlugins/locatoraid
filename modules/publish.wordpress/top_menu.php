<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Publish_WordPress_Top_Menu_LC_HC_MVC extends _HC_MVC
{
	public function before_children( $args, $src )
	{
		$link = $this->make('/html/view/link')
			->to('')
			->add( $this->make('/html/view/icon')->icon('edit') )
			->add( 'Publish' )
			;
		$src
			->add( 'publish', $link )
			;
	}
}