<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Conf_Controller_LC_HC_MVC extends _HC_Form
{
	public function before_form( $args, $src )
	{
		$return = NULL;

		$tab = array_shift($args);
		
		switch( $tab ){
			case 'locations-address':
				$return = $this->make('form/address');
				break;
			case 'locations-templates':
				$return = $this->make('form/templates');
				break;
		}

		if( $return !== NULL ){
			return $return;
		}
	}
}