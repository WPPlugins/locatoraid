<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Print_Controller_HC_MVC extends _HC_MVC
{
	public function is_print_view()
	{
		$return = FALSE;
		$uri = $this->make('/http/lib/uri');
		$printview = $uri->arg('print');
		if( $printview == 'print' ){
			$return = TRUE;
		}
		return $return;
	}

	public function print_view_args()
	{
		$return = array(
			'print'	=> 'print',
			);
		return $return;
	}
}