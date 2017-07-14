<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_List_Div_HC_MVC extends Html_View_Container_HC_MVC
{
	protected $gutter = 0; // from 0 to 4

	public function set_gutter( $gutter )
	{
		$this->gutter = $gutter;
		return $this;
	}
	public function gutter()
	{
		return $this->gutter;
	}

	function render()
	{
		$gutter = $this->gutter();

		$args = func_get_args();
		if( count($args) ){
			$items = array_shift($args);
		}
		else {
			$items = $this->children();
		}

		$out = $this->make('view/element')->tag('div')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out
				->add_attr( $k, $v )
				;
		}

		$ii = 0;
		foreach( $items as $key => $item ){
			$li = $this->make('view/element')->tag('div')
				;

			$classes = array();
			if( $gutter ){
				if( $ii < (count($items) - 1) ){
					$classes[] = 'hc-mb' . $gutter;
				}
			}
			else {
				// $classes[] = 'hc-m0';
			}

			reset( $classes );
			foreach( $classes as $cl ){
				$li
					->add_attr('class', $cl)
					;
			}

			$li->add( $item );
			$out->add( $li );

			$ii++;
		}

		return $out;
	}
}