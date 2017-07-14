<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Coordinates_Index_View_LC_HC_MVC extends _HC_MVC
{
	public function render( $location, $can_edit = 1 )
	{
		$out = $this->make('/html/view/list')
			->set_gutter(2)
			->add_attr('class', 'hcj2-container')
			;

		$id = $location['id'];

		$p = $this->make('/locations/presenter');
		$p->set_data( $location );
		$address = $p->run('present-address');

		$form = $this->make('form');
		$values = $form->run('from-model', $location);

		$latitude = $values['latitude'];
		$longitude = $values['longitude'];

	// map
		$map_id = 'hclc_map';
		$map = $this->make('/html/view/element')->tag('div')
			->add_attr('id', $map_id)
			// ->add_attr('class', 'hc-p2')
			->add_attr('class', 'hc-border')
			// ->add( HCM::__('Please wait while the map is loading') )
			;

		$p = $this->make('presenter');
		$p->set_data( $location );

		$pl = $this->make('/locations/presenter');
		$pl->set_data( $location );

		if( (($latitude == -1) && ($longitude == -1)) ){
			$map
				->add_attr('class', 'hc-p2')
				->add( $p->run('present-coordinates') )
				;
		}
		else {
			$map
				->add_attr('style', 'height: 10rem;')
				->add_attr('data-latitude',  $latitude)
				->add_attr('data-longitude', $longitude)
				->add_attr('data-edit', $can_edit)
				;

			$icon = $pl->run('present-icon-url');
			if( $icon ){
				$map
					->add_attr('data-icon', $icon)
					;
			}
		}

	// form
		$form
			->set_values( $values )
			;

		$link = $this->make('/html/view/link')
			->to('index/update', array('id' => $id))
			->href()
			;
		$display_form = $this->make('/html/view/form')
			->add_attr('action', $link )
			->set_form( $form )
			;

		if( ! (($latitude == -1) && ($longitude == -1)) ){
			$display_form
				->add(
					$this->make('/html/view/element')->tag('div')
						->add( HCM::__('You can use your mouse to move the location. Or manually enter the coordinates.') )
						->add_attr('class', 'hc-italic')
					)
				;
		}

		$inputs = $form->inputs();
		foreach( $inputs as $input_name => $input ){
			$input_view = $this->make('/html/view/label-input')
				->set_label( $input->label() )
				->set_content( $input )
				->set_error( $input->error() )
				;

			$display_form
				->add( $input_view )
				;
		}

		$buttons = $this->make('/html/view/buttons-row');

		$buttons->add(
			'save',
			$this->make('/html/view/element')->tag('input')
				->add_attr('type', 'submit')
				->add_attr('title', HCM::__('Save') )
				->add_attr('value', HCM::__('Save') )
				->add_attr('class', 'hc-theme-btn-submit', 'hc-theme-btn-primary')
				->add_attr('class', 'hc-block-xs')
			);

		if( ! (($latitude == -1) && ($longitude == -1)) ){
			$buttons->add(
				'reset',
				$this->make('/html/view/link')
					->to('index/reset', array('id' => $location['id']))
					->add( HCM::__('Reset') )
					->add_attr('class', 'hc-theme-btn-submit', 'hc-theme-btn-secondary')
					->add_attr('class', 'hc-block-xs')
				);
		}
		$display_form
			->add( $buttons )
			;

		// $display_form = $this->make('/html/view/collapse')
			// ->set_title( HCM::__('Edit Coordinates') )
			// ->set_content( $display_form )
			// ;

		if( $can_edit ){
			$out
				->add( $address )
				;
		}

		$out
			->add( $map )
			;

		if( $can_edit ){
			$out
				->add( $display_form )
				;
		}
		else {
			// $link = $this->make('/html/view/link')
				// ->to('', array('id' => $location['id']))
				// ->add( HCM::__('Edit Coordinates') )
				// ;
			// $out
				// ->add( $link )
				// ;
		}

		return $out;
	}
}