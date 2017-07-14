<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC_Form_Input2 extends Html_View_Element_HC_MVC
{
	protected $prefix = 'hc-';
	protected $type = 'text';
	protected $name = 'name';
	protected $id = '';
	protected $error = '';
	protected $value = NULL;
	protected $readonly = FALSE;
	protected $conf = array();

	protected $label = NULL;

	protected $validators = array();

	public function add_validator()
	{
		$args = func_get_args();
		$validator = array_shift($args);
		$this->validators[] = array( $validator, $args );
		return $this;
	}

	public function validators()
	{
		return $this->validators;
	}

	public function validate()
	{
		$return = TRUE;
		$readonly = $this->readonly();
		if( $readonly ){
			return $return;
		}

		$value = $this->value();

		$validators = $this->validators();
		foreach( $validators as $validator_array ){
			list( $validator, $args ) = $validator_array;

			$validation_method_name = 'validate';
			if( is_array($validator) ){
				list( $validator, $validation_method_name ) = $validator;
			}

			$method = new ReflectionMethod($validator, $validation_method_name);
			$need_args = $method->getNumberOfParameters();

			$validator_args = array();
			$validator_args[] = $value;
			$need_args--;
			while( $need_args > 0 ){
				$validator_args[] = array_shift( $args );
				$need_args--;
			}

		// remain args so it's custom message
			$msg = NULL;
			if( $args ){
				$msg = array_shift( $args );
			}

			$validator_return = call_user_func_array( array($validator, $validation_method_name), $validator_args );

			if( $validator_return !== TRUE ){
				if( $msg !== NULL ){
					if( count($validator_args) > 1 ){
						$format_args = array_slice($validator_args, 1);
						array_unshift( $format_args, $msg ); 
						$return = call_user_func_array('sprintf', $format_args );
					}
					else {
						$return = $msg;
					}
				}
				else {
					$return = $validator_return;
				}
				break;
			}
		}
		return $return;
	}

	function set_label( $label )
	{
		$this->label = $label;
		return $this;
	}
	function label()
	{
		// $return = ($this->label === NULL) ? $this->name() : $this->label;
		$return = $this->label;

		if( strlen($return) ){
			$validators = $this->validators();
			if( $validators ){
				if( in_array($this->type(), array('text')) ){
					$return .= ' *';
				}
			}
		}
		return $return;
	}

/*
	function __construct( $name = '' )
	{
		parent::__construct();
		if( ! $name ){
			$name = 'nts_' . HC_Lib2::generate_rand();
		}
		$this->set_name( $name );
	}
*/
	function set_conf( $k, $v )
	{
		$this->conf[$k] = $v;
		return $this;
	}
	function conf($k)
	{
		$return = NULL;
		if( isset($this->conf[$k]) ){
			$return = $this->conf[$k];
		}
		return $return;
	}

	function set_readonly( $readonly = TRUE )
	{
		$this->readonly = $readonly;
		return $this;
	}
	function readonly()
	{
		return $this->readonly;
	}

	/* if fails should return the error message otherwise NULL */
	function _validate()
	{
		$return = NULL;
		return $return;
	}

	/* this will add error messages and help text if needed*/
	function decorate( $return )
	{
		$validators = $this->validators();
		foreach( $validators as $validator_array ){
			list( $validator, $args ) = $validator_array;
			if( method_exists($validator, 'render') ){
				$return = $validator->run('render', $return);
			}
		}

		$error = $this->error();
		$children = $this->children();

		$return = $this->make('/html/view/container')
			->add( $return )
			;

		foreach( $children as $child ){
			$return
				->add( $child )
				;
		}

		if( $error ){
			if( is_array($error) ){
				$error = join( ' ', $error );
			}

			$error = $this->make('/html/view/element')->tag('span')
				->add( $error )
				->add_attr('class', 'hc-inline-block')
				->add_attr('class', 'hc-border-top')
				->add_attr('class', 'hc-border-red')
				->add_attr('class', 'hc-red')
				->add_attr('class', 'hc-py1')
				->add_attr('class', 'hc-mt2')
				;

			$return->add(
				$this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hc-block')
					->add( $error )
				);
		}

		return $return;
	}

	function set_type( $type )
	{
		$this->type = $type;
		return $this;
	}
	function type()
	{
		return $this->type;
	}

	function set_error( $error )
	{
		if( ! $this->error )
			$this->error = $error;
		return $this;
	}
	function error()
	{
		return $this->error;
	}

	function set_default( $value )
	{
		if( $this->value() === NULL ){
			return $this->set_value($value);
		}
		return $this;
	}

	function set_value( $value )
	{
		if( is_array($value) && array_key_exists('id', $value) ){
			$value = $value['id'];
		}

		$this->value = $value;
		if( $error = $this->_validate() ){
			$this->set_error( $error );
		}
		return $this;
	}
	function value()
	{
		return $this->value;
	}

	function set_name( $name )
	{
		$this->name = $name;
		if( ! strlen($this->id())){
			$id = 'hc-form-' . $name;
			$this->set_id( $id );
		}
		return $this;
	}
	function name()
	{
		$return = $this->name;
		if( substr($return, 0, strlen($this->prefix)) != $this->prefix ){
			$return = $this->prefix . $return;
		}
		return $return;
		// return $this->name;
	}
	public function set_id( $id )
	{
		$this->id = $id;
		return $this;
	}
	public function id()
	{
		return $this->id;
	}

// will be overwritten in child classes
	public function grab( $post )
	{
		$name = $this->name();
		$value = NULL;

		if( substr($name, -strlen('[]')) == '[]' ){
			$core_name = substr($name, 0, -strlen('[]'));
			if( isset($post[$core_name]) ){
				$value = $post[$core_name];
			}
		}
		else {
			if( isset($post[$name]) ){
				$value = $post[$name];
				if( ! is_array($value) ){
					// $value = trim($value);
				}
			}
		}

		$this->set_value( $value );
		return $this;
	}

	function to_array()
	{
		$return = array(
			'name'	=> $this->name(),
			'type'	=> $this->type(),
			'value'	=> $this->value(),
			'error'	=> $this->error(),
			);
		return $return;
	}
}

