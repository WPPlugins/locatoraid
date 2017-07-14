<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_Conf_Form_In_List_LC_HC_MVC extends _HC_Form
{
	public function _init()
	{
		$app_settings = $this->make('/app/lib/settings');

		$this_field_pname = 'front_list:advanced';
		$this_advanced = $app_settings->run('get', $this_field_pname);

		if( $this_advanced ){
			$this_field_pname = 'front_list:template';
			$this
				->set_input( $this_field_pname,
					$this->make('/form/view/textarea')
						->add_attr('rows', 14)
						->add_attr('class', 'hc-block')
					)
				;
		}
		else {
			$p = $this->make('/locations/presenter');
			$fields = $p->run('fields-labels');

			foreach( $fields as $fn => $flabel ){
				$checkboxes = array( 'show', 'w_label' );
				foreach( $checkboxes as $ch ){
					$this_field_pname = 'front_list:' . $fn  . ':' . $ch;
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
			
		}

		return $this;
	}

	public function render_advanced()
	{
		$out = parent::render();

		$links = $this->make('/html/view/list-inline')
			->set_gutter(2)
			;

		$links
			->add(
				$this->make('/html/view/link')
					->to( 'mode', array('what' => 'front-in-list', 'to' => 'basic') )
					->add( $this->make('/html/view/icon')->icon('arrow-left') )
					->add( HCM::__('Switch To Basic Mode') )
					->add_attr('class', 'hc-theme-btn-submit')
					// ->add_attr('class', 'hc-theme-btn-secondary')
				)
			;

		$app_settings = $this->make('/app/lib/settings');
		$this_field_pname = 'front_list:template';
		$this_template_raw = $app_settings->get($this_field_pname);
		if( strlen($this_template_raw) ){
			$links
				->add(
					$this->make('/html/view/link')
						->to( 'mode', array('what' => 'front-in-list', 'to' => 'reset') )
						->add( $this->make('/html/view/icon')->icon('times') )
						->add( HCM::__('Reset Template') )
						->add_attr('class', 'hc-theme-btn-submit')
						->add_attr('class', 'hcj2-confirm')
						->add_attr('class', 'hc-theme-btn-danger')
					)
				;
		}

		$out = $this->make('/html/view/list')
			->set_gutter(2)
			->add( $links )
			->add( $out )
			;

		return $out;
	}

	public function render(){
		$app_settings = $this->make('/app/lib/settings');

		$this_field_pname = 'front_list:advanced';
		$this_advanced = $app_settings->run('get', $this_field_pname);

		if( $this_advanced ){
			return $this->run('render-advanced');
		}

		// $out = $this->make('/html/view/table')
		$out = $this->make('/html/view/table-responsive')
			// ->add_attr('class', 'hc-mx1')
			// ->add_attr('class', 'hc-my1')
			;

		$header = array(
			'field'			=> HCM::__('Field'),
			'show_in_list'	=> HCM::__('Show In List'),
			'w_label'	=> HCM::__('With Label'),
			);

		$rows = array();

		$p = $this->make('/locations/presenter');
		$fields = $p->run('fields-labels');

		foreach( $fields as $fn => $flabel ){
			$rows[$fn] = array(
				'field'			=> $flabel,
				'show_in_list'	=> $this->render_input('front_list:' . $fn . ':show'),
				'w_label'	=> $this->render_input('front_list:' . $fn . ':w_label'),
				);
		}

		$out
			->set_header( $header )
			->set_rows( $rows )
			;

		$link_to_advanced = $this->make('/html/view/link')
			->to( 'mode', array('what' => 'front-in-list', 'to' => 'advanced') )
			->add( HCM::__('Switch To Advanced Mode') )
			->add( $this->make('/html/view/icon')->icon('arrow-right') )
			->add_attr('class', 'hc-theme-btn-submit')
			// ->add_attr('class', 'hc-theme-btn-secondary')
			;

		$out = $this->make('/html/view/list')
			->set_gutter(2)
			->add( $link_to_advanced )
			->add( $out )
			;

		return $out;
	}
}