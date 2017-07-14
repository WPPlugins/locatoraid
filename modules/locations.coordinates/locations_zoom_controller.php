<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Locations_Zoom_Controller_LC_HC_MVC extends _HC_MVC
{
	public function before_route_index( $args, $src )
	{
		$args = $this->make('/app/lib/args')->parse( $args );
		$id = $args->get('id');

		$location = $this->make('/http/lib/api')
			->request('/api/locations')
			->add_param('id', $id)
			->get()
			->response()
			;

		if( ! ($location['latitude'] && $location['longitude']) ){
			return;
		}

		if( ! ( ($location['latitude'] == -1) && ($location['longitude'] == -1) ) ){
		// add javascript
			$this->make('/app/enqueuer')
				->run('register-script', 'lc-locations-coordinates', 'modules/locations.coordinates/assets/js/map.js')
				->run('enqueue-script', 'lc-locations-coordinates')
				;
		}
	}
}