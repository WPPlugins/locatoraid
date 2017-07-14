<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Layout_View_Content_Header_Menubar_HC_MVC extends _HC_MVC
{
	private $content = NULL;
	private $header = NULL;
	private $menubar = NULL;

	public function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	public function content()
	{
		return $this->content;
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
	public function set_menubar( $menubar )
	{
		$this->menubar = $menubar;
		return $this;
	}
	public function menubar()
	{
		return $this->menubar;
	}

	public function render()
	{
		$header = (string) $this->header();

		$menubar = $this->menubar();

		$submenu = FALSE;

		if( is_object($menubar) && method_exists($menubar, 'children') && ($menubar_items = $menubar->children()) ){
			$submenu = TRUE;
		}

		if( is_object($menubar) && method_exists($menubar, 'render') ){
			$menubar = $menubar->run('render');
			// _print_r( $menubar );
			// echo 'children = ' . count($menubar->children());
			
			if( is_object($menubar) && method_exists($menubar, 'children') && ($menubar_items = $menubar->children()) ){
				$submenu = TRUE;
			}
		}

		if( strlen($header) ){
			$header_responsive = NULL;

			if( $submenu ){
				$submenu_content_responsive = $this->make('/html/view/list')
					// ->add_attr('class', 'hc-align-right')
					;
				foreach( $menubar_items as $k => $v ){
					if( is_object($v) ){
						if( method_exists($v, 'add_attr') ){
							$v
								// ->add_attr('class', 'hc-theme-block-link')
								// ->add_attr('class', 'hc-mb1')

								->add_attr('class', 'hc-theme-btn-submit')
								->add_attr('class', 'hc-theme-btn-secondary')

							// imitate wordpress
								->add_attr('class', 'page-title-action')
								// ->add_attr('class', 'hc-m0')
								->add_attr('style', 'margin: 0 0 0 0;')
								->add_attr('class', 'hc-mb1')
								->add_attr('class', 'hc-block')
								;
						}
						if( method_exists($v, 'admin') ){
							$v
								->admin()
								;
						}
					}
					$submenu_content_responsive->add( $k, $v );
				}

				$submenu_fullscreen = $this->make('/html/view/list-inline')
					->set_gutter(2)
					->add_attr('class', 'hc-show-md')
					->add_attr('class', 'hc-mt2')
					;
				reset( $menubar_items );
				foreach( $menubar_items as $k => $v ){
					$submenu_fullscreen->add( $k, $v );
				}

				$submenu_responsive = $this->make('/html/view/collapse')
					->add_attr('class', 'hc-btn')
					->set_title( $header )
					->set_content( $submenu_content_responsive )
					->set_caret( 'menu' )
					;

				$header_responsive = $submenu_responsive->render_trigger()
					->add_attr('class', 'hc-btn')
					->add_attr('class', 'hc-block')
					;
				$header_responsive = $this->make('/html/view/element')->tag('h1')
					->add( $header_responsive )
					->add_attr('style', 'line-height: 1.5em;')
					->add_attr('class', 'hc-py2')
					->add_attr('class', 'hc-hide-md')
					->add_attr('class', 'hc-nowrap')
					;
			}

			$header = $this->make('/html/view/element')->tag('h1')
				->add( $header )
				// ->add_attr('class', 'hc-py1')
				// ->add_attr('class', 'hc-border')
				->add_attr('style', 'padding: 0 0 0 0;')
				;

			if( $submenu ){
				if( count($menubar_items) < 4 ){
					$header = $this->make('/html/view/list-inline')
						->set_gutter(3)
						->add('content', $header)
						->add('menu', $submenu_fullscreen)
						->add_attr('class', 'hc-show-md')
						;
				}
				else {
					$header = $this->make('/html/view/list')
						->set_gutter(1)
						->add('content', $header)
						->add('menu', $submenu_fullscreen)
						->add_attr('class', 'hc-show-md')
						;
				}
			}

			$header = $this->make('/html/view/element')->tag('div')
				->add( $header )
				// ->add_attr('class', 'hc-mo')
				->add_attr('class', 'hc-mt1')
				->add_attr('class', 'hc-pb1')  
				// ->add_attr('class', 'hc-border-bottom')
				;

			if( $header_responsive ){
				$header
					->add( $header_responsive )
					;
			}

			if( $submenu ){
				$submenu_content_responsive_view = $submenu_responsive->render_content()
					;

				$header
					->add( $submenu_content_responsive_view )
					// ->add( $submenu_fullscreen )
					;

				// $submenu_view = $this->make('/html/view/grid')
					// ->add('content', $submenu_content, 6)
					// ->add('menu', $submenu_fullscreen, 6)
					// ;

				// $header
					// ->add( $submenu_view )
					// ;
			}
		}

		$content = $this->make('/html/view/element')->tag('div')
			->add( $this->content() )
			->add_attr('class', 'hc-py2')
			;

		$out = $this->make('/html/view/container');
		if( $header ){
			$out->add( 'header', $header );
		}
		// if( $menubar ){
			// $out->add( 'menubar', $menubar );
		// }

		$out->add( 'content', $content );
		return $out;
	}
}