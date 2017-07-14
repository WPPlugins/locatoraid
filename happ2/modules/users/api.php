<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
// class Users_Api_HC_MVC extends _HC_MVC
class Users_Api_HC_MVC extends _HC_Rest_Api
{
	// public function _init()
	// {
		// $api = $this->make('/api/api')
			// ->set_model('/users/model')
			// ->set_validator('/users/validator')
			// ->force_slug( $this->slug() )
			// ;
		// $this->set_decorated( $api );
		// return $this;
	// }

	public function custom_get_admins( $args = array() )
	{
		$model = $this->_prepare_get_many( $args );
		$model
			->where_admins()
			;

		$entries = $model
			->fetch_many()
			;
		$return = array();
		foreach( $entries as $e ){
			$e = $e->run('to-array');
			$return[] = $e;
		}

		$return = json_encode( $return );
		return $this->make('/http/view/response')
			->set_status_code('200')
			->set_view( $return )
			;
	}
}