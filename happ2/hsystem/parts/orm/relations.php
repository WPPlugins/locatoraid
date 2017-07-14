<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
require( dirname(__FILE__) . '/relations/has_one.php' );
require( dirname(__FILE__) . '/relations/belongs_to_one.php' );
require( dirname(__FILE__) . '/relations/has_many.php' );
require( dirname(__FILE__) . '/relations/belongs_to_many.php' );

require( dirname(__FILE__) . '/relations/has_wp_taxonomy.php' );

interface HC_ORM_Relation_Interface
{
	public function get();
	public function set( $rel );
	public function fetch();
	public function insert( $rel );
	public function update( $rel );
	public function delete( $rel );
	public function delete_all();
	public function with( $return );
	public function where( $parent, $their_where );
	public function is_loaded();
}

abstract class _HC_ORM_Relation
{
	protected $details = array();
	protected $related = NULL;
	protected $object = NULL;
	protected $type = NULL;
	protected $join = NULL;
	protected $rel_name_field	= 'relation_name';

	protected $jid = 1;

	public function __construct( $object, $details )
	{
		$this->object = $object;
		$this->details = $details;
	}

	public function details( $name = NULL )
	{
		if( $name ){
			$return = isset($this->details[$name]) ? $this->details[$name] : NULL;
		}
		else {
			$return = $this->details;
		}
		return $return;
	}

	public function type()
	{
		return $this->type;
	}

	public function delete_all()
	{
		$return = TRUE;

		$relation_name = $this->details['relation_name'];
		$join = $this->join();
		$where = array(
			$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
			);
		$join->delete( $where );

		return $return;
	}

	public function set_db( $db )
	{
		if( isset($this->details['join_table']) ){
			$join_table = $this->details['join_table'];
		}
		else {
			$join_table = 'relations';
		}
		$join_table = $db->protect_identifiers( $join_table, TRUE, FALSE );

		$join = new _HC_ORM_Storable( $db, $join_table );
		$this->set_join( $join );

		return $this;
	}

	public function set_join( $join )
	{
		$this->join = $join;
		return $this;
	}
	public function join()
	{
		return $this->join;
	}

	public function get_system_relation_id( $rel_id )
	{
		$return = NULL;
		$join = $this->join();

		$my_id = $this->object->id();
		$relation_name = $this->details['relation_name'];

		$where = array(
			$this->my_id_field		=> array( array('=', (int) $my_id, FALSE) ),
			$this->their_id_field	=> array( array('=', (int) $rel_id, FALSE) ),
			$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
			);

		$join_rows = $join->fetch( 
			array('id'),
			$where
			);
		if( ! $join_rows ){
			return $return;
		}

		foreach( $join_rows as $r ){
			$return = $r['id'];
			break;
		}
		return $return;
	}

	public function is_loaded()
	{
		return ( $this->related === NULL ) ? FALSE : TRUE;
	}

	public function where( $parent, $their_where )
	{
		$return = FALSE;

		$relation_name = $this->details['relation_name'];
		if( isset($this->details['join_table']) ){
			$relations_table = $this->details['join_table'];
		}
		else {
			$relations_table = 'relations';
		}

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

		if( ! $their_ids ){
			return $return;
		}

		static $jid = 1;
		$join_relations_as = 'rel' . $jid;
		$jid++;

		// $join_relations_as = 'rel' . $this->jid;
		// $this->jid++;

		if( method_exists($parent->storable, 'add_join') ){
			$parent->storable->add_join( 
				array(
					'table'			=>	$relations_table . ' ' . $join_relations_as,
					'their_column'	=>	$this->my_id_field,
					'as'			=> $join_relations_as
					)
				);

			$parent->where(
				$join_relations_as . '.' . 'relation_name',
				'=',
				$relation_name
				// FALSE
				);

			if( count($their_ids) > 1 ){
				$parent->where( 
					$join_relations_as . '.' . $this->their_id_field,
					'IN',
					$their_ids
					);
			}
			else {
				$their_id = array_shift( $their_ids );
				$parent->where(
					$join_relations_as . '.' . $this->their_id_field,
					'=',
					(int) $their_id
					);
			}
		}
		else {
			$relation_name = $this->details['relation_name'];
			$join = $this->join();

			$where = array(
				$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
				);
			if( count($their_ids) > 1 ){
				$where[ $this->their_id_field ] = array( array('IN', $their_ids, FALSE) );
			}
			else {
				$their_id = array_shift( $their_ids );
				$where[ $this->their_id_field ] = array( array('=', (int) $their_id, FALSE) );
			}

			$join_rows = $join->fetch( 
				array($this->my_id_field),
				$where
				);

			$my_ids = array();
			foreach( $join_rows as $r ){
				$my_ids[] = $r[$this->my_id_field];
			}

			if( count($my_ids) > 1 ){
				$parent->where_id(
					'IN',
					$my_ids
					);
			}
			elseif( count($my_ids) == 1 ){
				$my_id = array_shift($my_ids);
				$parent->where_id(
					'=',
					(int) $my_id
					);
			}
			else {
				$parent->where_id(
					'=',
					array()
					);
			}
		}

		$return = TRUE;
		return $return;
	}

