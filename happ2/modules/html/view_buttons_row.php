<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Buttons_Row_HC_MVC extends Html_View_Container_HC_MVC
{
	public function render()
	{
		$items = $this->children();
		if( ! $items ){
			return;
		}

		$out = $this->make('/html/view/list-inline')
			->set_scale('sm')

			->add_attr('class', 'hc-mt2')
			->add_attr('class', 'hc-py1')
			// ->add_attr('class', 'hc-border-top')
			;

		foreach( $items as $k => $v ){
			$v
				->add_attr('class', 'hc-block-xs')
				->add_attr('class', 'hc-mb2-xs')
				->add_attr('class', 'hc-align-center')
				;
			$out->add( $k, $v );
		}

		$attr = $this->attr();

		foreach( $attr as $k => $v ){
			$out
				->add_attr( $k, $v )
				;
		}

		return $out;
	}
}