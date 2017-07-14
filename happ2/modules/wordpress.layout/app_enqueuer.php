<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class WordPress_Layout_App_Enqueuer_HC_MVC extends _HC_MVC
{
	public function after_register_script( $return, $args, $src )
	{
		$handle = array_shift( $args );
		$src = array_shift( $args );

		$wp_handle = 'hc2-script-' . $handle;
		$src = $this->run('full-path', $src);

		wp_register_script( $wp_handle, $src, array('jquery') );

		return $return;
	}

	public function after_enqueue_script( $return, $args, $src )
	{
		$handle = array_shift( $args );

		$wp_handle = 'hc2-script-' . $handle;
		wp_enqueue_script( $wp_handle );

		return $return;
	}

	public function after_register_style( $return, $args, $src )
	{
		$handle = array_shift( $args );
		$src = array_shift( $args );

		$skip = array('reset', 'style', 'form', 'font');
		if( in_array($handle, $skip) ){
			return $return;
		}

		if( $handle == 'hc' ){
			$src = str_replace('/hc.css', '/hc-wp.css', $src);
		}

		$wp_handle = 'hc2-style-' . $handle;
		$src = $this->run('full-path', $src);

		wp_register_style( $wp_handle, $src );

		return $return;
	}

	public function after_enqueue_style( $return, $args, $src )
	{
		$handle = array_shift( $args );

		$wp_handle = 'hc2-style-' . $handle;
		wp_enqueue_style( $wp_handle );

		return $return;
	}

	public function after_localize_script( $return, $args, $src )
	{
		$handle = array_shift( $args );
		$params = array_shift( $args );

		$wp_handle = 'hc2-script-' . $handle;
		$js_var = 'hc2_' . $handle . '_vars'; 

		wp_localize_script( $wp_handle, $js_var, $params );

		return $return;
	}

	public function full_path( $path )
	{
		$return = $path;

		if( HC_Lib2::is_full_url($return) ){
			return $return;
		}

		if( defined('NTS_DEVELOPMENT2') && NTS_DEVELOPMENT2 ){
			$assets_web_dir = $this->app->web_dir . '/';
			$assets_happ_web_dir = plugins_url('happ2') . '/';
		}
		else {
			$assets_web_dir = $this->app->web_dir . '/';
			$assets_happ_web_dir = $assets_web_dir . 'happ2/';
		}


		if( substr($return, 0, strlen('happ2/')) == 'happ2/' ){
			$return = $assets_happ_web_dir . substr($return, strlen('happ2/'));
		}
		else {
			$return = $assets_web_dir . $return;
		}

		return $return;
	}
}