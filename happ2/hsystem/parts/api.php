<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class _HC_Rest_Api extends _HC_MVC
{
	protected $model = NULL;
	protected $validator = NULL;
	protected $user = array();

	public function user()
	{
		return $this->user;
	}

	public function set_user( $user )
	{
		$this->user = $user;
		return $this;
	}

// custom end points
	public function custom_get_count( $args = array() )
	{
		$model = $this->make( $this->model() );

		$where = $this->_build_where( $args );
		foreach( $where as $wh ){
			list( $k, $compare, $v ) = $wh;
			$model->where( $k, $compare, $v );
		}

		$having = $this->_build_having( $args );
		foreach( $having as $wh ){
			list( $k, $compare, $v ) = $wh;
			$model->having( $k, $compare, $v );
		}

		$return = $model->run('count');
		$return = json_encode( $return );

		return $this->make('/http/view/response')
			->set_status_code('200')
			->set_view( $return )
			;
	}

	public function set_model( $model ){
		$this->model = $model;
		return $this;
	}
	public function model()
	{
		if( $this->model ){
			$return = $this->model;
		}
		else {
			$return = '/' . $this->module() . '/model';
		}
		return $return;
	}

	public function set_validator( $validator ){
		$this->validator = $validator;
		return $this;
	}
	public function validator()
	{
		if( $this->validator ){
			$return = $this->validator;
		}
		else {
			$return = '/' . $this->module() . '/validator';
		}
		return $return;
	}

// READ
	public function get( $where = NULL )
	{
		$args = func_get_args();
		if( $args && isset($args[0]) ){
			if( is_array($args[0]) ){
				// echo 'DONTHAVE TO PARSE<br>';
				$args = array_shift($args);
			}
			else {
				// echo 'HAVE TO PARSE<br>';
				$args = hc2_parse_args( $args, TRUE );
			}
			// _print_r( $args );
		}

		if( isset($args['custom']) ){
			$what = $args['custom'];
			unset($args['custom']);

			if( method_exists($this, 'custom_get_' . $what) ){
				return call_user_func( array($this, 'custom_get_' . $what), $args );
			}
			else {
				return;
			}
		}

		if( isset($args['distinct']) ){
			$distinct_prop = $args['distinct'];
			unset( $args['distinct'] );
			return $this->get_distinct( $distinct_prop, $args );
		}
		elseif( isset($args['id']) && (! is_array($args['id'])) ){
			$args['limit'] = 1;
			$return = $this->_get_many( $args );
			$return = array_shift( $return );

			if( $return === NULL ){
				return $this->make('/http/view/response')
					->set_status_code('404')
					;
			}
			else {
				$return = json_encode( $return );
				return $this->make('/http/view/response')
					->set_status_code('200')
					->set_view( $return )
					;
			}
		}
		else {
			$return = $this->_get_many( $args );

			$return = json_encode( $return );
			return $this->make('/http/view/response')
				->set_status_code('200')
				->set_view( $return )
				;
		}
	}

// CREATE
	public function post( $json_input )
	{
		$values = json_decode( $json_input, TRUE );
		$validator = $this->make( $this->validator() );
		$valid = $validator->run( 'validate', $values );

		if( ! $valid ){
			$errors = $validator->errors();

			$return = array();
			$return['errors'] = $errors;

			$return = json_encode( $return );

			return $this->make('/http/view/response')
				->set_status_code('422')
				->set_view( $return )
				;
		}

		$model = $this->make( $this->model() );
		$model->from_array( $values );

		$model->run('save');

		$return = $model->run('to-array');
		$return = json_encode( $return );

		return $this->make('/http/view/response')
			->set_status_code('201')
			->set_view( $return )
			;
	}

// UPDATE
	public function put( $id, $json_input, $validate_change_only = TRUE )
	{
		$values = json_decode( $json_input, TRUE );
		$values['id'] = $id;

		$model = $this->make( $this->model() );

		$validator = $this->make( $this->validator() );
		$valid = $validator->run( 'validate', $values, $validate_change_only );

		if( ! $valid ){
			$errors = $validator->errors();

			$return = array();
			$return['errors'] = $errors;

			$return = json_encode( $return );

			return $this->make('/http/view/response')
				->set_status_code('422')
				->set_view( $return )
				;
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
		$return = json_encode( $return );

		return $this->make('/http/view/response')
			->set_status_code('200')
			->set_view( $return )
			;
	}

// DELETE
	public function delete( $id )
	{
		$model = $this->make( $this->model() )
			->where_id('=', $id)
			->fetch_one()
			;
		$model->run('delete');

		return $this->make('/http/view/response')
			->set_status_code('204')
			;
	}

	private function _build_where( $args )
	{
		$return = array();
		$compares = array();
		$allowed_compares = array('=', '<>', '>=', '<=', '>', '<', 'IN', 'NOTIN', 'LIKE');

		foreach( $args as $k => $v ){
			if( substr($k, 0, strlen('having_')) == 'having_' ){
				continue;
			}

			if( ! is_array($v) ){
				$compares[] = array($k, '=', $v);
			}
			else {
				while( $this_compare = array_shift($v) ){
					if( in_array($this_compare, array('IN', 'NOTIN')) ){
						if( is_array($v) && isset($v[0]) && is_array($v[0]) ){
							$v = array_shift( $v );
						}

						$compares[] = array($k, $this_compare, $v);
						$v = array();
					}
					else {
						$this_v = array_shift($v);
						$compares[] = array($k, $this_compare, $this_v);
					}
				}
			}
		}

		foreach( $compares as $cmp ){
			list( $k, $compare, $v ) = $cmp;

			$compare = trim( $compare );
			$compare = strtoupper( $compare );
			if( ! in_array($compare, $allowed_compares) ){
				echo "COMPARING BY '$compare' IS NOT ALLOWED!<br>";
				exit;
			}

			if( in_array($compare, array('IN', 'NOTIN')) ){
				if( ! is_array($v) ){
					echo 'V IS NOT ARRAY!';
					if( ! strlen($v) ){
						$v = 0;
					}
					$v = array($v);
				}
			}

			if( $compare == 'NOTIN' ){
				$compare = 'NOT IN';
			}

			$return[] = array( $k, $compare, $v );
		}

		return $return;
	}

	private function _build_having( $args )
	{
		$return = array();
		$compares = array();
		$allowed_compares = array('=', '<>', '>=', '<=', '>', '<', 'IN', 'NOTIN', 'LIKE');

		foreach( $args as $k => $v ){
			if( substr($k, 0, strlen('having_')) != 'having_' ){
				continue;
			}
			$k = substr($k, strlen('having_'));

			if( ! is_array($v) ){
				$compares[] = array($k, '=', $v);
			}
			else {
				while( $this_compare = array_shift($v) ){
					if( in_array($this_compare, array('IN', 'NOTIN')) ){
						if( is_array($v) && isset($v[0]) && is_array($v[0]) ){
							$v = array_shift( $v );
						}

						$compares[] = array($k, $this_compare, $v);
						$v = array();
					}
					else {
						$this_v = array_shift($v);
						$compares[] = array($k, $this_compare, $this_v);
					}
				}
			}
		}

		foreach( $compares as $cmp ){
			list( $k, $compare, $v ) = $cmp;

			$compare = trim( $compare );
			$compare = strtoupper( $compare );
			if( ! in_array($compare, $allowed_compares) ){
				echo "COMPARING BY '$compare' IS NOT ALLOWED!<br>";
				exit;
			}

			if( in_array($compare, array('IN', 'NOTIN')) ){
				if( ! is_array($v) ){
					echo 'V IS NOT ARRAY!';
					if( ! strlen($v) ){
						$v = 0;
					}
					$v = array($v);
				}
			}

			if( $compare == 'NOTIN' ){
				$compare = 'NOT IN';
			}

			$return[] = array( $k, $compare, $v );
		}

		return $return;
	}

	public function get_many( $args = array() )
	{
		$by = FALSE;
		if( isset($args['byid']) ){
			$by = 'id';
		}
		elseif( isset($args['by']) ){
			$by = $args['by'];
		}

		$model = $this->_prepare_get_many( $args );

		$entries = $model
			->run('fetch-many')
			;
		$return = array();
		foreach( $entries as $e ){
			$e = $e->run('to-array');
			if( $by ){
				$return[ $e[$by] ] = $e;
			}
			else {
				$return[] = $e;
			}
		}
		return $return;
	}

	protected function _prepare_get_many( $args = array() )
	{
		$model = $this->make( $this->model() );

		$by = FALSE;
		$with = array();

		if( isset($args['byid']) ){
			$by = 'id';
			unset($args['byid']);
		}
		elseif( isset($args['by'])){
			$by = $args['by'];
			unset($args['by']);
		}

		if( isset($args['with']) ){
			$with = is_array($args['with']) ? $args['with'] : array($args['with']);
			foreach( $with as $w ){
				$model->with( $w );
			}
			unset($args['with']);
		}

		if( isset($args['limit']) && isset($args['offset']) ){
			$model->limit( array($args['limit'], $args['offset']) );
			unset($args['limit']);
			unset($args['offset']);
		}
		unset($args['offset']);

		if( isset($args['limit']) ){
			$model->limit( $args['limit'] );
			unset($args['limit']);
		}

		if( isset($args['sort']) ){
			$sort = is_array($args['sort']) ? $args['sort'] : array($args['sort']);
			$k = array_shift($sort);
			$how = array_shift($sort);
			if( ! strlen($how) ){
				$how = 'asc';
			}
			else {
				$how = $how ? 'asc' : 'desc';
			}
			$model->order_by( $k, $how );
			unset($args['sort']);
		}

		$where = $this->_build_where( $args );
		foreach( $where as $wh ){
			list( $k, $compare, $v ) = $wh;
			$model->where( $k, $compare, $v );
		}

		$having = $this->_build_having( $args );
		foreach( $having as $wh ){
			list( $k, $compare, $v ) = $wh;
			$model->having( $k, $compare, $v );
		}

		return $model;
	}

	public function get_distinct( $pname, $args )
	{
		if( ! isset($args['sort']) ){
			$args['sort'] = $pname;
		}
		$model = $this->_prepare_get_many( $args );

		$return = $model
			->fetch_distinct( $pname )
			;

		$return = json_encode( $return );

		return $this->make('/http/view/response')
			->set_status_code('200')
			->set_view( $return )
			;
	}

	protected function _get_many( $args = array() )
	{
		$by = FALSE;
		if( isset($args['byid']) ){
			$by = 'id';
		}
		elseif( isset($args['by']) ){
			$by = $args['by'];
		}

		$fields = array();
		if( isset($args['select']) ){
			$fields = $args['select'];
			unset($args['select']);
		}

		$model = $this->_prepare_get_many( $args );

		if( $fields ){
			$model->fetch_fields( $fields );
		}

		$entries = $model
			->run('fetch-many')
			;
		$return = array();
		foreach( $entries as $e ){
			$e = $e->run('to-array');
			if( $fields && count($fields) == 1 ){
				$return[] = array_shift($e);
			}
			else {
				if( $by ){
					$return[ $e[$by] ] = $e;
				}
				else {
					$return[] = $e;
				}
			}
		}

		return $return;
	}
}