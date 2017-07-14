<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Ajax_Loaded_HC_MVC extends Html_View_Element_HC_MVC
{
	public function render( $ajax_url = NULL )
	{
		$out = $this->make('/html/view/container');

		$target = $this->make('/html/view/element')->tag('div')
			->add_attr('class', 'hcj2-ajax-container')
			->add_attr('style', 'position: relative;')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$target->add_attr( $k, $v );
		}

		if( ! isset($attr['data-src']) ){
			$target->add_attr( 'data-src', $ajax_url );
		}

		$children = $this->children();
		foreach( $children as $child ){
			$target
				->add( $child )
				;
		}

		if( $ajax_url ){
			$target_id = 'hc_' . HC_Lib2::generate_rand();
			$js = <<<EOT
<a id="$target_id"></a>

<script type="text/javascript">
	hc2_ajax_load( "$ajax_url", jQuery("#$target_id").closest('.hcj2-ajax-container') );
</script>

EOT;

			$target
				->add( $js )
				;
		}


		$out
			->add( $target )
			;

		return $out;
	}
}