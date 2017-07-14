<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC_ORM_Relation_Has_Many extends _HC_ORM_Relation implements HC_ORM_Relation_Interface
{
	protected $type = 'has_many';

	protected $my_id_field		= 'to_id';
	protected $their_id_field	= 'from_id';

	public function set( $rel_object )
	{
		$this->related[ $rel_object->id() ] = $rel_object;
	}

	public function get()
	{
		if( $this->related === NULL ){
			$this->related = $this->fetch();
		}
		return $this->related;
	}

	public function update( $rel )
	{
		$return = TRUE;

		$currents = $this->get();
		$current_ids = array_keys($currents);

		$relation_meta = isset($this->details['relation_meta']) ? $this->details['relation_meta'] : array();

	// with meta
		if( $relation_meta ){
			if( ! is_array($rel) ){
				return $return;
			}

			$rel_ids = array_keys( $rel );

		// insert
			$insert_ids = array_diff( $rel_ids, $current_ids );
			foreach( $insert_ids as $insert_id ){
				$this->insert( $insert_id, $rel[$insert_id] );
			}

		// update meta
			$update = array();
			$intersect_ids = array_intersect( $rel_ids, $current_ids );
			foreach( $intersect_ids as $this_id ){
				$new_meta = $rel[ $this_id ];
				foreach( $new_meta as $k => $v ){
					if( $currents[$this_id]->get('_' . $k) != $v ){
						if( ! isset($update[$this_id]) ){
							$update[$this_id] = array();
						}
						$update[$this_id][$k] = $v;
					}
				}
			}

			if( $update ){
				foreach( $update as $id => $meta ){
					$this->_update_meta( $id, $meta );
				}
			}
		}
	// plain
		else {
			$rel_ids = $rel;
			if( ! is_array($rel_ids) ){
				$rel_ids = array($rel_ids);
			}

		// insert
			$insert_ids = array_diff( $rel_ids, $current_ids );
			foreach( $insert_ids as $insert_id ){
				$this->insert( $insert_id );
			}
		}

	// delete
		$delete_ids = array_diff( $current_ids, $rel_ids );
		foreach( $delete_ids as $delete_id ){
			$this->delete( $delete_id );
		}

		return $return;
	}

	protected function _update_meta( $rel_id, $meta )
	{
		$relation_meta = isset($this->details['relation_meta']) ? $this->details['relation_meta'] : array();
		if( ! $relation_meta ){
			return;
		}

		$my_id = $this->object->id();
		$relation_name = $this->details['relation_name'];

		$new_join = array();
		if( $relation_meta ){
			for( $ii = 0; $ii < count($relation_meta); $ii++ ){
				$meta_name = $relation_meta[$ii];
				if( ! isset($meta[$meta_name]) ){
					continue;
				}
				$new_join['meta' . ($ii + 1)] = $meta[$meta_name];
			}
		}

		if( ! $new_join ){
			return;
		}

		$join = $this->join();

		$where = array(
			$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
			$this->my_id_field		=> array( array('=', (int) $my_id, FALSE) ),
			$this->their_id_field	=> array( array('=', (int) $rel_id, FALSE) ),
			);

		return $join->update( $new_join, $where );
	}

	public function insert( $rel, $meta = array() )
	{
		$return = TRUE;
		$rel_id = is_object($rel) ? $rel->id() : $rel;

		$my_id = $this->object->id();
		if( ! $my_id ){
			$return = FALSE;
			return $return;
		}

	// get current
		$currents = $this->get();

	// already
		if( $currents && isset($currents[$rel_id]) ){
			return $return;
		}

	// else insert new one
		$relation_name = $this->details['relation_name'];
		$their_class = $this->details['their_class'];
		$their_model = $this->object->make($their_class);
		$new_rel = $their_model
			->where_id('=', $rel_id)
			->fetch_one()
			;
		if( ! $new_rel->exists() ){
			$return = FALSE;
			return $return;
		}

		$join = $this->join();
		$new_join = array(
			$this->rel_name_field	=> $relation_name,
			$this->my_id_field		=> (int) $my_id,
			$this->their_id_field	=> (int) $rel_id,
			);

		$relation_meta = isset($this->details['relation_meta']) ? $this->details['relation_meta'] : array();
		if( $relation_meta ){
			for( $ii = 0; $ii < count($relation_meta); $ii++ ){
				$meta_name = $relation_meta[$ii];
				if( ! isset($meta[$meta_name]) ){
					continue;
				}
				$new_join['meta' . ($ii + 1)] = $meta[$meta_name];
			}
		}

		$relation_id = $join->insert( $new_join );

		return $return;
	}

	public function delete( $rel )
	{
		$return = TRUE;
		$rel_id = is_object($rel) ? $rel->id() : $rel;

		$my_id = $this->object->id();
		if( ! $my_id ){
			$return = FALSE;
			return $return;
		}

		$relation_name = $this->details['relation_name'];
		$cascade = isset($this->details['cascade']) ? $this->details['cascade'] : 0;

	// get current
		$currents = $this->get();

	// has it
		if( $currents && isset($currents[$rel_id]) ){
			$join = $this->join();
			$where = array(
				$this->my_id_field		=> array( array('=', (int) $my_id, FALSE) ),
				$this->their_id_field	=> array( array('=', (int) $rel_id, FALSE) ),
				$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
				);
			$join->delete( $where );

			if( $cascade ){
				$currents[$rel_id]->delete();
			}

			unset($this->related[$rel_id]);
		}
		return $return;
	}

	public function fetch()
	{
		$return = array();

		$relation_name = $this->details['relation_name'];
		$relation_meta = isset($this->details['relation_meta']) ? $this->details['relation_meta'] : array();

		$their_class = $this->details['their_class'];
		$their_name = $this->details['their_name'];
		$my_name = $this->details['my_name'];

		$my_id = $this->object->id();
		if( ! $my_id ){
			return $return;
		}

		$join = $this->join();

		$where = array(
			$this->my_id_field		=> array( array('=', (int) $my_id, FALSE) ),
			$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
			);

		$select_fields = array('id', $this->their_id_field);
		if( $relation_meta ){
			for( $ii = 0; $ii < count($relation_meta); $ii++ ){
				$select_fields[] = 'meta' . ($ii + 1) . ' AS ' . $relation_meta[$ii];
			}
		}

		$join_rows = $join->fetch(
			$select_fields,
			$where
			);

		if( ! $join_rows ){
			return $return;
		}

		$join_ids = array();
		$join_meta = array();
		$their_ids = array();
		foreach( $join_rows as $join_row ){
			$their_ids[ $join_row[$this->their_id_field] ] = (int) $join_row[$this->their_id_field];

			if( $relation_meta ){
				$this_join_meta = array();
				for( $ii = 0; $ii < count($relation_meta); $ii++ ){
					$meta_name = $relation_meta[$ii];
					$this_join_meta[$meta_name] = $join_row[$meta_name];
				}
				$join_meta[ $join_row[$this->their_id_field] ] = $this_join_meta;
			}
		}

		if( ! $their_ids ){
			return $return;
		}

		$their_model = $this->object->make($their_class);
		$their_objects = $their_model
			->where_id( 'IN', $their_ids )
			->run('fetch-many')
			;

		$their_ids = array_keys( $their_objects );
		foreach( $their_ids as $their_id ){
			if( isset($join_meta[$their_id]) ){
				foreach( $join_meta[$their_id] as $k => $v ){
					$their_objects[$their_id]->set('_' . $k, $v);
				}
			}
			$their_objects[$their_id]->related_set( $my_name, $this->object );
		}

		return $their_objects;
	}

	public function with( $return )
	{
		if( ! $return ){
			return $return;
		}

		$relation_name = $this->details['relation_name'];
		$relation_meta = isset($this->details['relation_meta']) ? $this->details['relation_meta'] : array();

		$their_class = $this->details['their_class'];
		$their_name = $this->details['their_name'];
		$my_name = $this->details['my_name'];

	// populate return with related
		$my_ids = array_keys($return);

		$join = $this->join();

		$where = array(
			$this->my_id_field		=> array( array('IN', $my_ids, FALSE) ),
			$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
			);

		$select_fields = array('id', $this->my_id_field, $this->their_id_field);
		if( $relation_meta ){
			for( $ii = 0; $ii < count($relation_meta); $ii++ ){
				$select_fields[] = 'meta' . ($ii + 1) . ' AS ' . $relation_meta[$ii];
			}
		}

		$join_rows = $join->fetch(
			$select_fields,
			$where
			);

		if( ! $join_rows ){
			return $return;
		}

		$join_ids = array();
		$join_meta = array();
		$their_ids = array();

		reset( $join_rows );
		foreach( $join_rows as $join_row ){
			$my_id = (int) $join_row[$this->my_id_field];
			$their_id = (int) $join_row[$this->their_id_field];
			$their_ids[$their_id] = $their_id;

			if( $relation_meta ){
				if( ! isset($join_meta[$my_id]) ){
					$join_meta[$my_id] = array();
				}

				$this_join_meta = array();
				for( $ii = 0; $ii < count($relation_meta); $ii++ ){
					$meta_name = $relation_meta[$ii];
					$this_join_meta[$meta_name] = $join_row[$meta_name];
				}
				$join_meta[$my_id][$their_id] = $this_join_meta;
			}
		}

		$their_model = $this->object->make($their_class);
		$their_objects = $their_model
			->where_id('IN', $their_ids)
			->run('fetch-many')
			;

		reset( $join_rows );
		foreach( $join_rows as $join_row ){
			$my_id		= $join_row[$this->my_id_field];
			$their_id	= $join_row[$this->their_id_field];
			$join_id	= $join_row['id'];

			if( isset($their_objects[$their_id]) && isset($return[$my_id]) ){
				$set_as_related = clone $their_objects[$their_id];
				// $set_as_related = $their_objects[$their_id];

				if( isset($join_meta[$my_id][$their_id]) ){
					foreach( $join_meta[$my_id][$their_id] as $k => $v ){
						$set_as_related->set('_' . $k, $v);
					}
				}

				$return[$my_id]->related_set( $their_name, $set_as_related );
				$their_objects[$their_id]->related_set( $my_name, $return[$my_id] );
			}
		}

		return $return;
	}
}