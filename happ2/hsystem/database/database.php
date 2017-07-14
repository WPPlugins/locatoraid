<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
if( ! class_exists('CI_DB_HC_System') ){
	require dirname(__FILE__) . '/DB_driver.php';
	require dirname(__FILE__) . '/DB_query_builder.php';
	class CI_DB_HC_System extends CI_DB_query_builder_HC_System
	{
	}
}

if( ! class_exists('Database_HC_System') ){

class Database_HC_System
{
	private $db = NULL;
	private $dbforge = NULL;
	private $params = array();

	public function __construct( $params )
	{
		$default_params = array(
			'dbdriver'	=> 'mysqli',
			'pconnect'	=> FALSE,
			'db_debug'	=> TRUE,
			'db_debug_level'	=> 'low',
			'cache_on'	=> FALSE,
			'cachedir'	=> '',
			'char_set'	=> 'utf8',
			'dbcollat'	=> 'utf8_general_ci',
			'swap_pre'	=> '',
			'autoinit'	=> TRUE,
			'stricton'	=> FALSE,
			);
		$params = array_merge( $default_params, $params );

		if( isset($GLOBALS['wpdb']) ){
			$dbdriver = 'mysql';
			$wpdb_array = (array) $GLOBALS['wpdb'];
			foreach( $wpdb_array as $k => $v ){
				if( substr($k, -strlen('use_mysqli')) == 'use_mysqli' ){
					if( $v ){
						$dbdriver = 'mysqli';
					}
					break;
				}
			}
			$params['dbdriver'] = $dbdriver;
		}

		// No DB specified yet?  Beat them senseless...
		if( ! isset($params['dbdriver']) OR $params['dbdriver'] == ''){
			echo('You have not selected a database type to connect to.');
			exit;
		}

		$this_dir = dirname(__FILE__);

		$driver_file = $this_dir . '/drivers/' . $params['dbdriver'] . '/' . $params['dbdriver'] . '_driver.php';
		include_once( $driver_file );

		// Instantiate the DB adapter
		$this->init_db( $params );
		$this->params = $params;
	}

	public function init_db( $params )
	{
		// Instantiate the DB adapter
		$driver = 'CI_DB_'.$params['dbdriver'].'_driver_HC_System';
		$this->db = new $driver($params);

		if ($this->db->autoinit == TRUE){
			$this->db->initialize();
		}

		if (isset($params['stricton']) && $params['stricton'] == TRUE){
			$this->db->query('SET SESSION sql_mode="STRICT_ALL_TABLES"');
		}
		return $this;
	}

	public function params()
	{
		return $this->params;
	}

	public function set_params( $params )
	{
		$this->params = $params;
		$this->db->set_params( $params );
	}
	
	public function dbforge()
	{
		if( null === $this->dbforge ){
			$class = 'CI_DB_' . $this->db->dbdriver . '_forge_HC_System';
			if( ! class_exists($class) ){
				require dirname(__FILE__) . '/DB_forge.php';
				require dirname(__FILE__) . '/drivers/' . $this->db->dbdriver . '/' . $this->db->dbdriver . '_forge.php';
			}
			$this->dbforge = new $class( $this->db );
		}
		return $this->dbforge;
	}

	public function __get($name)
	{
		return $this->db->{$name};
	}

	public function __call( $what, $args )
	{
		return call_user_func_array( array($this->db, $what), $args );
	}
}
}