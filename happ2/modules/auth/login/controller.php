<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Login_Controller_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$form = $this->make('form/login');
	
		$view = $this->make('login/view')
			->run('render', $form)
			;
		$view = $this->make('login/view/layout')
			->run('render', $view)
			;
		$view = $this->make('/layout/view/body')
			->set_content($view)
			;
		return $this->make('/http/view/response')
			->set_view($view)
			;
	}

	public function route_login()
	{
		$form = $this->make('form/login');
		$post = $this->make('/input/lib')->post();
		$form->grab( $post );
		$values = $form->values();

		if( ! ($values && isset($values['username']) && strlen($values['username']) && isset($values['password']) && strlen($values['password']) ) ){
			return $this->make('/http/view/response')
				->set_redirect('auth/login') 
				;
		}
		$auth = $this->make('lib');
		$user_id = $auth->check( $values['username'], $values['password'] );

		if( $user_id ){
			$remember = isset($values['remember']) && $values['remember'];
			$auth->login( $user_id, $remember );

			return $this->make('/http/view/response')
				->set_redirect('/') 
				;
		}
		else {
			$error = HCM::__('Wrong username or password');
			$msgbus = $this->make('/msgbus/lib');
			$msgbus->add('error', $error);

			return $this->make('/http/view/response')
				->set_redirect('auth/login') 
				;
		}
	}
}