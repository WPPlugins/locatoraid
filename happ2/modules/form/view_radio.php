<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Radio_HC_MVC extends HC_Form_Input2
{
	protected $type = 'radio';
	protected $options = array();
	protected $more = array();
	protected $holder = NULL;
	protected $inline = FALSE;
	protected $one_input = FALSE;

	public function set_options( $options )
	{
		$this->options = array();
		foreach( $options as $k => $v ){
			$this->add_option( $k, $v );
		}
		return $this;
	}

	public function always_label()
	{
		return TRUE;
	}

	function add_option( $value, $label = NULL, $more = '' )
	{
		$this->options[$value] = $label;
		if( $more ){
			$this->more[$value] = $more;
		}
		return $this;
	}
	public function remove_option( $value )
	{
		unset( $this->options[$value] );
		unset( $this->more[$value] );
		return $this;
	}

	function options()
	{
		return $this->options;
	}
	function more()
	{
		return $this->more;
	}

	function set_inline( $inline = TRUE )
	{
		$this->inline = $inline;
		return $this;
	}
	function inline()
	{
		return $this->inline;
	}

	function set_one_input( $one_input = TRUE )
	{
		$this->one_input = $one_input;
		return $this;
	}
	function one_input()
	{
		return $this->one_input;
	}

	public function set_holder( $holder )
	{
		$this->holder = $holder;
		return $this;
	}
	public function holder()
	{
		return $this->holder;
	}

	public function render_one( $value, $decorate = FALSE )
	{
		$options = $this->options();
		$full_value = $this->value();
		$label = $options[$value];
		$inline = $this->inline();

// echo "SETTING NAME TO: '" . $this->name() . "'<br>";
		$sub_el = $this->make('view/checkbox')
			->set_name($this->name())
			->set_my_value($value)
			->set_type('radio')
			;
		if( $this->readonly($value) ){
			$sub_el->set_readonly();
		}
		if( strlen($label) ){
			$sub_el->set_label( $label );
		}

		$sub_el->set_value($full_value);

		if( $decorate ){
			$return = $this->decorate( $sub_el );
		}
		else {
			$return = $sub_el;
		}
		return $return;
	}

	function render()
	{
		$options = $this->options();
		$full_value = $this->value();
		$inline = $this->inline();

		if( $this->readonly() ){
			$label = $options[$full_value];
			$el = $this->make('/html/view/element')->tag('span')
				->add( $label )
				;
		}
		else {
			if( $inline ){
				$el = $this->make('/html/view/list-inline')
					->set_gutter(2)
					;
			}
			else {
				$el = $this->make('/html/view/list');
			}

			$attr = $this->attr();
			foreach( $attr as $key => $val ){
				$el->add_attr($key, $val);
			}
			foreach( $options as $value => $label ){
				$el->add( $this->render_one($value) );
			}

			$el
				// ->add_attr('class', 'hc-form-control-static')
				;
		}

		$return = $this->decorate( $el, FALSE );
		return $return;
	}
}
