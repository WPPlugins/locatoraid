<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Index_View_Layout_LC_HC_MVC extends _HC_MVC
{
	public function header()
	{
		$return = HCM::__('Locations');
		return $return;
	}

	public function menubar()
	{
		$return = $this->make('/html/view/container');

		$return->add(
			'new',
			$this->make('/html/view/link')
				->to('new')
				// ->add( $this->make('/html/view/icon')->icon('plus') )
				->add( HCM::__('Add New') )
			);

		return $return;
	}

	public function render( $content )
	{
		$header = $this->run('header');
		$menubar = $this->run('menubar');

		$out = $this->make('/layout/view/content-header-menubar')
			->set_content( $content )
			->set_header( $header )
			->set_menubar( $menubar )
			;

		return $out;
	}
}