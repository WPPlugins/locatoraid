<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Flashdata_Form_View_HC_MVC extends _HC_MVC
{
	public function before_render( $args, $src )
	{

		$form = $src->form();
		if( ! $form ){
			return;
		}

		$session = $this->make('/session/lib');
		$form_errors = $session->flashdata('form_errors');
		$form_values = $session->flashdata('form_values');
		if( ! ($form_errors OR $form_values) ){
			return;
		}

		$slug = $form->slug();

		if( ! (isset($form_errors[$slug]) OR isset($form_values[$slug])) ){
			return;
		}

		if( isset($form_errors[$slug]) ){
			$form->set_errors( $form_errors[$slug] );
		}
		if( isset($form_values[$slug]) ){
			$form->set_values( $form_values[$slug] );
		}

		$src->set_form( $form );
		return;
	}

	public function before_set_form( $args, $src )
	{
		$form = array_shift( $args );
		if( ! $form ){
			return;
		}

		$session = $this->make('/session/lib');
		$form_errors = $session->flashdata('form_errors');
		$form_values = $session->flashdata('form_values');
		if( ! ($form_errors OR $form_values) ){
			return;
		}

		$slug = $form->slug();
		if( ! (isset($form_errors[$slug]) OR isset($form_values[$slug])) ){
			return;
		}

		if( isset($form_errors[$slug]) ){
			$form->set_errors( $form_errors[$slug] );
		}
		if( isset($form_values[$slug]) ){
			$form->set_values( $form_values[$slug] );
		}

		$src->set_form( $form );
		return;
	}
}