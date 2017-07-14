<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Wordpress_Auth_Layout_View_Profile_HC_MVC extends _HC_MVC
{
	public function render( $user )
	{
		$return = NULL;

		$auth = $this->make('/auth/lib');
		$logged_in = $auth->run('logged-in');
		if( ! $logged_in ){
			$return = $this->run('render-anon');
		}
		return $return;
	}

	public function render_anon()
	{
		$return = $this->make('/html/view/list-inline')
			->add_attr('class', 'hc-mt2')
			;

		$return_to = $this->make('/html/view/link')
			->to()
			->href()
			;
		$href = wp_login_url( $return_to );

		$return->add(
			'login',
			$this->make('/html/view/element')->tag('a')
				->add_attr('href', $href)
				->add_attr('class', 'hc-p2')
				->add_attr('class', 'hc-darkgray')
				->add_attr('class', 'hc-btn')
				->add( HCM::__('Log In') )
				->add_attr('title', HCM::__('Log In'))
			);

		return $return;
	}
}