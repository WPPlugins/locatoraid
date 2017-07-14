<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Form_LC_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$inputs = array(
			'latitude'	=>
				$this->make('/form/view/text')
					->set_label( HCM::__('Latitude') )
				,
			'longitude'	=>
				$this->make('/form/view/text')
					->set_label( HCM::__('Longitude') )
				,
			);

		foreach( $inputs as $k => $v ){
			$this
				->set_input( $k, $v )
				;
		}

		return $this;
	}
}