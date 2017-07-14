<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Edit_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$args = $this->make('/app/lib/args')->parse( func_get_args() );
		$id = $args->get('id');

		$api = $this->make('/http/lib/api')
			->request('/api/locations')
			->add_param('with', '-all-')
			->add_param('id', $id)
			;

		$model = $api
			->get()
			->response()
			;

		$view = $this->make('edit/view')
			->run('render', $model)
			;

		$view = $this->make('edit/view/layout')
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