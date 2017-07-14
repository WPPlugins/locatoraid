<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Delete_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$args = $this->make('/app/lib/args')->parse( func_get_args() );
		$id = $args->get('id');

	/* API */
		$api = $this->make('/http/lib/api')
			->request('/api/locations')
			;
		$api->delete( $id );

		$status_code = $api->response_code();
		$api_out = $api->response();
		
		if( $status_code != '204' ){
			echo $api_out['errors'];
			exit;
		}

	// OK
		$redirect_to = $this->make('/html/view/link')
			->to('/locations')
			->href()
			;

		return $this->make('/http/view/response')
			->set_redirect($redirect_to) 
			;
	}
}