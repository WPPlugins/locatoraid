<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
interface _HC_ORM_Storable_Interface
{
	public function count( $wheres = array() );
	public function fetch( $fields = '*', $wheres = array(), $limit = NULL, $orderby = NULL, $distinct = FALSE );
	public function insert( $data );
	public function update( $data, $wheres = array() );
	public function delete_all();
	public function delete( $wheres = array() );
}

class _HC_ORM_Storable implements _HC_ORM_Storable_Interface
{
	protected $db;
	protected $table;
	protected $id_field = 'id';
	static $query_cache = array();
	protected $use_cache = TRUE;
	protected $search_in = array();
	protected $cast = array();
	protected $join = array();

	public function __construct( $db, $table, $id_field = 'id' )
	{
		$this->db = $db;
		$this->table = $table;
		$this->id_field = $id_field;
	}

	public function set_cast( $cast )
	{
		$this->cast = $cast;
		return $this;
	}

	public function set_join( $join )
	{
		$this->join = $join;
		return $this;
	}

	public function add_join( $join ){
		$this->join[] = $join;
		return $this;
	}

	public function set_search_in( $search_in )
	{
		$this->search_in = $search_in;
		return $this;
	}

	public function search_in()
	{
		return $this->search_in;
	}

	public function table()
	{
		return $this->table;
	}

	public function count(
		$wheres = array(),
		$having = array()
		)
	{
		if( isset($wheres['search']) ){
			$search_in = $this->search_in();

			if( $search_in ){
				$search_wheres = $wheres['search'];

				$this->db->group_start();
				foreach( $search_in as $sin ){
					reset( $search_wheres );
					foreach( $search_wheres as $where ){
						list( $how, $value, $escape ) = $where;
						$this->db->or_like($sin, $value);
					}
				}
				$this->db->group_end();
			}
			unset( $wheres['search'] );
		}

	// optional search - probably we can find but also add 1=1
		if( isset($wheres['osearch']) ){
			$search_in = $this->search_in();

			if( $search_in ){
				$search_wheres = $wheres['osearch'];

				$this->db->group_start();
				foreach( $search_in as $sin ){
					reset( $search_wheres );
					foreach( $search_wheres as $where ){
						list( $how, $value, $escape ) = $where;
						$this->db->or_like($sin, $value);
					}
				}
				$this->db->or_where('1', '1', FALSE);
				$this->db->group_end();
			}
			unset( $wheres['osearch'] );
		}

		foreach( $wheres as $key => $key_wheres ){
			foreach( $key_wheres as $where ){
				list( $how, $value, $escape ) = $where;
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
			}
		}

		foreach( $having as $key => $key_wheres ){
			foreach( $key_wheres as $where ){
				list( $how, $value, $escape ) = $where;
				$this->db->having($key . $how, $value, $escape);
			}
		}

		if( $this->join ){
			foreach( $this->join as $jn ){
				if( isset($jn['as']) ){
					$table1 = $jn['table'];
					$table2 = $jn['as'];
				}
				else {
					$table1 = $table2 = $jn['table'];
				}

				$this->db->join( 
					$table1,
					$this->table . '.' . $this->id_field . ' = ' . $table2 . '.' . $jn['their_column'],
					'LEFT'
					);
			}
		}

		$return = $this->db->count_all_results( $this->table );
		return $return;
	}

