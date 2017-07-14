<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_404_HC_MVC extends Html_View_Element_HC_MVC
{
	public function render()
	{
		$header = '404 Page Not Found';
		$content = 'The page you requested was not found';

		$out = $this->make('/layout/view/content-header-menubar')
			->set_content( $content )
			->set_header( $header )
			;
		return $out;
	}
}