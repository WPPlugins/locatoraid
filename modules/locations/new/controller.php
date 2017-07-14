<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_New_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$view = $this->make('new/view')
			->run('render')
			;
		$view = $this->make('new/view/layout')
			->run('render', $view)
			;
		$view = $this->make('/layout/view/body')
			->set_content($view)
			;
		return $this->make('/http/view/response')
			->set_view($view)
			;
	}
}