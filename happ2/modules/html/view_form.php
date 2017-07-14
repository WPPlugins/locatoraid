<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Form_HC_MVC extends Html_View_Container_HC_MVC
{
	private $method = 'post';
	private $form = NULL;
	private $route = NULL;
	protected $ajax = FALSE;

	// private $id = '';

	function __construct()
	{
		parent::__construct();
	}

	public function set_ajax( $ajax = TRUE )
	{
		$this->ajax = $ajax;
		return $this;
	}

	public function set_route( $route )
	{
		$this->route = $route;
		return $this;
	}

	public function set_form( $form )
	{
		$this->form = $form;
		return $this;
	}

	public function form()
	{
		return $this->form;
	}

	public function set_method( $method )
	{
		$this->method = $method;
		return $this;
	}
	public function method()
	{
		return $this->method;
	}

	function render()
	{
		$this_id = $this->attr('id');
		if( ! $this_id ){
			$this_id = 'nts_' . hc_random();
			$this->add_attr('id', $this_id);
		}

		// no form tag
		if( $this->ajax ){
			$action = $this->attr('action');

			$out = $this->make('view/element')->tag('div')
				->add_attr('id', $this_id)
				->add_attr('class', 'hcj2-observe' )
				->add_attr('class', 'hcj2-ajax-form' )
				->add_attr('data-action', $action )
				;
			// $out
				// ->add(
					// $this->make('/form/view/hidden')
						// ->set_name('route')
						// ->set_value($this->route)
					// )
				// ;
		}
		elseif( $this->route ){
			$out = $this->make('view/element')->tag('div')
				->add_attr('id', $this_id)
				->add_attr('class', 'hcj2-observe' )
				;
			$out
				->add(
					$this->make('/form/view/hidden')
						->set_name('route')
						->set_value($this->route)
					)
				;
		}
		else {
			$out = $this->make('view/element')->tag('form')
				->add_attr('method', $this->method())
				->add_attr('accept-charset', 'utf-8')
				->add_attr('id', $this_id)
				->add_attr('class', 'hcj2-observe' )
				;
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		if( $this->form ){
		// check if have file then multipart/form-data
			$inputs = $this->form->inputs();

			$need_multipart = array('/form/view/file');
			foreach( $inputs as $k => $f ){
				if( in_array($f->slug(), $need_multipart) ){
					$out
						->add_attr('enctype', 'multipart/form-data')
						;
					break;
				}
			}

			$orphan_errors = $this->form->orphan_errors();
			if( $orphan_errors ){
				$errors = $this->make('view/list');

				foreach( $orphan_errors as $k => $v ){
					$view = $v;
					if( $k != '_' ){
						$view = $k . ': ' . $view;
					}
					$errors->add($view);
				}

				$errors = $this->make('view/element')->tag('div')
					->add( $errors )
					->add_attr('class', 'hc-bg-lighten-4')
					->add_attr('class', 'hc-p2')
					;

				$errors = $this->make('view/element')->tag('div')
					->add( $errors )
					->add_attr('class', 'hc-bg-red')
					->add_attr('class', 'hc-mb3')
					->add_attr('class', 'hc-p0')
					->add_attr('class', 'hc-rounded')
					;
				$out->add( $errors );
			}
		}

		$out->add( parent::render() );
		return $out;
	}
}