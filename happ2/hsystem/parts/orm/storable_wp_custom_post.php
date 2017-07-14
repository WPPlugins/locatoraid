<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class _HC_ORM_WordPress_Custom_Post_Storable implements _HC_ORM_Storable_Interface
{
	public $post_type = NULL;
	protected $cast = array();
	protected $join = array();
	protected $where_join = array();

	protected $db = NULL;
	protected $id_field = 'ID';

	public function set_cast( $cast )
	{
		$this->cast = $cast;
		return $this;
	}

	public function set_db( $db )
	{
		$this->db = $db;
		return $this;
	}

	public function set_join( $join )
	{
		$this->join = $join;
		return $this;
	}

// extend wp query
	public function posts_fields( $fields, $wp_query )
	{
		if( ! (isset($wp_query->query_vars['post_type']) && ($wp_query->query_vars['post_type'] == $this->post_type)) ){
			return $fields;
		}

		if( ! $this->join ){
			return $fields;
		}

		foreach( $this->join as $jn ){
			$more_fields = array();
			foreach( $jn['fields'] as $jf ){
				$table = $this->db->protect_identifiers( $jn['table'], TRUE, FALSE );
				$more_fields[] = $table . '.' . $jf . ' AS ' . $jf;
			}
			$fields .= ', ' . join(', ', $more_fields);
		}

		return $fields;
	}

	public function posts_join( $join, $wp_query )
	{
		if( ! (isset($wp_query->query_vars['post_type']) && ($wp_query->query_vars['post_type'] == $this->post_type)) ){
			return $join;
		}

		if( ! $this->join ){
			return $join;
		}

		global $wpdb;
		foreach( $this->join as $jn ){
			$table = $this->db->protect_identifiers( $jn['table'], TRUE, FALSE );
			$join .= ' LEFT JOIN ' . $table . ' ON (' . $table . '.' . $jn['their_column'] . ' = ' . $wpdb->posts . '.' . $this->id_field . ') ';
		}

		return $join;
	}

	public function posts_where( $where, $wp_query )
	{
		if( ! (isset($wp_query->query_vars['post_type']) && ($wp_query->query_vars['post_type'] == $this->post_type)) ){
			return $where;
		}

		if( ! $this->join ){
			return $where;
		}

		if( ! $this->where_join ){
			return $where;
		}

		foreach( $this->where_join as $key => $conds ){
			$key = $this->db->protect_identifiers( $key, TRUE, FALSE );

			foreach( $conds as $subwhere ){
				list( $how, $value, $escape ) = $subwhere;
				if( $how == 'IN' ){
					$this->db->where_in($key, $value);
				}
				elseif( $how == 'NOT IN' ){
					$this->db->where_not_in($key, $value);
				}
				elseif( $how == 'LIKE' ){
					$this->db->like($key, $value);
				}
				else {
					$this->db->where($key . $how, $value, $escape);
				}

				$this_where = $this->db->get_compiled_where();
				$where .= ' AND ' . $this_where;
			}
		}
		return $where;
	}

	protected function _get_id( $wheres = array() )
	{
		$return = NULL;
		foreach( $wheres as $key => $key_wheres ){
			if( $key != 'id' ){
				continue;
			}

			$return = array();
			foreach( $key_wheres as $this_key_wheres ){
				$this_return = array();
				if( $this_key_wheres[0] == '=' ){
					$this_return = array( $this_key_wheres[1] );
				}
				elseif( $this_key_wheres[0] == 'IN' ){
					$this_return = $this_key_wheres[1];
				}

				if( $return ){
					$return = array_intersect( $return, $this_return );
					if( ! $return ){
						break;
					}
				}
				else {
					$return = $this_return;
				}
			}
		}
		return $return;
	}

	protected function _prepare_args( $wheres = array() )
	{
		$post_id = $this->_get_id( $wheres );

		$statuses = array('publish', 'future', 'draft', 'pending');
		if( $post_id && count($post_id) == 1 ){
			$statuses = array('*');
		}

		$args = array( 
			'post_type'		=> $this->post_type,
			'post_status'	=> $statuses,
			);

		if( isset($wheres['search']) ){
			foreach( $wheres['search'] as $where ){
				$args['s'] = $where[1];
			}
			unset($wheres['search']);
		}


	// one
		if( $post_id ){
			$args['post__in'] = $post_id;
			unset( $wheres['id'] );
		}

		if( $wheres ){
			$meta_query = array();
			foreach( $wheres as $k => $conds ){
				reset( $conds );
				if( substr($k, 0, strlen('post_')) == 'post_' ){
					foreach( $conds as $cond ){
						$args['s'] = $cond[1];
					}
				}
			// join where
				elseif( strpos($k, '.') !== FALSE ){
					if( ! isset($args['_where_join']) ){
						$args['where_join'] = array();
					}
					$args['where_join'][$k] = $conds;
				}
				else {
					foreach( $conds as $cond ){
						switch( $cond[0] ){
							case 'NOT IN':
							case 'NOTIN':
								$args['post__not_in'] = $cond[1];
								continue;
								break;

							default:
								$this_meta_q = array(
									'key'		=> $k,
									'value'		=> $cond[1],
									'compare'	=> $cond[0],
									);
								if( isset($this->cast[$k]) ){
									$this_meta_q['type'] = $this->cast[$k];
								}
								$meta_query[] = $this_meta_q;
								break;
						}
					}
				}
			}
			$args['meta_query'] = $meta_query;
		}

		return $args;
	}

	protected function _fetch_distinct( $field, $wheres = array(), $limit = NULL, $orderby = NULL )
	{
		global $wpdb;
		if( is_array($field) ){
			$field = array_shift( $field );
		}

		$query = "
			SELECT      DISTINCT m.meta_value
			FROM        $wpdb->postmeta AS m
			INNER JOIN  $wpdb->posts AS p
						ON m.post_id = p.ID
			WHERE       m.meta_key = %s 
						AND p.post_type = %s
			";

		if( $orderby ){
			foreach( $orderby as $ord ){
				$query .= 'ORDER BY meta_value ' . $ord[1];
			}
		}

		$query = $wpdb->prepare( $query, $field, $this->post_type );
		$return = $wpdb->get_col( $query );

		return $return;
	}

	public function fetch( $fields = '*', $wheres = array(), $limit = NULL, $orderby = NULL, $distinct = FALSE )
	{
		if( $distinct ){
			return $this->_fetch_distinct( $fields, $wheres, $limit, $orderby );
		}

		$return = array();

	// check if we have any post_ parts
		$post_wheres = array();
		reset( $wheres );
		foreach( $wheres as $k => $w ){
			if( substr($k, 0, strlen('post_')) == 'post_' ){
				$post_wheres[$k] = $w;
			}
		}

		$args = $this->_prepare_args( $wheres );

		if( isset($args['where_join']) ){
			$this->where_join = $args['where_join'];
			unset( $args['where_join'] );
		}

		if( $orderby ){
			foreach( $orderby as $ord ){
				$args['orderby'] = $ord[0];
				$args['order'] = $ord[1];
			}
		}

		if( $limit ){
			if( count($limit) > 1 ){
				$args['posts_per_page'] = $limit[0];
				$args['offset'] = $limit[1];
			}
			else {
				$args['posts_per_page'] = $limit[0];
			}
		}
		else {
			$args['nopaging'] = TRUE;
		}

		// $posts = get_posts( $args );

		if( $this->join ){
			add_filter( 'posts_fields', array($this, 'posts_fields'), 10, 2 );
			add_filter( 'posts_join', array($this, 'posts_join'), 10, 2);
			add_filter( 'posts_where', array($this, 'posts_where'), 10, 2);
		}

		$query = new WP_Query( $args );
		// echo $query->request;

		if( $this->join ){
			remove_filter( 'posts_fields', array($this, 'posts_fields') );
			remove_filter( 'posts_join', array($this, 'posts_join') );
			remove_filter( 'posts_where', array($this, 'posts_where'));
		}
		$this->where_join = array();

		if( $query->have_posts() ){
			while( $query->have_posts() ){
				$post = $query->next_post();
				$post = $post->to_array();
				$post['id'] = $post['ID'];

				$skip_this = FALSE;
				if( $post_wheres ){
					reset( $post_wheres );
					foreach( $post_wheres as $k => $conds ){
						if( ! isset($post[$k]) ){
							$skip_this = TRUE;
							break;
						}

						foreach( $conds as $cond ){
							$compare = $cond[0];
							$value = $cond[1];

							switch( $compare ){
								case '=':
									if( $post[$k] != $value ){
										// echo "POST FAILED!";
										// _print_r( $post );
										$skip_this = TRUE;
									}
									break;
							}
							if( $skip_this ){
								break;
							}
						}
					}
				}

				if( $skip_this ){
					continue;
				}

				$meta = get_post_meta( $post['id'] );
				foreach( $meta as $k => $v ){
					if( substr($k, 0, 1) == '_' ){
						continue;
					}
					if( is_array($v) && count($v) == 1 ){
						$v = array_shift( $v );
					}
					$post[$k] = $v;
				}

				$return[ $post['id'] ] = $post;
			}
		}

		return $return;
	}

	public function count( $wheres = array() )
	{
		$return = 0;
		$args = $this->_prepare_args( $wheres );
		if( isset($args['where_join']) ){
			$this->where_join = $args['where_join'];
			unset( $args['where_join'] );
		}

		if( $this->join ){
			add_filter( 'posts_fields', array($this, 'posts_fields'), 10, 2 );
			add_filter( 'posts_join', array($this, 'posts_join'), 10, 2);
			add_filter( 'posts_where', array($this, 'posts_where'), 10, 2);
		}

		$query = new WP_Query( $args );

		if( $this->join ){
			remove_filter( 'posts_fields', array($this, 'posts_fields'), 10, 2 );
			remove_filter( 'posts_join', array($this, 'posts_join') );
			remove_filter( 'posts_where', array($this, 'posts_where'));
		}
		$this->where_join = array();

		$return = $query->found_posts;
		return $return;
	}

	public function delete_all()
	{
		global $wpdb;

		$return = TRUE;

		$table_posts = $wpdb->posts;
		$table_postmeta = $wpdb->postmeta;
		$table_term_relationships = $wpdb->term_relationships;
		$post_type = $this->post_type;

		$tables = array('a', 'b', 'c');
		if( 0 && $this->join ){
			reset( $this->join );
			foreach( $this->join as $jn ){
				$table = $this->db->protect_identifiers( $jn['table'], TRUE, FALSE );
				$tables[] = $table;
			}
		}
		$tables = join(', ', $tables);

		$sql = "
DELETE $tables
    FROM $table_posts a
LEFT JOIN $table_term_relationships b
	ON (a.ID = b.object_id)
LEFT JOIN $table_postmeta c
	ON (a.ID = c.post_id)
";

		if( 0 && $this->join ){
			reset( $this->join );
			foreach( $this->join as $jn ){
				$table = $this->db->protect_identifiers( $jn['table'], TRUE, FALSE );
				$sql .= "
LEFT JOIN $table 
	ON (a.ID = $table." . $jn['their_column'] . ")
";
			}
		}

		$sql .= "
WHERE 
	a.post_type = '$post_type'
";

		$wpdb->query($sql);

		if( $this->join ){
			reset( $this->join );
			foreach( $this->join as $jn ){
				$table = $this->db->protect_identifiers( $jn['table'], TRUE, FALSE );
				$sql = "DELETE FROM $table";
				$wpdb->query($sql);
			}
		}

		return $return;
	}

	public function delete_joins( $post_id )
	{
		if( ! $this->join ){
			return;
		}

		global $wpdb;
		reset( $this->join );
		foreach( $this->join as $jn ){
			$table = $this->db->protect_identifiers( $jn['table'], TRUE, FALSE );
			$sql = "DELETE FROM $table WHERE " . $jn['their_column'] . '=' . $post_id;
			$wpdb->query($sql);
		}
	}

	public function delete( $wheres = array() )
	{
		$return = FALSE;

		$id = $this->id();
		if( ! $id ){
			return $return;
		}

		$return = wp_delete_post( $id, TRUE );
		return $return;
	}

	public function insert( $data, $post_id = NULL )
	{
		$core_data = array();
		$meta_data = array();

		if( ! $post_id ){
			$core_data = array();
			$new_status = isset($data['post_status']) ? $data['post_status'] : 'publish';
			$core_data['post_status'] = $new_status;
			$core_data['post_type'] = $this->post_type;

			// if( ! isset($data['post_name']) ){				
			// }

			reset( $data );
			foreach( $data as $k => $v ){
			// skip all starting with 'post_'
				if( substr($k, 0, strlen('post_')) != 'post_' ){
					continue;
				}
				$core_data[$k] = $data[$k];
			}
		}


		reset( $data );
		foreach( $data as $k => $v ){
		// skip all starting with 'post_'
			if( substr($k, 0, strlen('post_')) == 'post_' ){
				continue;
			}
			if( $k == 'id' ){
				continue;
			}
			$meta_data[ $k ] = $v;
		}

	// do insert
		$post_id = wp_insert_post( $core_data );

		if( ! $post_id ){
			return;
		}
		if( is_array($post_id) ){
			$post_id = array_shift( $post_id );
		}

		$tweak_insert = TRUE;
		// $tweak_insert = FALSE;

	// insert meta
		if( $tweak_insert ){
			global $wpdb;

			$table = $wpdb->postmeta;

			$params = array();
			$query = 'INSERT INTO ' . $table;
			$query .= ' (`post_id`, `meta_key`, `meta_value`)';
			$query .= ' VALUES ';
			
			$query_values = array();
			reset( $meta_data );
			foreach( $meta_data as $k => $v ){
				$query_values[] = '(' .  $post_id . ',' . "'" . '%s' . "'" . ','  . "'" . '%s' . "'" .  ')';
				$params[] = $k;
				$params[] = $v;
			}
			$query .= join(',', $query_values);

			$query = $wpdb->prepare( $query, $params );
			$wpdb->query( $query );
		}
		else {
			reset( $meta_data );
			foreach( $meta_data as $k => $v ){
				add_post_meta( $post_id, $k, $v );
			}
		}

		return $post_id;
	}

	public function update( $data, $wheres = array() )
	{
		$post_id = $this->_get_id( $wheres );
		if( ! $post_id ){
			return $return;
		}

		reset( $data );
		foreach( $data as $k => $v ){
			foreach( $post_id as $pid ){
				if( substr($k, 0, strlen('post_')) == 'post_' ){
					$my_post = array(
						'ID'	=> $pid,
						$k		=> $v,
						);
					wp_update_post( $my_post );
				}
				else {
					update_post_meta( $pid, $k, $v );
				}
			}
		}
	}
}