class Form_View_Textarea_HC_MVC extends HC_Form_Input2
{
	function render()
	{
		$readonly = $this->readonly();

		$value = $this->value();
		$label = $this->label();

		if( $readonly ){
			$el = $this->make('/html/view/element')->tag('span' )
				->add_attr( 'id', $this->id() )
				->add( $this->value() )
				;
		}
		else {
			$el = $this->make('/html/view/element')->tag('textarea' )
				->add_attr( 'name', $this->name() )
				->add_attr( 'id', $this->id() )
				->add( $value )
				->add_attr('class', 'hc-field')
				;
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el );
		return $return;
	}
}

class Form_View_Select_HC_MVC extends HC_Form_Input2
{
	protected $options = array();
	protected $option_groups = array();
	protected $options_to_groups = array();

	public function add_option( $key, $label, $optgroup_id = NULL )
	{
		$this->options[ $key ] = $label;

		if( $this->option_groups && $optgroup_id && isset($this->option_groups[$optgroup_id]) ){
			if( ! isset($this->options_to_groups[$optgroup_id]) ){
				$this->options_to_groups[$optgroup_id] = array();
			}
			$this->options_to_groups[$optgroup_id][] = $key;
		}

		return $this;
	}

	public function set_option_groups( $option_groups )
	{
		$this->option_groups = $option_groups;
	}
	public function option_groups()
	{
		return $this->option_groups;
	}

	public function remove_option( $key )
	{
		unset( $this->options[$key] );
		return $this;
	}

	public function set_options( $options )
	{
		foreach( $options as $key => $label ){
			$this->add_option( $key, $label );
		}
		// $this->options = $options;
		return $this;
	}

