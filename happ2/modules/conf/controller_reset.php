<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Conf_Controller_Reset_HC_MVC extends _HC_MVC
{
	function route_index()
	{
		$args = hc2_parse_args( func_get_args() );
		if( ! isset($args['pname']) ){
			$redirect_to = $this->make('/html/view/link')
				->to('-referrer-')
				->href()
				;
			return $this->make('/http/view/response')
				->set_redirect($redirect_to) 
				;
		}

		$pname = $args['pname'];

		$app_settings = $this->make('/app/lib/settings');
		$app_settings->run('reset', $pname );

		$model = $this->make('model');
		$model->run('save');

	// redirect back
		$redirect_to = $this->make('/html/view/link')
			->to('-referrer-')
			->href()
			;

		return $this->make('/http/view/response')
			->set_redirect($redirect_to) 
			;
	}
}