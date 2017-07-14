<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Commands_Delete_HC_MVC extends _HC_MVC
{
	protected $model = NULL;

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

// DELETE
	public function execute( $id )
	{
		$return = FALSE;

		if( ! $id ){
			return $return;
		}

		$model = $this->make( $this->model() )
			->where_id('=', $id)
			->fetch_one()
			;
		$model->run('delete');

		$return = TRUE;
		return $return;
	}
}