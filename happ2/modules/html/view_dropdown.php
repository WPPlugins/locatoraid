<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
include_once( dirname(__FILE__) . '/view_list.php' );
class Html_View_Dropdown_HC_MVC extends Html_View_List_HC_MVC
{
	protected $title = NULL;
	protected $no_caret = FALSE;
	protected $wrap = TRUE;
	private $active = NULL;

	function set_active( $active )
	{
		$this->active = $active;
		return $this;
	}
	function active()
	{
		return $this->active;
	}

	function set_title( $title )
	{
		$this->title = $title;
		return $this;
	}
	function title()
	{
		return $this->title;
	}

	function set_no_caret( $no_caret = TRUE )
	{
		$this->no_caret = $no_caret;
		return $this;
	}
	function no_caret()
	{
		return $this->no_caret;
	}

	function set_wrap( $wrap = TRUE )
	{
		$this->wrap = $wrap;
		return $this;
	}
	function wrap()
	{
		return $this->wrap;
	}

	function render()
	{
		$out = array();

	/* build trigger */
		$title = $this->title();
		if( 
			( $active = $this->active() ) && $this->child($active)
			){
			$title = $this->child($active);
			$this->remove_child( $active );
		}
	
		if( 
			is_object($title) &&
			( $title->tag() == 'a' )
			){
			$trigger = $title;
		}
		else {
			$full_title = $title;
			$title = strip_tags($title);
			$title = trim($title);

			$trigger = $this->make('view/element')->tag('a')
				->add_attr('title', $title)
					->add( 
						$full_title
						)
				;
		}

		$trigger
			->add_attr('href', '#')
			->add_attr('class', 'hcj2-dropdown-toggle')
			;

		if( ! $this->no_caret() ){
			$trigger
				->add( $this->make('/html/view/icon')->icon('caret-down') )
				;
		}

		$out[] = $trigger;

		$this->add_attr('class', 'hcj2-dropdown-menu');
		$out[] = parent::render();

		$return = '';
		foreach( $out as $o ){
			$return .= $o;
		}

		if( $this->wrap() ){
			$wrap = $this->make('view/element')->tag('div')
				->add_attr('class', 'hcj2-dropdown')
				->add( $return )
				;
			$return = $wrap;
		}
		return $return;
	}
}