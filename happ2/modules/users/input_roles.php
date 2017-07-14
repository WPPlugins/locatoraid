<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Input_Roles_HC_MVC extends HC_Form_Input2
{
	protected $input = NULL;

	public function _init()
	{
		$role_manager = $this->make('/users/roles');
		$roles = $role_manager->roles();

		$options = $roles;
		$this->input = $this->make('/form/view/checkbox-set')
			->set_name( $this->name() )
			->set_options( $options )
			;

		return $this;
	}

	function set_readonly( $ro = TRUE )
	{
		$values = $this->input->options();
		$values = array_keys( $values );
		foreach( $values as $v ){
			$this->input->set_readonly($v, $ro);
		}
		return $this;
	}

	public function set_value( $value )
	{
		parent::set_value( $value );

	// convert bits to role names
		$role_manager = $this->make('/users/roles');
		$value = $role_manager->get_roles( $value );

		$this->input
			->set_value( $value )
			;
		return $this;
	}

	public function set_name( $name )
	{
		$this->input
			->set_name( $name )
			;
		return $this;
	}

	public function render()
	{
		$out = $this->input
			->run('render')
			;
		return $out;
	}

	public function render_one( $value, $decorate = FALSE, $withlabel = TRUE )
	{
		$out = $this->input
			->run('render-one', $value, $decorate, $withlabel);
			;
		return $out;
	}

	public function grab( $post )
	{
		$this->input
			->grab( $post )
			;
		$value = $this->input
			->value()
			;

	// convert role names to bits
		$role_manager = $this->make('/users/roles');
		$value = $role_manager->get_bits( $value );

		$this->set_value( $value );
		return $this;
	}
}