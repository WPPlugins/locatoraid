<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Geocode_Index_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
	// add javascript
		$this->make('/app/enqueuer')
			->run('register-script', 'lc-geocode', 'modules/geocode/assets/js/geocode.js')
			->run('enqueue-script', 'lc-geocode')
			;

		$args = $this->make('/app/lib/args')->parse( func_get_args() );
		$id = $args->get('id');

		$location = $this->make('/http/lib/api')
			->request('/api/locations')
			->add_param('id', $id)
			->get()
			->response()
			;

		$view = $this->make('index/view')
			->run('render', $location)
			;
		$view = $this->make('index/view/layout')
			->run('render', $view, $location)
			;
		$view = $this->make('/layout/view/body')
			->set_content($view)
			;
		return $this->make('/http/view/response')
			->set_view($view) 
			;
	}
}