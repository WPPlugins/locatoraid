<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Priority_Locations_Locations_Form_LC_HC_MVC extends _HC_MVC
{
	public function after_init( $return )
	{
		$inputs = array();

		$p = $this->make('/priority/presenter');
		$options = $p->run('present-options');
		$inputs['priority'] = 
			$this->make('/form/view/radio')
				->set_label( HCM::__('Priority') )
				->set_options( $options )
				->set_inline()
			;

		foreach( $inputs as $k => $v ){
			$return
				->set_input( $k, $v )
				;
		}

		$return
			->set_child_order( 'priority', 1 )
			;

		return $return;
	}
}