<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Search_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$args = $this->make('/app/lib/args')->parse( func_get_args() );

		$search = $args->get('search');
		$lat = $args->get('lat');
		$lng = $args->get('lng');
		$limit = $args->get('limit');
		$sort = $args->get('sort');
		$radius = $args->get('radius');
		$offset = $args->get('offset');

		$results = array();

		$api = $this->make('/http/lib/api')
			->request('/api/locations') 
			// ->add_param('search', $search)
			->add_param('osearch', $search)
			->add_param('with', '-all-')
			// ->add_param('by', 'id')
			// ->add_param('limit', 2)
			;

		if( $limit ){
			$api
				->add_param('limit', $limit)
				;
		}

		if( $sort ){
			$api
				->add_param('sort', $sort)
				;
		}

		if( $offset ){
			$api
				->add_param('offset', $offset)
				;
		}

		$search_coordinates = array();
		if( $lat && $lng && ($lat != '_LAT_') && ($lng != '_LNG_') ){
			$search_coordinates = array($lat, $lng);
			$api
				->add_param('lat', $lat)
				->add_param('lng', $lng)
				;

			if( $radius ){
				$radius = (int) $radius;
				$api
					->add_param('having_computed_distance', array('<=', $radius))
					;
			}
		}

		$p = $this->make('/locations/presenter');
		$also_take = $p->run('database-fields');
		$also_take[] = 'product';

		reset( $also_take );
		foreach( $also_take as $tk ){
			$v = $args->get($tk);
			if( is_array($v) ){
				$api
					->add_param($tk, array('IN', $v))
					;
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
			}
		}

		$results = $api
			->get()
			->response()
			;

		$view = $this->make('view')
			->run('render', $results, $search, $search_coordinates)
			;
		// echo $view;
		// exit;
		return $view;
	}
}