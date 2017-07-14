<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class MVC_Factory_HC_System
{
	private $modules = array();
	private $aliases = array();
	private $registry = array(); // keep singletons

	public function __construct( $modules = array(), $aliases = array() )
	{
		$this->modules = $modules;
		foreach( $aliases as $k => $va ){
			$this->aliases[$k] = array_shift($va);
		}
	}

	public function modules()
	{
		return $this->modules;
	}

	public function module_exists( $module )
	{
		$return = array_key_exists($module, $this->modules) ? TRUE : FALSE;
		return $return;
	}
	
	public function register( $object )
	{
		$class_name = strtolower(get_class($object));
		$this->registry[ $class_name ] = $object;
		return $this;
	}

	public function make_classname_path( $slug )
	{
		static $slug2class = array();
		if( isset($slug2class[$slug]) ){
			$return = $slug2class[ $slug ];
			return $return;
		}

		$prepared_slug = $slug;
		$prepared_slug = str_replace('-', '_', $prepared_slug);

		$slug_array = explode('/', $prepared_slug);

		$return_classname = join('_', $slug_array);
		$return_classname = str_replace('.', '_', $return_classname);

		$return_module = array_shift( $slug_array );

// echo "SLUG ARRAY<br>";
// _print_r( $slug_array );

		$return_path = join('_', $slug_array);
		$return_path .= '.php';
		$return_path = array( $return_path );

		if( count($slug_array) > 1 ){
			$return_path = array();
			$this_return_path = join('_', $slug_array);
			$this_return_path .= '.php';
			array_unshift( $return_path, $this_return_path );
			// $final_return_path[] = $this_return_path;

			while( ($subdir = array_shift($slug_array)) && $slug_array ){
				$this_return_path = $subdir . '/' . join('_', $slug_array);
				$this_return_path .= '.php';
				// $final_return_path[] = $this_return_path;
				array_unshift( $return_path, $this_return_path );
			}
		}

// echo "FINAL RETURN PATH<br>";
// _print_r( $return_path );

		$return = array( $return_module, $return_classname, $return_path );

// echo "MAKING CLASSNAME PATH FROM '$slug'<br>";
// _print_r( $return );
		$slug2class[ $slug ] = $return;
		return $return;
	}

	public function make_full_class_name( $return, $this_class_add = '' )
	{
		$prefix = '';
		$suffix = '_hc_mvc';

		if( strlen($this_class_add) ){
			// $prefix = $this_class_add . '_';
			$suffix = '_' . $this_class_add . $suffix;
		}
		$return = $prefix . $return . $suffix;
		return $return;
	}

// should look like this: module/model|view|controller|lib/path
// for example
// locations/controller/admin
// locations/view/admin/index
// locations/view/admin/edit/delete

// RETURNS A BARE MVC OBJECT
	public function make( $original_slug )
	{
		$args = func_get_args();
		$original_slug = array_shift($args);

// echo "<h4>$original_slug</h4>";
		$slug = strtolower($original_slug);
		$slug = trim($slug);

		if( isset($this->aliases[$slug]) ){
			// echo "USE ALIAS FOR '$slug'<br>";
			$slug = $this->aliases[$slug];
		}

		$slug = trim($slug, '/');

		$method = NULL;
		if( strpos($slug, '@') !== FALSE ){
			list( $slug, $method ) = explode('@', $slug);
		}

	// TRY TO FIND OR LOAD THIS CLASS
		$class_found = FALSE;

		static $slug2classname = array();
		if( isset($slug2classname[$slug]) ){
			$class_found = TRUE;
			$class_name = $slug2classname[$slug];
		}

		if( ! $class_found ){
			list( $module, $class_name, $path_array ) = $this->make_classname_path( $slug );
// echo "SLUG = '$slug', MODULE = '$module', CLASS_NAME = '$class_name', PATH = '$path_array'<br>";

		// trying find
			$this_dirs = isset($this->modules[$module]) ? $this->modules[$module] : array();

			reset( $this_dirs );
			foreach( $this_dirs as $this_dir_array ){
				list( $this_dir, $this_class_prefix ) = $this_dir_array;
				$full_class_name = $this->make_full_class_name( $class_name, $this_class_prefix );
				if( class_exists($full_class_name) ){
					$class_name = $full_class_name;
					$class_found = TRUE;
					break;
				}
			}

			// trying to load
			if( ! $class_found ){
				reset( $this_dirs );
				foreach( $this_dirs as $this_dir_array ){
					list( $this_dir, $this_class_prefix ) = $this_dir_array;
					$full_class_name = $this->make_full_class_name( $class_name, $this_class_prefix );

					reset( $path_array );
					foreach( $path_array as $path ){
						$file = $this_dir . '/' . $path;


						if( file_exists($file) ){
		// echo "FOR CLASS: '$class_name' FULL CLASS '$full_class_name' GOT FILE: '$file'<br>";
							require $file;
							if( class_exists($full_class_name) ){
								$class_name = $full_class_name;
								$class_found = TRUE;
								break;
							}
						}
						if( $class_found ){
							break;
						}
					}

				}
			}

			if( $class_found ){
				$slug2classname[$slug] = $class_name;
			}
			else {
				// _print_r( $this_dirs );
				$error_msg = array();
				$error_msg[] = "Can't locate class for '$slug'<br>";

				if( defined('NTS_DEVELOPMENT2') ){
					reset( $this_dirs );
					foreach( $this_dirs as $this_dir_array ){
						list( $this_dir, $this_class_prefix ) = $this_dir_array;
						$file = $this_dir . '/' . $path;
						$full_class_name = $this->make_full_class_name( $class_name, $this_class_prefix );

						$error_msg[] = "tried: '" . $full_class_name . "' in '" . $file;
					}

					// $error_msg[] = "slug '$slug'";
					// $error_msg[] = "tried path: '$path'";
					// $error_msg[] = "module: '$module'";
				}

				$error_msg = join('<br>', $error_msg);
				hc_show_error( $error_msg );
			}
		}

	// FIND MVC OBJECT
		if( method_exists($class_name, 'get_instance')){
			$return = call_user_func(array($class_name, 'get_instance'));
		}
		elseif( method_exists($class_name, 'single_instance') ){
			if( ! isset($this->registry[$class_name]) ){
				$this->registry[$class_name] = new $class_name;
			}
			$return = $this->registry[$class_name];
		}
		else {
			if( $args ){
				$r = new ReflectionClass($class_name);
				$return = $r->newInstanceArgs($args);
			}
			else {
				$return = new $class_name;
			}
		}

		if( $method ){
			$real_method = str_replace('-', '_', $method);
			if( ! method_exists($return, $real_method) ){
				hc_show_error("Can't locate mvc for '$slug' @ '$method' (1)<br>");
			}
		}

		return $return;
	}
}