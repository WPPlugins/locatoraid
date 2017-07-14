<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
/* gets one by id */
class Commands_Get_HC_MVC extends _HC_MVC
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

	public function execute( $id = NULL, $args = array() )
	{
		$return = array();
		$model = $this->make( $this->model() );

		$with_related = 1;

		reset( $args );
		foreach( $args as $arg ){
			list( $k, $v ) = $arg;

			switch( $k ){
				case 'flat':
					if( $v ){
						$with_related = 2;
					}
					break;

				case 'with':
					$with = is_array($v) ? $v : array($v);
					foreach( $with as $w ){
						$model
							->with( $w )
							;
					}
					break;
			}
		}

		$model
			->where_id( '=', $id )
			->limit(1)
			;

		$entries = $model
			->run('fetch-many')
			;

		foreach( $entries as $e ){
			$return = $e->run('to-array', $with_related);
			break;
		}

		return $return;
	}
}