<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Directions_Front_Front_View_LC_HC_MVC extends _HC_MVC
{
	public function after_render( $return )
	{
		$app_settings = $this->make('/app/lib/settings');

		$this_pname = 'fields:directions:use';
		$this_pname_config = $app_settings->run('get', $this_pname);
		if( ! $this_pname_config ){
			return $return;
		}

		$this->make('/app/enqueuer')
			->run('register-script', 'lc-directions-front', 'modules/directions.front/assets/js/directions.js' )
			->run('enqueue-script', 'lc-directions-front' )
			;
	}
}