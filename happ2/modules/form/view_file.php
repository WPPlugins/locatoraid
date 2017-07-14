<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_File_HC_MVC extends HC_Form_Input2
{
	function render()
	{
		$readonly = $this->readonly();

		if( $readonly ){
			return;
		}
		else {
			$el = $this->make('/html/view/element')->tag('input')
				->add_attr( 'type', 'file' )
				->add_attr( 'name', $this->name() )
				->add_attr( 'id', $this->id() )
				->add_attr( 'value', $this->value() )
				->add_attr('class', 'hc-field')
				;
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		if( $readonly ){
			$el->add_attr('readonly', 'readonly');
		}

		$return = $this->decorate( $el );
		return $return;
	}

	public function grab( $post )
	{
		$name = $this->name();
		$value = NULL;

		if( isset($_FILES[$name]) && is_uploaded_file($_FILES[$name]['tmp_name']) ){
			$value = $_FILES[$name];
		}

		$this->set_value( $value );
		return $this;
	}


	public function always_label()
	{
		return TRUE;
	}
}
