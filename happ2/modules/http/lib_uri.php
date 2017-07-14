<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Http_Lib_Uri_HC_MVC extends _HC_MVC
{
	protected $hca = 'hca';
	protected $hcs = 'hcs';

	protected $ajax_url = NULL;
	protected $ajax = FALSE;

	protected $admin_url = NULL;
	protected $admin = FALSE;

	private $base_url = '';
	private $base_params = array();
	private $params = array();
	private $url_slug = '';
	private $args = NULL;

	protected $persist = TRUE;
	
	public function single_instance()
	{
	}

	public function set_persist( $persist )
	{
		$this->persist = $persist;
		return $this;
	}

	public function hca_param()
	{
		return $this->hca;
	}

	public function __construct()
	{
		$this->set_current( $this->current() );
	}

	public function set_ajax_url( $ajax_url )
	{
		$this->ajax_url = $ajax_url;
		return $this;
	}
	public function ajax()
	{
		$this->ajax = TRUE;
		return $this;
	}

	public function set_admin_url( $admin_url )
	{
		$this->admin_url = $admin_url;
		return $this;
	}
	public function admin()
	{
		$this->admin = TRUE;
		return $this;
	}

	public function slug()
	{
		return $this->url_slug;
	}
	public function force_slug( $slug )
	{
		$this->url_slug = $slug;
		return $this;
	}

	public function params()
	{
		return $this->params;
	}

	public function args()
	{
		if( $this->args === NULL ){
			$params = $this->params();
			$this->args = hc2_parse_args( $params );
		}
		return $this->args;
	}

	public function arg($k, $array = FALSE )
	{
		$args = $this->args();
		$return = array_key_exists($k, $args) ? $args[$k] : NULL;
		if( strpos($return, '|') !== FALSE ){
			$return = explode('|', $return);
		}
		elseif( $array ){
			$return = array( $return );
		}
		return $return;
	}

	public function __toString()
	{
		return $this->url();
	}

	public function base_url()
	{
		return $this->base_url;
	}

	public function set_base_url( $base_url )
	{
		$this->base_url = $base_url;
		return $this;
	}

	public function get_slug_and_params( $string )
	{
// echo "GET SLUG AND PARAMS FROM '$string'<br>";
		$slug = array();
		$params = array();

	// trim slashes
		$string = trim($string, '/');

		$slug = $string;
		$full_slug = $slug;
		$params = array();

		if( strpos($string, ':') !== FALSE ){
			list( $slug, $params ) = explode(':', $string, 2);
			$params = explode('/', $params);
		}

		$return = array( $slug, $params );
		return $return;
	}

	public function get_slug_from_url( $url )
	{
		$slug = NULL;

		$purl = parse_url( $url );
		if( isset($purl['query']) && $purl['query']){
			parse_str($purl['query'], $base_params);
			if( isset($base_params[$this->hca]) ){
				$hca = $base_params[$this->hca];
				list( $slug, $params ) = $this->get_slug_and_params( $hca );
			}
		}
		return $slug;
	}

	public function parse_url( $url )
	{
		$return = array(
			'base_url'		=> '',
			'slug'			=> '',
			'params'		=> array(),
			'base_params'	=> array(),
			);

		$purl = parse_url( $url );
		$return['base_url'] = $purl['scheme'] . '://'. $purl['host'] . $purl['path'];

		if( isset($purl['query']) && $purl['query']){
			parse_str($purl['query'], $base_params);
		/* grab our hca */
			if( isset($base_params[$this->hca]) ){
				$hca = $base_params[$this->hca];

				list( $slug, $params ) = $this->get_slug_and_params( $hca );
				$return['slug'] = $slug;
				$return['params'] = $params;
				// unset( $base_params[$this->hca] ); // ?
			}

		/* store base params */
			$return['base_params'] = $base_params;
		}

		return $return;
	}

	public function set_current( $url )
	{
		$parsed = $this->parse_url( $url );
		$this->base_url		= $parsed['base_url'];
		$this->url_slug		= $parsed['slug'];
		$this->params		= $parsed['params'];
		$this->base_params	= $parsed['base_params'];
		return $this;

		$purl = parse_url( $url );
		$this->base_url = $purl['scheme'] . '://'. $purl['host'] . $purl['path'];

		if( isset($purl['query']) && $purl['query']){
			parse_str($purl['query'], $base_params);

		/* grab our hca */
			if( isset($base_params[$this->hca]) ){
				$hca = $base_params[$this->hca];

				list( $slug, $params ) = $this->get_slug_and_params( $hca );
				$this->url_slug = $slug;
				$this->params = $params;
			}

		/* store base params */
			$this->base_params = $base_params;
		}
	}

	public function url()
	{
		$args = func_get_args();
		$slug = array_shift($args);
		if( $slug == '-' ){
			$slug = $this->slug();
		}
		elseif( substr($slug, 0, strlen('-/')) == '-/' ){
			$slug = $this->slug() . '/' . substr($slug, strlen('-/'));
		}
		elseif( substr($slug, 0, strlen('-.')) == '-.' ){
			$slug = $this->slug() . '.' . substr($slug, strlen('-.'));
		}

		if( HC_Lib2::is_full_url($slug) ){
			$return = $slug;
			$this->persist = TRUE;
			return $return;
		}

		$current_slug = $this->slug();
		$current_slug_parts = explode('/', $current_slug);
		$current_module = $current_slug_parts[0];
		if( count($current_slug_parts) > 1 ){
			$current_controller = join('/', array_slice($current_slug_parts, 0, 2));
		}
		else {
			$current_controller = $current_module . '/index';
		}
		$current_params = $this->params();
		$current_params = hc2_parse_args($current_params, FALSE, FALSE);

		$params = array();
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

		$hca = array();
		if( $slug ){
			$hca[] = $slug;
		}

		$final_params = array();

	// persist all params within current module
		if( $this->persist ){
			$this_slug_parts = explode('/', $slug);
			$do_persist = FALSE;

			if( count($current_slug_parts) >= 2 ){
				if( ($current_slug_parts[0] == $this_slug_parts[0]) && isset($this_slug_parts[1]) && ($current_slug_parts[1] == $this_slug_parts[1]) ){
					$do_persist = TRUE;
				}
			}
			else {
				if( $this_slug_parts[0] == $current_slug_parts[0] ){
					$do_persist = TRUE;
				}
				
			}

			if( $do_persist ){
				reset($current_params);
				foreach( $current_params as $k => $v ){
					$final_params[$k] = $v;
				}
			}
		}

	// persistent params
	// starting with '-' within the same module
	// starting with '--' within the same module
	// starting with '---' for all
		if( 0 && $this->persist ){
			$this_slug_parts = explode('/', $slug);
			$this_module = $this_slug_parts[0];
			if( count($this_slug_parts) > 1 ){
				$this_controller = join('/', array_slice($this_slug_parts, 0, 2));
			}
			else {
				$this_controller = $this_module . '/index';
			}

			reset($current_params);
			foreach( $current_params as $k => $v ){
				if( substr($k, 0, 3) == '---' ){
					$final_params[$k] = $v;
				}
			}
			if( $current_module == $this_module ){
				reset($current_params);
				foreach( $current_params as $k => $v ){
					// if( substr($k, 0, 2) == '--' ){
					if( substr($k, 0, 1) == '-' ){
						$final_params[$k] = $v;
					}
				}
			}
			// if( $current_controller == $this_controller ){
				// reset($current_params);
				// foreach( $current_params as $k => $v ){
					// if( substr($k, 0, 1) == '-' ){
						// $final_params[$k] = $v;
					// }
				// }
			// }
		}

		$this->persist = TRUE;

		if( HC_Lib2::array_is_assoc($params) ){
			$final_params = array_merge( $final_params, $params );
			$params = array();
		}

		foreach( $final_params as $k => $v ){
			if( $v === NULL ){
				continue;
			}
			$params[] = $k;
			// $v = urlencode( $v );
			$params[] = $v;
		}

		$my_params = array();
		if( $hca OR $params ){
			$hca = join('/', $hca);

			$hca_slug = $hca;
			$hca_params = array();
			if( $params ){
				$final_params = array();
				foreach( $params as $p ){
					if( is_array($p) ){
						$final_p = array();
						foreach( $p as $p2 ){
							if( is_array($p2) ){
								$p2 = join('|', $p2);
							}
							$p2 = urlencode( $p2 ); 
							$final_p[] = $p2;
						}
						$p = join('|', $final_p);
					}
					else {
						$p = urlencode($p);
					}
					// $p = urlencode($p);
					$final_params[] = $p;
				}
				$params = join('/', $final_params);
				$hca .= ':' . $params;
				$hca_params = $final_params;
			}

			$my_params = array(
				$this->hca	=> $hca,
				);
		}

// echo '<br><br>';
// _print_r( $this->base_params );
// _print_r( $my_params );

		if( $this->ajax && $this->ajax_url ){
			$params = $my_params;
		}
		elseif( $this->admin && $this->admin_url ){
			$params = $my_params;
		}
		else {
			$params = array_merge( $this->base_params, $my_params );
		}
		// $params = http_build_query( $params );
// $params['page'] = 'shiftexec';

		$hca_param = '';
		if( isset($params[$this->hca]) && ($params[$this->hca] != '/') ){
			$hca_param = $this->hca . '=' . $params[$this->hca];
		}

		unset($params[$this->hca]);

		$other_params = http_build_query( $params );

		$params = array();
		if( $other_params ){
			$params[] = $other_params;
		}
		if( $hca_param ){
			$params[] = $hca_param;
		}
		$params = join('&', $params);

		if( $this->ajax && $this->ajax_url ){
			$return = $this->ajax_url;
			$this->ajax = FALSE;
		}
		elseif( $this->admin && $this->admin_url ){
			$return = $this->admin_url;
			$this->admin = FALSE;
		}
		else {
			$return = $this->base_url();
		}

		if( $params ){
			if( strpos($return, '?') === FALSE ){
				$return .= '?' . $params;
			}
			else {
				$return .= '&' . $params;
			}
		}

// alternative return
// $href = $hca_slug;
// if( $hca_params ){
	// $append = array();
	// for( $ii = 0; $ii < count($hca_params)/2; $ii++ ){
		// $append[] = $hca_params[$ii] . '=' . $hca_params[$ii+1];
	// }
	// $append = join('&', $append);
	// $href .= '?' . $append;
// }

		return $return;
	}

	public function current()
	{
		$return = 'http';
		if(
			( isset($_SERVER['HTTPS']) && ( $_SERVER['HTTPS'] == 'on' ) )
			OR
			( defined('NTS_HTTPS') && NTS_HTTPS )
			){
			$return .= 's';
		}
		$return .= "://";
		// if( isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80'){
			// $return .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
		// }
		// else {
			// $return .= $_SERVER['SERVER_NAME'];
		// }

		if( isset($_SERVER['HTTP_HOST']) && $_SERVER['SERVER_PORT'] != '80'){
			$return .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'];
		}
		else {
			$return .= $_SERVER['HTTP_HOST'];
		}

		if ( ! empty($_SERVER['REQUEST_URI']) ){
			$return .= $_SERVER['REQUEST_URI'];
		}
		else {
			$return .= $_SERVER['SCRIPT_NAME'];
		}

		$return = urldecode( $return );
		return $return;
	}
}