<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class _HC_Form extends _HC_MVC
{
	protected $inputs = array();
	protected $errors = array();
	protected $orphan_errors = array();
	protected $readonly = FALSE;
	protected $options = array();

	protected $children_order = array();

	private $model = NULL;
	protected $validators = array();

	public function add_validator( $validator )
	{
		$this->validators[] = $validator;
		return $this;
	}

	public function render()
	{
		$out = $this->make('/html/view/container');

		$inputs = $this->inputs();
		foreach( $inputs as $input_name => $input ){
			$input_view = $this->run('render-input', $input_name );
			$out
				->add( $input_view )
				;
		}
		return $out;
	}

	public function render_input( $input_name )
	{
		$return = NULL;
		$input = $this->input( $input_name );
		if( ! $input ){
			return $return;
		}

		$input_view = $this->make('/html/view/label-input')
			->set_label( $input->label() )
			->set_content( $input )
			->set_error( $input->error() )
			;
		return $input_view;
	}

	public function validate()
	{
		$return = TRUE;
		$errors = array();

		$validators = array();
		if( $this->validators ){
			$values = $this->values();

			foreach( $this->validators as $validator ){
				$validation_method_name = 'prepare';
				if( is_array($validator) ){
					list( $validator, $validation_method_name ) = $validator;
				}

				$this_validators = $validator->run($validation_method_name, $values);
				$validators = array_merge( $validators, $this_validators );
			}
		}

	// inputs
		$inputs = $this->inputs();

		foreach( $inputs as $k => $input ){
			if( isset($validators[$k]) ){
				foreach( $validators[$k] as $validator_handle => $validator_args ){
					$input = call_user_func_array( array($input, 'add_validator'), $validator_args );
				}
			}

			$input_validate = $input->validate();
			if( $input_validate !== TRUE ){
				$errors[ $k ] = $input_validate;
				$return = FALSE;
			}
		}
		if( $errors ){
			$this->add_errors( $errors );
		}

	// then my own validators if any
		// $this->_init_validators();
		// $value = $this->value();

		return $return;
	}

	public function set_child_order( $child_key, $order )
	{
		$this->children_order[ $child_key ] = $order;
		return $this;
	}

	public function set_model( $model )
	{
		$this->model = $model;
		return $this;
	}
	public function model()
	{
		return $this->model;
	}

	public function to_model( $values )
	{
		return $values;
	}

	public function from_model( $values )
	{
		return $values;
	}

	public function reset_inputs()
	{
		$this->inputs = array();
		return $this;
	}

	public function set_options( $options )
	{
		$this->options = $options;
		$this->inputs = $this->inputs();
		return $this;
	}
	public function options()
	{
		return $this->options;
	}

	function set_readonly( $ro = TRUE )
	{
		$this->readonly = $ro;
		return $this;
	}

	function readonly()
	{
		$return = TRUE;

		if( ! $this->readonly ){
			// also check all inputs
			foreach( $this->inputs as $name => $input ){
				if( ! $input->readonly() ){
					$return = FALSE;
					break;
				}
			}
		}

		return $return;
	}

	public function inputs()
	{
		$return = array();

		$names = array_keys($this->inputs);
		if( $this->children_order ){
			$rex_order = 10;
			foreach( $names as $k ){
				if( isset($this->children_order[$k]) ){
					$this_order = $this->children_order[$k];
				}
				else {
					$this_order = $rex_order += 10;
				}
				$sort[ $k ] = $this_order;
			}
			asort($sort);
			$names = array_keys($sort);
		}

		foreach( $names as $name ){
			$input = $this->input($name);
			if( $input ){
				if( $this->orphan_errors && isset($this->orphan_errors[$name]) ){
					$input->set_error($this->orphan_errors[$name]);
					unset($this->orphan_errors[$name]);
				}
				$return[$name] = $input;
			}
		}
		return $return;
	}

	public function set_input( $name, $input )
	{
		$this->inputs[ $name ] = $input->set_name($name);
		return $this;
	}

	public function remove_input( $name ){
		return $this->unset_input( $name );
	}

	public function unset_input( $name )
	{
		unset( $this->inputs[$name] );
		return $this;
	}

	public function exists( $name )
	{
		return isset($this->inputs[$name]);
	}

	function input( $name )
	{
		$return = isset($this->inputs[$name]) ? $this->inputs[$name] : NULL;

	/* also check options */
		$options = $this->options();
		if( isset($options[$name]) ){
			if( ! $options[$name] ){
				$return = NULL;
			}
			elseif( (count($options[$name]) == 1) && ($options[$name][0] != '*') ){
				$return->set_readonly();
			}
		}

		if( $return && $this->readonly() ){
			$return->set_readonly();
		}

		return $return;
 	}
	function input_call( $name, $method, $params = array() )
	{
		if( isset($this->inputs[$name]) ){
			call_user_func_array( array($this->inputs[$name], $method), $params );
		}
	}

	function input_names()
	{
		return array_keys($this->inputs);
	}

	function grab( $post )
	{
		foreach( array_keys($this->inputs) as $k ){
			if( $this->inputs[$k]->readonly() ){
				continue;
			}
			$this->inputs[$k]->grab( $post );
		}
		return $this;
	}

	public function set_value( $k, $v )
	{
		if( array_key_exists($k, $this->inputs) ){
			$this->inputs[$k]->set_value($v);
		}
		return $this;
	}

	function set_values( $values )
	{
		foreach( array_keys($this->inputs) as $k ){
			if( isset($values[$k]) ){
				$this->inputs[$k]->set_value( $values[$k] );
			}
		}
		return $this;
	}

	public function value($k)
	{
		$return = NULL;
		if( isset($this->inputs[$k]) ){
			$return = $this->inputs[$k]->value();
		}
		return $return;
	}

	function values()
	{
		$return = array();
		foreach( array_keys($this->inputs) as $k ){
			$value = $this->inputs[$k]->value();
			if( ! ( ($value === NULL) OR $this->inputs[$k]->readonly() ) ){
				$return[$k] = $this->inputs[$k]->value();
			}
		}
		return $return;
	}

	public function add_errors( $errors )
	{
		$input_names = array_keys($this->inputs);
		if( ! is_array($errors) ){
			$errors = array($errors);
		}

		foreach( $errors as $k => $e ){
			if( in_array($k, $input_names) && isset($this->inputs[$k]) ){
				$this->inputs[$k]->set_error( $e );
				$this->errors[$k] = $e;
			}
			else {
				$this->orphan_errors[$k] = $e;
			}
		}
		return $this;
	}

	function set_errors( $errors )
	{
		$this->errors = array();
		$this->orphan_errors = array();

		$this->add_errors( $errors );
		return $this;
	}

	function errors()
	{
		return $this->errors;
	}

	function orphan_errors()
	{
		return $this->orphan_errors;
	}
}