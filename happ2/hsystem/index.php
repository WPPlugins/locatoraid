<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
if( class_exists('HC_Application') ){
	return;
}

class HC_Application
{
	private $is_started = FALSE;
	private $is_bootstraped = FALSE;

	private $app_name = '';
	private $app_dirs = array();
	private $db_params = array();
	private $app_short_name = '';
	private $app_pages = array();

	private $factory = NULL;
	private $orm_relman = NULL;
	private $extender = NULL;
	private $uri = NULL;

	private $profiler = NULL;

	public $migration = NULL;
	public $app_config = array();
	protected $config_loader = NULL;

	public $web_dir = NULL;

	public function __construct( $app_name, $app_dirs, $db_params = array() )
	{
		$this->app_name		= $app_name;
		$this->db_params	= $db_params;

		$app_code = '';
		reset( $app_dirs );
		foreach( $app_dirs as $app_dir ){
			if( ! is_array($app_dir) ){
				$app_dir = array($app_dir, ''); // second is class prefix
			}
			if( (! $app_code) && strlen($app_dir[1]) ){
				$app_code = $app_dir[1];
			}
			$this->app_dirs[] = $app_dir;
		}

		$this->app_short_name = 'hc' . $app_code;
		if( ! class_exists('HC_lib2') ){
			require dirname(__FILE__) . '/../lib/lib.php';
		}
	}

	public function add_app_page( $page )
	{
		$this->app_pages[] = $page;
		return $this;
	}

	public function db_params()
	{
		return $this->db_params;
	}

	public function app_name()
	{
		return $this->app_name;
	}

	public function app_short_name()
	{
		return $this->app_short_name;
	}

	public function app_pages()
	{
		$return = $this->app_pages;
		if( ! in_array($this->app_short_name(), $return) ){
			$return[] = $this->app_short_name();
		}
		return $return;
	}

	public function go()
	{
		$this->start();
		$this->bootstrap();
		$view = $this->handle_request();
		echo $this->display_view( $view );
	}

	public function display_view( $view )
	{
		$return = '';
$this->profiler->mark('view_render_start');
		$return .= $view;
$this->profiler->mark('view_render_end');

$this->profiler->mark('total_execution_time_end');

$show_profiler = FALSE;
if( defined('NTS_DEVELOPMENT2') ){
// check if want json
	$is_print_view = $this->make('/print/controller')->run('is-print-view');
	if( $is_print_view ){
	}
	else {
		if( isset($_SERVER["CONTENT_TYPE"]) && (strtolower($_SERVER["CONTENT_TYPE"]) == 'application/json') ){
		}
		else {
			$uri = $this->make('/http/lib/uri');
			$slug = $uri->slug();
			if( substr($slug, 0, strlen('api/')) == 'api/' ){
$show_profiler = TRUE;
			}
			else {
				$show_profiler = TRUE;
			}
		}
	}

	// is wp
	if( defined('DB_NAME') ){
		$show_profiler = FALSE;
		if( defined('WP_DEBUG') && WP_DEBUG ){
			$show_profiler = TRUE;
		}
	}

	// is ajax
	if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ){
		$show_profiler = FALSE;
	}
}
// $show_profiler = TRUE;
// $show_profiler = FALSE;

if( $show_profiler ){
	$return .= '<div class="hc-hide-xs">';

	$return .= $this->profiler->run();

	if( isset($GLOBALS['wpdb']) ){
		global $wpdb;

		$return .= 'WP Queries';
		$return .= '<table border="1">';
		foreach( $GLOBALS['wpdb']->queries as $q ){
			$time = number_format($q[1], 4);
			$return .= '<tr><td>' . $time . '</td><td style="overflow: auto;">' . $q[0] . '</td></tr>';
		}
		$return .= '</table>';
	}
	$return .= '</div>';
}

