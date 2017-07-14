<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Layout_View_Body_HC_MVC extends _HC_MVC
{
	private $content = NULL;
	public function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	public function content()
	{
		return $this->content;
	}

	public function top_header()
	{
		$current_slug = $this->make('/http/lib/uri')->slug();

		$slug = explode('/', $current_slug);
		$module = array_shift($slug);
		if( in_array($module, array('setup')) ){
			return;
		}

		$return = $this->make('/html/view/element')->tag('div')
			->add_attr('class', 'print-hide')
			;

	// profile - blank so far
		$return->add( 'profile', NULL );

		return $return;
	}

	public function render()
	{
		$out = $this->make('/html/view/container');

		$nts = $this->make('/html/view/element')->tag('div')
			->add_attr('class', 'hc-container')
			;

		$top_header = $this->run('top-header');

		if( isset($brand) ){
			$top_header->add( $brand );
		}
		if( isset($header) ){
			$top_header->add( $header );
		}
		if( isset($header_ajax) ){
			$top_header->add( $header_ajax );
		}

		$content = '' . $this->content();

		$nts
			->add( 'top-header', $top_header )
			;

		if( 0 && defined('NTS_DEVELOPMENT2') ){
			$div_xs = $this->make('/html/view/element')->tag('div')
				->add_attr('class', 'hc-p2')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-border-red')
				->add_attr('class', 'hc-show-xs-only')
				->add( 'xs-only' )
				;

			$div_sm = $this->make('/html/view/element')->tag('div')
				->add_attr('class', 'hc-p2')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-border-red')
				->add_attr('class', 'hc-show-sm-only')
				->add( 'sm-only' )
				;

			$div_md = $this->make('/html/view/element')->tag('div')
				->add_attr('class', 'hc-p2')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-border-red')
				->add_attr('class', 'hc-show-md-only')
				->add( 'md-only' )
				;

			$div_lg = $this->make('/html/view/element')->tag('div')
				->add_attr('class', 'hc-p2')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-border-red')
				->add_attr('class', 'hc-show-lg-only')
				->add( 'lg-only' )
				;

			$nts
				->add( $div_xs )
				->add( $div_sm )
				->add( $div_md )
				->add( $div_lg )
				;
		}

		$nts
			->add( 'content', $content )
			;

		$nts = $this->make('/html/view/element')->tag('div')
			->add_attr('id', 'nts')
			->add_attr('class', 'wrap')
			->add( $nts )
			;

		$out->add( $nts );
		if( isset($js_footer) ){
			$out->add( $js_footer );
		}
		if( isset($theme_footer) ){
			$out->add( $theme_footer );
		}

		return $out;
	}
}