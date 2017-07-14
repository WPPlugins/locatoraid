<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Radio_Set_HC_MVC extends HC_Form_Input2
{
	protected $options = array();

	public function add_options( $options )
	{
		$this->options[] = $options;
		return $this;
	}

	public function set_options( $options )
	{
		$this->options = $options;
		return $this;
	}
	function options()
	{
		return $this->options;
	}

	function render()
	{
		$options = $this->options();
		$value = $this->value();

		$el = $this->make('/html/view/element')->tag('div');

		foreach( $options as $os ){
			$sub_el = $this->make('view/radio')
				->set_name($this->name())
				->set_inline()
				->set_one_input(TRUE)
				;
			foreach( $os as $k => $v ){
				$sub_el->add_option( $k, $v );
			}

			$sub_el->set_value( $value );

			$el
				->add(
					$this->make('/html/view/element')->tag('div')
						->add_attr('class', 'hc-mb2')
						->add( $sub_el )
					)
				;
		}

		$return = $this->decorate( $el );
		return $return;
	}
}