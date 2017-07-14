<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Wordpress_Auth_Layout_Controller_HC_MVC extends _HC_MVC
{
	public function extend_top_menu( $return, $args, $src )
	{
		$slug = $this->make('/http/lib/uri')->slug();
		$slug = explode('/', $slug);
		$module = array_shift($slug);
		if( in_array($module, array('setup')) ){
			return;
		}

		$user = $this->make('/auth/model/user')->get();

		$profile = $this->make('view/profile')
			->render( $user->run('to-array') )
			;
		$return->add('profile', $profile);
		return $return;
	}
}