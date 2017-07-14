<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_Form_LC_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$inputs = array();

		$inputs['search'] = $this->make('/form/view/text')
			->add_attr('placeholder', HCM::__('Address or Zip Code'))
			->add_attr('class', 'hc-block')
			;

		foreach( $inputs as $k => $v ){
			$this
				->set_input( $k, $v )
				;
		}

		return $this;
	}
}