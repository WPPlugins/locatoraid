<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Label_Input_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $label = NULL;
	protected $content = array();
	protected $error = FALSE;

	function set_error( $error )
	{
		$this->error = $error;
		return $this;
	}
	function error()
	{
		return $this->error;
	}
	function set_label( $label )
	{
		$this->label = $label;
		return $this;
	}
	function label()
	{
		return $this->label;
	}
	function set_content( $content )
	{
		$this->content = $content;

		if( is_object($content) && method_exists($content, 'observe') ){
			if( $observe = $content->observe() ){
				$this
					->add_attr('data-hc-observe', $observe)
					;
			}
		}
		return $this;
	}
	function content()
	{
		return $this->content;
	}

	function render()
	{
		$error = $this->error();
		$label = $this->label();
		$content = $this->content();

		if( is_string($content) && (! strlen($content)) ){
			return;
		}
		// if( (! is_array($content)) && (! strlen($content)) ){
			// return;
		// }

		if( $observe = $this->observe() ){
			$this
				->add_attr('data-hc-observe', $observe)
				;
		}

		$out = $this->make('view/element')->tag('div')
			->add_attr('class', 'hc-block')
			->add_attr('class', 'hc-mb2')
			// ->add_attr('class', 'hc-border')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$content_holder = $this->make('view/element')->tag('div')
			->add_attr('class', 'hc-block')
			;

		if( $error ){
			$content_holder
				->add_attr('class', 'hc-theme-form-error')
				;
		}

		$content_holder->add( $content );

		$no_labels = array('/form/view/hidden', '/form/view/checkbox');

		if( is_object($content) ){
			if( $label == '-nolabel-' ){
				$label = NULL;
			}
			elseif( in_array($content->slug(), $no_labels) ){
				$label = NULL;
			}
			elseif( method_exists($content, 'always_label') && $content->always_label() ){
			}
			elseif( method_exists($content, 'value') ){
				$content_value = $content->value();
				if( 
					( $content_value === array() ) OR
					( $content_value === NULL ) OR
					( $content_value === '' )
					){
// show label anyway
					// $label = NULL;
				}
				else {
					// echo "SHOW LABEL '" . $label . ' AS CONTENT = "' . $content_value . '"<br>';
				}
			}
		}

		if( $label ){
			$label_holder = $this->make('view/element')->tag('div')
				->add_attr('class', 'hc-block')
				// ->add_attr('class', 'hc-fs2')
				->add_attr('class', 'hc-muted-2')
				->add_attr('class', 'hc-mb1')
				->add( $label )
				;
			$out
				->add( $label_holder )
				;
		}

		$out
			->add( $content_holder )
			;

		return $out;
	}
}