	public function set_options_flat( $options )
	{
		$options = array_combine($options, $options);
		foreach( $options as $key => $label ){
			$this->add_option( $key, $label );
		}
		// $this->options = $options;
		return $this;
	}

	public function options()
	{
		return $this->options;
	}

	public function render()
	{
		$return = NULL;
		$readonly = $this->readonly();
//$readonly = FALSE;
		$options = $this->options();
		$value = $this->value();
		$label = $this->label();

		if( ! is_array($options) ){
			return;
		}

		if( count($options) == 1 ){
			$options_keys = array_keys($options);
			$value = array_shift( $options_keys );

			$sub_el = $this->make('/html/view/element')->tag('input')
				->add_attr('type', 'hidden')
				->add_attr('name', $this->name())
				->add_attr('id', $this->id())
				->add_attr('value', $value)
				;

			$return = $this->make('/html/view/element')->tag('label')
				->add_attr('style', 'padding-left: 0;')
				->add_attr('class', 'hc-nowrap')
				;

			$label = $options[$value];
			$return
				->add( $sub_el )
				->add( $label )
				;
		}
		else {
			$option_keys = array_keys( $options );

			if( strlen($label) ){
				if( ! in_array($value, $option_keys) ){
					$new_options = array(' ' => ' - ' . $label . ' - ');
					foreach( $options as $k => $v ){
						$new_options[ $k ] = $v;
					}
					$options = $new_options;
				}
			}

			if( $readonly ){
				$label = isset($options[$value]) ? $options[$value] : $value;

				$return = $this->make('/html/view/element')->tag('select')
					->add_attr( 'id', $this->id() )
					->add_attr( 'name', $this->name() )
					->add_attr('class', 'hc-field')
					;

				$option = $this->make('/html/view/element')->tag('option');
				$option->add_attr( 'value', $value );
				$option->add( $label );
				$option->add_attr( 'selected', 'selected' );
				$return->add( $option );
			}
			else {
				$return = $this->make('/html/view/element')->tag('select')
					->add_attr( 'id', $this->id() )
					->add_attr( 'name', $this->name() )
					->add_attr('class', 'hc-field')
					;

				if( $this->option_groups && $this->options_to_groups ){
					// if anything in no group
					$orphan_options = array();
					reset( $options );
					foreach( $options as $key => $option ){
						$is_orphan = TRUE;
						reset( $this->options_to_groups );
						foreach( $this->options_to_groups as $optgroup_id => $this_options ){
							if( in_array($key, $this_options) ){
								$is_orphan = FALSE;
								break;
							}
						}
						if( $is_orphan ){
							$orphan_options[] = $key;
						}
					}

					if( $orphan_options ){
						foreach( $orphan_options as $key ){
							$option = $options[$key];

							$option_view = $this->make('/html/view/element')->tag('option');
							$option_view->add_attr( 'value', $key );
							$option_view->add( $option );

							if( $value == $key ){
								$option_view->add_attr( 'selected', 'selected' );
							}
							$return->add( $option_view );
						}
					}

					reset( $this->options_to_groups );
					foreach( $this->options_to_groups as $optgroup_id => $this_options ){
						$optgroup = $this->make('/html/view/element')->tag('optgroup');
						$optgroup->add_attr( 'label', $this->option_groups[$optgroup_id] );

						foreach( $this_options as $key ){
							$option = $options[$key];

							$option_view = $this->make('/html/view/element')->tag('option');
							$option_view->add_attr( 'value', $key );
							$option_view->add( $option );

							if( $value == $key ){
								$option_view->add_attr( 'selected', 'selected' );
							}
							$optgroup->add( $option_view );
						}
						$return->add( $optgroup );
					}
				}
				else {
					reset( $options );
					foreach( $options as $key => $label ){
						$option = $this->make('/html/view/element')->tag('option');
						$option->add_attr( 'value', $key );
						$option->add( $label );
						if( $value == $key ){
							$option->add_attr( 'selected', 'selected' );
						}
						$return->add( $option );
					}
				}
			}
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$return->add_attr($k, $v);
		}

		$return = $this->decorate( $return );
		return $return;
	}
}

