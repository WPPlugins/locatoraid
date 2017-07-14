<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Geocode_Locations_Zoom_Controller_LC_HC_MVC extends _HC_MVC
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

		if( $location['latitude'] && $location['longitude'] ){
			return;
		}

	// add javascript
		$this->make('/app/enqueuer')
			->run('register-script', 'lc-geocode', 'modules/geocode/assets/js/geocode.js')
			->run('enqueue-script', 'lc-geocode')
			;
	}
}