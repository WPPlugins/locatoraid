<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Security_View_Form_HC_MVC extends _HC_MVC
{
	public function extend_csrf( $form )
	{
		$security = $this->make('lib');

		$csrf_name = $security->get_csrf_token_name();
		$csrf_value = $security->get_csrf_hash();

		if( strlen($csrf_name) && strlen($csrf_value) ){
			$hidden = $this->make('/form/view/hidden')
				->set_name($csrf_name)
				->set_value($csrf_value)
				;
			$form->add(
				$this->make('/html/view/element')->tag('div')
					->add_attr('style', 'display:none')
					->add( $hidden )
				);
		}
		return $form;
	}
}