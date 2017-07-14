<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
abstract class _HC_Validator extends _HC_MVC
{
	protected $errors = array();

	// prepare validators
	public function prepare( $values )
	{
		$return = array();
		return $return;
	}

	public function validate( $values, $change_only = FALSE )
	{
		$return = TRUE;

		$errors = array();
		$validators = $this->run('prepare', $values);

		if( $change_only ){
			// remove those that are not needed now
			$not_needed = array_diff( array_keys($validators), array_keys($values) );
			foreach( $not_needed as $nn ){
				unset($validators[$nn]);
			}
		}

		foreach( $validators as $pname => $this_validators ){
			if( isset($errors[$pname]) ){
				continue;
			}

			$value = array_key_exists($pname, $values) ? $values[$pname] : NULL;

			reset( $this_validators );
			foreach( $this_validators as $validator_key => $args ){
				$validator = array_shift( $args );

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

			// if args remain args then it's a custom message
				$msg = NULL;
				if( $args ){
					$msg = array_shift( $args );
				}

				$validator_return = call_user_func_array( array($validator, $validation_method_name), $validator_args );

				if( $validator_return !== TRUE ){
					$return = FALSE;

					if( $msg !== NULL ){
						if( count($validator_args) > 1 ){
							$format_args = array_slice($validator_args, 1);
							array_unshift( $format_args, $msg ); 
							$msg = call_user_func_array('sprintf', $format_args );
						}
					}
					else {
						$msg = $validator_return;
					}

					$errors[ $pname ] = $msg;
					break;
				}
			}
		}

		if( $errors ){
			foreach( $errors as $k => $v ){
				$this->add_error( $k, $v );
			}
		}

		return $return;
	}

	public function add_error( $attr, $error )
	{
		$this->errors[ $attr ] = $error;
	}

	public function errors()
	{
		return $this->errors;
	}
}