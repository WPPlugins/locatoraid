<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_Conf_Form_Fields_LC_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$app_settings = $this->make('/app/lib/settings');

		$p = $this->make('/locations/presenter');
		$fields = $p->run('fields');
		$no_label_for = array('name', 'address');
		// $no_label_for = array();

		foreach( $fields as $fn => $flabel ){
			if( ! in_array($fn, $no_label_for) ){
				$this
					->set_input( 'fields:' . $fn  . ':label',
						$this->make('/form/view/text')
							// ->add_attr('size', 32)
							->add_attr('style', 'width: 100%;')
							// ->add_validator( $this->make('/validate/required') )
						)
					;
			}

			$checkboxes = array( 'use' );
			foreach( $checkboxes as $ch ){
				$this_field_pname = 'fields:' . $fn  . ':' . $ch;
				$this_field_conf = $app_settings->run('get', $this_field_pname);

				if( ($this_field_conf === TRUE) OR ($this_field_conf === FALSE) ){
					$this
						->set_input( $this_field_pname,
							$this->make('/form/view/label')
							)
						;
				}
				else {
					$this
						->set_input( $this_field_pname,
							$this->make('/form/view/checkbox')
							)
						;
				}
			}
		}

		return $this;
	}

	public function render(){
		// $out = $this->make('/html/view/table')
		$out = $this->make('/html/view/table-responsive')
			// ->add_attr('class', 'hc-mx1')
			// ->add_attr('class', 'hc-my1')
			;

		$header = array(
			'field'	=> HCM::__('Field'),
			'label'	=> HCM::__('Label'),
			'use'	=> HCM::__('Use'),
			);

		$rows = array();

		$p = $this->make('/locations/presenter');
		$fields = $p->run('fields');

		foreach( $fields as $fn => $flabel ){
			$rows[$fn] = array(
				'field'	=> $flabel,
				'label'	=> $this->render_input('fields:' . $fn  . ':label'),
				'use'	=> $this->render_input('fields:' . $fn  . ':use'),
				);
		}

		$out
			->set_header( $header )
			->set_rows( $rows )
			;

		return $out;
	}
}