<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Search_View_LC_HC_MVC extends _HC_MVC
{
	public function render( $results = array(), $search = '', $search_coordinates = array() )
	{
		$p = $this->make('/locations/presenter');
		for( $ii = 0; $ii < count($results); $ii++ ){
			$p->set_data( $results[$ii] );

			$results[$ii] = $p->run('present-front', $search, $search_coordinates);
			// $results[$ii]['address'] = $p->run('present-address');
			if( array_key_exists('distance', $results[$ii]) ){
				$results[$ii]['distance_raw'] = $results[$ii]['distance'];
				$results[$ii]['distance'] = $p->run('present-distance');
			}
		}

		$return = array(
			'search'				=> $search,
			'search_coordinates'	=> $search_coordinates,
			'results'				=> $results,
			);

		$return = json_encode( $return );
		return $return;
	}
}