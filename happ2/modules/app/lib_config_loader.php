<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class App_Lib_Config_Loader_HC_MVC extends _HC_MVC
{
	private $modules = array();
	private $config = array();

	public function single_instance()
	{
	}

	public function set_modules( $modules )
	{
		$this->modules = $modules;
		return $this;
	}

	public function get_files( $file )
	{
		$return = array();

		$modules = $this->modules;

		$already_loaded = array();
		foreach( $modules as $module => $paths ){
			foreach( $paths as $path_array ){
				list( $path, $class_prefix ) = $path_array;
				// $this_file = $path . '/config/' . $file . '.php';
				$this_file = $path . '/config_' . $file . '.php';
// echo "THIS FILE = '$this_file'<br>";

				if( in_array($this_file, $already_loaded) ){
					continue;
				}

				if( file_exists($this_file) ){
					$already_loaded[] = $this_file;
					if( ! isset($return[$module]) ){
						$return[$module] = array();
					}
					$return[$module][] = $this_file;
				}
			}
		}
		return $return;
	}

	protected function _load_parse_modules( $file, $categories = array() )
	{
		$create_subkeys = FALSE;
		$modules = $this->modules;

		$assume_vars = $categories ? $categories : array('config');
		if( $categories ){
			foreach( $assume_vars as $var ){
				$this_config[$var] = array();
			}
		}
		else {
			$this_config = array();
		}

		$files = $this->get_files( $file );

		foreach( $files as $module => $files2 ){
			foreach( $files2 as $this_file ){
				foreach( $assume_vars as $var ){
					${$var} = array();
				}

				require($this_file);
				// echo "THIS FILE = '$this_file'<br>";

				foreach( $assume_vars as $var ){
					foreach( ${$var} as $k => $vs ){
						if(
							( substr($k, 0, 1) != '/' )
							&& 
							( substr($k, 0, 1) != '@' )
							){
							$k = '/' . $module . '/' . $k;
						}

						if( $categories ){
							if( ! isset($this_config[$var][$k]) ){
								$this_config[$var][$k] = array();
							}
						}
						else {
							if( ! isset($this_config[$k]) ){
								$this_config[$k] = array();
							}
						}

						if( ! is_array($vs) ){
							$vs = array($vs);
						}

						$ii = 0;
						foreach( $vs as $subkey => $v ){
							if( ! is_string($subkey) ){
								if( $create_subkeys ){
									$ii++;
									$subkey = $module . '-' . $ii;
								}
								else {
									// $subkey = $ii;
									if( $categories ){
										while( isset($this_config[$var][$k][$subkey]) ){
											$ii++;
											$subkey = $ii;
										}
									}
									else {
										while( isset($this_config[$var][$subkey]) ){
											$ii++;
											$subkey = $ii;
										}
									}
								}
							}

							if(
								( substr($v, 0, 1) != '/' )
								){
								$v = '/' . $module . '/' . $v;
							}

							if( $categories ){
								$this_config[$var][$k][$subkey] = $v;
							}
							else {
								$this_config[$k][$subkey] = $v;
							}
						}
					}
				}
			}
		}

		$this->config[$file] = $this_config;
	}

	protected function _load( $file, $categories = array(), $parse_modules = FALSE )
	{
		if( $parse_modules ){
			return $this->_load_parse_modules( $file, $categories );
		}

		$modules = $this->modules;

		$assume_vars = $categories ? $categories : array('config');
		if( $categories ){
			foreach( $assume_vars as $var ){
				$this_config[$var] = array();
			}
		}
		else {
			$this_config = array();
		}

		$already_loaded = array();
		foreach( $modules as $module => $paths ){
			foreach( $paths as $path_array ){
				list( $path, $class_prefix ) = $path_array;
				// $this_file = $path . '/config/' . $file . '.php';
				$this_file = $path . '/config_' . $file . '.php';

				if( in_array($this_file, $already_loaded) ){
					continue;
				}

				if( file_exists($this_file) ){
					$already_loaded[] = $this_file;
					foreach( $assume_vars as $var ){
						${$var} = array();
					}

					require($this_file);

					foreach( $assume_vars as $var ){
						foreach( ${$var} as $k => $va ){
							if( $categories ){
								if( ! isset($this_config[$var][$k]) ){
									$this_config[$var][$k] = array();
								}
								if( is_array($va) ){
									$this_config[$var][$k] = array_merge( $this_config[$var][$k], $va );
								}
								else {
									$this_config[$var][$k] = $va;
								}
							}
							else {
								if( ! isset($this_config[$k]) ){
									$this_config[$k] = array();
								}
								if( is_array($va) ){
									if( HC_Lib2::array_is_assoc($va) ){
										foreach( $va as $k2 => $va2 ){
											if( is_array($va2) ){
												if( ! isset($this_config[$k][$k2]) ){
													$this_config[$k][$k2] = array();
												}
												$this_config[$k][$k2] = array_merge( $this_config[$k][$k2], $va2 );
											}
											else {
												$this_config[$k][$k2] = $va2;
											}
										}
									}
									else {
										$this_config[$k] = array_merge( $this_config[$k], $va );
									}
								}
								else {
									$this_config[$k] = $va;
								}
							}
						}
					}
				}
			}
		}

		$this->config[$file] = $this_config;
	}

	public function get( $file, $categories = array(), $parse_modules = FALSE )
	{		
		if( ! array_key_exists($file, $this->config) ){
			$this->_load($file, $categories, $parse_modules);
		}
		$return = $this->config[$file];
		return $return;
	}
}