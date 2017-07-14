<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Search_Radius_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$args = $this->make('/app/lib/args')->parse( func_get_args() );

		$search = $args->get('search');
		$lat = $args->get('lat');
		$lng = $args->get('lng');
		$limit = $args->get('limit');

		$radiuses = $args->get('radius');

		$link_params = array();
		$link_params['search'] = $search;

		$results = array();

		$p = $this->make('/locations/presenter');
		$also_take = $p->run('database-fields');
		$also_take[] = 'product';

		$api = $this->make('/http/lib/api')
			->request('/api/locations') 
			->add_param('osearch', $search)
			;

	// radius counts
		$radius_count = array();
		if( $lat && $lng && ($lat != '_LAT_') && ($lng != '_LNG_') ){
			$api
				->add_param('custom', 'radiuscount')
				// ->add_param('osearch', $search)
				;

			$api
				->add_param('radius', $radiuses)
				// ->add_param('osearch', $search)
				;

			$search_coordinates = array($lat, $lng);
			$api
				->add_param('lat', $lat)
				->add_param('lng', $lng)
				;

			$link_params['lat'] = $lat;
			$link_params['lng'] = $lng;

			reset( $also_take );
			foreach( $also_take as $tk ){
				$v = $args->get($tk);
				if( is_array($v) ){
					$api
						->add_param($tk, array('IN', $v))
						;
					$link_params[$tk] = array('IN', $v);
				}
				else {
					if( ! strlen($v) ){
						continue;
					}
					if( substr($v, 0, 1) == '_' ){
						continue;
					}
					$api
						->add_param($tk, $v)
						;
					$link_params[$tk] = $v;
				}
			}
			
			if( $limit ){
				$api
					->add_param('limit', $limit)
					;
			}

// echo $api->url();
			$radius_count = $api
				->get()
				->response()
				;
		}

// _print_r( $radius_count );
// exit;
		$return = $radius_count;

		$return = array();
		foreach( $radius_count as $radius => $count ){
			$this_link_params = $link_params;
			if( $radius ){
				$this_link_params['radius'] = $radius;
			}
			if( $limit ){
				$this_link_params['limit'] = $limit;
			}

			$link = $this->make('/html/view/link')
				->to('', $this_link_params)
				->href()
				;
			$return[] = array( $link, $count );
		}

		$return = json_encode( $return );
		return $return;
	}
}