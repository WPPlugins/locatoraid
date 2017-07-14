<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Flashdata_Layout_View_HC_MVC extends _HC_MVC
{
	public function render()
	{
		$flash_out = NULL;

		$session = $this->make('/session/lib');

		$message = $session->flashdata('message');
		$error = $session->flashdata('error');
		$warning = $session->flashdata('warning');

		$form_errors = $session->flashdata('form_errors');
		$debug = $session->flashdata('debug');

		// $message = 'LALA';
		// $error = 'ERROR';
		// $debug = 'DEBUGME';

		if( $message OR $error OR $warning OR $form_errors OR $debug ){
			$flash_out = $this->make('/html/view/container');

			if( $form_errors ){
				$out = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hcj2-auto-dismiss')
					->add_attr('class', 'hcj2-alert')
					->add_attr('class', 'hc-mb2')
					->add_attr('class', 'hc-p0')
					->add_attr('class', 'hc-border')
					;

				$msg_view = $this->make('/html/view/list')
					->add_attr('class', 'hc-m0')
					->add_attr('style', 'border-width: 4px;')
					->add_attr('class', 'hc-border-left')
					->add_attr('class', 'hc-border-red')
					->add_attr('class', 'hc-block')
					;

				$msg_view2 = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hc-p2')
					->add( HCM::__('Please correct the form errors and try again') )
					->add(
						$this->make('/html/view/element')->tag('a')
							->add( $this->make('/html/view/icon')->icon('times') )
							->add_attr('class', 'hc-red')
							->add_attr('class', 'hc-closer')
							->add_attr('class', 'hcj2-alert-dismisser')
						)
					;
				$msg_view->add( $msg_view2 );

				$out->add( $msg_view );

				$flash_out->add( $out );
			}

			if( $message ){
				$out = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hcj2-auto-dismiss')
					->add_attr('class', 'hcj2-alert')
					->add_attr('class', 'hc-mb2')
					->add_attr('class', 'hc-p0')
					->add_attr('class', 'hc-border')
					;

				if( ! is_array($message) ){
					$message = array( $message );
				}

				$msg_view = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hc-m0')
					->add_attr('style', 'border-width: 4px;')
					->add_attr('class', 'hc-border-left')
					->add_attr('class', 'hc-border-olive')
					->add_attr('class', 'hc-block')
					;

				foreach( $message as $m ){
					$msg_view2 = $this->make('/html/view/element')->tag('div')
						->add_attr('class', 'hc-p2')

						->add( $m )
						->add(
							$this->make('/html/view/element')->tag('a')
								->add( $this->make('/html/view/icon')->icon('times') )
								->add_attr('class', 'hc-red')
								->add_attr('class', 'hc-closer')
								->add_attr('class', 'hcj2-alert-dismisser')
							)
						;
					$msg_view->add( $msg_view2 );
				}
				$out->add( $msg_view );

				$flash_out->add( $out );
			}

			if( $error ){
				$out = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hcj2-auto-dismiss')
					->add_attr('class', 'hcj2-alert')
					->add_attr('class', 'hc-mb2')
					->add_attr('class', 'hc-p0')
					->add_attr('class', 'hc-border')
					;

				if( ! is_array($error) ){
					$error = array( $error );
				}

				$msg_view = $this->make('/html/view/list')
					->add_attr('class', 'hc-m0')
					->add_attr('style', 'border-width: 4px;')
					->add_attr('class', 'hc-border-left')
					->add_attr('class', 'hc-border-red')
					->add_attr('class', 'hc-block')
					;

				foreach( $error as $m ){
					$msg_view2 = $this->make('/html/view/element')->tag('div')
						->add_attr('class', 'hc-p2')

						->add( $m )
						->add(
							$this->make('/html/view/element')->tag('a')
								->add( $this->make('/html/view/icon')->icon('times') )
								->add_attr('class', 'hc-red')
								->add_attr('class', 'hc-closer')
								->add_attr('class', 'hcj2-alert-dismisser')
							)
						;
					$msg_view->add( $msg_view2 );
				}
				$out->add( $msg_view );

				$flash_out->add( $out );
			}

			if( $warning ){
				$out = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hcj2-auto-dismiss')
					->add_attr('class', 'hcj2-alert')
					->add_attr('class', 'hc-mb2')
					->add_attr('class', 'hc-p0')
					->add_attr('class', 'hc-border')
					;

				if( ! is_array($warning) ){
					$warning = array( $warning );
				}

				$msg_view = $this->make('/html/view/list')
					->add_attr('class', 'hc-m0')
					->add_attr('style', 'border-width: 4px;')
					->add_attr('class', 'hc-border-left')
					->add_attr('class', 'hc-border-orange')
					->add_attr('class', 'hc-block')
					;

				foreach( $warning as $m ){
					$msg_view2 = $this->make('/html/view/element')->tag('div')
						->add_attr('class', 'hc-p2')

						->add( $m )
						->add(
							$this->make('/html/view/element')->tag('a')
								->add( $this->make('/html/view/icon')->icon('times') )
								->add_attr('class', 'hc-red')
								->add_attr('class', 'hc-closer')
								->add_attr('class', 'hcj2-alert-dismisser')
							)
						;
					$msg_view->add( $msg_view2 );
				}
				$out->add( $msg_view );

				$flash_out->add( $out );
			}

			if( $debug ){
				$out = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hc-theme-box')
					->add_attr('class', 'hc-border')
					->add_attr('class', 'hc-border-orange')
					;

				if( ! is_array($debug) ){
					$debug = array( $debug );
				}

				$msg_view = $this->make('/html/view/list')
					->add_attr('class', 'hc-m0')
					;
				foreach( $error as $m ){
					$msg_view2 = $this->make('/html/view/element')->tag('div')
						->add_attr('class', 'hc-p1')

						->add( $m )
						;
					$msg_view->add( $msg_view2 );
				}
				$out->add( $msg_view );

				$flash_out->add( $out );
			}
		}

		return $flash_out;
	}
}