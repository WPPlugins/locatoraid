<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Commands_Create_HC_MVC extends _HC_MVC
{
	protected $model = NULL;
	protected $validator = NULL;

	public function set_model( $model )
	{
		$this->model = $model;
		return $this;
	}

	public function model()
	{
		$return = $this->model;
		return $return;
	}

	public function set_validator( $validator )
	{
		$this->validator = $validator;
		return $this;
	}

	public function validator()
	{
		$return = $this->validator;
		return $return;
	}

// CREATE
	public function execute( $values )
	{
		$validator = $this->make( $this->validator() );
		$valid = $validator->run( 'validate', $values );
		if( ! $valid ){
			$errors = $validator->errors();

			$return = array();
			$return['errors'] = $errors;

			return $return;
		}

		$model = $this->make( $this->model() );
		$model->from_array( $values );

		$model->run('save');

		$return = $model->run('to-array');
		return $return;
	}

}