<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Table_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $striped = TRUE;

	protected $header = array();
	protected $rows = array();
	protected $footer = array();

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

	protected function _render_cell( $content, $tag = 'td' )
	{
		$out = $this->make('/html/view/element')->tag($tag);
		$out->add( $content );
		$out
			->add_attr('class', 'hc-p2')
			;
		return $out;
	}

	protected function _render_row()
	{
		$out = $this->make('/html/view/element')->tag('tr');
		$out->add_attr('class', 'hc-border-bottom');

		return $out;
	}

	protected function _render_tbody()
	{
		$out = $this->make('/html/view/element')->tag('tbody');
		return $out;
	}

	public function generate_row( $row )
	{
		$header = $this->header();
		$col_count = count($header);

		$tr = $this->_render_row();

		for( $ii = 0; $ii < $col_count; $ii++ ){
			$v = array_shift($row);
			$td = $this->_render_cell( $v );

			$tr->add( $td );
		}
		return $tr;
	}

	function render()
	{
		$header = $this->header();

		$col_count = count($header);
		$rows = $this->rows();

	// prerender
		foreach( $header as $k => $v ){
			$header[$k] = '' . $v;
		}

		foreach( $rows as $rid => $row ){
			foreach( $row as $k => $v ){
				$rows[$rid][$k] = '' . $v;
			}
		}

		$full_out = $this->make('/html/view/element')->tag('table');

		$out = $this->_render_tbody()
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$full_out->add_attr($k, $v);
		}

	// header
		$tr = $this->_render_row();

		foreach( $header as $k => $v ){
			$td = $this->_render_cell( $v, 'th' );

			$td
				->add_attr('class', 'hc-fs4')
				->add_attr('class', 'hc-fs4')
				->add_attr('class', 'hc-regular')
				;

			$tr->add( $td );
		}

		$tr
			->add_attr('class', 'hc-border-bottom')
			->add_attr('class', 'hc-border-gray')
			;

		$full_out->add( $tr );

	// rows
		$rri = 0;
		foreach( $rows as $rid => $row ){
			$rri++;
			$tr = $this->_render_row();

			if( $this->striped ){
				if( $rri % 2 ){
					$tr
						->add_attr('class', 'hc-bg-lightsilver')
						->add_attr('class', 'hc-border-bottom')
						;
				}
				else {
					$tr
						->add_attr('class', 'hc-bg-white')
						->add_attr('class', 'hc-border-bottom')
						;
				}
			}

			if( ! $header ){
				$col_count = count($row);
			}
			for( $ii = 0; $ii < $col_count; $ii++ ){
				$v = array_shift($row);
				$td = $this->_render_cell( $v );

				$tr->add( $td );
			}

			$out->add( $tr );
		}

	// additional
		$children = $this->children();
		foreach( $children as $child ){
			$out->add( $child );
		}

	// footer
		$footer = $this->footer();
		if( $footer ){
			$tr = $this->_render_row();

			reset( $footer );
			foreach( $footer as $k => $v ){
				$td = $this->_render_cell( $v, 'td' );

				$td
					->add_attr('class', 'hc-fs4')
					;
				$tr->add( $td );
			}

			$tr
				->add_attr('class', 'hc-border-top')
				->add_attr('class', 'hc-border-gray')
				;
			$out->add( $tr );
		}

		$full_out
			->add( $out )
			;

		$full_out
			->add_attr('style', 'border-collapse: collapse;')
			;

		return $full_out;
	}
}