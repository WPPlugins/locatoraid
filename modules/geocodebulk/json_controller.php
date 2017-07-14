<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class GeocodeBulk_Json_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$limit = 10;
		$locations = $this->make('/http/lib/api')
			->request('/api/locations')
			->add_param('custom', 'notgeocoded')
			->add_param('limit', $limit)
			->get()
			->response()
			;

		$total_count = $this->make('/http/lib/api')
			->request('/api/locations')
			->add_param('custom', 'notgeocodedcount')
			->get()
			->response()
			;

		$p = $this->make('/locations/presenter');
		$geocoder = $this->make('/geocode/lib');

		$out = array();
		$out['total'] = $total_count;
		$out['locations'] = array();
		foreach( $locations as $e ){
			$p->set_data( $e );
			$address = $p->run('present-address');
			$address = $geocoder->run('prepare-address', $address);
			$this_e = array(
				'id'		=> $e['id'],
				'address'	=> $address,
				);
			$out['locations'][] = $this_e;
		}

		$out = json_encode( $out );
		echo $out;
		exit;
	}
}