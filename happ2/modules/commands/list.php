<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Commands_List_HC_MVC extends _HC_MVC
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

	public function execute( $args = array() )
	{
		$allowed_compares = array('=', '<>', '>=', '<=', '>', '<', 'IN', 'NOTIN', 'LIKE', '&');
		$model = $this->make( $this->model() );

		$with_related = 1;
		reset( $args );
		foreach( $args as $arg ){
			if( count($arg) == 3 ){
				list( $k, $compare, $v ) = $arg;
				$compare = strtoupper( $compare );
			}
			else {
				list( $k, $v ) = $arg;
				$compare = '=';
			}
			if( ! in_array($compare, $allowed_compares) ){
				echo "COMPARING BY '$compare' IS NOT ALLOWED!<br>";
				exit;
			}

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

				case 'limit':
					$model
						->limit( $v )
						;
					break;

				case 'sort':
					$sort = is_array($v) ? $v : array($v);
					$sort_by = array_shift( $sort );
					$sort_how = array_shift( $sort );
					if( ! $sort_how ){
						$sort_how = 'asc';
					}
					$sort_how = strtolower( $sort_how );
					$allowed_how = array('asc', 'desc');
					if( ! in_array($sort_how, $allowed_how) ){
						echo "SORTING '$sort_how' IS NOT ALLOWED, ONLY ASC OR DESC!<br>";
						$sort_how = 'asc';
					}

					$model
						->order_by( $sort_by, $sort_how )
						;
					break;

				default:
					if( in_array($compare, array('IN', 'NOTIN')) ){
						if( ! is_array($v) ){
							if( ! strlen($v) ){
								$v = 0;
							}
							$v = array($v);
						}
					}

					if( $compare == 'NOTIN' ){
						$compare = 'NOT IN';
					}

					$model
						->where( $k, $compare, $v )
						;
					break;
			}
		}

		$entries = $model
			->run('fetch-many')
			;

		$return = array();
		foreach( $entries as $e ){
			$e = $e->run('to-array', $with_related);
			$return[] = $e;
		}

		return $return;
	}
}