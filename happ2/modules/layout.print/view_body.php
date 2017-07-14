<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Layout_Print_View_Body_HC_MVC extends _HC_MVC 
{
	public function remove_top_header( $args, $src )
	{
		$is_print_view = $this->make('/print/controller')->run('is-print-view');
		if( $is_print_view ){
			$return = $this->make('/html/view/container');
			return $return;
		}
	}
}