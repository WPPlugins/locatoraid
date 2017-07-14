<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Login_View_Layout_HC_MVC extends _HC_MVC
{
	public function header()
	{
		$return = HCM::__('Log In');
		return $return;
	}

	public function render( $content )
	{
		$header = $this->run('header');

		$out = $this->make('/layout/view/content-header-menubar')
			->set_content( $content )
			->set_header( $header )
			;

		return $out;
	}
}