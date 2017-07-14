<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Logout_Controller_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$auth = $this->make('lib');
		$auth->logout();
		return $this->make('/http/view/response')
			->set_redirect('/') 
			;
	}
}