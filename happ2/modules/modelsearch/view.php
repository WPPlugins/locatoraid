<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class ModelSearch_View_HC_MVC extends _HC_MVC
{
	public function render( $search = '', $ajax = FALSE )
	{
		$out = $this->make('/html/view/container');
		$current_slug = $this->make('/http/lib/uri')->slug();

		$link = $this->make('/html/view/link')
			->to('/modelsearch')
			->href()
			;

		$form = $this->make('form')
			->set_values( array('search' => $search) )
			;

		$display_form = $this->make('/html/view/form')
			->add_attr('action', $link )
			->set_form( $form )
			;
		if( $ajax ){
			$display_form->set_ajax();
		}

		$display = $this->make('/html/view/list-inline')
			->set_gutter(1)
			;

		$inputs = $form->inputs();
		foreach( $inputs as $input ){
			$display->add( $input );
		}

		$buttons = $this->make('/html/view/list-inline')
			->set_gutter(1)
			;

		$button = $this->make('/html/view/element')->tag('input')
			->add_attr('type', 'submit')
			->add_attr('title', HCM::__('Search') )
			->add_attr('value', HCM::__('Search') )
			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
			->add_attr('class', 'hc-mt1')
			;
		$buttons->add( $button );

		if( $search ){
			$button2 = $this->make('/html/view/link')
				->to('-', array('-search' => NULL))
				->add( HCM::__('Reset') ) 
				->add_attr('class', 'hc-theme-btn-submit')
				->add_attr('class', 'hc-theme-btn-secondary')
				->add_attr('class', 'hc-mt1')
				;
			if( $ajax ){
				$button2
					->add_attr('class', 'hcj2-ajax-loader')
					;
			}
			$buttons->add( $button2 );
		}

		if( ! $form->readonly() ){
			$display->add( $buttons );
		}

		$display_form->add( $display );
		$out->add( $display_form );

		return $out;
	}
}