<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Conf_Form_Templates_LC_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$this
			->set_input( 'locations_templates:in_list',
				$this->make('/form/view/textarea')
					->set_label( HCM::__('View In List') )
					->add_attr('rows', 8)
					// ->add_attr('cols', 48)
					->add_attr('style', 'width: 100%')
					->add_validator( $this->make('/validate/required') )
				)
			->set_input( 'locations_templates:on_map',
				$this->make('/form/view/textarea')
					->set_label( HCM::__('View On Map') )
					->add_attr('rows', 8)
					// ->add_attr('cols', 48)
					->add_attr('style', 'width: 100%')
					->add_validator( $this->make('/validate/required') )
				)
			;

		return $this;
	}

	public function render_input( $input_name ){
		$return = NULL;
		$app_settings = $this->make('/app/lib/settings');

		switch( $input_name ){
			case 'locations_templates:in_list':
				$return = $this->make('/html/view/list')
					;

				$return
					->add( parent::render_input($input_name) )
					;

				$current = $app_settings->run('get', $input_name);
				$default = $app_settings->run('get-default', $input_name);
				if( $current != $default ){
					$return
						->add(
							$this->make('/html/view/link')
								->to('/conf/reset', array('pname' => $input_name))
								->add( HCM::__('Problems? Reset to Defaults') )
							)
						->add_attr('class', 'hc-mb3')
						;
				}
				break;

			case 'locations_templates:on_map':
				$return = $this->make('/html/view/list')
					;

				$return
					->add( parent::render_input($input_name) )
					;

				$current = $app_settings->run('get', $input_name);
				$default = $app_settings->run('get-default', $input_name);
				if( $current != $default ){
					$return
						->add(
							$this->make('/html/view/link')
								->to('/conf/reset', array('pname' => $input_name))
								->add( HCM::__('Problems? Reset to Defaults') )
							)
						->add_attr('class', 'hc-mb3')
						;
				}

				break;

			default:
				$return = parent::render_input( $input_name );
				break;
		}

		return $return;
	}
}