<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
require( dirname(__FILE__) . '/storable.php' );
require( dirname(__FILE__) . '/storable_wp_custom_post.php' );
require( dirname(__FILE__) . '/storable_wp_taxonomy.php' );

abstract class _HC_ORM_WP_Custom_Post extends _HC_ORM
{
	public function __construct()
	{
		$this->storable = new _HC_ORM_WordPress_Custom_Post_Storable();
		$this->storable->set_cast( $this->cast );
		if( $this->join ){
			$this->storable->set_join( $this->join );
		}
	}

	public function set_db( $db )
	{
		$this->storable->set_db( $db );
		return $this;
	}

	public function delete_joins( $post_id )
	{
		return $this->storable->delete_joins( $post_id );
	}
}

abstract class _HC_ORM_WP_Taxonomy extends _HC_ORM
{
	public function __construct()
	{
		$this->storable = new _HC_ORM_WordPress_Taxonomy_Storable();
	}

	public function set_db( $db )
	{
		return $this;
	}
}

abstract class _HC_ORM extends _HC_MVC
{
	protected $relman = NULL;

	// protected $storable = NULL;
	public $storable = NULL;
	protected $id_key = 'id';

	// protected $where = array();
	public $where = array();
	public $having = array();
	protected $limit = NULL;
	protected $order_by = array();

	private $with = array();
	protected $where_with = array();

	private $props_stored = array();
	private $props = array();

	private $snapshot = array();

	private $related = NULL;

	protected $errors = array();

	protected $fields = array();
	protected $id = NULL;
	protected $was_id = NULL;
	protected $type = NULL;

	protected $search_in = array();
	protected $cast = array();
	protected $join = array();
	protected $fetch_fields = '*';
	protected $distinct = FALSE;

	public function fetch_fields( $fields )
	{
		if( ! is_array($fields) ){
			$fields = array( $fields );
		}
		$this->fetch_fields = $fields;
		return $this;
	}

	public function get_fetch_fields()
	{
		return $this->fetch_fields;
	}

	public function set_db( $db )
	{
		$this->storable = new _HC_ORM_Storable( 
			$db,
			$this->table,
			$this->id_key
			);
		$this->storable->set_search_in( $this->search_in() );
		if( $this->join ){
			$this->storable->set_join( $this->join );
		}
	}

	public function search_in()
	{
		return $this->search_in;
	}

	public function make_snapshot()
	{
		$values = $this->run('to-array', 2);
		$this->snapshot = $values;
		return $this;
	}

	public function snapshot()
	{
		return $this->snapshot;
	}

	public function relman()
	{
		return $this->relman;
	}
	public function set_relman( $relman )
	{
		$this->relman = $relman;
		return $this;
	}

	public function _init()
	{
		$this->init_relations();
		return $this;
	}

	public function type()
	{
		return $this->type;
	}
	
	public function clear()
	{
		$this->set_id( NULL );
		$this->props = array();
		return $this;
	}

	public function id()
	{
		return $this->id;
	}

	public function was_id()
	{
		$return = $this->was_id ? $this->was_id : $this->id;
		return $return;
	}

	public function set_was_id( $was_id )
	{
		$this->was_id = $was_id;
	}

	public function exists()
	{
		return $this->id ? TRUE : FALSE;
	}

	public function init_relations()
	{
		// if( $this->related === NULL ){
			$relman = $this->relman();
			if( $relman ){
				$this->related = $relman->init_relations( $this );
			}
		// }
	}

	public function set_id( $id )
	{
		$this->id = $id;
		$this->set( $this->id_key, $id, 'no_snapshot_yet' );
		if( $id === NULL ){
			$this->related = NULL;
		}
		else {
			$this->init_relations();
		}
		return $this;
	}

	public function limit( $limit )
	{
		if( ! is_array($limit) ){
			$limit = array($limit);
		}
		$this->limit = $limit;
		return $this;
	}

	public function current_where()
	{
		return $this->where;
	}

	public function having( $key, $how, $value, $escape = TRUE )
	{
		if( ! isset($this->having[$key]) ){
			$this->having[$key] = array();
		}
		if( is_numeric($value) ){
			$value = (float) $value;
		}
		$this->having[$key][] = array( $how, $value, $escape );
		return $this;
	}

	public function where( $key, $how, $value, $escape = TRUE )
	{
		$this->init_relations();
		if( isset($this->related[$key]) ){
			$this->where_with($key, 'id', $how, $value, $escape);
		}
		else {
			if( ! isset($this->where[$key]) ){
				$this->where[$key] = array();
			}
			$this->where[$key][] = array( $how, $value, $escape );
		}
		return $this;
	}

