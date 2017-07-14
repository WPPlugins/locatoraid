<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Zoom_Controller_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$args = $this->make('/app/lib/args')->run('parse', func_get_args());
		$id = $args->get('id');

		$api = $this->make('/http/lib/api')
			->request('/api/users')
			->add_param('id', $id)
			->add_param('with', '-all-')
			;

		$model = $api
			->get()
			->response()
			;

		$view = $this->make('zoom/view')
			->run('render', $model)
			;
		$view = $this->make('zoom/view/layout')
			->run('render', $view, $model)
			;
		$view = $this->make('/layout/view/body')
			->set_content($view)
			;
		return $this->make('/http/view/response')
			->set_view($view) 
			;
	}
}