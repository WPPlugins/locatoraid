<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Geocode_Save_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$args = $this->make('/app/lib/args')->parse( func_get_args() );
		$id = $args->get('id');
		if( is_array($id) ){
			$id = array_shift($id);
		}
		if( ! $id ){
			return;
		}

		$latitude = $args->get('latitude');
		$longitude = $args->get('longitude');

		if( ! ($id && $latitude && $longitude) ){
			echo "id, latitude, longitude required";
			echo $this->make('/http/view/response')
				->set_status_code(500) 
				;
			exit;
		}

		$location = $this->make('/http/lib/api')
			->request('/api/locations')
			->add_param('id', $id)
			->get()
			->response()
			;

		$values = array(
			'latitude'	=> $latitude,
			'longitude'	=> $longitude,
			);

		$api = $this->make('/http/lib/api')
			->request('/api/locations')
			;
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

	// OK
		$redirect_to = $this->make('/html/view/link')
			->to('-referrer-')
			->href()
			;

		return $this->make('/http/view/response')
			->set_redirect($redirect_to) 
			;
	}
}