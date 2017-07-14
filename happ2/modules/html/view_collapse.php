<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Collapse_HC_MVC extends Html_View_Container_HC_MVC
{
	private $title = '';
	private $more_title = '';
	private $content = '';
	private $default_in = FALSE;
	private $self_hide = FALSE;
	private $panel = NULL;
	protected $no_caret = TRUE;
	protected $caret_type = 'arrow-down';

	function __construct()
	{
		parent::__construct();
		$this->id = 'nts_' . HC_Lib2::generate_rand();
	}

	public function add_more_title( $more )
	{
		$this->more_title[] = $more;
		return $this;
	}
	public function more_title()
	{
		return $this->more_title;
	}

	public function set_title( $title )
	{
		$this->title = $title;
		return $this;
	}
	public function title()
	{
		return $this->title;
	}

	public function set_caret( $caret = 'arrow-down' )
	{
		$this->set_no_caret( FALSE );
		$this->caret_type = $caret;
		return $this;
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

	function set_panel( $panel = TRUE )
	{
		$this->panel = $panel;
		return $this;
	}
	public function panel()
	{
		return $this->panel;
	}

	public function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	public function content()
	{
		return $this->content;
	}

	public function set_default_in( $default_in = TRUE )
	{
		$this->default_in = $default_in;
		return $this;
	}
	public function default_in()
	{
		return $this->default_in;
	}

	public function set_self_hide( $self_hide = TRUE )
	{
		$this->self_hide = $self_hide;
		return $this;
	}
	public function self_hide()
	{
		return $this->self_hide;
	}

	public function render_content()
	{
		$return = $this->make('view/element')->tag('div')
			->add_attr('class', 'hcj2-collapse')
			->add_attr('id', $this->id)
			;
		$return->add( $this->content() );
		return $return;
	}

	public function render_trigger( $self = FALSE )
	{
		$panel = $this->panel();

	/* build trigger */
		$title = $this->title();
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

			$full_title = $this->make('/html/view/element')->tag('span')
				->add_attr('class', 'hc-inline-block')
				->add( $full_title )
				;

			$trigger = $this->make('view/element')->tag('a')
				->add( $full_title )
				->add_attr('title', $title)
				;
		}

		if( $self ){
			$trigger
				->add_attr('href', '#')
				->add_attr('class', 'hcj2-collapse-next')
				;
		}
		else {
			$trigger
				// ->add_attr('href', '#' . $this->id)
				->add_attr('data-target', $this->id)
				->add_attr('class', 'hcj2-collapser')
				;
		}

		$caret = NULL;
		if( ! $this->no_caret() ){
			if( $this->caret_type ){
				if( is_object($this->caret_type) ){
					$caret = $this->caret_type;
				}
				else {
					$caret = $this->make('/html/view/icon')->icon( $this->caret_type )
						;
				}
			}
			else {
				$caret = '&#9662;';  // caret
				// $caret = $this->make('/html/view/icon')->icon('arrow-down')
			}

			$caret = $this->make('/html/view/element')->tag('span')
				->add( $caret )
				->add_attr('class', 'hcj2-collapse-caret')
				;

			$caret
				->add_attr('class', 'hc-muted-1')
				->add_attr('class', 'hc-inline-block')
				->add_attr('class', 'hc-px1')
				// ->add_attr('style', 'border: blue 1px solid;')
				;

			if( $this->caret_type && (in_array($this->caret_type, array('menu'))) ){
				$caret
					->add_attr('class', 'hc-border')
					->add_attr('class', 'hc-rounded')
					;
			}

			$caret_left = TRUE;
			if( $caret_left ){
				$caret
					->add_attr('class', 'hc-left')
					;
				if( $this->caret_type && (in_array($this->caret_type, array('menu'))) ){
					$caret
						->add_attr('class', 'hc-mr2')
						;
				}
			}
			else {
				$caret
					->add_attr('class', 'hc-right')
					;
				if( $this->caret_type && (in_array($this->caret_type, array('menu'))) ){
					$caret
						->add_attr('class', 'hc-ml2')
						;
				}
			}

			if( $caret_left ){
				$trigger
					->prepend( $caret )
					;
			}
			else {
				$trigger
					->add( $caret )
					;
			}

			$trigger
				->add_attr('class', 'hc-decoration-none')
				->add_attr('class', 'hc-position-relative')
				;
		}

		$more_title = $this->more_title();

		if( $more_title ){
			// $trigger = $this->make('view/list-inline')
			$trigger = $this->make('view/container')
				->add( 'trigger', $trigger )
				;

			foreach( $more_title as $mt ){
				$trigger->add( $mt );
			}
		}

		return $trigger;
	}

	public function render()
	{
		$panel = $this->panel();

		$out = $this->make('view/element')->tag('div')
			->add_attr('class', 'hcj2-collapse-panel')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$trigger = $this->render_trigger('self');

		$self_hide = $this->self_hide();
		if( $self_hide ){
			$trigger->add_attr('class', 'hcj2-collapser-hide');
		}

		$wrap_trigger = $this->make('view/element')->tag('div')
			->add( $trigger )
			;
		if( $panel ){
			$out
				->add_attr('class', 'hc-py2')
				->add_attr('class', 'hc-border-bottom')
				;
		}

		$out->add(
			$wrap_trigger
			);

		$content = $this->make('view/element')->tag('div')
			->add_attr('class', 'hcj2-collapse')
			;

		if( $panel ){
			$content
				->add_attr('class', 'hcj2-panel-collapse')
				// ->add_attr('class', 'hc-border-top');
				;
			$content
				->add_attr('class', 'hc-ml2')
				->add_attr('class', 'hc-mt1')
				// ->add_attr('class', 'hc-border-top');
				;
		}

		if( $this->default_in() ){
			$content->add_attr('class', 'hcj2-open');
		}

		if( $panel ){
			$out
				// ->add_attr('class', 'hc-border')
				// ->add_attr('class', 'hc-rounded')
				;
		}

		if( $panel ){
			$content->add( 
				$this->make('view/element')->tag('div')
					->add( $this->content() )
					->add_attr('class', 'hc-p1')
				);
		}
		else {
			$content->add( $this->content() );
		}

		$out->add( $content );
		return $out;
	}
}