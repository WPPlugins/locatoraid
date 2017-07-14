<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Acl_Api_HC_MVC extends _HC_MVC
{
	protected $user = NULL;

	public function user()
	{
		return $this->user;
	}

	public function set_user( $user )
	{
		$this->user = $user;
		return $this;
	}

	public function _init()
	{
		$user = $this->make('/auth/model/user')
			->get()
			;
		$user = $user->run('to-array');
		$this->set_user( $user );
		return $this;
	}

// READ
	public function get()
	{
		$raw_args = func_get_args();
		$args = hc2_parse_args( $raw_args, TRUE );

	// filter
		$which = $this->run('which', 'read');
		if( isset($which['id']) ){
			$which[' id'] = $which['id'];
			unset($which['id']);
		}
		$args = array_merge( $args, $which );

		$args = array( $args );

	// pass over to basic api
		$api = $this->decorated();

		$return = call_user_func_array( 
			array($api, 'get'),
			$args
			);

		if( $return === $api ){
			return $this;
		}
		else {
			return $return;
		}
	}

// CREATE
	public function post( $json_input )
	{
		$check = $this->table() . '@create';

		// $can = $this->am->run('can', $check);
		// if( ! $can ){
			// $return = array();
			// $return['errors'] = HCM::__('Not Authorized') . ': ' . $check;
			// $return = json_encode( $return );

			// return $this->make('/http/view/response')
				// ->set_status_code('401')
				// ->set_view( $return )
				// ;
		// }
// echo $check;

	// pass over to basic api
		$api = $this->decorated();
		$raw_args = func_get_args();
		$return = call_user_func_array( 
			array($api, 'post'),
			$raw_args
			);

		if( $return === $api ){
			return $this;
		}
		else {
			return $return;
		}
	}

// UPDATE
	public function put( $id, $json_input, $validate_change_only = TRUE )
	{
		$raw_args = func_get_args();

		// $check = $this->table() . '@update';
		// $can = $this->am->run('can', $check);
		// if( ! $can ){
			// $return = array();
			// $return['errors'] = HCM::__('Not Authorized') . ': ' . $check;
			// $return = json_encode( $return );

			// return $this->make('/http/view/response')
				// ->set_status_code('401')
				// ->set_view( $return )
				// ;
		// }

		$api = $this->decorated();
		$return = call_user_func_array( 
			array($api, 'put'),
			$raw_args
			);

		if( $return === $api ){
			return $this;
		}
		else {
			return $return;
		}
	}

// DELETE
	public function delete( $id )
	{
		$raw_args = func_get_args();
		// $check = $this->table() . '@delete';
		// $can = $this->am->run('can', $check);
		// if( ! $can ){
			// $return = array();
			// $return['errors'] = HCM::__('Not Authorized') . ': ' . $check;
			// $return = json_encode( $return );

			// return $this->make('/http/view/response')
				// ->set_status_code('401')
				// ->set_view( $return )
				// ;
		// }

		$api = $this->decorated();
		$return = call_user_func_array( 
			array($api, 'delete'),
			$raw_args
			);

		if( $return === $api ){
			return $this;
		}
		else {
			return $return;
		}
	}
}
