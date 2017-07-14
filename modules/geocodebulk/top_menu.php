<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class GeocodeBulk_Top_Menu_LC_HC_MVC extends _HC_MVC
{
	public function before_children( $args, $src )
	{
		$is_setup = $this->make('/setup/lib')->run('is-setup');
		if( ! $is_setup ){
			return;
		}

		$api = $this->make('/http/lib/api')
			->request('/api/locations')
			;
		$api
			->add_param('custom', 'notgeocodedcount')
			;

		$count = $api
			->get()
			->response()
			;
// echo "COUNT = '$count'<br>";
// exit;
		if( ! $count ){
			return;
		}

		$label = HCM::__('Geocode');
		$label .= ' (' . $count . ')';

		$link = $this->make('/html/view/link')
			->to('')
			->add( $this->make('/html/view/icon')->icon('exclamation') )
			->add( $label )
			;

		$src
			->add( 'geocodebulk', $link )
			;
	}
}