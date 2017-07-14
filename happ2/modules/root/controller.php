<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Root_Controller_HC_MVC extends _HC_MVC
{
	public function init()
	{
	}

	public function link_check( $slug, $params = array() )
	{
		if( is_array($slug) ){
			$slug[0] = trim($slug[0], '/');
		}
		else {
			$slug = trim($slug, '/');
		}
		$return = array( $slug, $params );
		return $return;
	}
}