	public function where_with( $with, $key, $how, $value, $escape = TRUE )
	{
		if( ! isset($this->where_with[$with]) ){
			$this->where_with[$with] = array();
		}
		if( ! isset($this->where_with[$with][$key]) ){
			$this->where_with[$with][$key] = array();
		}
		$this->where_with[$with][$key][] = array( $how, $value, $escape );
		return $this;
	}

	public function order_by( $k, $how ){
		$this->order_by[$k] = $how;
		return $this;
	}

	public function set_order_by( $order_by = array() )
	{
		$this->order_by = $order_by;
		$this->default_order_by = array();
		return $this;
	}

	public function get_order_by()
	{
		$return = array_merge( $this->default_order_by, $this->order_by );
		return $return;
	}

	public function where_id( $how, $value, $escape = TRUE )
	{
		if( ! is_array($value) ){
			$value = (int) $value;
		}
		$this->where( $this->id_key, $how, $value, $escape );
		return $this;
	}

	public function count()
	{
		$return = 0;

		if( $this->where_with ){
			$where_with = $this->where_with;

			$this->init_relations();
			foreach( array_keys($where_with) as $rel_name ){
				if( isset($this->related[$rel_name]) ){
					$related_found = $this->related[$rel_name]->where($this, $where_with[$rel_name]);
					if( ! $related_found ){
						return $return;
					}
				}
			}
		}

		$return = $this->storable->count( $this->where, $this->having, $this->get_fetch_fields() );
		return $return;
	}

	public function distinct( $distinct = TRUE )
	{
		$this->distinct = $distinct;
		return $this;
	}

	public function fetch_distinct( $field )
	{
		$order_by = array();
		if( $this->order_by ){
			foreach( $this->order_by as $what => $how ){
				$order_by[] = array( $what, $how );
			}
		}

		$where = $this->where;
		$return = $this->storable->fetch( $field, $where, $this->limit, $order_by, TRUE );
		return $return;
	}

	public function fetch_array( $fields = '*' )
	{
		$return = array();

		if( $this->where_with ){
			$where_with = $this->where_with;

			$this->init_relations();
			foreach( array_keys($where_with) as $rel_name ){
				if( isset($this->related[$rel_name]) ){
					$related_found = $this->related[$rel_name]->where($this, $where_with[$rel_name]);
					if( ! $related_found ){
						return $return;
					}
				}
			}
		}

		$order_by = array();
		if( $this->order_by ){
			foreach( $this->order_by as $what => $how ){
				$order_by[] = array( $what, $how );
			}
		}
		elseif( isset($this->default_order_by) && $this->default_order_by ){
			foreach( $this->default_order_by as $what => $how ){
				$order_by[] = array( $what, $how );
			}
		}

		$where = $this->where;
		if( $this->fetch_fields ){
			$fields = $this->fetch_fields;
		}

		$return = $this->storable->fetch( $fields, $where, $this->limit, $order_by, $this->distinct, $this->having );

		$single_field = ( is_array($fields) OR ($fields == '*') ) ? FALSE : TRUE;
		if( $single_field ){
			$new_return = array();
			foreach( $return as $id => $row ){
				if( is_array($row) ){
					$new_return[] = array_shift($row);
				}
				else {
					$new_return[] = $row;
				}
			}
			$return = $new_return;
		}

		$this->where = array();
		$this->limit = NULL;
		$this->order_by = array();
		$this->distinct = FALSE;
		return $return;
	}

	private function _fetch_flat()
	{
		$return = array();

		$rows = $this->fetch_array();
		foreach( $rows as $id => $row ){
			$obj = clone $this;
			$obj->stored_from_array( $row );
			$return[ $id ] = $obj;
		}

		return $return;
	}

	public function fetch_many()
	{
		$return = $this->_fetch_flat();

		$with = $this->with;
		$this->with = array();

		if( $with ){
			$this->init_relations();
			foreach( array_keys($with) as $rel_name ){
				if( isset($this->related[$rel_name]) ){
					$return = $this->related[$rel_name]->with($return);
				}
			}
		}

		// $ids = array_keys($return);
		// foreach( $ids as $id ){
			// $return[$id]->make_snapshot();
		// }

		return $return;
	}

	public function fetch_one()
	{
		$this->clear();
		$this->limit(1);

		$return = $this->run('fetch-many');
		if( count($return) ){
			$return = array_shift($return);
			return $return;
		}
		return $this;
	}

// RELATED PROXY
	public function with( $what )
	{
		if( $what == '-all-' ){
			if( $this->related ){
				$withs = array_keys($this->related);
				foreach( $withs as $w ){
					$this->with[$w] = 1;
				}
			}
		}
		else {
			$this->with[$what] = 1;
		}
		return $this;
	}

