<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class _HC_ORM_WordPress_Taxonomy_Storable implements _HC_ORM_Storable_Interface
{
	public $taxonomy_type = NULL;

	public function fetch( $fields = '*', $wheres = array(), $limit = NULL, $orderby = NULL, $distinct = FALSE )
	{
		$return = array();

		$tax_query = array(
			'taxonomy'		=> $this->taxonomy_type,
			'hide_empty'	=> FALSE,
			// 'hide_empty'	=> TRUE,
			);
		$terms = get_terms( $tax_query );

		foreach( $terms as $term ){
			$return[ $term->term_id ] = array(
				'id'	=> $term->term_id,
				'title'	=> $term->name,
				);
		}

		return $return;
	}

	public function count( $wheres = array() )
	{
		$return = wp_count_terms( $this->taxonomy_type );
		return $return;
	}

	public function insert( $data )
	{
	}

	public function update( $data, $wheres = array() )
	{
	}

	public function delete_all()
	{
	}

	public function delete( $wheres = array() )
	{
	}
}
