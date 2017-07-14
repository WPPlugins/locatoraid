<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_401_HC_MVC extends Html_View_Element_HC_MVC
{
	public function render( $body = NULL )
	{
		$header = '401 Unauthorized';
		$content = $body;

		$out = $this->make('/layout/view/content-header-menubar')
			->set_content( $content )
			->set_header( $header )
			;
		return $out;
	}
}