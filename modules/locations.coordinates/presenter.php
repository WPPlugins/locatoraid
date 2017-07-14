<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Presenter_LC_HC_MVC extends _HC_MVC_Model_Presenter
{
	public function is_geocoded()
	{
		$return = TRUE;

		$lat = $this->data('latitude');
		$lon = $this->data('longitude');

		if( ((! $lat) OR ($lat == -1)) && ((! $lon) OR ($lon == -1)) ){
			$return = FALSE;
		}

		return $return;
	}

	public function geocoding_status()
	{
		$lat = $this->data('latitude');
		$lng = $this->data('longitude');

		if( ! ($lat && $lng) ){
			$return = 0;
		}
		elseif( ($lat == -1) && ($lng == -1) ){
			$return = -1;
		}
		else {
			$return = 1;
		}

		return $return;
	}

	public function present_coordinates()
	{
		$geocoded = $this->run('is-geocoded');

		$lat = $this->data('latitude');
		$lng = $this->data('longitude');

		$wrap = $this->make('/html/view/element')->tag('span')
			->add_attr('class', 'hc-inline-block')
			->add_attr('class', 'hc-p1')
			->add_attr('class', 'hc-rounded')
			;

		if( $geocoded ){
			$return = $lat . ', ' . $lng;
			$wrap
				->add_attr('class', 'hc-bg-olive')
				->add_attr('class', 'hc-white')
				;
		}
		elseif( ($lat == -1) && ($lng == -1) ){
			$return = HCM::__('Address Not Found');
			$wrap
				->add_attr('class', 'hc-bg-red')
				->add_attr('class', 'hc-white')
				;
		}
		else {
			$return = HCM::__('Not Geocoded');
			$wrap
				->add_attr('class', 'hc-bg-orange')
				->add_attr('class', 'hc-white')
				;
		}

		$wrap
			->add( $return )
			;

		return $wrap;
	}
}