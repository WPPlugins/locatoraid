<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Table_Responsive_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $striped = TRUE;
	protected $scale = 'md';

	protected $header = array();
	protected $rows = array();
	protected $footer = array();

	private $default_sort = array();
	private $sort = array();

	protected $is_wp_admin = FALSE;
	protected $no_footer = FALSE;

	protected $cell_padding = 2;

	public function _init()
	{
		$this->is_wp_admin = ( defined('WPINC') && is_admin() ) ? TRUE : FALSE;
		return $this;
	}

	public function set_no_footer( $no_footer = TRUE )
	{
		$this->no_footer = $no_footer;
		return $this;
	}

	public function set_cell_padding( $cell_padding )
	{
		$this->cell_padding = $cell_padding;
		return $this;
	}

	public function scale()
	{
		return $this->scale;
	}
	public function set_scale( $scale )
	{
		$this->scale = $scale;
		return $this;
	}

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

	public function set_striped( $striped = TRUE )
	{
		$this->striped = $striped;
		return $this;
	}

	public function set_header( $header )
	{
		$this->header = $header;
		return $this;
	}
	public function header()
	{
		return $this->header;
	}
	public function set_footer( $footer )
	{
		$this->footer = $footer;
		return $this;
	}
	public function footer()
	{
		return $this->footer;
	}
	public function set_rows( $rows )
	{
		$this->rows = $rows;
		return $this;
	}
	public function rows()
	{
		return $this->rows;
	}

	public function widths( $col_count )
	{
		$counts = array(
			1	=> array(12),
			2	=> array(6, 6),
			3	=> array(4, 4, 4),
			4	=> array(3, 3, 3, 3),
			5	=> array(3, 3, 2, 2, 2),
			6	=> array(2, 2, 2, 2, 2, 2),
			);
		$return = isset($counts[$col_count]) ? $counts[$col_count] : array();
		return $return;
	}

	protected function _render_cell( $content, $header_label = NULL )
	{
		$scale = $this->scale();

		$cell_content = $this->make('/html/view/element')->tag('div');
		$cell_content
			->add( $content )
			;

		$padding_classes = array( 
			'hc-p' . $this->cell_padding,
			'hc-px' . $this->cell_padding . '-xs',
			'hc-py1-xs',
			);
		
		foreach( $padding_classes as $c ){
			$cell_content
				->add_attr('class', $c)
				;
		}

		if( ! strlen($header_label) ){
			return $cell_content;
		}

		$out = $this->make('/html/view/element')->tag('div')
			->add_attr('class', 'hc-py1-' . $scale)
			;

		$cell_header = $this->make('/html/view/element')->tag('div');
		$cell_header
			->add( $header_label )

			->add_attr('class', 'hc-fs1')
			->add_attr('class', 'hc-muted-2')
			->add_attr('class', 'hc-hide-' . $this->scale())

			->add_attr('class', 'hc-p1-xs')
			;

		$out
			->add( $cell_header )
			;
		if( strlen($content) ){
			$out
				->add( $cell_content )
				;
		}
		
		return $out;
	}

	protected function _render_row()
	{
		$out = $this->make('view/grid')
			->set_scale( $this->scale() )
			;

		// $out
			// ->add_attr('class', 'hc-mb1-xs')
			// ;

		return $out;
	}

	protected function _render_tbody()
	{
		$out = $this->make('view/element')->tag('tbody');
		return $out;
	}

	public function generate_row( $row )
	{
		$tr = $this->_render_row();

		$header = $this->header();
		$checkbox = FALSE;
		if( array_key_exists('checkbox', $header) ){
			$checkbox = TRUE;
			unset($header['checkbox']);
		}
		$widths = $this->widths( count($header) );

		$keys = array_keys($header);
		for( $ki = 0; $ki < count($keys); $ki++ ){
			$k = $keys[$ki];
			$w = isset($widths[$ki]) ? $widths[$ki] : 1;
			$v = isset($row[$k]) ? $row[$k] : NULL;
			$tr->add( $v, $w );
		}

		return $tr;
	}

	function render()
	{
		$header = $this->header();
		$checkbox = FALSE;
		if( array_key_exists('checkbox', $header) ){
			$checkbox = TRUE;
			unset($header['checkbox']);
		}

		$col_count = count($header);
		$rows = $this->rows();

	// prerender
		foreach( $header as $k => $v ){
			if( $v !== NULL ){
				$header[$k] = '' . $v;
			}
			else {
				$header[$k] = $v;
			}
		}

		foreach( $rows as $rid => $row ){
			foreach( $row as $k => $v ){
				if( is_array($v) ){
					$v = join('', $v);
				}
				$rows[$rid][$k] = '' . $v;
			}
		}

		$full_out = $this->make('view/element')->tag('div')
			// ->add_attr('class', 'hc-border')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$full_out->add_attr($k, $v);
		}

	// header
		$row_cells = array();

	// if all null then we don't need header
		$show_header = FALSE;
		reset( $header );
		foreach( $header as $k => $hv ){
			if( $hv !== NULL ){
				$show_header = TRUE;
				break;
			}
		}

		if( $show_header ){
			reset( $header );
			foreach( $header as $k => $hv ){
				$td = $this->_render_cell( $hv );
				$row_cells[$k] = $td;
			}

			$tr = $this->generate_row($row_cells);

			if( $checkbox ){
				$tr = $this->make('/html/view/element')->tag('div')
					->add(
						$this->make('/html/view/element')->tag('div')
							// ->add_attr('class', 'hc-border')
							->add_attr('class', 'hc-left')
							->add_attr('style', 'width: 2em;')
							->add( '&nbsp;' )
						)
					->add( 
						$tr 
							// ->add_attr('class', 'hc-border')
							->add_attr('style', 'margin-left: 2.5em;')
						)
					;
			}

			$tr
				->add_attr('class', 'hc-show-' . $this->scale())
				->add_attr('class', 'hc-fs4')
				->add_attr('style', 'line-height: 1.5em;')
				;

			if( $this->is_wp_admin ){
				$tr
					->add_attr('class', 'hc-bg-white')
					;
			}

			$header_row = clone $tr;

			$full_out->add(
				$tr
					->add_attr('class', 'hc-border-bottom')
				);
		}

	// rows
		$rri = 0;
		foreach( $rows as $rid => $row ){
			$rri++;

			$row_cells = array();
			reset( $header );
			$hii = 0;
			foreach( $header as $k => $hv ){
				$v = NULL;
				if( isset($row[$k . '_view']) ){
					$v = $row[$k . '_view'];
				}
				elseif( isset($row[$k]) ){
					$v = $row[$k];
				}

			// skip labels for certain cells
				// first one
				if( (! $hii) && in_array($k, array('title')) ){
					$hv = NULL;
				}

				if( 0 && in_array($k, array('id')) ){
					// $v = $hv . ': ' . $v;
					$hv = $hv . ': ' . $v;

					$v = $this->make('/html/view/element')->tag('span')
						->add( $v )
						->add_attr('class', 'hc-fs1')
						->add_attr('class', 'hc-muted-2')
						;
					// $hv = NULL;
					$v = NULL;
				}

				$td = $this->_render_cell( $v, $hv );
				$row_cells[$k] = $td;
				$hii++;
			}

			$tr = $this->generate_row($row_cells);

			if( $checkbox ){
				$this_checkbox_view = isset($row['checkbox']) ? $row['checkbox'] : NULL;

				$tr = $this->make('/html/view/element')->tag('div')
					->add_attr('class', 'hc-show-' . $this->scale())
					->add(
						$this->make('/html/view/element')->tag('div')
							// ->add_attr('class', 'hc-border')
							->add_attr('class', 'hc-left')
							->add_attr('style', 'width: 2em;')
							->add_attr('class', 'hc-align-center')
							->add_attr('class', 'hc-py2')
							->add( $this_checkbox_view )
						)
					->add( 
						$tr 
							// ->add_attr('class', 'hc-border')
							->add_attr('style', 'margin-left: 2.5em;')
						)
					;
			}

			if( $this->striped ){
				if( $this->is_wp_admin ){
					if( $rri % 2 ){
						$tr
							->add_attr('style', 'background-color: #f9f9f9;')
							;
					}
					else {
						$tr
							->add_attr('class', 'hc-bg-white')
							;
					}
				}
				else {
					if( $rri % 2 ){
						$tr
							->add_attr('class', 'hc-bg-darken-1')
							;
					}
					else {
						// $tr
							// ->add_attr('class', 'hc-bg-white')
							// ;
					}
				}
			}

			$full_out->add( $tr );
		}

	// copy from header
		if( $show_header ){
			if( ! $this->no_footer ){
				$footer_row = $header_row
					->add_attr('class', 'hc-border-top')
					;
				$full_out->add( $footer_row );
			}
		}

		// $full_out
			// ->add_attr('class', 'hc-border')
			// ;

		return $full_out;

	// additional
		$children = $this->children();
		foreach( $children as $child ){
			$out->add( $child );
		}

	// footer
		$footer = $this->footer();
		if( $footer ){
			$tr = $this->_render_row();

			$row_cells = array();
			reset( $footer );
			foreach( $footer as $k => $hv ){
				$td = $this->_render_cell( $hv );
				$td
					->add_attr('class', 'hc-fs4')
					->add_attr('class', 'hc-regular')
					;
				$row_cells[$k] = $td;
			}

			$tr = $this->generate_row($row_cells);
			$tr
				->add_attr('class', 'hc-border-top')
				->add_attr('class', 'hc-border-gray')
				;

			$full_out->add( $tr );
		}

		return $full_out;
	}
}