	public function is_related( $rel_name )
	{
		$return = FALSE;
		if( isset($this->related[$rel_name]) ){
			$return = TRUE;
		}
		return $return;
	}

	public function related( $rel_name )
	{
		$return = NULL;
		if( isset($this->related[$rel_name]) ){
			$return = $this->related[$rel_name]->get();
		}
		else {
			echo "'$rel_name' IS NOT RELATED TO '" . $this->slug . "'";
		}
		return $return;
	}

	public function related_set( $rel_name, $rel_object )
	{
		if( isset($this->related[$rel_name]) ){
			$return = $this->related[$rel_name]->set($rel_object);
			return $return;
		}
		return $this;
	}

	public function related_insert( $rel_name, $rel )
	{
		if( isset($this->related[$rel_name]) ){
			$return = $this->related[$rel_name]->insert($rel);
		}
		return $this;
	}

	public function related_update( $rel_name, $rel )
	{
		if( isset($this->related[$rel_name]) ){
			$return = $this->related[$rel_name]->update($rel);
		}
		return $this;
	}

	public function related_delete( $rel_name, $rel = NULL )
	{
		if( isset($this->related[$rel_name]) ){
			$return = $this->related[$rel_name]->delete($rel);
		}
		return $this;
	}

	public function related_delete_all( $rel_name )
	{
		if( isset($this->related[$rel_name]) ){
			$return = $this->related[$rel_name]->delete_all();
		}
		return $this;
	}

// PROPERTIES
	public function changes()
	{
		$return = array();

		$new = $this->run('to-array', 2);
		$old = $this->snapshot();

		// if( $id = $this->id() ){
			// $new['id'] = $id;
		// }

		foreach( $new as $k => $v ){
			if( array_key_exists($k, $old) ){
				if( $old[$k] && $v ){
					if( ($old[$k] != $v) OR (strlen($old[$k]) != strlen($v)) ){
						$return[$k] = $old[$k];
					}
				}
				else {
					if( $old[$k] !== $v ){
						$return[$k] = $old[$k];
					}
				}
			}
			else {
				$return[$k] = NULL;
			}
		}
		return $return;
	}

	public function get( $pname )
	{
		$return = NULL;

		$get_method = 'get_' . $pname;
		if( method_exists($this, $get_method) ){
			$return = call_user_func( array($this, $get_method) );
		}
		else {
			if( isset($this->props[$pname]) ){
				$return = $this->props[$pname];
			}
		}
		return $return;
	}

	public function reset( $pname )
	{
		unset( $this->props[$pname] );
		return $this;
	}

	public function set( $pname, $pvalue, $no_snapshot = FALSE )
	{
		if( (! $no_snapshot) && (! $this->snapshot()) ){
			$this->make_snapshot();
		}

		$this->props[$pname] = $pvalue;
		return $this;
	}

	public function unset_prop( $pname )
	{
		unset( $this->props[$pname] );
		return $this;
	}

	protected function _filter_in( $array ){
		if( $this->fields ){
			$excessive_keys = array_diff( array_keys($array), $this->fields );
			foreach( $excessive_keys as $ek ){
				unset( $array[$ek] );
			}
		}

	// convert computed_* props
		foreach( $array as $k => $v ){
			if( substr($k, 0, strlen('computed_')) == 'computed_' ){
				$new_k = substr($k, strlen('computed_'));
				$array[$new_k] = $v;
				unset( $array[$k] );
			}
		}

		return $array;
	}

	public function to_array( $with_related = 1, $skip_related = array() ) // 0 - no, 1 - full, 2 - flat
	{
		$flat_related = ($with_related == 2) ? TRUE : FALSE;

		if( $this->fields ){
			$return = array();
			foreach( $this->fields as $f ){
				if( array_key_exists($f, $this->props) ){
					$return[$f] = $this->props[$f];
				}
			}
		}
		else {
			$return = $this->props;
		}

		$current_id = $this->id();
		if( $current_id ){
			$return['id'] = $current_id;
		}

		if( $with_related && $this->related ){
			foreach( array_keys($this->related) as $rel_name ){
				if( array_key_exists($rel_name, $return) ){
					continue;
				}

				if( $skip_related && in_array($rel_name, $skip_related) ){
					continue;
				}

				$ro = $this->related[$rel_name];
				$this_my_name = $ro->details('my_name');
				if( $this->related[$rel_name]->is_loaded() ){
					$related = $this->related[$rel_name]->get();
					if( is_object($related) ){
						if( $flat_related ){
							$return[$rel_name] = $related->id();
						}
						else {
							// $return[$rel_name] = $related->run('to-array', FALSE);
							$return[$rel_name] = $related->run('to-array', 1, array($this_my_name));
						}
					}
					elseif( is_array($related) ){
						$return[$rel_name] = array();
						foreach( $related as $r ){
							if( $flat_related ){
								$return[$rel_name][] = $r->id();
							}
							else {
								// $return[$rel_name][$r->id()] = $r->run('to-array', FALSE);
								$return[$rel_name][$r->id()] = $r->run('to-array', 1, array($this_my_name));
							}
						}
					}
					else {
						$return[$rel_name] = $related;
					}
				}
				else {
					$return[$rel_name] = NULL;
				}
			}
		}

		$return = $this->prepare_for_output( $return );
		return $return;
	}

