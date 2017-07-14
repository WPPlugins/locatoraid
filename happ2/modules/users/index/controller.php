<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Index_Controller_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$model = $this->run('prepare-model');

		$entries = $model
			->get()
			->response()
			;
		$view = $this->make('index/view')
			->run('render', $entries)
			;
		$view = $this->make('index/view/layout')
			->run('render', $view)
			;
		$view = $this->make('/layout/view/body')
			->set_content($view)
			;
		return $this->make('/http/view/response')
			->set_view($view)
			;
	}

	/* prepares model - this can probably be extended by other modules */
	public function prepare_model()
	{
		$api = $this->make('/http/lib/api')
			->request('/api/users')
			;
		return $api;
	}
}