<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Controller_Notallowed_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$view = $this->make('view/notallowed');
		return $this->make('/http/view/response')
			->set_view($view)
			;
	}
}