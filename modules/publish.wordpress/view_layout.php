<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Publish_Wordpress_View_Layout_LC_HC_MVC extends _HC_MVC
{
	public function header()
	{
		$return = HCM::__('Publish');
		return $return;
	}

	public function menubar()
	{
		$menubar = $this->make('/html/view/container');
		return $menubar;
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