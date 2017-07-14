<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Conf_Form_Address_LC_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$this
			->set_input( 'locations_address:format',
				$this->make('/form/view/textarea')
					->set_label( HCM::__('Address Format') )
					->add_attr('rows', 8)
					->add_attr('cols', 32)
					->add_validator( $this->make('/validate/required') )
				)
			;

		return $this;
	}

	public function render_input( $input_name ){
		switch( $input_name ){
			case 'locations_address:format':
				$return = $this->make('/html/view/list-inline')
					->set_gutter(3)
					;

				$return
					->add( parent::render_input($input_name) )
					->add(
						$this->make('/html/view/label-input')
							->set_label( HCM::__('Default Setting') )
							->set_content( 
								nl2br(
									'{STREET}
									{CITY} {STATE} {ZIP}
									{COUNTRY}'
									)
								)
						)
					;

				break;

			default:
				$return = parent::render_input( $input_name );
		}

		return $return;
	}
}