<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_Conf_Controller_LC_HC_MVC extends _HC_MVC
{
	public function before_form( $args, $src )
	{
		$return = NULL;
		$tab = array_shift($args);

		switch( $tab ){
			case 'fields':
				$return = $this->make('form-fields');
				break;

			case 'front-map':
				$return = $this->make('form-on-map');
				break;

			case 'front-list':
				$return = $this->make('form-in-list');
				break;
		}

		if( $return !== NULL ){
			return $return;
		}
	}
}