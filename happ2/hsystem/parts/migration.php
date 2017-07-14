<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
if( class_exists('HC_Migration') ){
	return;
}

class HC_Migration
{
	public function __construct( $db )
	{
		$this->db = $db;
		$this->dbforge = $this->db->dbforge();
	}
}

class Migration_HC_System
{
	private $db = NULL;
	private $dbforge = NULL;
	private $modules = array();

	protected $_migration_enabled = FALSE;
	protected $_migration_paths = array();
	protected $_migration_version = 0;

	protected $_error_string = '';

	protected $_current_module = '';

	protected $_current_versions = array();

// $modules = array('module_name' => migrations_path);
	public function __construct( $db, $modules )
	{
		$this->db = $db;
		$this->dbforge = $this->db->dbforge();
		$this->modules = $modules;
	}

// create migrations table
	public function init()
	{
		// echo 'RESET1<br>';
		$this->db->reset_data_cache();
		if( ! $this->db->table_exists('migrations') ){
			$this->dbforge->add_field(array(
				'module'  => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE),
				'version' => array('type' => 'INT', 'constraint' => 3),
				));
			$this->dbforge->create_table('migrations', TRUE);
		}
	}

	public function current()
	{
		$return = TRUE;

		// echo 'RESET2<br>';
		$this->db->reset_data_cache();
		if( ! $this->db->table_exists('migrations') ){
			return $return;
		}

		$config = array(
			);

		$this->_current_versions = array();

	/* load current versions */
		$current_results = $this->db->get('migrations')->result_array();
		if( isset($current_results[0]) && (! array_key_exists('module', $current_results[0])) ){
			$this->dbforge->add_column(
				'migrations',
				array(
					'module' => array(
						'type'	=> 'VARCHAR(100)',
						'null'	=> TRUE,
						),
					)
				);
			$current_results = $this->db->get('migrations')->result_array();
		}

		foreach( $current_results as $row ){
			$module = strtolower( $row['module'] );
			$this->_current_versions[ $module ] = $row['version'];
		}

	/* get modules */
		$modules = $this->get_modules();

		foreach( $modules as $module => $path ){
			if( $this->init_module($module) ){
				$this_return = $this->version( $this->_migration_version );
				$return = $return && $this_return;
			}
		}
		return $return;
	}

	public function get_modules()
	{
		$modules = $this->modules;
		if( ! is_array($modules) ){
			$modules = array();
		}

	// sort: first go main, then subs
		$module_names = array_keys( $modules );
		$firsts = array();
		$seconds = array();
		reset( $module_names );
		foreach( $module_names as $mn ){
			if( strpos($mn, '_') === FALSE ){
				$firsts[] = $mn;
			}
			else {
				$seconds[] = $mn;
			}
		}

		reset( $firsts );
		foreach( $firsts as $mn ){
			$return[ $mn ] = $modules[$mn];
		}
		reset( $seconds );
		foreach( $seconds as $mn ){
			$return[ $mn ] = $modules[$mn];
		}
		return $return;
	}

	public function init_module( $module )
	{
		$modules = $this->get_modules();
		$migration_paths = array();

		if( ! isset($modules[$module]) ){
			return FALSE;
		}

		$config = array();
		$paths = $modules[$module];

		foreach( $paths as $path_array ){
			list( $path, $class_prefix ) = $path_array;
			// $config_file = $path . '/config/migration.php';
			$config_file = $path . '/config_migration.php';
			if( file_exists($config_file) ){
				require( $config_file );

				$need_migration_paths = FALSE;
				$current_version = isset($this->_current_versions[$module]) ? $this->_current_versions[$module] : 0;

				if( 
					isset($config['migration_version']) && 
					($config['migration_version'] > $current_version)
					){
					$need_migration_paths = TRUE;
				}

				if( $need_migration_paths ){
					if( (! isset($config['migration_path'])) OR (! $config['migration_path'])  ){
						// $config['migration_path'] = '/migrations';
						$config['migration_path'] = '/';
					}

					$migration_path = $path . $config['migration_path'];
					$migration_path = rtrim($migration_path, '/') . '/';
					unset( $config['migration_path'] );
					$migration_paths[] = array( $migration_path, $class_prefix );

				// check if we have submodules too
					$submodules = array();
					$submodules_path = $path . '/modules';
					$subdirs = glob($submodules_path . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
					foreach( $subdirs as $mod2_dir ){
						$module2 = substr( $mod2_dir, strlen($submodules_path) + 1 );
						$submodules[] = $module2;
					}

					foreach( $submodules as $submodule ){
						// $submodule_migration_path = $path . '/modules/' . $submodule . '/migrations';
						$submodule_migration_path = $path . '/modules/' . $submodule . '/';
						$submodule_migration_path = rtrim($submodule_migration_path, '/') . '/';
						$migration_paths[] = array( $submodule_migration_path, $class_prefix);
					}
				}
			}
		}
		if( ! $config ){
			return FALSE;
		}

		foreach ($config as $key => $val){
			$this->{'_' . $key} = $val;
		}

		$this->_migration_paths = $migration_paths;
		$this->_current_module = $module;
		return TRUE;
	}

	/**
	 * Migrate to a schema version
	 *
	 * Calls each migration step required to get to the schema version of
	 * choice
	 *
	 * @param    int $target_version Target schema version
	 * @return    mixed    TRUE if already latest, FALSE if failed, int if upgraded
	 */
	public function version( $target_version )
	{
		// echo 'RESET3<br>';
		// $this->db->reset_data_cache();

		$start = $current_version = $this->_get_version();
		$stop  = $target_version;

		if ($target_version > $current_version) {
			// Moving Up
			++$start;
			++$stop;
			$step = 1;
		} else {
			// Moving Down
			$step = -1;
		}

		$method     = ($step === 1) ? 'up' : 'down';
		$migrations = array();

		// We now prepare to actually DO the migrations
		// But first let's make sure that everything is the way it should be
		$migration_paths = $this->_migration_paths;

		foreach( $migration_paths as $migration_path_array ){
			list( $migration_path, $class_prefix ) = $migration_path_array;
			for ($i = $start; $i != $stop; $i += $step) {
				// $f = glob(sprintf($migration_path . '%03d_*.php', $i));
				$f = glob(sprintf($migration_path . 'migration_' . '%03d_*.php', $i));
				// Only one migration per step is permitted
				if (count($f) > 1) {
					$this->_error_string = 'migration_multiple_version' . $i;
					return FALSE;
				}

				// Migration step not found
				if (count($f) == 0) {
					// If trying to migrate up to a version greater than the last
					// existing one, migrate to the last one.
					if ($step == 1) {
						break;
					}

					// If trying to migrate down but we're missing a step,
					// something must definitely be wrong.
					$this->_error_string = 'migration_not_found' . ':' . $i;

					return FALSE;
				}

				$file = basename($f[0]);
				$name = basename($f[0], '.php');

				// Filename validations
				// if (preg_match('/^\d{3}_(\w+)$/', $name, $match)){
				if (preg_match('/^migration_\d{3}_(\w+)$/', $name, $match)){
					$this_migration = strtolower($match[1]);
					// echo "<h2>$this_migration</h2>";
	//				$this_migration = ucfirst($match[1]);

					// Cannot repeat a migration at different steps
					if (in_array($this_migration, $migrations))
					{
						$this->_error_string = 'migration_multiple_version' . ':' . $match[1];
						return FALSE;
					}

					$class = $this->_current_module . '_' . $this_migration;
					if( $class_prefix ){
						$class = $class . '_' . $class_prefix;
					}
					$class = $class . '_' . 'HC_Migration';

					$class = str_replace('-', '_', $class);
					$class = str_replace('.', '_', $class);

					if( ! class_exists($class) ){
						require $f[0];
					}

					if( ! class_exists($class) ){
						$this->_error_string = 'migration_class_doesnt_exist' . ':' . $class;
						return FALSE;
					}

					if( ! is_callable(array($class, $method)) ){
						$this->_error_string = 'migration_missing_' . $method . '_method' . ':' . $class;
						return FALSE;
					}

					if( ! isset($migrations[$i]) ){
						$migrations[$i] = array();
					}
					$migrations[$i][] = $class;
				}
				else {
					$this->_error_string = 'migration_invalid_filename' . ':' . $file;
					return FALSE;
				}
			}
		}

		// If there is nothing to do so quit
		if ($migrations === array()){
			return TRUE;
		}

		// Loop through the migrations
		foreach ($migrations as $migration => $migration_classes ) {
			foreach( $migration_classes as $migration_class ){
				// Run the migration class
				call_user_func(array(new $migration_class($this->db), $method));
			}
			$current_version += $step;
			$this->_update_version($current_version);
		}

		return $current_version;
	}

// --------------------------------------------------------------------

	/**
	 * Retrieves current schema version
	 *
	 * @param string $module
	 * @return    int    Current Migration
	 */
	protected function _get_version($module = '')
	{
		if( ! $module ){
			$module = $this->_current_module;
		}
		$module = strtolower( $module );

		$return = isset($this->_current_versions[$module]) ? $this->_current_versions[$module] : 0;
		return $return;
	}

	public function error_string()
	{
		return $this->_error_string;
	}

// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 *
	 * @param    int    Migration reached
	 * @param string $module
	 * @return    bool
	 */
	protected function _update_version($migrations, $module = '')
	{
		! $module AND $module = $this->_current_module;
		$row = $this->db->get_where('migrations', array('module' => $module))->row();
		if (count($row)) {
			return $this->db->where(array('module' => $module))->update('migrations', array('version' => $migrations));
		} else {
			return $this->db->insert('migrations', array('module' => $module, 'version' => $migrations));
		}
	}

// --------------------------------------------------------------------
}
