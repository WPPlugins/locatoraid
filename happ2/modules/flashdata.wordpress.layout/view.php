<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Flashdata_Wordpress_Layout_View_HC_MVC extends _HC_MVC
{
	public function before_render( $args, $src )
	{
		// in admin show by admin notices
		if( is_admin() ){
			return;
		}

		$flash_out = $this->make('/flashdata.layout/view')
			->run('render')
			;

		if( ! $flash_out ){
			return;
		}

		$return = $this->make('/html/view/container')
			->add( $flash_out )
			->add( $return )
			;

		$src
			->set_content($return)
			;
	}
}