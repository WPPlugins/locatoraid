<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Forgot_Controller_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$form = $this->make('form/forgot');
	
		$view = $this->make('view/forgot')
			->run('render', $form)
			;
		$view = $this->make('/layout/view/body')
			->set_content($view)
			;
		return $this->make('/http/view/response')
			->set_view($view)
			;
	}
}