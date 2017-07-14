<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Roles_HC_MVC extends _HC_MVC
{
	protected $roles = array();

	public function _init()
	{
		$roles = array(
			'admin'		=> HCM::__('Administrator'),
			);
		$this
			->set_roles( $roles )
			;
		return $this;
	}

	public function set_roles( $roles )
	{
		$this->roles = $roles;
		return $this;
	}


	public function roles()
	{
		return $this->roles;
	}

	public function get_bits( $role_names )
	{
		$return = 0;
		if( ! is_array($role_names) ){
			$role_names = array( $role_names );
		}

		$roles = $this->roles();
		$keys = array_keys( $roles );

		$masks = array();
		for( $bit = 0; $bit < count($keys); $bit++ ){
			$masks[ $keys[$bit] ] = pow(2, $bit);
		}

		$role_names = array_unique( $role_names );
		reset( $role_names );
		foreach( $role_names as $role_name ){
			if( ! array_key_exists($role_name, $masks) ){
				continue;
			}
			$return += $masks[$role_name];
		}

		return $return;
	}

	public function get_roles( $bits )
	{
		$roles = $this->roles();
		$keys = array_keys( $roles );

		$return = array();
		for( $ii = 0; $ii < count($keys); $ii++ ){
			if( $bits & pow(2, $ii) ){
				$return[] = $keys[$ii];
			}
		}
		return $return;
	}
}