<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Maps_Google_Conf_Controller_HC_MVC extends _HC_Form
{
	public function before_form( $args, $src )
	{
		$return = NULL;

		$tab = array_shift($args);

		switch( $tab ){
			case 'maps-google':
				$return = $this->make('form');
				break;
		}

		if( $return !== NULL ){
			return $return;
		}
	}
}