<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class GeocodeBulk_View_LC_HC_MVC extends _HC_MVC
{
	public function render( $total_count )
	{
		$out = $this->make('/html/view/list')
			->set_gutter(2)
			->add_attr('class', 'hcj2-container')
			;

		if( $total_count ){
			$save_url = $this->make('/html/view/link')
				->to('/geocode/save',
					array(
						'id'		=> '_ID_',
						'latitude'	=> '_LATITUDE_',
						'longitude'	=> '_LONGITUDE_',
						)
					)
				->href()
				;
			$json_url = $this->make('/html/view/link')
				->to('json')
				->href()
				;

			$map_id = 'hclc_map';
			$map = $this->make('/html/view/element')->tag('div')
				->add_attr('id', $map_id)
				->add_attr('class', 'hc-p1')
				->add_attr('class', 'hc-b1')

				->add_attr('data-json-url', $json_url)
				->add_attr('data-save-url', $save_url)
				;

			$out
				->add( $map )
				;
		}

		return $out;
	}
}