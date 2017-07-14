<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class ModelSearch_Controller_HC_MVC extends _HC_MVC
{
	public function route_index()
	{
		$post = $this->make('/input/lib')->post();

		$form = $this->make('form');
		$form->grab( $post );
		$values = $form->values();

		$redirect_to = $this->make('/html/view/link')
			->to('-referrer-', array('-search' => $values['search'], '-page' => NULL))
			->href()
			;
		return $this->make('/http/view/response')
			->set_redirect($redirect_to) 
			;
	}
}