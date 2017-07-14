<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Link_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $ajax = FALSE;
	protected $admin = FALSE;
	private $to = '';
	private $to_params = NULL;
	protected $always_show = FALSE;
	protected $new_window = FALSE;
	protected $hidden = FALSE;

	protected $persist = TRUE;

	public function set_persist( $persist )
	{
		$this->persist = $persist;
		return $this;
	}

	// show regardless if the link is not allowed
	public function always_show( $always_show = TRUE )
	{
		$this->always_show = $always_show;
		return $this;
	}

	public function is_always_show()
	{
		return $this->always_show;
	}

	public function ajax()
	{
		$this->ajax = TRUE;
		return $this;
	}
	public function admin()
	{
		$this->admin = TRUE;
		return $this;
	}

	public function hide()
	{
		$this->hidden = TRUE;
		return $this;
	}

	public function new_window( $new_window = TRUE )
	{
		$this->new_window = $new_window;
		return $this;
	}

	public function set_to_params( $params = array() ){
		$this->to_params = $params;
		return $this;
	}

	public function add_to( $part )
	{
		if( substr($part, 0, 1) != '/' ){
			$part = '/' . $part;
		}
		$this->to .= $part;
		return $this;
	}

	public function to( $slug = '', $params = array() )
	{
		$args = func_get_args();
		$slug = array_shift($args);

		if( is_array($slug) ){
			$this->to = $slug;
			return $this;
		}

		if( substr($slug, 0, strlen('-referrer-')) == '-referrer-' ){
			$hash = '';
			if( strpos($slug, '#') !== FALSE ){
				list( $slug, $hash ) = explode( '#', $slug );
			}

			if( $_POST && isset($_POST['hc-referrer']) ){
				$slug = sanitize_text_field( $_POST['hc-referrer'] );
			}
			else {
				$slug = ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
			}

			if( $slug ){
				$uri = $this->make('/http/lib/uri');
				$parsed = $uri->parse_url( $slug );

				if( $parsed['slug'] ){
					$parsed_params = hc2_parse_args($parsed['params'], FALSE, FALSE);
					$params = array_merge( $parsed_params, $params );

					$this->to_params = $params;
					$this->to = $parsed['slug'];
				}
				else {
					$this->to = $slug;
				}
			}
			// $this->to = $slug;

			if( strlen($hash) ){
				$this->to .= '#' . $hash;
			}

			return $this;
		}

		if( HC_Lib2::is_full_url($slug) ){
			$this->to = $slug;
			return $this;
		}

		if( $slug == '-' ){
			$this->to = $slug;
			// $slug = array('-');
			// if( ! $params ){
				// $params = $uri->params();
			// }
			$this->to_params = $params;
			return $this;
		}
		elseif( substr($slug, 0, strlen('-/')) == '-/' ){
			$this->to = $slug;
			$this->to_params = $params;
			return $this;
		}
		elseif( substr($slug, 0, strlen('-.')) == '-.' ){
			$this->to = $slug;
			$this->to_params = $params;
			return $this;
		}

		$params = array();
		$uri = $this->make('/http/lib/uri');

		if( (! is_array($slug)) && (substr($slug, 0, 1) != '/') ){
			// append this module path
// echo "WAS '$slug', ";
			$slug = '/' . $this->module() . '/' . $slug;
			if( strpos($slug, '_') !== FALSE ){
				$slug = str_replace('_', '-', $slug);
			}
// echo "NOW '$slug', <br> ";
// echo '<br>';
			$slug = trim($slug, '/');
		}

		if( count($args) == 1 ){
			$params = array_shift($args);
			if( ! is_array($params) ){
				$params = array( $params );
			}
		}
		elseif( count($args) > 1 ){
			while( $param = array_shift($args) ){
				$params[] = $param;
			}
		}

		$this->to = $slug;
		$this->to_params = $params;
		return $this;
	}

	public function href( $relative = FALSE )
	{
		static $root_controller = NULL;
		$return = '';

		if( HC_Lib2::is_full_url($this->to) ){
			$return = $this->to;
			return $return;
		}

		$uri = $this->make('/http/lib/uri');
		if( $this->ajax ){
			$uri->ajax();
			$this->ajax = FALSE;
		}
		elseif( $this->admin ){
			$uri->admin();
			$this->admin = FALSE;
		}

		$uri->set_persist( $this->persist );
		if( $this->to == '/' ){
			$return = $uri->url('/');
		}
		else {
			if( $root_controller === NULL ){
				$root_controller = $this->make('/root/controller');
			}

			list( $slug, $params ) = $root_controller
				->run('link-check', $this->to, $this->to_params) 
				;
			if( $slug ){
				$return = $uri->url($slug, $params);
			}
		}

		if( $relative ){
			$pos1 = strpos($return, '?');
			if( $pos1 === FALSE ){
				$pos2 = strrpos($return, '/');
			}
			else {
				$pos2 = strrpos(substr($return, 0, $pos1), '/');
			}
			$return = substr($return, $pos2 + 1);
		}

		return $return;
	}

	public function content()
	{
		$return = $this->_prepare_children();
		return $return;
	}

	public function render()
	{
		$return = '';
		if( $this->hidden ){
			return $return;
		}

	// check if this link is allowed
		$href = $this->href();
		// $this->persist = TRUE;

		if( $href && $this->readonly ){
			$this
				->tag('span')
				;
			$return = parent::render();
			return $return;
		}

		if( $href ){
			$this->reset_attr('href');
			$this
				->tag('a')
				->add_attr('href', $href)
				;
			if( $this->new_window ){
				$this
					->add_attr('target', '_blank')
					;
			}
			$return = parent::render();
		}
	// not allowed 
		else {
			if( $this->is_always_show() ){
				$this
					->tag('span')
					;
				$return = parent::render();
			}
		}
		return $return;
	}
}