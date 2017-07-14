<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Validate_Unique_HC_MVC extends _HC_MVC
{
	public function validate( $value, $model, $field, $skip = NULL )
	{
		$return = TRUE;
		$msg = HCM::__('This value is already used');
		$id_field = 'id';

		$model_slug = '/' . $model . '/model';
		$model = $this->make($model_slug);

		$model
			->where( $field, '=', $value )
			->limit(1)
			;

		if( $skip ){
			if( ! is_array($skip) ){
				$skip = array($skip);
			}
			$model
				->where( $id_field, 'NOT IN', $skip )
				;
		}

		$count = $model
			->run('count')
			;
		if( $count ){
			$return = $msg;
		}

		return $return;
	}
}