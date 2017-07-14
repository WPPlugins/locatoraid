<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC_ORM_Relation_Has_One extends _HC_ORM_Relation implements HC_ORM_Relation_Interface
{
	protected $type = 'has_one';

	protected $my_id_field		= 'to_id';
	protected $their_id_field	= 'from_id';

	public function set( $rel_object )
	{
		$this->related = $rel_object;
	}

	public function get()
	{
		if( $this->related === NULL ){
			$this->related = $this->fetch();
		}
		return $this->related;
	}

	public function fetch()
	{
		$return = NULL;

		$relation_name = $this->details['relation_name'];
		$relation_meta = isset($this->details['relation_meta']) ? $this->details['relation_meta'] : array();

		$their_class = $this->details['their_class'];
		$their_name = $this->details['their_name'];
		$my_name = $this->details['my_name'];

		$their_model = $this->object->make($their_class);

		$my_id = $this->object->id();
		if( ! $my_id ){
			return $their_model;
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
			$where,
			1
			);

		if( ! $join_rows ){
			return $their_model;
		}

		$join_row = array_shift( $join_rows );
		$their_id = $join_row[$this->their_id_field];

		$their_object = $their_model
			->where_id( '=', $their_id )
			->fetch_one()
			;

		if( $relation_meta ){
			for( $ii = 0; $ii < count($relation_meta); $ii++ ){
				$meta_name = $relation_meta[$ii];
				if( isset($join_rows[$meta_name]) ){
					$their_objects[$their_id]->set('_' . $meta_name, $join_rows[$meta_name]);
				}
			}
		}

		$their_object->related_set( $my_name, $this->object );
		return $their_object;
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
			$their_id = (int) $join_row[$this->their_id_field];
			$their_ids[$their_id] = $their_id;

			if( $relation_meta ){
				$this_join_meta = array();
				for( $ii = 0; $ii < count($relation_meta); $ii++ ){
					$meta_name = $relation_meta[$ii];
					$this_join_meta[$meta_name] = $join_row[$meta_name];
				}
				$join_meta[$their_id] = $this_join_meta;
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
				if( isset($join_meta[$their_id]) ){
					foreach( $join_meta[$their_id] as $k => $v ){
						$their_objects[$their_id]->set('_' . $k, $v);
					}
				}
				$set_as_related = $their_objects[$their_id];

				$return[$my_id]->related_set($their_name, $set_as_related);
				$their_objects[$their_id]->related_set($my_name, $return[$my_id]);
			}
		}

		return $return;
	}

	public function delete( $rel )
	{
		$return = TRUE;

		$relation_name = $this->details['relation_name'];
		$my_id = $this->object->id();

		$join = $this->join();

		$where = array(
			$this->my_id_field		=> array( array('=', (int) $my_id, FALSE) ),
			$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
			);

		$join->delete( $where );
		return $return;
	}

	public function insert( $rel, $meta = array() )
	{
		$return = TRUE;
		if( is_object($rel) ){
			$rel_id = $rel->id();
		}
		elseif( is_array($rel) && isset($rel[0]) ){
			$rel_id = $rel[0];
		}
		elseif( is_array($rel) && isset($rel['id']) ){
			$rel_id = $rel['id'];
		}
		else {
			$rel_id = $rel;
		}

	// already?
		$relation_name = $this->details['relation_name'];
		$their_class = $this->details['their_class'];

		$my_id = $this->object->id();
		if( ! $my_id ){
			$return = FALSE;
			return $return;
		}

		$join = $this->join();
		$where = array(
			$this->my_id_field		=> array( array('=', (int) $my_id, FALSE) ),
			$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
			);

		$join_rows = $join->fetch(
			array($this->their_id_field),
			$where,
			1
			);

		$join_id = NULL;

		if( $join_rows ){
			$join_row = array_shift( $join_rows );
			$current_rel_id	= $join_row[$this->their_id_field];
			$join_id		= $join_row['id'];
		}

		if( $rel_id ){
			$their_model = $this->object->make($their_class);
			$new_rel = $their_model
				->where_id('=', $rel_id)
				->fetch_one()
				;
			if( ! $new_rel->exists() ){
				$return = FALSE;
				return $return;
			}

			if( $join_id ){
				if( $current_rel_id == $rel_id ){
					return $return;
				}

				$update_join = array(
					$this->their_id_field	=> (int) $rel_id,
					);
				$where = array(
					'id'	=> array( array('=', (int) $join_id, FALSE) ),
					);
				$join->update( $update_join, $where );
			}
			else {
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

				$join->insert( $new_join );
			}
		}
	// delete existing
		elseif( $join_id ){
			$where = array(
				'id'	=> array( array('=', (int) $join_id, FALSE) ),
				);
			$join->delete( $where );
		}

		return $return;
	}

	public function update( $rel )
	{
		return $this->insert( $rel );
	}
}