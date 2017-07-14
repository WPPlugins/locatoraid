<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Colorpicker_HC_MVC extends HC_Form_Input2
{
	protected $colors = array();
	
	function __construct( $name = '' )
	{
		parent::__construct( $name );
		$this->colors = array(
			'#ffb3a7',	// 1
			'#cbe86b',	// 2
			'#89c4f4',	// 3
			'#f5d76e',	// 4
			'#be90d4',	// 5
			'#fcf13a',	// 6
			'#ffffbb',	// 7
			'#ffbbff',	// 8
			'#87d37c',	// 9
			'#ff8000',	// 12
			'#73faa9',	// 13
			'#c8e9fc',	// 14
			'#cb9987',	// 15
			'#cfd8dc',	// 16
			'#99bb99',	// 17
			'#99bbbb',	// 18
			'#bbbbff',	// 19
			'#dcedc8',	// 20
			'#bbbbbb',	// 21
			);
	}

	function render()
	{
		$value = $this->value();
		$name = $this->name();
		$readonly = $this->readonly();

		if( $readonly ){
			$out = $this->make('/html/view/element')->tag('div')
				->add('&nbsp;')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-p1')
				->add_attr('style', 'background-color: ' . $value . ';')
				->add_attr('style', 'width: 2em;')
				;
		}
		else {
			$hidden = $this->make('view/hidden')
				->set_name( $name )
				->set_value( $value )
				->add_attr('class', 'hcj2-color-picker-value')
				;

			$title = $this->make('/html/view/element')->tag('a')
				->add('&nbsp;')
				->add_attr('class', 'hc-btn')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-p1')
				->add_attr('style', 'background-color: ' . $value . ';')
				->add_attr('style', 'width: 2em;')
				->add_attr('class', 'hcj2-color-picker-display')
				;

			$options = $this->make('/html/view/list-inline')
				->add_attr('class', 'hc-mt2')
				->add_attr('class', 'hc-py2')
				->add_attr('class', 'hc-border-top')
				;

			foreach( $this->colors as $color ){
				$option = $this->make('/html/view/element')->tag('a')
					->add('&nbsp;')
					->add_attr('class', 'hc-btn')
					->add_attr('class', 'hc-border')
					->add_attr('class', 'hc-p1')
					->add_attr('style', 'background-color: ' . $color . ';')
					->add_attr('style', 'width: 2em;')
					->add_attr('data-color', $color)
					->add_attr('class', 'hcj2-color-picker-selector')
					->add_attr('class', 'hcj2-collapse-closer')
					;
				$options->add( $option );
			}

			$display = $this->make('/html/view/collapse')
				->set_title( $title )
				->set_content( $options )
				;

			$out = $this->make('/html/view/element')->tag('div')
				->add_attr('class', 'hcj2-color-picker')
				->add( $hidden )
				->add( $display )
				;
		}

		return $out;
	}
	
}