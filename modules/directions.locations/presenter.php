<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Directions_Locations_Presenter_LC_HC_MVC extends _HC_MVC_Model_Presenter
{
	public function extend_fields( $return, $args, $src )
	{
		$fn = 'directions';
		$return[ $fn ] = HCM::__('Directions');
		return $return;
	}

	public function after_present_front( $return, $args, $src )
	{
		if( ! ($return['latitude'] && $return['longitude']) ){
			return $return;
		}

		if( ( ($return['latitude'] == -1) OR ($return['longitude'] == -1) ) ){
			return $return;
		}

		$search = array_shift( $args );
		$search_coordinates = array_shift( $args );
		if( ! $search_coordinates ){
			return $return;
		}
		if( ! is_array($search_coordinates) ){
			return $return;
		}

		$search_lat = array_shift( $search_coordinates );
		$search_lng = array_shift( $search_coordinates );
		if( ! ($search_lat && $search_lng) ){
			return $return;
		}

		$app_settings = $this->make('/app/lib/settings');

		$this_pname = 'fields:directions:use';
		$this_pname_config = $app_settings->run('get', $this_pname);
		if( ! $this_pname_config ){
			return $return;
		}

		$this_pname = 'fields:directions:label';
		$this_label = $app_settings->run('get', $this_pname);
		$this_label = strlen($this_label) ? $this_label : HCM::__('Directions');

		$link_args = array(
			'class'			=> 'lpr-directions',
			'href'			=> '#',
			'data-to-lat'	=> $return['latitude'],
			'data-to-lng'	=> $return['longitude'],
			'data-from-lat'	=> $search_lat,
			'data-from-lng'	=> $search_lng,
			);

		$link_view = '<a';
		foreach( $link_args as $k => $v ){
			$link_view .= ' ' . $k . '="' . $v . '"';
		}

		$link_view .= '>';
		$link_view .= $this_label;
		$link_view .= '</a>';

		$return['directions'] = $link_view;
		return $return;
	}
}