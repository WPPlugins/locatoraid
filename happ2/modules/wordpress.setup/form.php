<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Wordpress_Setup_Form_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$form = $this->make('/conf/controller')
			->form('wordpress-users')
			;
		return $form;
	}
}