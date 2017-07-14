<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
abstract class _HC_MVC_Model_Presenter extends _HC_MVC
{
	protected $data = array();

	public function set_data( $data )
	{
		$this->data = $data;
		return $this;
	}

	public function data( $key = NULL )
	{
		if( $key === NULL ){
			return $this->data;
		}
		else {
			$return = ($this->data && array_key_exists($key, $this->data)) ? $this->data[$key] : NULL;
			return $return;
		}
	}

	public function __call( $what, $args )
	{
		if( substr($what, 0, strlen('present_')) == 'present_' ){
			$what = substr( $what, strlen('present_') );
			$return = ($this->data && array_key_exists($what, $this->data)) ? $this->data[$what] : NULL;
			return $return;
		}
		return parent::__call( $what, $args );
	}
}