<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_View_Notallowed_HC_MVC extends _HC_MVC
{
	public function render()
	{
		$out = $this->make('/html/view/container');

		$out
			->add(
				$this->make('/html/view/element')->tag('h1')
					->add( HCM::__('Access denied') )
					->add_attr('class', 'hc-mb2')
				)
			->add(
				$this->make('/html/view/element')->tag('p')
					->add( HCM::__('You do not have sufficient permissions to access this page') )
				)
			;

		return $out;
	}
}