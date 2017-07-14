<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Conf_Index_View_HC_MVC extends _HC_MVC
{
	public function render( $form, $args )
	{
		$link = $this->make('/html/view/link')
			->to('update')
			->href()
			;

		$display_form = $this->make('/html/view/form')
			->add_attr('action', $link )
			->set_form( $form )
			;

		$form_view = $form
			->run('render', $args)
			;
		$display_form
			->add( $form_view )
			;

		if( ! $form->readonly() ){
			$buttons = $this->make('/html/view/buttons-row')
				;

			$buttons->add(
				$this->make('/html/view/element')->tag('input')
					->add_attr('type', 'submit')
					->add_attr('title', HCM::__('Save') )
					->add_attr('value', HCM::__('Save') )
					->add_attr('class', 'hc-theme-btn-submit', 'hc-theme-btn-primary')
				);

			$display_form->add( $buttons );
		}

		return $display_form;
	}
}