<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Edit_Form_LC_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$p = $this->make('/locations/presenter');
		$labels = $p->run('fields-labels');

		$inputs = array(
			'name'	=>
				$this->make('/form/view/text')
					// ->set_label( HCM::__('Name') )
					// ->add_attr('size', 32)
					->add_attr('class', 'hc-block')
					->add_attr('class', 'hc-fs5')
					->add_attr('style', 'height: 2em;')

					->add_validator( $this->make('/validate/required') )
				,
			'street1'	=>
				$this->make('/form/view/text')
					->set_label( HCM::__('Street Address 1') )
					// ->add_attr('size', 42)
					->add_attr('class', 'hc-block')
					// ->add_validator( $this->make('/validate/required') )
				,
			'street2'	=>
				$this->make('/form/view/text')
					->set_label( HCM::__('Street Address 2') )
					// ->add_attr('size', 42)
					->add_attr('class', 'hc-block')
				,
			'city'	=>
				$this->make('/form/view/text')
					->set_label( HCM::__('City') )
					->add_attr('size', 32)
				,
			'state'	=>
				$this->make('/form/view/text')
					->set_label( HCM::__('State') )
					->add_attr('size', 16)
				,
			'zip'	=>
				$this->make('/form/view/text')
					->set_label( HCM::__('Zip Code') )
					->add_attr('size', 16)
				,
			'country'	=>
				$this->make('/form/view/text')
					->set_label( HCM::__('Country') )
					->add_attr('size', 32)
				,
			'phone'	=>
				$this->make('/form/view/text')
					->set_label( (isset($labels['phone']) && strlen($labels['phone'])) ? $labels['phone'] : HCM::__('Phone') )
					// ->add_attr('size', 16)
					->add_attr('class', 'hc-block')
				,
			'website'	=>
				$this->make('/form/view/text')
					->set_label( (isset($labels['website']) && strlen($labels['website'])) ? $labels['website'] : HCM::__('Website') )
					// ->add_attr('size', 42)
					->add_attr('class', 'hc-block')
				,
			);

		$always_show = array('name', 'street1', 'street2', 'city', 'state', 'zip', 'country');
		$app_settings = $this->make('/app/lib/settings');
		foreach( $inputs as $k => $v ){
			if( ! in_array($k, $always_show) ){
				$this_field_pname = 'fields:' . $k  . ':use';
				$this_field_conf = $app_settings->run('get', $this_field_pname);
				if( ! $this_field_conf ){
					continue;
				}

				$this_field_pname = 'fields:' . $k  . ':label';
				$this_label = $app_settings->run('get', $this_field_pname);
				if( strlen($this_label) ){
					$v
						->set_label( $this_label )
						;
				}
			}

			$this
				->set_input( $k, $v )
				;
		}

		foreach( $inputs as $k => $v ){
			$this
				->set_input( $k, $v )
				;
		}

		return $this;
	}

	public function inputs()
	{
		$return = parent::inputs();
		$keys = array_keys( $return );

		$always_show = array('name', 'street1', 'street2', 'city', 'state', 'zip', 'country');
		$app_settings = $this->make('/app/lib/settings');

		$p = $this->make('/locations/presenter');
		$labels = $p->run('fields-labels');

		foreach( $keys as $k ){
			if( ! in_array($k, $always_show) ){
				$this_field_pname = 'fields:' . $k  . ':use';
				$this_field_conf = $app_settings->run('get', $this_field_pname);
				if( ! $this_field_conf ){
					unset( $return[$k] );
					continue;
				}

				if( array_key_exists($k, $labels) ){
					$return[$k]
						->set_label( $labels[$k] )
						;
				}
			}
		}

		return $return;
	}
}