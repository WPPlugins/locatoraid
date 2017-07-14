<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Wordpress_Layout_Print_View_Full_HC_MVC extends _HC_MVC
{
	public function extend_body( $body )
	{
		$is_print_view = $this->make('/print/controller')->run('is-print-view');
		if( ! $is_print_view ){
			return $body;
		}

		$head = $this->make('view/head');

		$out = $this->make('/html/view/container');
		$out
			->add('<!DOCTYPE html>' . "\n" )
			->add(
				$this->make('/html/view/element')->tag('html')
					->add_attr('xmlns', 'http://www.w3.org/1999/xhtml')
					->add("\n")
					->add(
						$this->make('/html/view/element')->tag('head')
							->add( $head )
						)
					->add(
						$this->make('/html/view/element')->tag('body')
							->add( $body )
						)
				)
			;
		return $out;
	}
}