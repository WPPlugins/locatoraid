<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_List_Inline_HC_MVC extends Html_View_Container_HC_MVC
{
	protected $scale = ''; // can be xs, sm, md, lg
	protected $gutter = 2; // from 0 to 3
	protected $separated = 0; // from 0 to 1

	function scale()
	{
		return $this->scale;
	}
	function set_scale( $scale )
	{
		$this->scale = $scale;
		return $this;
	}

	public function set_gutter( $gutter )
	{
		$this->gutter = $gutter;
		return $this;
	}
	public function gutter()
	{
		return $this->gutter;
	}

	public function set_separated( $separated = 1 )
	{
		$this->separated = $separated;
		return $this;
	}
	public function separated()
	{
		return $this->separated;
	}

	public function render()
	{
		$items = $this->children();
		if( ! $items ){
			return;
		}

		$scale = $this->scale();
		if( (! $scale) OR ($scale == 'xs') ){
			$scale = '';
		}
		else {
			$scale = '-' . $scale;
		}

		$gutter = $this->gutter();
		$separated = $this->separated();

		$render_items = array();
		$ii = 0;
		foreach( $items as $item ){
			$item2 = $this->make('view/element')->tag('div')
				->add($item)
				// ->add_attr('style', 'vertical-align: top;')
				->add_attr('style', 'vertical-align: middle;')
				->add_attr('class', 'hc-inline-block' . $scale)
				// ->add_attr('style', 'display:table-cell; vertical-align: middle;')
				;

			$margin = array();
			if( $separated ){
				$margin[] = 'b2-xs';
			}

			if( $ii ){
				if( $separated ){
					$item2
						->add_attr('class', 'hc-border-left' . $scale)
						;
				}
			}

			if( $gutter ){
				if( $ii < (count($items) - 1) ){
					$margin[] = 'r' . $gutter . $scale;
				}
			}

			if( $margin ){
				reset( $margin );
				foreach( $margin as $mrg ){
					$item2
						->add_attr('class', 'hc-m' . $mrg)
						;
				}
			}

			$render_items[] = $item2;
			$ii++;
		}

		$out = $this->make('view/element')->tag('div')
			// ->add_attr('style', 'display:table;')
			;
		$attr = $this->attr();

		foreach( $attr as $k => $v ){
			$out
				->add_attr( $k, $v )
				;
		}

		$out
			->add( parent::render($render_items) )
			;
		return $out;
	}
}