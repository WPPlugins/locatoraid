<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class WordPress_Layout_Layout_View_Content_Header_Menubar_HC_MVC extends _HC_MVC
{
	public function after_render( $return )
	{
		$header = $return->child('header');
		if( $header ){
			$wp_header_end = $this->make('/html/view/element')->tag('hr')
				->add_attr('class', 'wp-header-end')
				;
			$header
				->add($wp_header_end)
				;
			// $return
				// ->add( 'header', $header )
				// ;
		}
		
		return $return;
	}
}