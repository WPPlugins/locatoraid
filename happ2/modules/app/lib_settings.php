<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class App_Lib_Settings_HC_MVC extends _HC_MVC
{
	private $db = NULL;
	private $config_loader = NULL;
	private $settings = array();
	private $defaults = array();

	public static function single_instance()
	{
	}

	public function set_config_loader( $config_loader )
	{
		$this->settings = array();
		$this->defaults = array();

		$settings_conf = $config_loader->get('settings');
// _print_r( $settings_conf );
		$settings = array();
		foreach( $settings_conf as $k => $va ){
			$this_value = NULL;
			if( ! is_array($va) ){
				$this_value = $va;
			}
			else {
				if( isset($va['default']) ){
					$this_value = $va['default'];
				}
				else {
					if( isset($va['type']) && in_array($va['type'], array('checkbox_set')) ){
						$this_value = array();
					}
					else {
						$this_value = NULL;
					}
				}
			}
			$settings[ $k ] = $this_value;
		}
		$this->settings = array_merge( $this->settings, $settings );
		$this->defaults = array_merge( $this->defaults, $settings );
	}

	public function is_modified( $pname = NULL ){
		$return = TRUE;
		if( $pname !== NULL ){
			if( isset($this->settings[$pname]) && isset($this->defaults[$pname]) && ($this->settings[$pname] == $this->defaults[$pname]) ){
				$return = FALSE;
			}
		}
		return $return;
	}

	public function reload()
	{
		if( ! $this->db ){
			return $return;
		}

		$settings = $this->_get_all();

		foreach( $settings as $k => $v ){
			if( 
				array_key_exists($k, $this->settings) && 
				is_array($this->settings[$k])
				){
				if( is_array($v) ){
					$this->settings[$k] = $v;
				}
				else {
					$this->settings[$k] = array($v);
				}
			}
			else {
				$this->settings[$k] = $v;
			}
		}
	}

	public function set_db( $db )
	{
		$this->db = $db;
		return $this->reload();

		$this->db = $db;
		$settings = $this->_get_all();

		foreach( $settings as $k => $v ){
			if( 
				array_key_exists($k, $this->settings) && 
				is_array($this->settings[$k])
				){
				if( is_array($v) ){
					$this->settings[$k] = $v;
				}
				else {
					$this->settings[$k] = array($v);
				}
			}
			else {
				$this->settings[$k] = $v;
			}
		}
	}

	public function get( $pname = NULL )
	{
		$return = NULL;
		if( $pname === NULL ){
			$return = $this->settings;
		}
		else {
			if( isset($this->settings[$pname]) ){
				$return = $this->settings[$pname];
			}
		}
		return $return;
	}

	public function get_default( $pname = NULL )
	{
		$return = NULL;
		if( $pname === NULL ){
			$return = $this->defaults;
		}
		else {
			if( isset($this->defaults[$pname]) ){
				$return = $this->defaults[$pname];
			}
		}
		return $return;
	}

	public function set( $pname, $pvalue )
	{
		return $this->_save( $pname, $pvalue );
	}

	public function reset( $pname )
	{
		return $this->_delete( $pname );
	}

	private function _get_all( )
	{
		$return	= array();
		if( ! $this->db ){
			return $return;
		}

		if( ! $this->db->table_exists('conf') ){
			return $return;
		}

		$this->db->reset_data_cache();
		$this->db->select('name, value');
		$result	= $this->db->get('conf');

		foreach($result->result_array() as $i){
			if( isset($return[$i['name']]) ){
				if( ! is_array($return[$i['name']]) )
					$return[$i['name']] = array( $return[$i['name']] );
				if( ! in_array($i['value'], $return[$i['name']]) )
					$return[$i['name']][] = $i['value'];
			}
			else {
				$return[$i['name']] = $i['value'];
			}
		}
		return $return;
	}

	private function _save( $pname, $pvalue )
	{
		$return	= TRUE;
		if( ! $this->db ){
			return $return;
		}

		if( is_array($pvalue) ){
			$this->db->where( 'name', $pname );
			$this->db->select('name, value');
			$result	= $this->db->get('conf');

			$current = array();
			foreach($result->result_array() as $i){
				$current[] = $i['value'];
			}

			$to_delete = array_diff( $current, $pvalue );
			$to_add = array_diff( $pvalue, $current );
			foreach( $to_add as $v ){
				$item = array(
					'name'	=> $pname,
					'value'	=> $v
					);
				$this->db->insert('conf', $item);
			}
			foreach( $to_delete as $v ){
				$this->db->where('name', $pname);
				$this->db->where('value', $v);
				$this->db->delete('conf');
			}
		}
		else
		{
			$exists = $this->db->get_where('conf', array('name'=>$pname))->row_array();

			if( $exists ){
				$item = array(
					'value'	=> $pvalue
					);
				$this->db->where('name', $pname);
				$this->db->update('conf', $item);
			}
			else {
				$item = array(
					'name'	=> $pname,
					'value'	=> $pvalue
					);
				$this->db->insert('conf', $item);
			}
		}
	}

	private function _delete( $pname )
	{
		$return	= TRUE;
		if( ! $this->db ){
			return $return;
		}

		$this->db->where('name', $pname);
		$this->db->delete('conf');
	}
}