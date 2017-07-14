<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_Bootstrap_HC_MVC extends _HC_MVC
{
	public function run()
	{
		if( ! class_exists('Html_View_Element_HC_MVC') ){
			include_once( dirname(__FILE__) . '/view_element.php' );
		}
	}
}
