<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Top_Menu_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $current;

	public function single_instance()
	{
	}

	public function set_current( $current )
	{	
		$this->current = $current;
		return $this;
	}

	public function render_small()
	{
		$out = $this->make('view/element')->tag('div')
			->add_attr('class', 'hc-mb2')
			->add_attr('class', 'hc-rounded')
			;

		$out = $this->make('view/collapse')
			->add_attr('class', 'hc-mb2')
			->add_attr('class', 'hc-rounded')
			;

		$out
			->add_attr('class', 'hc-bg-darkgray')
			->add_attr('class', 'hc-silver')
			;

		$children = $this->run('children');
		$uri = $this->make('/http/lib/uri');

		$content = $this->make('/html/view/list')
			;

		foreach( $children as $child_o ){
			if( ! $child_o ){
				continue;
			}

			if( is_object($child_o) ){
				$child = clone $child_o;
			}
			else {
				$child = $child_o;
			}

			if( is_object($child) && method_exists($child, 'add_attr') ){
				$child
					->add_attr('class', 'hc-btn')
					->add_attr('class', 'hc-block')
					->add_attr('class', 'hc-px2')
					->add_attr('class', 'hc-py3')
					->add_attr('class', 'hc-border-bottom', 'hc-border-gray')
					;

				if( method_exists($child, 'href') ){
					$href = $child->href();
					$this_slug = $uri->get_slug_from_url( $href );
					$child->set_persist( FALSE );

				// active
					if( 
						( $this_slug == $this->current )
						OR
						(
							( substr($this->current, 0, strlen($this_slug)) == $this_slug ) &&
							( substr($this->current, strlen($this_slug), 1) == '/' )
						)
					){
						$child
							->add_attr('class', 'hc-bg-black')
							->add_attr('class', 'hc-silver')
							;
					}
				}
			}
			$content->add( 
				$child
				);
		}

		$title = $this->make('/html/view/element')->tag('a')
			->add_attr('class', 'hc-btn')
			->add_attr('class', 'hc-block')
			->add_attr('class', 'hc-px2')
			->add_attr('class', 'hc-py3')
			->add_attr('class', 'hc-align-center')
			->add_attr('class', 'hc-border-bottom', 'hc-border-gray')
			->add( HCM::__('Menu') )
			;

		$out
			->set_title( $title )
			->set_content( $content )
			;

		return $out;
	}

	public function render()
	{
		$out = $this->make('view/element')->tag('div')
			->add_attr('class', 'hc-mb2')
			->add_attr('class', 'hc-rounded')
			;

		$out
			->add_attr('class', 'hc-bg-darkgray')
			->add_attr('class', 'hc-silver')
			;

		$children = $this->run('children');

		$uri = $this->make('/http/lib/uri');

		foreach( $children as $child_o ){
			if( ! $child_o ){
				continue;
			}

			if( is_object($child_o) ){
				$child = clone $child_o;
			}
			else {
				$child = $child_o;
			}

			if( is_object($child) && method_exists($child, 'add_attr') ){
				$child
					->add_attr('class', 'hc-btn')
					->add_attr('class', 'hc-px2')
					->add_attr('class', 'hc-py3')
					->add_attr('class', 'hc-mr2')
					;

				if( method_exists($child, 'href') ){
					$href = $child->href();
					$this_slug = $uri->get_slug_from_url( $href );
					$child->set_persist( FALSE );

				// active
					if( 
						( $this_slug == $this->current )
						OR
						(
							( substr($this->current, 0, strlen($this_slug)) == $this_slug ) &&
							( substr($this->current, strlen($this_slug), 1) == '/' )
						)
					){
						$child
							->add_attr('class', 'hc-bg-black')
							->add_attr('class', 'hc-silver')
							;
					}
				}
			}

			$out->add( 
				$child
				);
		}

	// xs version
		$xs_out = $this->run('render-small');

		$out
			->add_attr('class', 'hc-show-md')
			;
		$xs_out
			->add_attr('class', 'hc-hide-md')
			;

		$out = $this->make('/html/view/container')
			->add( $out )
			->add( $xs_out )
			;

		return $out;
	}
}