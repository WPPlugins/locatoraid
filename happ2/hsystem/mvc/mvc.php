<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class _HC_MVC_Decorated
{
	protected $src = NULL;
	protected $app = NULL;

	public function __construct( $src, $app )
	{
		$this->src = $src;
		$this->app = $app;
	}

	public function __toString()
	{
		return '' . $this->render();
	}

	public function __call( $what, $args )
	{
		if( $this->src ){
			$return = call_user_func_array( 
				array($this->src, $what),
				$args
				);

			$route = $this->src->slug() . '@' . $what;
			$route = str_replace('-', '_', $route );

			$return = $this->app->extend_after( $return, $route, $args );

			if( $return === $this->src ){
				return $this;
			}
			else {
				return $return;
			}
		}
		else {
			$msg = "METHOD NOT DEFINED: '" . get_class($this->src) . '@' . $what . "'<br>";
			trigger_error($msg, E_USER_ERROR);
		}
	}
}

abstract class _HC_MVC
{
	public $app = NULL;
	public $slug = NULL;
	public $module = NULL;
	protected $temp = array();

	protected $decorated = NULL;

	public function set_decorated( $decorated )
	{
		$this->decorated = $decorated;
		return $this;
	}

	public function decorated()
	{
		return $this->decorated;
	}

	public function __toString()
	{
		return '' . $this->run('render');
	}

	public function set_temp( $k, $v )
	{
		$this->temp[$k] = $v;
		return $this;
	}

	public function get_temp( $k )
	{
		$return = isset($this->temp[$k]) ? $this->temp[$k] : NULL;
		return $return;
	}

	public function force_module( $module )
	{
		$this->module = $module;
		return $this;
	}

	public function force_slug( $slug )
	{
		$this->slug = $slug;
		return $this;
	}

	public function app_name()
	{
		return $this->app->app_name();
	}

	public function app_short_name()
	{
		return $this->app->app_short_name();
	}

	public function app_pages()
	{
		return $this->app->app_pages();
	}

	public function slug()
	{
		return $this->slug;
	}

	public function module()
	{
		if( $this->module ){
			$return = $this->module;
		}
		else {
			$slug = $this->slug;

			$slug = trim($slug, '/');
			$slug = explode('/', $slug);
			$return = array_shift( $slug );
		}
		return $return;
	}

	public function _const( $cname )
	{
		$name = get_class($this) . '::' . $cname;
		$return = constant($name);
		return $return;
	}

	public function make( $slug )
	{
		$args = func_get_args();
		$slug = $args[0];

		if( substr($slug, 0, 1) != '/' ){
			// append this module path
			$slug = '/' . $this->module() . '/' . $slug;
		}

		if( count($args) > 1 ){
			$args[0] = $slug;
			$return = call_user_func_array( array($this->app, 'make'), $args );
		}
		else {
			$return = $this->app->make( $slug );
		}

// sort of temporary
		if( $slug == '/html/view/link' ){
			$return->force_module( $this->module() );
		}

		return $return;
	}

	public function run()
	{
		$args = func_get_args();
		$method = array_shift( $args );
		return $this->app->run( $this, $method, $args );
	}

	public function __call( $what, $args )
	{
		if( $this->decorated ){
			$return = call_user_func_array( 
				array($this->decorated, $what),
				$args
				);
			if( $return === $this->decorated ){
				return $this;
			}
			else {
				return $return;
			}
		}
		else {
			$msg = "METHOD NOT DEFINED: '" . get_class($this) . '@' . $what . "'<br>";
			trigger_error($msg, E_USER_ERROR);
		}
	}
}