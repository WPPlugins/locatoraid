<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Wordpress_Conf_Controller_HC_MVC extends _HC_MVC
{
	public function before_form( $args, $src )
	{
		$return = NULL;
		$tab = array_shift($args);

		switch( $tab ){
			case 'wordpress-users':
				$return = $this->make('form');
				break;
		}

		if( $return !== NULL ){
			return $return;
		}
	}
}