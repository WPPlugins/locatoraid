<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Publish_Wordpress_Controller_LC_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$view = $this->make('view')
			->run('render')
			;
		$view = $this->make('view/layout')
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