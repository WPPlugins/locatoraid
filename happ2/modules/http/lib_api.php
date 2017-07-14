<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
// this is a class to call our rest api internally, as if it's done through http
class Http_Lib_Api_HC_MVC extends _HC_MVC
{
	private $route = NULL;
	private $body = NULL;
	private $status_code = NULL;
	private $params = array();

	public function request( $route )
	{
		$this->route = $route;
		return $this;
	}

	public function add_param()
	{
		$ps = func_get_args();

		$p = array_shift($ps);
		$this->params[] = $p;

		if( count($ps) ){
			if( count($ps) > 1 ){
				$p = array();
				while( $this_p = array_shift($ps) ){
					$p = array_merge( $p, $this_p );
				}
				$this->params[] = $p;
			}
			else {
				$p = array_shift($ps);
				$this->params[] = $p;
			}
		}

		// foreach( $ps as $p ){
			// $this->params[] = $p;
		// }
		return $this;
	}

	public function get( $params = NULL )
	{
		$use_cache = 1;
		static $cache = array();

		$params = $this->params;

		list( $controller, $method ) = $this->app->route( $this->route );
		$method = 'get';

		$args = array();
		if( $params ){
			$args = $params;
		}

		if( $use_cache ){
			$cache_key = $this->url();
		}

		if( (! $use_cache) OR (! isset($cache[$cache_key])) ){
			$mvc = $this->make( $controller );
// echo "<br>CONTROLLER " . get_class($mvc);
// echo "<br>METHOD " . $method;

			$out = $this->app->run( $mvc, $method, $args );
			$this->status_code = $out->status_code();

			if( substr($this->status_code, 0, 1) != '4' ){
				$body = $out->view();
				$body = json_decode( $body, TRUE );
				$this->body = $body;
			}
			else {
				if( $this->status_code == '404' ){
					$return = $this->make('/html/view/404');
					echo $return;
					exit;
				}
				if( $this->status_code == '401' ){
					$return = $this->make('/html/view/401');
					$body = $out->view();
					$body = json_decode( $body, TRUE );
					echo $return
						->run('render', $body)
						;
					echo $return;
					exit;
				}
			}

			if( $use_cache ){
				$cache[$cache_key] = array(
					$this->status_code,
					$this->body
					);
			}

// static $cache_noncount = 0;
// $cache_noncount++;
// echo "NO CACHE: $cache_noncount<br>";
		}
		else {
			$this->status_code = $cache[$cache_key][0];
			$this->body = $cache[$cache_key][1];
// static $cache_count = 0;
// $cache_count++;
// echo "ONNN CACHE: $cache_count<br>";
		}

		$this->params = array();
		return $this;
	}

// add
	public function post( $input = NULL )
	{
		if( $input !== NULL ){
			$input = json_encode( $input );
		}

		list( $controller, $method ) = $this->app->route( $this->route );
		$method = 'post';

		$args = array();
		if( $input ){
			$args[] = $input;
		}

		$mvc = $this->make( $controller );
		$out = $this->app->run( $mvc, $method, $args );

		$this->status_code = $out->status_code();
		$body = $out->view();

		if( $this->status_code == '404' ){
			$return = $this->make('/html/view/404');
			echo $return;
			exit;
		}
		if( $this->status_code == '401' ){
			$return = $this->make('/html/view/401');
			$body = $out->view();
			$body = json_decode( $body, TRUE );
			echo $return
				->run('render', $body)
				;
			exit;
		}

		$body = json_decode( $body, TRUE );
		$this->body = $body;

		return $this;
	}

// update
	public function put( $params = NULL, $input = NULL )
	{
		list( $controller, $method ) = $this->app->route( $this->route );
		$method = 'put';

		$args = array();
		$args[] = $params;
		if( $input !== NULL ){
			$input = json_encode( $input );
		}
		$args[] = $input;

		$mvc = $this->make( $controller );
		$out = $this->app->run( $mvc, $method, $args );

		$this->status_code = $out->status_code();

		if( $this->status_code == '404' ){
			$return = $this->make('/html/view/404');
			echo $return;
			exit;
		}
		if( $this->status_code == '401' ){
			$return = $this->make('/html/view/401');
			$body = $out->view();
			$body = json_decode( $body, TRUE );
			echo $return
				->run('render', $body)
				;
			exit;
		}

		$body = $out->view();
		$body = json_decode( $body, TRUE );
		$this->body = $body;

		return $this;
	}

	public function delete( $params = NULL )
	{
		list( $controller, $method ) = $this->app->route( $this->route );
		$method = 'delete';

		$args = array();
		if( $params ){
			$args[] = $params;
		}
		$mvc = $this->make( $controller );
		$out = $this->app->run( $mvc, $method, $args );

		$this->status_code = $out->status_code();
		$body = $out->view();
		if( $body ){
			$body = json_decode( $body, TRUE );
			$this->body = $body;
		}

		return $this;
	}

	public function response()
	{
		return $this->body;
	}

	public function response_code()
	{
		return $this->status_code;
	}

	public function url()
	{
		$uri = $this->make('/http/lib/uri');
		$params = $this->params;
		$return = $uri->url( $this->route, $params );

		return $return;
	}
}