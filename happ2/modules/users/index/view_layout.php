<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Index_View_Layout_HC_MVC extends _HC_MVC
{
	public function header()
	{
		$return = HCM::__('Users');
		return $return;
	}

	public function menubar()
	{
		$return = $this->make('/html/view/container');
		return $return;
	}

	public function render( $content )
	{
		$header = $this->run('header');
		$menubar = $this->run('menubar');

		$out = $this->make('/layout/view/content-header-menubar')
			->set_content( $content )
			->set_header( $header )
			->set_menubar( $menubar )
			;

		return $out;
	}
}