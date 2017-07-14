<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_WordPress_Form_Edit_HC_MVC extends _HC_Form
{
	public function extend( $return, $args, $user_form )
	{
		$return
			->set_input( 'username',
				$this->make('/form/view/text')
					->set_label( HCM::__('Username') )
				)
			;
		return $return;
	}
}