	public function stored_from_array( $array )
	{
		$supplied_id = isset($array[$this->id_key]) ? $array[$this->id_key] : NULL;
		$array = $this->_filter_in( $array );

		$this->props = $array;
		if( (! $this->id) && $supplied_id ){
			$this->set_id( $supplied_id );
		}
	}

	public function from_array( $array )
	{
		$array = $this->_filter_in( $array );

		foreach( $array as $k => $v ){
			$this->set( $k, $v );
		}

		return $this;
	}

// ACL STUFF
// by default everyting is allowed, extend it in chained objects
	public function options()
	{
		$return = array();
		foreach( $this->fields as $fn ){
			$return[$fn] = array('*');
		}
		return $return;
	}

// SAVE PROXY
	public function prepare_for_save( $values )
	{
		return $values;
	}

	public function prepare_for_output( $values )
	{
		return $values;
	}

	public function save()
	{
		$return = NULL;
		$id = $this->id();
		if( $id ){
			$return = $this->_update();
		}
		else {
			$return = $this->_insert();
		}
		return $return;
	}

	protected function _update()
	{
		$return = TRUE;
		$data = $this->run('to-array');

		$changes = $this->changes();
		if( $changes ){
			$new_data = array();
			foreach( array_keys($changes) as $k ){
				$new_data[$k] = $data[$k];
			}

			$new_data = $this->run('prepare-for-save', $new_data );

		// check related
			$new_related = array();
			$keys = array_keys($new_data);

			foreach( $keys as $k ){
				if( $this->is_related($k) ){
					$new_related[$k] = $new_data[$k];
					unset( $new_data[$k] );
					continue;
				}
			}

			if( $new_data ){
				$this->where_id('=', $this->id());
				if( $this->storable->update($new_data, $this->where) ){
					$return = TRUE;
				}
				else {
					$return = FALSE;
				}
			}

		// check related
			foreach( $new_related as $k => $v ){
				$this->related_update($k, $v);
			}
		}
		return $return;
	}

	protected function _insert()
	{
		$this->init_relations();
		$return = FALSE;

		$data = $this->run('to-array');
		$data = $this->run('prepare-for-save', $data);

	// check related
		$related = array();

		$keys = array_keys($data);
		foreach( $keys as $k ){
			if( $this->is_related($k) ){
				$related[$k] = $data[$k];
				unset($data[$k]);
				continue;
			}
		}

		if( array_key_exists('id', $data) && (! isset($data['id'])) ){
			unset( $data['id'] );
		}

		$new_id = $this->storable->insert($data);
		if( $new_id ){
			$this->set_id( $new_id );

		// check related
			foreach( $related as $k => $v ){
				$this->related_update($k, $v);
			}

			$return = TRUE;
		}
		return $return;
	}

	public function delete_all()
	{
		$return = TRUE;

		$this->init_relations();
		$rel_names = array_keys($this->related);
		foreach( $rel_names as $rel_name ){
			$this->related_delete_all($rel_name);
		}

		$this->storable->delete_all();
		return $return;
	}

	public function delete()
	{
		$return = FALSE;
		$id = $this->id();

		if( $id ){
		// delete relations
			$this->init_relations();
			$rel_names = array_keys($this->related);

			foreach( $rel_names as $rel_name ){
				$related = $this->related($rel_name);
				if( ! is_array($related) ){
					$related = array( $related->id() => $related );
				}
				foreach( $related as $rel_id => $rel_obj ){
					$this->related_delete($rel_name, $rel_obj);
				}
			}

		// delete myself
			$this->where( $this->id_key, '=', $id );
			$this->storable->delete($this->where);
			$this->set_id( NULL );
			$this->set_was_id( $id );
			$return = TRUE;
		}
		return $return;
	}
}