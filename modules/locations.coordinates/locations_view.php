<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Locations_View_LC_HC_MVC extends _HC_MVC
{
	public function after_prepare_header( $return )
	{
		$return['coordinates'] = HCM::__('Coordinates');
		return $return;
	}

	public function after_prepare_row( $return, $args, $src )
	{
		$e = array_shift( $args );

		$p = $this->make('presenter')
			->set_data( $e )
			;

		$coordinates_view = $p->run('present-coordinates');
		$geocoding_status = $p->run('geocoding-status');
		if( ! $geocoding_status ){
			$coordinates_view = $this->make('/html/view/link')
				->to( '/geocode', array('id' => $e['id']) )
				->add( $coordinates_view )
				;
		}
		else {
			$coordinates_view = $this->make('/html/view/link')
				->to( '', array('id' => $e['id']) )
				->add( $coordinates_view )
				;
		}

		$return['coordinates'] = $coordinates_view;
		return $return;
	}
}