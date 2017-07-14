<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class GeocodeBulk_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$total_count = $this->make('/http/lib/api')
			->request('/api/locations')
			->add_param('custom', 'notgeocodedcount')
			->get()
			->response()
			;

		if( $total_count ){
		// add javascript
			$this->make('/app/enqueuer')
				->run('register-script', 'lc-geocodebulk', 'modules/geocodebulk/assets/js/geocode.js')
				->run('enqueue-script', 'lc-geocodebulk')
				;
		}

		$view = $this->make('view')
			->run('render', $total_count)
			;
		$view = $this->make('view/layout')
			->run('render', $view, $total_count)
			;
		$view = $this->make('/layout/view/body')
			->set_content($view)
			;
		return $this->make('/http/view/response')
			->set_view($view) 
			;
	}
}