class Form_View_Dropdown_HC_MVC extends Form_View_Select_HC_MVC
{
}

class Form_View_Text_HC_MVC extends HC_Form_Input2
{
	function render()
	{
		$readonly = $this->readonly();
		$label = $this->label();

		if( $readonly ){
			$el = $this->make('/html/view/element')->tag('span')
				->add_attr( 'id', $this->id() )
				->add( $this->value() );
				;
		}
		else {
			$el = $this->make('/html/view/element')->tag('input')
				->add_attr( 'type', 'text' )
				->add_attr( 'name', $this->name() )
				->add_attr( 'id', $this->id() )
				->add_attr( 'value', $this->value() )
				->add_attr('class', 'hc-field')

				->add_attr('class', 'hc-block-xs')
				->add_attr( 'style', 'margin:0;' )
				;

			// if( strlen($label) ){
				// $el
					// ->add_attr( 'placeholder', $label )
					// ;
			// }
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
}

class Form_View_Label_HC_MVC extends HC_Form_Input2
{
	protected $type = 'label';
	protected $readonly = TRUE;

	function render()
	{
		$return = $this->value();

		if( $return === TRUE ){
			$return = $this->make('/html/view/icon')->icon('check')
				->add_attr('class', 'hc-olive')
				;
		}
		elseif( $return === FALSE ){
			$return = $this->make('/html/view/icon')->icon('times')
				->add_attr('class', 'hc-maroon')
				;
		}

		$return = $this->decorate( $return );
		return $return;
	}
}

class Form_View_Password_HC_MVC extends HC_Form_Input2
{
	function render()
	{
		$label = $this->label();

		$el = $this->make('/html/view/element')->tag('input')
			->add_attr( 'type', 'password' )
			->add_attr( 'name', $this->name() )
			->add_attr( 'id', $this->id() )
			->add_attr( 'value', $this->value() )
			->add_attr('class', 'hc-field')

			->add_attr('class', 'hc-block-xs')
			;

		// if( strlen($label) ){
			// $el
				// ->add_attr( 'placeholder', $label )
				// ;
		// }

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		$return = $this->decorate( $el );
		return $return;
	}
}

class Form_View_Link_HC_MVC extends HC_Form_Input2
{
	public function render()
	{
		$value = $this->value();
		$label = $this->label();

		$out = $this->make('/html/view/link')
			->to( $value )
			->add( $label )
			;
		return $out;
	}
}

class Form_View_Checkbox_HC_MVC extends HC_Form_Input2
{
	protected $value = 0;
	protected $my_value = '';
	protected $my_type = 'checkbox';

	public function set_type( $type )
	{
		$this->my_type = $type;
		return $this;
	}

	public function value()
	{
		$return = $this->value;
		$type = $this->my_type ? $this->my_type : 'checkbox';

		switch( $type ){
			case 'checkbox':
				$return = $this->value ? 1 : 0;
				break;
		}
		return $return;
	}

	public function set_my_value( $my_value = '' )
	{
		$this->my_value = $my_value;
		return $this;
	}

	public function my_value()
	{
		return $this->my_value;
	}

	public function render( $decorate = TRUE )
	{
		$label = $this->label();
		$value = $this->value();
		$my_value = $this->my_value();

		$type = $this->my_type ? $this->my_type : 'checkbox';

		$el = $this->make('/html/view/element')->tag('input')
			->add_attr( 'type', $type )
			->add_attr( 'id', $this->id() )
			->add_attr( 'name', $this->name() )
			->add_attr( 'value', $this->my_value() )

			->add_attr( 'style', 'margin:0;' )
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$el->add_attr($k, $v);
		}

		if( $this->readonly() ){
			// $el->add_attr('readonly', 'readonly' );
			$el->add_attr('disabled', 'disabled' );
		}

		switch( $type ){
			case 'radio':
				if( $my_value == $value ){
					$el->add_attr('checked', 'checked');
				}
				break;
			default:
				if( $value ){
					$el->add_attr('checked', 'checked');
				}
				break;
		}

		if( is_object($label) OR strlen($label) ){
			if( is_object($label) ){
				$label = $this->make('/html/view/element')
					->tag('div')
					->add( $label )
					->add_attr('class', 'hc-nowrap')
					;
			}
			elseif( strlen($label) ){
				$label = $this->make('/html/view/element')
					->tag('div')
					->add( $label )
					->add_attr('class', 'hc-nowrap')
					->add_attr('title', $label)
					;
			}

			$label
				->add_attr('class', 'hcj2-as-label')
				;

			$el = $this->make('/html/view/list-inline')
				->set_gutter(1)
				->add_attr('class', 'hc-mb2-xs')
				->add( $el )
				;

			if( $this->readonly() ){
				$hidden = $this->make('view/hidden')
					->set_name( $this->name() )
					->set_value( $my_value )
					;
				$el
					->add( $hidden )
					;
			}

			$el
				->add($label)
				;
		}
		else {
			$el = $this->make('/html/view/container')
				->add( $el )
				;
		}

		if( $this->readonly() ){
			$hidden = $this->make('view/hidden', $this->name() );
			if( $value )
				$hidden->set_value( $my_value );
			else
				$hidden->set_value( '' );
			$el->add($hidden);
		}

		// $el = $this->make('/html/view/element')->tag('label')
		$el = $this->make('/html/view/element')->tag('div')
			->add( $el )
			->add_attr('class', 'hc-nowrap')
			->add_attr('class', 'hc-inline-block')
			->add_attr('class', 'hcj2-as-label-container')
			;
		return $el;
	}
}

class Form_View_Hidden_HC_MVC extends HC_Form_Input2
{
	function render()
	{
		$this_value = $this->value();
		$this_name = $this->name();
		$attr = $this->attr();

		if( is_array($this_value) ){
			$el = $this->make('/html/view/container');
			foreach( $this_value as $this_val ){
				$this_el = $this->make('/html/view/element')->tag('input')
					->add_attr( 'type', 'hidden' )
					->add_attr( 'name', $this_name . '[]' )
					->add_attr( 'id', $this->id() )
					->add_attr( 'value', $this_val )
					;
				reset( $attr );
				foreach( $attr as $k => $v ){
					$this_el->add_attr($k, $v);
				}
				$el
					->add( $this_el )
					;
			}
		}
		else {
			$el = $this->make('/html/view/element')->tag('input')
				->add_attr( 'type', 'hidden' )
				->add_attr( 'name', $this->name() )
				->add_attr( 'id', $this->id() )
				->add_attr( 'value', $this->value() )
				;
			foreach( $attr as $k => $v ){
				$el->add_attr($k, $v);
			}
		}

		$return = $this->decorate( $el );
		return $return;
	}
}

class Form_View_Composite_HC_MVC extends HC_Form_Input2
{
	protected $fields = array();

