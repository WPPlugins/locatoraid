<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Geocode_Index_View_LC_HC_MVC extends _HC_MVC
{
	public function render( $location )
	{
		$out = $this->make('/html/view/list')
			->set_gutter(2)
			->add_attr('class', 'hcj2-container')
			;

		$id = $location['id'];

		$p = $this->make('/locations/presenter');
		$p->set_data( $location );
		$address = $p->run('present-address');

		$geocoder = $this->make('/geocode/lib');
		$escape_address = $geocoder->run('prepare-address', $address);
		$escape_address = addslashes( $escape_address );

	// map
		$save_url = $this->make('/html/view/link')
			->to('save',
				array(
					'id'		=> $location['id'],
					'latitude'	=> '_LATITUDE_',
					'longitude'	=> '_LONGITUDE_',
					)
				)
			->href()
			;

		$map_id = 'hclc_map';
		$map = $this->make('/html/view/element')->tag('div')
			->add_attr('id', $map_id)
			->add_attr('style', 'height: 15em;')

			->add_attr('data-address', $escape_address)
			->add_attr('data-save-url', $save_url)
			;

		$out
			->add( $map )
			;

		return $out;
	}
}