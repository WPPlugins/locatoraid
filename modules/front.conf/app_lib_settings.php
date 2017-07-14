<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_Conf_App_Lib_Settings_LC_HC_MVC extends _HC_MVC
{
	public function after_get( $return, $args, $src )
	{
		$pname = array_shift( $args );

		switch( $pname ){
			case 'front_list:template':
				$advanced = $src->get('front_list:advanced');
				if( (! $advanced) OR (! strlen($return)) ){
					$return = $this->make('/front/view/list/template');
				}
				break;

			case 'front_map:template':
				$advanced = $src->get('front_map:advanced');
				if( (! $advanced) OR (! strlen($return)) ){
					$return = $this->make('/front/view/map/template');
				}
				break;
		}

		return $return;
	}
}