	public function _init()
	{
		return $this;
	}

	public function set_name( $name )
	{
		foreach( array_keys($this->fields) as $field_name ){
			$full_field_name = $name . '_' . $field_name;
			$this->fields[$field_name]
				->set_name( $full_field_name )
				;
		}
		return parent::set_name( $name );
	}

	public function set_field( $name, $field )
	{
		$this->fields[ $name ] = $field;
		return $this;
	}

	public function grab( $post )
	{
		$value = array();
		foreach( $this->fields as $k => $f ){
			$f->grab($post);
			$field_value = $f->value();

			if( ($field_value === NULL) OR (is_array($field_value) && (!$field_value)) ){
				continue;
			}

			$value[$k] = $f->value();
		}
		$this->set_value( $value );
		return $this;
	}

	public function set_value( $value )
	{
		reset( $this->fields );
		foreach( $this->fields as $k => $f ){
			if( is_array($value) && array_key_exists($k, $value) ){
				$this->fields[$k]->set_value($value[$k]); 
			}
		}
		return parent::set_value( $value );
	}

	public function render()
	{
		$return = NULL;

		if( ! $this->fields ){
			return $return;
		}

		$return = $this->make('/html/view/list')
			->set_gutter(0)
			;

		if( count($return) ){
			$return
				->add_attr('class', 'hc-ml3')
				;
		}

		foreach( $this->fields as $name => $field ){
			$field_label = $field->run('label');
			$field_view = $field->run('render');

			if( strlen($field_label) ){
				$field_label = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hc-block')
					// ->add_attr('class', 'hc-fs2')
					->add_attr('class', 'hc-muted-2')
					->add( $field_label )
					;
				
				$field_view = $this->make('/html/view/list')
					->set_gutter(1)
					->add( $field_label )
					->add( $field_view )
					;
			}

			$return
				->add( $field_view )
				;
		}

		$return = $this->decorate( $return );
		return $return;
	}
}

class Form_View_Include_Exclude_HC_MVC extends Form_View_Composite_HC_MVC
{
	public function _init()
	{
		$this
			->set_field( 
				'x', 
				$this->make('/form/view/radio')
					->set_inline()
					->set_options(
						array(
							0	=> HCM::__('Equals To'),
							1	=> HCM::__('Not Equals To'),
							)
						)
				)

			->set_field( 
				'value', 
				$this->make('/form/view/checkbox-set')
				)
			;

		return parent::_init();
	}

