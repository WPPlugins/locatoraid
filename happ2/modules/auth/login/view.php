<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Login_View_HC_MVC extends _HC_MVC
{
	public function render( $form )
	{
		$username_label = HCM::__('Email');

		$link = $this->make('/html/view/link')
			->to('login/login')
			->href()
			;

		$display_form = $this->make('/html/view/form')
			->add_attr('action', $link )
			;

		if( $form->exists('username') ){
			$display_form->add(
				$this->make('/html/view/label-input')
					->set_label( $username_label )
					->set_content( 
						$form->input('username')
						)
					->set_error( $form->input('username')->error() )
				);
		}

		if( $form->exists('password') ){
			$display_form->add(
				$this->make('/html/view/label-input')
					->set_label( HCM::__('Password') )
					->set_content( 
						$form->input('password')
						)
					->set_error( $form->input('password')->error() )
				);
		}

		if( $form->exists('remember') ){
			$display_form->add(
				$this->make('/html/view/label-input')
					->set_content( 
						$form->input('remember')
						)
					->set_error( $form->input('remember')->error() )
				);
		}

		if( ! $form->readonly() ){
			$buttons = $this->make('/html/view/buttons-row')
				;

			$buttons->add(
				$this->make('/html/view/element')->tag('input')
					->add_attr('type', 'submit')
					->add_attr('title', HCM::__('Log In') )
					->add_attr('value', HCM::__('Log In') )
					->add_attr('class', 'hc-theme-btn-submit', 'hc-theme-btn-primary')
				);

			$display_form->add( $buttons );
		}

		$out = $this->make('/html/view/container');
		$out->add( $display_form );

		$out->add(
			$this->make('/html/view/link')
				->to('forgot')
				->add( HCM::__('Lost your password?') )
			);
		return $out;
	}
}