<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Commands_Update_HC_MVC extends _HC_MVC
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

// UPDATE
	public function execute( $id, $values )
	{
		$values['id'] = $id;

		$model = $this->make( $this->model() );

		$validator = $this->make( $this->validator() );
		$validate_change_only = TRUE;

		$valid = $validator->run( 'validate', $values, $validate_change_only );

		if( ! $valid ){
			$errors = $validator->errors();

			$return = array();
			$return['errors'] = $errors;

			return $return;
		}

	// fetch current value to make sure it logs changes
		$with = array();
		foreach( array_keys($values) as $k ){
			if( $model->is_related($k) ){
				$with[] = $k;
			}
		}

		foreach( $with as $w ){
			$model->with( $w );
		}
		$model = $model
			->where_id('=', $id)
			->fetch_one()
			;

		$model->make_snapshot();

		$model->set_id( $id );

		$model->from_array( $values );
		$model->run('save');

		$return = $model->run('to-array');
		return $return;
	}
}