	public function _where( $parent, $their_where )
	{
		$return = FALSE;

		$relation_name = $this->details['relation_name'];

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

		$join = $this->join();

		$where = array(
			$this->rel_name_field	=> array( array('=', $relation_name, TRUE) ),
			);
		if( count($their_ids) > 1 ){
			$where[ $this->their_id_field ] = array( array('IN', $their_ids, FALSE) );
		}
		else {
			$their_id = array_shift( $their_ids );
			$where[ $this->their_id_field ] = array( array('=', (int) $their_id, FALSE) );
		}

		$join_rows = $join->fetch( 
			array($this->my_id_field),
			$where
			);

		$my_ids = array();
		foreach( $join_rows as $join_row ){
			$my_ids[ $join_row[$this->my_id_field] ] = (int) $join_row[$this->my_id_field];
		}
		if( ! $my_ids ){
			return $return;
		}

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
}

class HC_ORM_Relations_Manager
{
	private $config	= array();
	private $db = NULL;

	public function set_config( $config )
	{
		$this->config = $config;
		return $this;
	}

	public function set_db( $db )
	{
		$this->db = $db;
		return $this;
	}

	public function init_relations( $object )
	{
		$return = array();
		$class = $object->slug;

		if( isset($this->config[$class]['has_many']) ){
			foreach( $this->config[$class]['has_many'] as $rel_name => $rel_details ){
				$rel_details['their_name'] = $rel_name;
				$rel = new HC_ORM_Relation_Has_Many($object, $rel_details); 
				if( $this->db ){
					$rel->set_db( $this->db );
				}
				$return[$rel_name] = $rel;
			}
		}

		if( isset($this->config[$class]['has_one']) ){
			foreach( $this->config[$class]['has_one'] as $rel_name => $rel_details ){
				$rel_details['their_name'] = $rel_name;
				$rel = new HC_ORM_Relation_Has_One($object, $rel_details);
				if( $this->db ){
					$rel->set_db( $this->db );
				}
				$return[$rel_name] = $rel;
			}
		}

		if( isset($this->config[$class]['belongs_to_many']) ){
			foreach( $this->config[$class]['belongs_to_many'] as $rel_name => $rel_details ){
				$rel_details['their_name'] = $rel_name;
				$rel = new HC_ORM_Relation_Belongs_To_Many($object, $rel_details);
				if( $this->db ){
					$rel->set_db( $this->db );
				}
				$return[$rel_name] = $rel;
			}
		}

		if( isset($this->config[$class]['belongs_to_one']) ){
			foreach( $this->config[$class]['belongs_to_one'] as $rel_name => $rel_details ){
				$rel_details['their_name'] = $rel_name;
				$rel = new HC_ORM_Relation_Belongs_To_One($object, $rel_details);
				if( $this->db ){
					$rel->set_db( $this->db );
				}
				$return[$rel_name] = $rel;
			}
		}

		if( isset($this->config[$class]['has_wp_taxonomy']) ){
			foreach( $this->config[$class]['has_wp_taxonomy'] as $rel_name => $rel_details ){
				$rel_details['their_name'] = $rel_name;
				$rel = new HC_ORM_Relation_Has_Wp_Taxonomy($object, $rel_details);
				$return[$rel_name] = $rel;
			}
		}

		if( isset($this->config[$class]['belongs_to_wp_taxonomy']) ){
			foreach( $this->config[$class]['belongs_to_wp_taxonomy'] as $rel_name => $rel_details ){
				$rel_details['their_name'] = $rel_name;
				$rel = new HC_ORM_Relation_Belongs_To_Wp_Taxonomy($object, $rel_details);
				$return[$rel_name] = $rel;
			}
		}

		return $return;
	}
}