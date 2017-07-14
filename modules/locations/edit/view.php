<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Edit_View_LC_HC_MVC extends _HC_MVC
{
	public function render( $model )
	{
		$id = $model['id'];

		$link = $this->make('/html/view/link')
			->to('edit/update', array('id' => $id))
			->href()
			;

		$form = $this->make('edit/form');
		$values = $form->run('from-model', $model);
		$form
			->set_values( $values )
			;

		$display_form = $this->make('/html/view/form')
			->add_attr('action', $link )
			->set_form( $form )
			;

		$inputs = $form->inputs();

	// reorganize inputs
		$rows = array( 
			array(),
			array( array(), array() ),
			array()
			);

		reset( $inputs );
		foreach( $inputs as $name => $input ){
			$input_view = $this->make('/html/view/label-input')
				->set_label( $input->label() )
				->set_content( $input )
				->set_error( $input->error() )
				;

			switch( $name ){
				case 'name':
					$rows[0][$name] = $input_view;
					break;

				case 'street1':
				case 'street2':
				case 'city':
					$rows[1][0][$name] = $input_view;
					break;

				case 'state':
				case 'zip':
				case 'country':
					$rows[1][1][$name] = $input_view;;
					break;

				default:
					$rows[2][$name] = $input_view;;
					break;
			}
		}

		$row1 = $this->make('/html/view/container');
		foreach( $rows[0] as $input ){
			$row1
				->add( $input )
				;
		}

		$row2 = $this->make('/html/view/grid')
			->set_scale('sm')
			->set_gutter(2)
			;

		$row2_1 = $this->make('/html/view/container');
		foreach( $rows[1][0] as $input ){
			$row2_1
				->add( $input )
				;
		}
		$row2_2 = $this->make('/html/view/container');
		foreach( $rows[1][1] as $input ){
			$row2_2
				->add( $input )
				;
		}
		$row2
			->add( $row2_1, 6 )
			->add( $row2_2, 6 )
			;

		$row3 = $this->make('/html/view/container');
		foreach( $rows[2] as $input ){
			$row3
				->add( $input )
				;
		}

		$out = $this->make('/html/view/list')
			->set_gutter(1)
			;

		$out
			->add( $row1 )
			->add( $row2 )
			->add( $row3 )
			;

		$display_form
			->add( $out )
			;

		$buttons = $this->make('/html/view/element')->tag('div')
			->add(
				$this->make('/html/view/element')->tag('input')
					->add_attr('type', 'submit')
					->add_attr('title', HCM::__('Save') )
					->add_attr('value', HCM::__('Save') )
					->add_attr('class', 'hc-theme-btn-submit', 'hc-theme-btn-primary')
				)

			->add( 
				$this->make('/html/view/link')
					->to('delete', array('id' => $model['id']) )
					->add_attr('class', 'hcj2-confirm')
					// ->add_attr('class', 'hc-block')
					->add_attr('class', 'hc-right')
					->add( HCM::__('Delete') )
					->add_attr('class', 'hc-theme-btn-submit')
					->add_attr('class', 'hc-theme-btn-danger')
				)
			;

		$display_form->add( $buttons );

		return $display_form;
	}
}