		return $return;
	}

	public function handle_request( $force_slug = NULL, $force_args = array() )
	{
		$this_dir = dirname(__FILE__);

		if( ! $this->is_started() ){
			$this->start();
			$this->bootstrap();
		}

$this->profiler->mark('controller_start');

		$args = array();
		$uri = $this->make('/http/lib/uri');

		$uri->set_current( $uri->current() );
		$slug = $uri->slug();
		$args = $uri->params();

		if( (! $slug) && $force_slug ){
			$slug = $force_slug;
			if( (! $args) && $force_args ){
				$args = $force_args;
			}
		}

		$root = $this->make('/root/controller');

		$response = $root->run('init');
	// out if it is a http response
		if( $this->is_redirect($response) ){
			echo $response;
			exit;
		}

// init session
		$session = $root->make('/session/lib');

		$original_slug = $slug;
		list( $slug, $args ) = $root->run('link-check', $slug, $args);

		if( $slug != $original_slug ){
			$uri = $this->make('/http/lib/uri');
			$uri->force_slug( $slug );
		}

		if( ! $slug ){
			$auth = $this->make('/auth/lib');
			$logged_in = $auth->run('logged-in');
			// $user = $this->make('/auth/model/user')->get();
			// $user_id = $user->id();
			if( $logged_in ){
				$slug = '/auth/notallowed';
			}
			else {
				$slug = '/auth/login';
			}
		}

	// LOCATE CONTROLLER
		list( $controller, $method, $more_args ) = $this->route($slug);
// echo "CONTROLLER = '$controller', METHOD = '$method'<br>";
		if( $more_args ){
			$args = array_merge( $more_args, $args );
		}

		// $mvc = $this->make( $controller );
		$mvc = $root->make( $controller );

		$view = $this->run( $mvc, $method, $args );

$this->profiler->mark('controller_end');

	// out if it is a redirect
		if( $this->is_redirect($view) ){
			echo $view;
			exit;
		}

	// if print view
		$is_print_view = $this->make('/print/controller')->run('is-print-view');
		if( $is_print_view ){
			echo $this->display_view( $view );
			exit;
		}

		return $view;
	}

	public function profiler()
	{
		return $this->profiler;
	}

	public function is_redirect( $return )
	{
		if( is_object($return) && is_callable(array($return, 'redirect')) && $return->redirect() ){
			return TRUE;
		}
		return FALSE;
	}

	public function is_started()
	{
		return $this->is_started;
	}

	public function db_copy()
	{
		$return = new Database_HC_System( $this->db_params );
		return $return;
	}

	public function start()
	{
		if( $this->is_started ){
			return $this;
		}

		$this->is_started = TRUE;
		$this_dir = dirname(__FILE__);

		if ( ! hc_is_php('5.3')){
			@set_magic_quotes_runtime(0); // Kill magic quotes
		}

	// PROFILER
		if( ! class_exists('Profiler_HC_System') ){
			require $this_dir . '/parts/profiler.php';
		}

$this->profiler = new Profiler_HC_System;
$this->profiler->mark('total_execution_time_start');
$this->profiler->mark('loading_time:_base_classes_start');

	// APP
		if( ! class_exists('_HC_MVC') ){
			require $this_dir . '/mvc/mvc.php';
			require $this_dir . '/mvc/model_presenter.php';
			require $this_dir . '/mvc/command_pattern.php';
		}

$this->profiler->mark('app_init_start');
		// application config file
		$config = $this->_load_application_config();

		$this->app_config = $config;

$this->profiler->mark('modules_init_start');
		$modules = $this->_init_modules( $config['modules'] );
$this->profiler->mark('modules_init_end');
// _print_r( $modules );
// exit;

$this->profiler->mark('app_init_end');

	// DATABASE
		$db_params = $this->db_params();
// _print_r( $db_params );
// exit;
		$hcdb = NULL;
		if( $db_params !== NULL ){
$this->profiler->mark('database_init_start');
			require $this_dir . '/database/database.php';

			if( isset($config['dbprefix_version']) ){
				$this->db_params['dbprefix'] = $this->db_params['dbprefix'] . $config['dbprefix_version'] . '_';
			}

			if( defined('NTS_DEVELOPMENT2') ){
				$this->db_params['db_debug_level'] = 'high';
			}
			$hcdb = new Database_HC_System( $this->db_params );
			$this->db = $hcdb;
$this->profiler->mark('database_init_end');
$this->profiler->add_db( $hcdb );

		// MIGRATIONS
	$this->profiler->mark('migration_start');
			require $this_dir . '/parts/migration.php';
			$this->migration = new Migration_HC_System( $hcdb, $modules );

			if( ! $this->migration->current()){
				hc_show_error( $this->migration->error_string());
				exit;
			}

$this->profiler->mark('migration_end');
		}

	// CONFIG LOADER
		if( ! class_exists('App_Lib_Config_Loader_HC_MVC') ){
			require $this_dir . '/../modules/app/lib_config_loader.php';
		}

		$config_loader = new App_Lib_Config_Loader_HC_MVC;
		$config_loader->set_modules( $modules );

	// APPLICATION SETTINGS
		if( ! class_exists('App_Lib_Settings_HC_MVC') ){
			require $this_dir . '/../modules/app/lib_settings.php';
		}
		$settings = new App_Lib_Settings_HC_MVC;
		$settings->set_config_loader( $config_loader );
		if( $hcdb ){
			$settings->set_db( $hcdb );
		}

	// MODEL FACTORY
		if( ! class_exists('MVC_Factory_HC_System') ){
			require $this_dir . '/mvc/factory.php';
			require $this_dir . '/mvc/extender.php';
			require $this_dir . '/parts/orm/orm.php';
			require $this_dir . '/parts/orm/relations.php';
			require $this_dir . '/parts/validator.php';
			require $this_dir . '/parts/form.php';
			require $this_dir . '/parts/api.php';
		}

		$extend = $config_loader->get(
			'extend', 
			array('before', 'after', 'alias'),
			TRUE
			);
// _print_r( $extend );
// _print_r( $extend['after'] );
// exit;
		$this->factory = new MVC_Factory_HC_System( $modules, $extend['alias'] );
		$this->factory
			->register( $config_loader )
			->register( $settings )
			;

		$this->extender = new MVC_Extender_HC_System( $extend );

		$relations = $config_loader->get('relations');
		$orm_relations = array();
		foreach( $relations as $class1 => $my_relations ){
			foreach( $my_relations as $has => $has_relations ){
				foreach( $has_relations as $class2 => $relation ){
					if( ! isset($orm_relations[$class1][$has]) ){
						$orm_relations[$class1][$has] = array();
					}
					$orm_relations[$class1][$has][$class2] = $relation;
				}
			}
		}

		$this->orm_relman = new HC_ORM_Relations_Manager;
		$this->orm_relman->set_config( $orm_relations );

		if( $db_params !== NULL ){
			$this->orm_relman->set_db( $hcdb );
		}

		$this->config_loader = $config_loader;
		return $this;
	}

	public function bootstrap()
	{
		if( $this->is_bootstraped ){
			return $this;
		}
		$this->is_bootstraped = TRUE;

	// run modules bootstrap
$this->profiler->mark('loading_time:_base_classes_end');

		$bootstrap = $this->config_loader->get('bootstrap', array(), TRUE);
		if( $bootstrap ){
$this->profiler->mark('loading_time:_bootstrap_start');
			foreach( $bootstrap as $key => $bts ){
				foreach( $bts as $callable ){
					list( $mvc_slug, $mvc_method ) = explode('@', $callable);
					$mvc = $this->make( $mvc_slug );
					$this->run( $mvc, $mvc_method, array() );
				}
			}
$this->profiler->mark('loading_time:_bootstrap_end');
		}

		return $this;
	}

	// returns an array(controller_name, method_name)
	function route( $slug )
	{
		$slug = strtolower( $slug );
		$slug = trim( $slug );
		$slug = trim( $slug, '/' );

		$config_route = $this->config_loader->get( 'route' );
		if( isset($config_route[$slug]) ){
			$slug = $config_route[$slug];
		}

		$route = explode('/', $slug);
		// _print_r( $route );
		// exit;

	// REST API
		$more_args = array();
		if( isset($route[0]) && ($route[0] == 'api2') ){
			$full_route = $route;
			$route = array( array_shift($full_route) );
			$route[] = 'controller';

			$real_method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';

// echo "FULL ROUTE = ";
// _print_r( $full_route );

			$end_point = array();
			$end_point[] = array_shift( $full_route );

			$id = NULL;
			if( $full_route ){
				// is numeric then is id
				if( (string)(int) $full_route[0] == $full_route[0] ){
					$id = array_shift( $full_route );
				}
				$end_point = array_merge( $end_point, $full_route );
			}

			$end_point = join('/', $end_point);
// echo "ENDPOINT = '$end_point'<br>";
// exit;
			$more_args[] = $end_point;
			$more_args[] = $id;

			$full_route = join('/', $full_route);
		}
		elseif( isset($route[0]) && ($route[0] == 'api') ){
			$api = array_shift( $route );
			array_splice( $route, 1, 0, array($api) );
			if( count($route) < 2 ){
				$route[] = 'index';
			}

			$request_method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'get';
			switch( $request_method ){
				case 'get':
					$real_method = 'get';
					break;
				case 'post':
					$real_method = 'post';
					break;
				case 'put':
					$real_method = 'post';
					break;
				case 'delete':
					$real_method = 'delete';
					break;
			}
		}
	// HTML INTERFACE
		elseif(1) {
			$full_route = $route;
			$route = array( array_shift($full_route) );

			$real_method = 'route-index';

			$end_point = array();
			$id = NULL;
			if( $full_route ){
				// is numeric then is id
				if( (string)(int) $full_route[0] == $full_route[0] ){
					$id = array_shift( $full_route );
				}
				$end_point = array_merge( $end_point, $full_route );
			}

			if( $end_point ){
				// echo 'END POINT!';
				$route = array_merge( $route, $end_point );
				// _print_r( $route );
			}
			else {
				// echo 'NO END POINT!';
				// _print_r( $route );
			}

			if( count($route) < 3 ){
				$route[] = 'controller';
			}
			else {
				array_splice( $route, -1, 0, 'controller');
			}

			// $route[] = 'controller';
// echo "ENDPOINT = '$end_point'<br>";
// exit;
			// $more_args[] = $end_point;
			if( $id ){
				$more_args[] = $id;
			}
// echo 'MORE ARRS';
// _print_r( $more_args );
			$full_route = join('/', $full_route);

		}
		else {
// _print_r( $route );

			if( count($route) < 3 ){
				$route[] = 'controller';
			}
			else {
				array_splice( $route, -1, 0, 'controller');
			}

			$route[] = 'index';
			$method = array_pop( $route );
			$real_method = 'route-' . $method;
		}

		$real_method_name = str_replace('-', '_', $real_method );
		$controller_slug = '/' . join('/', $route);
		$return = array( $controller_slug, $real_method, $more_args );
// echo 'HERE';
// _print_r( $return );
		return $return;
	}

	public function make( $slug )
	{
		$args = func_get_args();
		$slug = $args[0];

		if( substr($slug, 0, 1) != '/' ){
			hc_show_error("NEED FULL SLUG STARTING WITH '/', '$slug' WAS GIVEN<br>");
		}

		if( count($args) > 1 ){
			$args[0] = $slug;
			$return = call_user_func_array( array($this->factory, 'make'), $args );
		}
		else {
			$return = $this->factory->make( $slug );
		}

	// if it's a model then assign database and orm relations
		if( strpos($slug, '/model') !== FALSE ){
			if( method_exists($return, 'set_db')){
				$return->set_db( $this->db );
			}
			if( method_exists($return, 'set_relman')){
				$return->set_relman( $this->orm_relman );
			}
		}

		$return->app = $this;
		$return->slug = $slug;

		$to_decorate = TRUE;
		$to_decorate = FALSE;
		if( $to_decorate ){
			$decorated_return = new _HC_MVC_Decorated( $return, $this );
			if( method_exists($return, '_init') ){
				$decorated_return = $decorated_return->_init();
			}
			return $decorated_return;
		}
		else {
			if( method_exists($return, '_init') ){
				$return = $return->run('-init');
			}
			return $return;
		}
	}

	// RUN AN MVC ACTION
	public function run( $mvc, $method, $args )
	{
		$return = NULL;
		$route_method = str_replace('_', '-', $method );
		$route = $mvc->slug() . '@' . $route_method;

		$extend = $this->extender->get( $route );

		$extend_slug = $this->extender->get( $mvc->slug() );

		$real_method = str_replace('-', '_', $method );

		if(
			(! is_callable( array($mvc, $real_method))) &&
			(! $extend) ){
			hc_show_error("No method or extension for $route");
		}

// echo "<h4>$route</h4>";
// if( $extend ){
	// _print_r( $extend );
// }


		if( ! isset($extend['before']) ){
			$extend['before'] = array();
		}
		if( ! isset($extend['after']) ){
			$extend['after'] = array();
		}
		if( ! isset($extend['alias']) ){
			$extend['alias'] = array();
		}

	// BEFORE
		reset( $extend['before'] );

		foreach( $extend['before'] as $ck => $callable ){
			list( $mvc2_slug, $mvc2_method ) = explode('@', $callable);

			$mvc2_args = array($args, $mvc);
			$mvc2 = $mvc->make( $mvc2_slug );
// echo "RUNNING MVC2: $mvc2_slug: $mvc2_method CALLED BY '$route'<br>";
			$mvc2_return = $this->run( $mvc2, $mvc2_method, $mvc2_args );

			if( $mvc2_return !== NULL ){
			// change arguments
				if( is_array($mvc2_return) && array_key_exists('args', $mvc2_return) ){
					$args = $mvc2_return['args'];
				}
				else {
					// echo "MVC2 RETURN ON '$mvc2_slug'<br>";
					$return = $mvc2_return;
					break;
				} 
				
			}
		}

		$skip_own = FALSE;
		if( $return !== NULL ){
			$skip_own = TRUE;
		}

	// OWN ACTION
		// ALIASED
		if( ! $skip_own ){
			if( $extend['alias'] ){
				$alias = array_shift($extend['alias']);
				list( $mvc2_slug, $mvc2_method ) = explode('@', $alias);
				$mvc2 = $mvc->make( $mvc2_slug );
				$return = $this->run( $mvc2, $mvc2_method, $args );
			}
			else {
				if( $return === NULL ){
					if( method_exists($mvc, $real_method) OR method_exists($mvc, '__call') ){
						$return = call_user_func_array( array($mvc, $real_method), $args );
					}
				}
			}
		}

	// AFTER
		reset( $extend['after'] );

		foreach( $extend['after'] as $ck => $callable ){
			list( $mvc2_slug, $mvc2_method ) = explode('@', $callable);
			$mvc2 = $mvc->make( $mvc2_slug );

			$mvc2_args = array($return, $args, $mvc);

			$mvc2_return = $this->run( $mvc2, $mvc2_method, $mvc2_args );
			if( $mvc2_return !== NULL ){
				$return = $mvc2_return;
			}
		}

		return $return;
	}

	public function extend_after( $return, $route, $args )
	{
		$extend = $this->extender->get( $route );
		if( ! (isset($extend['after']) && $extend['after']) ){
			return $return;
		}

		foreach( $extend['after'] as $ck => $callable ){
			list( $mvc2_slug, $mvc2_method ) = explode('@', $callable);
			$mvc2 = $this->make( $mvc2_slug );

			$mvc2_args = array($return, $args, $mvc);

			// $mvc2_return = $this->run( $mvc2, $mvc2_method, $mvc2_args );
			$mvc2_return = call_user_func_array( array($mvc2, $mvc2_method), $mvc2_args );
			if( $mvc2_return !== NULL ){
				$return = $mvc2_return;
			}
		}

		return $return;
	}

	private function _load_application_config()
	{
		$file_found = FALSE;
		$search_files = array(
			// $this->app_name . '_pro.php',
			$this->app_name . '.php',
			);

		$files_found = array();

		foreach( $this->app_dirs as $app_dir_array ){
			list( $app_dir, $class_prefix ) = $app_dir_array;
			foreach( $search_files as $f ){
				$target_file = $app_dir . '/config/' . $f;
				if( file_exists($target_file) ){
					$files_found[] = $target_file;
				}
			}
		}

		if( ! $files_found ){
			echo 'NO APP CONFIG FILE FOUND!';
			_print_r( $search_files );
			exit;
			return;
		}

		$final_config = array();
		foreach( $files_found as $f ){
			require($f);
			foreach( $config as $k => $v ){
				if( array_key_exists($k, $final_config) ){
					if( is_array($final_config[$k]) && is_array($config[$k]) ){
						$final_config[$k] = array_merge( $final_config[$k], $config[$k] );
					}
				}
				else {
					$final_config[$k] = $config[$k];
				}
			}
		}
		$config = $final_config;

	/* process modules */
		if( isset($config['modules']) ){
			$new_modules = array();
			foreach( $config['modules'] as $m ){
				$new_modules[$m] = $m;
			}
			$config['modules'] = $new_modules;
		}
		return $config;
	}

	private function _init_modules( $modules )
	{
		// $MODULES_SEPARATOR = '_';
		$MODULES_SEPARATOR = '.';

		$return = array();
		if( ! is_array($modules) ){
			return $return;
		}

		$app_dirs = array();
		reset( $this->app_dirs );
		foreach( $this->app_dirs as $app_dir_array ){
			list( $app_dir, $class_prefix ) = $app_dir_array;
			$app_dirs[] = array( $app_dir . '/modules', $class_prefix );
		}

	// compile the list of subdirs
		$pre_return = array();
		$submodules = array();

		foreach( $app_dirs as $app_dir_array ){
			list( $app_dir, $class_prefix ) = $app_dir_array;

			$subdirs = glob($app_dir . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
			foreach( $subdirs as $mod_dir ){
				$module = substr( $mod_dir, strlen($app_dir) + 1 );

				$underscore_pos = strrpos($module, $MODULES_SEPARATOR);
				if( $underscore_pos === FALSE ){
					if( ! in_array($module, $modules) ){
						continue;
					}
				}
				else {
					list( $parent_module, $child_module ) = explode( $MODULES_SEPARATOR, $module, 2 );
					if( ! in_array($parent_module, $modules) ){
						continue;
					}

					$take_this = TRUE;
					$child_modules = explode( $MODULES_SEPARATOR, $child_module );
					foreach( $child_modules as $chm ){
						if( ! in_array($chm, $modules) ){
							$take_this = FALSE;
							break;
						}
					}
					if( ! $take_this ){
						continue;
					}

					if( ! isset($submodules[$parent_module]) ){
						$submodules[$parent_module] = array();
					}
					$submodules[$parent_module][] = $module;
				}

				if( ! isset($pre_return[$module]) ){
					$pre_return[$module] = array();
				}
				$pre_return[$module][] = array( $mod_dir, $class_prefix );
			}
		}

	// order them by the order in modules
		$return = array();
		foreach( $modules as $module ){
			if( isset($pre_return[$module]) ){
				$return[$module] = $pre_return[$module];
			}
			if( isset($submodules[$module]) ){
				foreach( $submodules[$module] as $submodule ){
					$return[$submodule] = $pre_return[$submodule];
				}
			}
		}

	// now extend the core dirs by submodules if any
		reset( $return );
		$keys = array_keys($return);
		foreach( $keys as $module ){
			$underscore_pos = strrpos($module, $MODULES_SEPARATOR);
			if( $underscore_pos === FALSE ){
				continue;
			}

			$target_module = substr( $module, $underscore_pos + 1 );
			if( ! isset($return[$target_module]) ){
				$return[$target_module] = array();
			}
		}
		return $return;
	}
}