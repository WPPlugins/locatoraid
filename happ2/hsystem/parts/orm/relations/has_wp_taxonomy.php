<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC_ORM_Relation_Has_WP_Taxonomy extends _HC_ORM_Relation implements HC_ORM_Relation_Interface
{
	protected $type = 'has_wp_taxonomy';

	public function set( $rel_object )
	{
		$this->related[ $rel_object->id() ] = $rel_object;
	}

	public function fetch()
	{
		$return = array();

		$my_id = $this->object->id();
		if( ! $my_id ){
			return $return;
		}

		$taxonomy = $this->details['relation_name'];
		$their_class = $this->details['their_class'];
		$their_name = $this->details['their_name'];

		$terms = get_the_terms( $my_id, $taxonomy );
		if( ! $terms ){
			return $return;
		}

		foreach( $terms as $term ){
			$their_model = $this->object->make($their_class);

			$term_array = $term->to_array();
			$their_id = $term_array['term_id'];
			$their_model->from_array( $term_array );
			$their_model->set_id( $their_id );

			$return[ $their_id ] = $their_model;
		}

		return $return;
	}

	public function insert( $rel )
	{
	}

	public function update( $rel )
	{
	}

	public function delete( $rel )
	{
	}

	public function delete_all()
	{
	}

	public function where( $parent, $their_where )
	{
		$return = FALSE;

		$taxonomy_name = $this->details['relation_name'];
		$my_post_type = $this->details['my_post_type'];

	// find their ids
		if( (count($their_where) == 1) && isset($their_where['id']) && count($their_where['id'] == 1) && in_array($their_where['id'][0][0], array('=', 'IN')) ){
			$their_ids = is_array($their_where['id'][0][1]) ? $their_where['id'][0][1] : array($their_where['id'][0][1]);
		}
		else {
			$their_class = $this->details['their_class'];
			$their_model = $this->object->make($their_class);

			foreach( $their_where as $k => $wheres ){
				foreach( $wheres as $wh ){
					$their_model->where( $k, $wh[0], $wh[1], $wh[2] );
				}
			}

			$their_rows = $their_model->fetch_array( array('id') );
			if( ! $their_rows ){
				return $return;
			}

			$their_ids = array();
			foreach( $their_rows as $their_row ){
				$their_ids[ $their_row['id'] ] = (int) $their_row['id'];
			}
		}

		$my_ids = array();
		$tax_query = array(
			array(
				'taxonomy'			=> $taxonomy_name,
				'field'				=> 'id',
				'terms'				=> $their_ids,
				)
			);

		$args = array(
			'fields'			=> 'ids',
			'posts_per_page'	=> -1,
			'post_type'			=> $my_post_type,
			'tax_query'			=> $tax_query,
			);
		$my_ids = get_posts( $args );

		$return = TRUE;
		if( count($my_ids) > 1 ){
			$parent->where_id('IN', $my_ids, FALSE);
		}
		else {
			$my_id = array_shift( $my_ids );
			$parent->where_id('=', (int) $my_id);
		}
		return $return;
	}

	public function with( $return )
	{
		if( ! $return ){
			return $return;
		}

		$taxonomy = $this->details['relation_name'];
		$their_class = $this->details['their_class'];
		$their_name = $this->details['their_name'];

		$return_ids = array_keys( $return );
		foreach( $return_ids as $my_id ){
			$terms = get_the_terms( $my_id, $taxonomy );
			if( ! $terms ){
				continue;
			}

			foreach( $terms as $term ){
				$their_model = $this->object->make($their_class);

				$term_array = $term->to_array();
				$their_model->from_array( $term_array );
				$their_model->set_id( $term_array['term_id'] );

				$return[$my_id]->related_set( $their_name, $their_model );
			}
		}

		return $return;
	}

	public function get()
	{
		if( $this->related === NULL ){
			$this->related = $this->fetch();
		}
		return $this->related;
	}

	public function is_loaded()
	{
		return ( $this->related === NULL ) ? FALSE : TRUE;
	}
}