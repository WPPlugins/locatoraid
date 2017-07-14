<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Acl_Api_HC_MVC extends _HC_MVC
{
	public function after_init( $return, $args, $src )
	{
		$api = $return->decorated();

		$acl_api = $this->make('/acl/api')
			->force_slug( $this->slug() )
			->set_decorated( $api )
			;
		$return
			->set_decorated( $acl_api )
			;

		return $return;
	}
}