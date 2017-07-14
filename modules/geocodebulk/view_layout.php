<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class GeocodeBulk_View_Layout_LC_HC_MVC extends _HC_MVC
{
	public function header( $total_count )
	{
		if( $total_count ){
			$return = sprintf(HCM::_n('%d Location To Geocode', '%d Locations To Geocode', $total_count), $total_count);
		}
		else {
			$return = HCM::__('No Locations To Geocode');
		}
		return $return;
	}

	public function menubar( $total_count )
	{
		$menubar = $this->make('/html/view/container');

	// LIST
		$menubar->add(
			'list',
			$this->make('/html/view/link')
				->to('/locations')
				->add( $this->make('/html/view/icon')->icon('arrow-left') )
				->add( HCM::__('Locations') )
			);

		return $menubar;
	}

	public function render( $content, $total_count )
	{
		$menubar = $this->run('menubar', $total_count);
		$header = $this->run('header', $total_count);

		$out = $this->make('/layout/view/content-header-menubar')
			->set_content( $content )
			->set_header( $header )
			->set_menubar( $menubar )
			;

		return $out;
	}
}