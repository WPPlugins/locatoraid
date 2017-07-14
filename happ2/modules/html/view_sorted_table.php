<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
include_once( dirname(__FILE__) . '/view_table.php' );
class Html_View_Sorted_Table_HC_MVC extends Html_View_Table_HC_MVC
{
	private $default_sort = array();
	private $sort = array();

	public function set_sort( $sort )
	{
		$this->sort = $sort;
		return $this;
	}

	public function sort()
	{
		return $this->sort;
	}

	public function set_default_sort( $sort, $asc = 1 )
	{
		$this->default_sort = array( $sort, $asc );
		return $this;
	}

	public function render()
	{
	// prepare
		$header = $this->header();
		$rows = $this->rows();
		$sort_options = $this->sort();

		$uri = $this->make('/http/lib/uri');

		$sort = $uri->arg('sort');
		if( ! $sort ){
			$sort = $this->default_sort;
		}

		if( ! (is_array($sort) && (count($sort) == 2)) ){
			if( $sort_options ){
				reset( $sort_options );
				foreach( $sort_options as $k => $default_asc ){
					$sort = array($k, $default_asc);
					break;
				}
			}
		}
		if( ! $sort ){
			$sort = array('', '');
		}

		list( $sort_by, $sort_asc ) = $sort;

		$icon_sort_asc = $this->make('/html/view/icon')->icon('arrow-down');
		$icon_sort_desc = $this->make('/html/view/icon')->icon('arrow-up');

		$columns = array_keys($header);
		$table_header = array();
		reset( $header );

		foreach( $header as $k => $v ){
			$header_view = $this->make('view/container');

			$already_link = FALSE;
			if( is_object($v) && method_exists($v, 'slug') && ($v->slug() == '/html/view/link') ){
				$already_link = TRUE;
			}

			if( (count($rows) > 1) && isset($sort_options[$k]) ){
				$sort_param = array();
				$sort_param[] = $k;
				if( $k == $sort_by ){
					$sort_param[] = $sort_asc ? 0 : 1;
				}
				else {
					$sort_param[] = $sort_options[$k];
				}
				
				$sort_param = join('|', $sort_param);
				$params = array(
					'-sort' => $sort_param
					);
				$href = $uri->url('-', $params);

			// already link
				if( ! $already_link ){
					$v = $this->make('view/link')
						->add( $v )
						;
				}

				$v
					->to( $href )
					->always_show()
					;
			}
			else {
				if( $already_link ){
					$children = $v->children();
					$v = array_shift( $children );
				}
			}

			$header_view
				->add( $v )
				;

			if( (count($rows) > 1) && ($k == $sort_by) ){
				if( $sort_asc ){
					$header_view
						->add( $icon_sort_asc )
						;
				}
				else {
					$header_view
						->add( $icon_sort_desc )
						;
				}
			}

			$table_header[] = $header_view;
		}

	// sort rows
		if( $sort_by ){
			if( $sort_asc ){
				$sort_func = create_function('$a, $b', 'if(! array_key_exists("' . $sort_by . '", $b)){return 0;}; return ($b["' . $sort_by . '"] < $a["' . $sort_by . '"]);' );
			}
			else {
				$sort_func = create_function('$a, $b', 'if(! array_key_exists("' . $sort_by . '", $b)){return 0;}; return ($b["' . $sort_by . '"] > $a["' . $sort_by . '"]);' );
			}
			uasort( $rows, $sort_func );
		}

		$table_rows = array();

		foreach( $rows as $rid => $row ){
			$this_row = array();
			reset( $columns );
			foreach( $columns as $column ){
				if( isset($row[$column . '_view']) ){
					$this_cell = $row[$column . '_view'];
				}
				elseif( isset($row[$column]) ){
					$this_cell = $row[$column];
				}
				else {
					$this_cell = NULL;
				}
// $this_cell .= ' ['. $row[$column] . ']';
				$this_row[] = $this_cell;
			}

			$table_rows[] = $this_row;
		}

		$table_footer = array();
		$footer = $this->footer();

		if( $footer ){
			reset( $header );
			foreach( $header as $k => $v ){
				$v = isset($footer[$k]) ? $footer[$k] : NULL;
				$table_footer[] = $v;
			}
		}

	// compile out
		$this
			->set_header( $table_header )
			->set_footer( $table_footer )
			->set_rows( $table_rows )
			;
		return parent::render();
	}
}