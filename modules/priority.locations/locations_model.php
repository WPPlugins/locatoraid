<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Priority_Locations_Locations_Model_LC_HC_MVC extends _HC_MVC
{
	public function before_fetch_many( $args, $src )
	{
		$src_order_by = $src->get_order_by();
		$order_by = array(
			'priority'	=> 'DESC'
			);

		$order_by = array_merge( $order_by, $src_order_by );
		$src->set_order_by( $order_by );
	}
}