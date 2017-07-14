<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Silentsetup_Bootstrap_HC_MVC extends _HC_MVC
{
	public function run()
	{
		$setup = $this->app->db->table_exists('migrations');
		if( ! $setup ){
			$this->app->migration->init();
			if( ! $this->app->migration->current()){
				hc_show_error( $this->app->migration->error_string());
			}
		}
	}
}