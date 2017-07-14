<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Locations_Zoom_View_Layout_LC_HC_MVC extends _HC_MVC 
{
	public function after_menubar( $return, $args, $src )
	{
		if( ! $return ){
			return $return;
		}

		$e = array_shift( $args );

	// coordinates
		$return->add(
			'coordinates',
			$this->make('/html/view/link')
				->to('', array('id' => $e['id']))
				->add( $this->make('/html/view/icon')->icon('location') )
				->add( HCM::__('Edit Coordinates') )
			);

		return $return;
	}
}
