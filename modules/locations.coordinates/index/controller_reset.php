<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Index_Controller_Reset_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$args = $this->make('/app/lib/args')->run('parse', func_get_args());
		$id = $args->get('id');

		$api = $this->make('/http/lib/api')
			->request('/api/locations')
			->add_param('id', $id)
			;
		$location = $api
			->get()
			->response()
			;

	/* API */
		$api = $this->make('/http/lib/api')
			->request('/api/locations')
			;

		$values = array(
			'latitude'	=> NULL, 
			'longitude'	=> NULL,
			);

		$api->put( $id, $values );

		$status_code = $api->response_code();
		$api_out = $api->response();

		if( substr($status_code, 0, 1) != '2' ){
			$errors = $api_out['errors'];

			$session = $this->make('/session/lib');
			$session
				->set_flashdata('errors', $errors)
				;

			$redirect_to = $this->make('/html/view/link')
				->to('-referrer-')
				->href()
				;
			return $this->make('/http/view/response')
				->set_redirect($redirect_to) 
				;
		}

		$redirect_to = $this->make('/html/view/link')
			->to('/locations/edit', array('id' => $id))
			->href()
			;

		return $this->make('/http/view/response')
			->set_redirect($redirect_to) 
			;
	}
}