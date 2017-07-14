<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Forgot_Controller_Send_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$form = $this->make('form/forgot');
		$post = $this->make('/input/lib')->post();
		$form->grab( $post );
		$values = $form->values();

		if( ! $values['email'] ){
			$redirect_to = $this->make('/html/view/link')
				->to('-referrer-')
				->href()
				;
			return $this->make('/http/view/response')
				->set_redirect($redirect_to) 
				;
		}

		$email = $values['email'];
		
		$api = $this->make('/http/lib/api')
			->request('/api/users')
			->add_param('email', $email)
			;
		$user = $api
			->get()
			->response()
			;

		if( ! $user ){
			$error = HCM::__('User not found');
			$msgbus = $this->make('/msgbus/lib');
			$msgbus->add('error', $error);

			$redirect_to = $this->make('/html/view/link')
				->to('-referrer-')
				->href()
				;
			return $this->make('/http/view/response')
				->set_redirect($redirect_to) 
				;
		}

		$user = array_shift( $user );
		$user_id = $user['id'];

		$new_password = HC_Lib2::generate_rand( 12, array('caps' => FALSE, 'hex' => TRUE) );

		$values = array(
			'password'	=> $new_password
			);

		$model = $this->make('model/login')
			->where_id('=', $user_id )
			->fetch_one()
			;

		$model->from_array( $values );
		$model->run('save');

		$msg = array();
		$msg['email'] = HCM::__('Email') . ': ' . $email;
		$msg['password'] = HCM::__('New Password') . ': ' . $new_password;
		$to = $this->make('/html/view/link')
			->to('/')
			->href()
			;
		$msg['address'] = '<a href="' . $to . '">' . $to . '</a>';

		$subject = HCM::__('Password Reset');
		$app_title = isset($this->app->app_config['nts_app_title']) ? $this->app->app_config['nts_app_title'] : '';
		if( $app_title ){
			$subject = $app_title . ': ' . $subject;
		}

		$message = join("\n", $msg);
		$message = nl2br( $message );

		if( $email != 'test@test.com' ){
			mail( $email, $subject, $message );
		}

		$msg = HCM::__('Password reset message has been sent to your email');
		$msgbus = $this->make('/msgbus/lib');
		$msgbus->add('message', $msg);

		$redirect_to = $this->make('/html/view/link')
			->to('/')
			->href()
			;
		return $this->make('/http/view/response')
			->set_redirect($redirect_to) 
			;
	}
}