	public function fetch( 
		$fields = '*',
		$wheres = array(),
		$limit = NULL,
		$orderby = NULL,
		$distinct = FALSE,
		$having = array()
		)
	{
		$return = array();

		if( isset($wheres['search']) ){
			$search_in = $this->search_in();

			if( $search_in ){
				$search_wheres = $wheres['search'];

				$this->db->group_start();
				foreach( $search_in as $sin ){
					reset( $search_wheres );
					foreach( $search_wheres as $where ){
						list( $how, $value, $escape ) = $where;
						$this->db->or_like($sin, $value);
					}
				}
// _print_r( $search_wheres );
				// $this->db->or_where('1', '1', FALSE);
				$this->db->group_end();
			}
			unset( $wheres['search'] );
		}

	// optional search - probably we can find but also add 1=1
		if( isset($wheres['osearch']) ){
			$search_in = $this->search_in();

			if( $search_in ){
				$search_wheres = $wheres['osearch'];

				$this->db->group_start();
				foreach( $search_in as $sin ){
					reset( $search_wheres );
					foreach( $search_wheres as $where ){
						list( $how, $value, $escape ) = $where;
						$this->db->or_like($sin, $value);
					}
				}
				$this->db->or_where('1', '1', FALSE);
				$this->db->group_end();
			}
			unset( $wheres['osearch'] );
		}

		if( ! is_array($fields) && ($fields != '*') ){
			$fields = array($fields);
		}
		if( is_array($fields) && ( (! in_array($this->id_field, $fields)) && (! in_array('*', $fields))  ) && (! $distinct) ){
			$fields[] = $this->table . '.' . $this->id_field;
		}

		if( $distinct ){
			$this->db->distinct();
		}

		if( ! is_array($fields) ){
			$fields = array($fields);
		}
		for( $ii = 0; $ii < count($fields); $ii++ ){
			if( (strpos($fields[$ii], '.') === FALSE) ){
				$fields[$ii] = $this->table . '.' . $fields[$ii];
			}
		}

		// if( $fields == '*' ){
			// if( $this->join ){
				// $fields = $this->table . '.*';
				// foreach( $this->join as $jn ){
					// $more_fields = array();
					// if( isset($jn['fields']) ){
						// foreach( $jn['fields'] as $jf ){
							// $more_fields[] = $jn['table'] . '.' . $jf;
						// }
					// }
					// $fields .= ', ' . join(', ', $more_fields);
				// }
			// }
		// }

		if( $this->join ){
			foreach( $this->join as $jn ){
				$more_fields = array();
				if( isset($jn['fields']) ){
					foreach( $jn['fields'] as $jf ){
						$more_fields[] = $jn['table'] . '.' . $jf;
					}
				}
				$fields = array_merge( $fields, $more_fields);
			}
		}

		$this->db->select( $fields );

		if( $limit ){
			if( count($limit) > 1 ){
				$this->db->limit( $limit[0], $limit[1] );
			}
			else {
				$this->db->limit( $limit[0] );
			}
		}
		if( $orderby ){
			foreach( $orderby as $ord ){
				list( $order_field, $order_how ) = $ord;

				if( 
					$this->join && 
					(strpos($order_field, '.') === FALSE) && 
					(substr($order_field, 0, strlen('computed_')) != 'computed_')
					){
					$order_field = $this->table . '.' . $order_field;
				}

				$this->db->order_by( $order_field, $order_how );
			}
		}

		foreach( $wheres as $key => $key_wheres ){
			if( $this->join && (strpos($key, '.') === FALSE) ){
				$key = $this->table . '.' . $key;
			}

			foreach( $key_wheres as $where ){
				list( $how, $value, $escape ) = $where;
				if( $how == 'OR' ){
					$this->db->or_where($key, $value, $escape);
				}
				elseif( $how == 'IN' ){
					$this->db->where_in($key, $value);
				}
				elseif( $how == 'NOT IN' ){
					$this->db->where_not_in($key, $value);
				}
				elseif( $how == 'LIKE' ){
					$this->db->like($key, $value);
				}
				elseif( ($how == '=') && ($value == 'null') ){
					$this->db->where($key, NULL, $escape);
				}
				else {
					$how = ' ' . $how;
					$this->db->where($key . $how, $value, $escape);
				}
			}
		}

		foreach( $having as $key => $key_wheres ){
			foreach( $key_wheres as $where ){
				list( $how, $value, $escape ) = $where;
				$this->db->having($key . $how, $value, $escape);
			}
		}

		$run = TRUE;

		if( (! $distinct) && $this->join ){
			foreach( $this->join as $jn ){
				if( isset($jn['as']) ){
					$table1 = $jn['table'];
					$table2 = $jn['as'];
				}
				else {
					$table1 = $table2 = $jn['table'];
				}

				$this->db->join( 
					$table1,
					$this->table . '.' . $this->id_field . ' = ' . $table2 . '.' . $jn['their_column'],
					'LEFT'
					);
			}
		}

		$sql = $this->db->get_compiled_select( $this->table );
		if( $this->use_cache ){
			if( isset(self::$query_cache[$sql]) ){
				// echo "ON CACHE: '$sql'<br>";
				$return = self::$query_cache[$sql];
				$run = FALSE;
			}
		}

		if( $run ){
			// $q = $this->db->get( $this->table );
			$q = $this->db->query( $sql );

			if( $distinct ){
				foreach( $q->result_array() as $row ){
					$return[] = array_shift($row);
				}
			}
			else {
				foreach( $q->result_array() as $row ){
					$return[ $row[$this->id_field] ] = $row;
				}
			}
		}

		if( $this->use_cache && $run ){
			// echo "SET ON CACHE: '$sql'<br>";
			self::$query_cache[$sql] = $return;
		}
		return $return;
	}

	public function insert( $data )
	{
		$return = NULL;
		if( $this->db->insert( $this->table, $data ) ){
			$return = $this->db->insert_id();
		}
		return $return;
	}

	public function update( $data, $wheres = array() )
	{
		$return = FALSE;
		if( ! $wheres ){
			return $return;
		}

		foreach( $wheres as $key => $key_wheres ){
			foreach( $key_wheres as $where ){
				list( $how, $value, $escape ) = $where;
				$this->db->where($key . $how, $value, $escape);
			}
		}

		if(
			$this->db
				->update( $this->table, $data )
			){
				$return = TRUE;
			}
		else {
			$return = FALSE;
		}
		return $return;
	}

	public function delete_all()
	{
		// $wheres = array(
			// $this->id_field => array( 
				// array('>', 0, TRUE)
				// )
			// );
		// return $this->delete( $wheres );

		$tables = array( $this->table );
		if( $this->join ){
			foreach( $this->join as $jn ){
				$tables[] = $jn['table'];
			}
		}

		$return = TRUE;
		foreach( $tables as $table ){
			if(
				$this->db
					->where('1', '1')
					->delete( $table )
				){
				}
			else {
				$return = FALSE;
			}
		}
		return $return;
	}

	public function delete( $wheres = array() )
	{
		$return = FALSE;
		if( ! $wheres ){
			return $return;
		}

		foreach( $wheres as $key => $key_wheres ){
			foreach( $key_wheres as $where ){
				list( $how, $value, $escape ) = $where;
				$key = $this->table . '.' . $key;
				$this->db->where($key . $how, $value, $escape);
			}
		}

		if( $this->join ){
			foreach( $this->join as $jn ){
				$this->db->join( 
					$jn['table'],
					$this->table . '.' . $this->id_field . ' = ' . $jn['table'] . '.' . $jn['their_column'],
					'LEFT'
					);
			}
		}

		$sql = $this->db->get_compiled_delete( $this->table );
// echo "SQL = '$sql'";
// exit;

		$q = $this->db->query( $sql );
		$return = TRUE;
		return $return;


		if(
			$this->db
				->delete( $this->table )
			){
				$return = TRUE;
			}
		else {
			$return = FALSE;
		}

		return $return;
	}
}