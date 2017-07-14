<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_Print_View_Link_HC_MVC extends _HC_MVC
{
	public function print_view( $args, $src )
	{
		$is_print_view = $this->make('/print/controller')->run('is-print-view');
		if( $is_print_view ){
			if( $src->is_always_show() ){
				$src->set_readonly();
			}
			else {
				$src->hide();
			}
		}
	}
}