<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
abstract class _HC_MVC_Command extends _HC_MVC
{
	protected $params = array();

	public function settings_form()
	{
		return NULL;
	}

	public function present_params()
	{
		return NULL;
	}

	public function to_model( $values )
	{
		return $values;
	}

	public function from_model( $values )
	{
		return $values;
	}

	public function set_params( $params )
	{
		$this->params = $params;
		return $this;
	}

	public function params()
	{
		return $this->params;
	}
}