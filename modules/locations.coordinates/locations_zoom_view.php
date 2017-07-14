<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Locations_Zoom_View_LC_HC_MVC extends _HC_MVC 
{
	public function after_render( $return, $args, $src )
	{
		$location = array_shift( $args );
		if( ! ($location['latitude'] && $location['longitude']) ){
			return $return;
		}

		$edit = 0;
		$coordinates_view = $this->make('index/view')
			->run('render', $location, $edit)
			;

		$out = $this->make('/html/view/list')
			->set_gutter( 2 )
			;

		$out
			->add( 'coordinates', $coordinates_view )
			->add( 'content', $return )
			;
		return $out;
	}
}
