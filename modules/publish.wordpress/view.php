<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Publish_Wordpress_View_LC_HC_MVC extends _HC_MVC
{
	public function render()
	{
		$out = $this->make('/html/view/container');

		ob_start();
		require( dirname(__FILE__) . '/view.html.php' );
		$view = ob_get_contents();
		ob_end_clean();

		$out->add( $view );
		return $out;
	}
}