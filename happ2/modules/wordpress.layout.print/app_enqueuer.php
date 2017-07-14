<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class WordPress_Layout_Print_App_Enqueuer_HC_MVC extends _HC_MVC
{
	public function after_get_scripts( $return )
	{
		$is_print_view = $this->make('/print/controller')->run('is-print-view');
		if( $is_print_view ){
			$return = array();
		}
		return $return;
	}

	public function after_get_styles( $return )
	{
		$is_print_view = $this->make('/print/controller')->run('is-print-view');
		if( $is_print_view ){
			unset( $return['javascript'] );
			unset( $return['datepicker'] );

			$new_params = array();
			$new_params['reset2'] = 'happ2/assets/css/hc-1-reset.css';

			$return = array_merge($new_params, $return);
		}

		return $return;
	}
}