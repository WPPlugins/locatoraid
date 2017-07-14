<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class App_Enqueuer_LC_HC_MVC extends _HC_MVC
{
	protected $scripts = array();
	protected $localize_scripts = array();
	protected $enqueue_scripts = array();
	protected $styles = array();
	protected $enqueue_styles = array();

	public function single_instance()
	{
	}

	public function _init()
	{
	// register
		$this
			->run('register-script', 'hc', 'happ2/assets/js/hc2.js' )
			;

		if( defined('NTS_DEVELOPMENT2') ){
			$this
				->run('register-style',  'reset',		'happ2/assets/css/hc-1-reset.css' )
				->run('register-style', 'utilities',	'happ2/assets/css/hc-2-utilities.css' )
				->run('register-style', 'basstheme',	'happ2/assets/css/hc-3-bass-theme.css' )
				->run('register-style', 'bass',		'happ2/assets/css/hc-3-bass.css' )
				->run('register-style', 'style',		'happ2/assets/css/hc-4-style.css' )
				->run('register-style', 'form',		'happ2/assets/css/hc-5-form.css' )
				->run('register-style', 'grid',		'happ2/assets/css/hc-6-grid.css' )
				->run('register-style', 'javascript',	'happ2/assets/css/hc-7-javascript.css' )
				->run('register-style', 'schecal',	'happ2/assets/css/hc-9-schecal.css' )
				->run('register-style', 'animate',	'happ2/assets/css/hc-10-animate.css' )
				;
		}
		else {
			$this
				->run('register-style', 'hc', 'happ2/assets/css/hc.css' )
				;
		}

		$this
			->run('register-style', 'font', 'https://fonts.googleapis.com/css?family=PT+Sans' )
			;

	// enqueue
		$this
			->run('enqueue-script', 'hc' )
			;

		if( defined('NTS_DEVELOPMENT2') ){
			$this
				->run('enqueue-style', 'reset' )
				->run('enqueue-style', 'utilities' )
				->run('enqueue-style', 'basstheme' )
				->run('enqueue-style', 'bass' )
				->run('enqueue-style', 'style' )
				->run('enqueue-style', 'form' )
				->run('enqueue-style', 'grid' )
				->run('enqueue-style', 'javascript' )
				// ->run('enqueue-style', 'schecal' )
				->run('enqueue-style', 'animate' )
				;
		}
		else {
			$this
				->run('enqueue-style', 'hc' )
				;
		}
		$this
			->run('enqueue-style', 'font' )
			;

		return $this;
	}

	public function register_script( $handle, $path )
	{
		$this->scripts[ $handle ] = $path;
		return $this;
	}

	public function register_style( $handle, $path )
	{
		$this->styles[ $handle ] = $path;
		return $this;
	}

	public function localize_script( $handle, $params )
	{
		if( array_key_exists($handle, $this->localize_scripts) ){
			$this->localize_scripts[$handle] = array_merge( $this->localize_scripts[$handle], $params );
		}
		else {
			$this->localize_scripts[$handle] = $params;
		}
		return $this;
	}

	public function enqueue_script( $handle )
	{
		$this->enqueue_scripts[ $handle ] = $handle;
		return $this;
	}

	public function enqueue_style( $handle )
	{
		$this->enqueue_styles[ $handle ] = $handle;
		return $this;
	}

	public function get_localize_scripts()
	{
		return $this->localize_scripts;
	}

	public function get_scripts()
	{
		$return = array();

		foreach( $this->enqueue_scripts as $handle ){
			if( ! array_key_exists($handle, $this->scripts) ){
				continue;
			}
			$src = $this->scripts[$handle];
			$return[ $handle ] = $src;
		}
		return $return;
	}

	public function get_styles()
	{
		$return = array();

		foreach( $this->enqueue_styles as $handle ){
			if( ! array_key_exists($handle, $this->styles) ){
				continue;
			}
			$src = $this->styles[$handle];
			$return[ $handle ] = $src;
		}

		return $return;
	}
}