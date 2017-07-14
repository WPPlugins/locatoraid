<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class WordPress_Layout_Print_View_Head_HC_MVC extends _HC_MVC
{
	function render()
	{
		$assets = $this->make('/layout/view/assets');
		$css = $assets->run('css');

		$out = $this->make('/html/view/container');

		if( isset($page_title) ){
			$out->add(
				$this->make('/html/view/element')->tag('title')
					->add( $page_title )
				);
		}

		$out->add(
			$this->make('/html/view/element')->tag('meta')
				->add_attr('http-equiv',	'Content-Type')
				->add_attr('content',		'text/html; charset=UTF-8')
			);

		$out->add(
			$this->make('/html/view/element')->tag('meta')
				->add_attr('name',		'viewport')
				->add_attr('content',	'width=device-width, initial-scale=1.0')
			);

		$check = array('dashicons');
		$css_handles = array_keys($css);
		reset( $css );
		foreach( $css_handles as $handle ){
			if( ! in_array($handle, $check) ){
				continue;
			}

			$wp_styles = wp_styles();

			ob_start();
			$wp_styles->do_item($handle);
			$this_one = ob_get_contents();
			ob_end_clean();

			$out->add( $this_one );

			unset( $css[$handle] );
		}

		foreach( $css as $handle => $src ){
			$out->add(
				$this->make('/html/view/element')->tag('link')
					->add_attr('rel',	'stylesheet')
					->add_attr('type',	'text/css')
					->add_attr('id',	'hc-css-' . $handle)
					->add_attr('href',	$src)
				);
		}

		return $out;
	}
}