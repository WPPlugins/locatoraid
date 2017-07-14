<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Commands_List_HC_MVC extends _HC_MVC
{
	protected $filter_role = array();

	public function filter_role( $role )
	{
		$this->filter_role[] = $role;
		return $this;
	}

	public function execute( $args = array() )
	{
		$command = $this->make('/commands/list')
			->set_model('/users/model')
			;

		if( $this->filter_role ){
			$role_manager = $this->make('/users/roles');
			$bits = $role_manager->get_bits( $this->filter_role );
			$args[] = array('roles', '&', $bits);
			$this->filter_role = array();
		}

		$return = $command
			->execute( $args )
			;

		return $return;
	}
}