	public function set_options( $options )
	{
		$this->fields['value']
			->set_options( $options )
			;
		return $this;
	}

	public function set_value( $value )
	{
		if( ! is_array($value) ){
			$value = array($value);
		}

		$x_value = 0;
		$final_value = array();
		foreach( $value as $v ){
			if( substr($v, 0, 1) == '_' ){
				$x_value = 1;
				$v = substr($v, 1);
			}
			$final_value[] = $v;
		}

		$this->fields['value']->set_value( $final_value );
		$this->fields['x']->set_value( $x_value );

		return parent::set_value( $value );
	}


	public function grab( $post )
	{
		$value = array();

		foreach( array_keys($this->fields) as $fname ){
			$this->fields[$fname]->grab($post);
		}

		$x = $this->fields['x']->value();
		$value = $this->fields['value']->value();

		if( $x ){
			for( $ii = 0; $ii < count($value); $ii++ ){
				$value[$ii] = '_' . $value[$ii];
			}
		}

		$this->set_value( $value );
		return $this;
	}
}

class HC_Form_Input_Composite2 extends HC_Form_Input2
{
	protected $fields = array();

	public function _init()
	{
		return $this;
	}

	protected function _init_fields()
	{
		return $this;
	}

	public function set_field( $name, $field )
	{
		$this->fields[ $name ] = $field;
		return $this;
	}

	// public function validate()
	// {
		// $return = TRUE;
		// reset( $this->fields );
		// foreach( $this->fields as $k => $f ){
			// $this_return = $this->fields[$k]->validate();
			// if( $this_return !== TRUE ){
				// $return = $this_return;
			// }
		// }
		// return $return;
	// }

	public function set_value( $value )
	{
		$this->_init_fields();

		reset( $this->fields );
		foreach( $this->fields as $k => $f ){
			if( is_array($value) && array_key_exists($k, $value) ){
				$this->fields[$k]->set_value($value[$k]); 
			}
		}
		return parent::set_value( $value );
	}

	public function grab( $post )
	{
		$this->_init_fields();

		$value = array();
		foreach( $this->fields as $k => $f ){
			$f->grab($post);
			$value[$k] = $f->value();
		}
		$this->set_value( $value );
		return $this;
	}

	public function render()
	{
		$return = '';

		if( ! $this->fields ){
			return $return;
		}

		foreach( $this->fields as $name => $field ){
			$return .= $field->run('render');
		}

		return $return;
	}
}
