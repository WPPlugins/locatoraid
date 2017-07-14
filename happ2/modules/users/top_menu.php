<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Top_Menu_HC_MVC extends _HC_MVC
{
	public function before_children( $args, $src )
	{
		$link = $this->make('/html/view/link')
			->to('/users')
			->add( $this->make('/html/view/icon')->icon('user') )
			->add( HCM::__('Users') )
			;
		$src
			->add( 'users', $link )
			;
		$src
			->set_child_order( 'users', 90 )
			;
	}
}