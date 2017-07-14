<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class App_Controller_LC_HC_MVC extends _HC_MVC
{
	public function set_default_route( $args, $src )
	{
		list( $slug, $params ) = $args;
		if( ! $slug ){
			$slug = 'locations';
			$return = array( $slug, $params );
			return $return;
		}
	}
}