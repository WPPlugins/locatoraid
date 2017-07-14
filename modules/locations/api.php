<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Api_LC_HC_MVC extends _HC_Rest_Api
{
// custom end points
	public function custom_get_radiuscount( $args = array() )
	{
		$return = array();

		$limit = NULL;
		if( isset($args['limit']) ){
			$limit = $args['limit'];
			unset( $args['limit'] );
		}

		if( isset($args['radius']) ){
			$radiuses = $args['radius'];
			unset( $args['radius'] );
		}
		else {
			$radiuses = array( 10, 20, 50, 100, 200, 500 );
		}

		if( ! is_array($radiuses) ){
			$radiuses = array( $radiuses );
		}

		rsort( $radiuses, SORT_NUMERIC );

		$api = $this->make('/http/lib/api')
			->request('/api/locations') 
			;

	// include all
		// $api
			// ->add_param('custom', 'count')
			// ;
		// $this_count = $api
			// ->get()
			// ->response()
			// ;
		// $return[0] = $this_count;
		// $last_count = $this_count;

		$last_count = 0;
		foreach( $radiuses as $r ){
			reset( $args );
			foreach( $args as $k => $v ){
				$api
					->add_param( $k, $v )
					;
			}

			$r = (int) $r;
			$api
				->add_param('having_computed_distance', array('<=', $r))
				;

			$api
				->add_param('custom', 'count')
				;

			$this_count = $api
				->get()
				->response()
				;

			if( ! $this_count ){
				break;
			}

			if( $this_count == $last_count ){
				array_pop($return); 
			}

		// remove everything above
			if( $limit && ($limit < $this_count) ){
				$return = array();
			}

			$return[ $r ] = $this_count;
			$last_count = $this_count;
		}

		$return = array_reverse( $return, TRUE );
		$return = json_encode( $return );

		return $this->make('/http/view/response')
			->set_status_code('200')
			->set_view( $return )
			;
	}

	public function custom_get_notgeocodedcount( $args = array() )
	{
		$model = $this->_prepare_get_many( $args );
		$model
			->where( 'latitude', 'IN', array(NULL,0) )
			->where( 'longitude', 'IN', array(NULL,0) )
			;
		$return = $model
			->count()
			;

		return $this->make('/http/view/response')
			->set_status_code('200')
			->set_view( $return )
			;
	}

	public function custom_get_notgeocoded( $args = array() )
	{
		$model = $this->_prepare_get_many( $args );
		$model
			->where( 'latitude', 'IN', array(NULL,0) )
			->where( 'longitude', 'IN', array(NULL,0) )
			;

		$entries = $model
			->fetch_many()
			;
		$return = array();
		foreach( $entries as $e ){
			$e = $e->run('to-array');
			$return[] = $e;
		}

		$return = json_encode( $return );
		return $this->make('/http/view/response')
			->set_status_code('200')
			->set_view( $return )
			;
	}
}