<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Flashdata_Wordpress_Layout_Bootstrap_HC_MVC extends _HC_MVC
{
	public function run()
	{
		add_action( 'admin_notices', array($this, 'admin_notices') );
	}

	public function admin_notices()
	{
		$session = $this->make('/session/lib');

		$message = $session->flashdata('message');
		$error = $session->flashdata('error');
		$warning = $session->flashdata('warning');

		$form_errors = $session->flashdata('form_errors');
		$debug = $session->flashdata('debug');

		$out = NULL;

		if( $form_errors OR $error OR $message OR $warning ){
			$out = $this->make('/html/view/container');
		}

		if( $form_errors ){
			$this_out = $this->make('/html/view/element')->tag('div')
				->add_attr('class', array('notice', 'notice-error', 'is-dismissible'))
				;
			$this_out
				->add(
					$this->make('/html/view/element')->tag('p')
						->add( HCM::__('Please correct the form errors and try again') )
					)
				;
			$out
				->add( $this_out )
				;
		}

		if( $error ){
			if( ! is_array($error) ){
				$error = array( $error );
			}

			$this_out = $this->make('/html/view/element')->tag('div')
				->add_attr('class', array('notice', 'notice-error', 'is-dismissible'))
				;
			foreach( $error as $e ){
				$this_out
					->add(
						$this->make('/html/view/element')->tag('p')
							->add( $e )
						)
					;
			}
			$out
				->add( $this_out )
				;
		}

		if( $message ){
			if( ! is_array($message) ){
				$message= array( $message );
			}

			$this_out = $this->make('/html/view/element')->tag('div')
				->add_attr('class', array('notice', 'notice-success', 'is-dismissible'))
				;

			foreach( $message as $e ){
				$this_out
					->add(
						$this->make('/html/view/element')->tag('p')
							->add( $e )
						)
					;
			}
			$out
				->add( $this_out )
				;
		}

		if( $warning ){
			if( ! is_array($warning) ){
				$warning= array( $warning );
			}

			$this_out = $this->make('/html/view/element')->tag('div')
				->add_attr('class', array('notice', 'notice-warning', 'is-dismissible'))
				;

			foreach( $warning as $e ){
				$this_out
					->add(
						$this->make('/html/view/element')->tag('p')
							->add( $e )
						)
					;
			}
			$out
				->add( $this_out )
				;
		}

		if( $out ){
			echo $out;
		}
	}
}