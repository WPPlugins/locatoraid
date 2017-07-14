<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Icons_Dashicons_Wordpress_View_Icon_HC_MVC extends Html_View_Element_HC_MVC
{
	public function after_get_icons( $return, $args, $src )
	{
		$return = array();
		return $return;

		$file = dirname(__FILE__) . '/assets/css/dashicons.css';
		$css = file_get_contents($file);

		preg_match_all( '/\.(dashicons-)([^,}]*)\s*:before\s*{\s*(content:)\s*"(\\\\[^"]+)"/s', $css, $matches );
		$icons = $matches[2];

		return $icons;
	}
}
