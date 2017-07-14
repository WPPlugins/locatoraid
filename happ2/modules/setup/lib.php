<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Setup_Lib_HC_MVC extends _HC_MVC
{
	public function is_setup()
	{
	// check if not setup then don't run bootstraps
		$db_params = $this->app->db->params();
		$prefix = $db_params['dbprefix'];
		$tables = $this->app->db->list_tables();

		$my_tables = array();
		foreach( $tables as $tbl ){
			if( substr($tbl, 0, strlen($prefix)) == $prefix ){
				$my_tbl = substr($tbl, strlen($prefix));
				$my_tables[$my_tbl] = $my_tbl;
			}
		}

		if( $my_tables ){
			$return = TRUE;
		}
		else {
			$return = FALSE;
		}

		return $return;
	}
}
