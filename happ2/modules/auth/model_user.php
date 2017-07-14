<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Auth_Model_User_HC_MVC extends _HC_MVC
{
	private $user = NULL;

	public function single_instance()
	{
	}

	public function _init()
	{
		if( $this->user === NULL ){
			$auth = $this->make('lib');
			$logged_in = $auth->logged_in();
			$user = $this->make('/users/model');
			if( $logged_in ){
				$user = $user
					->where_id('=', $logged_in)
					->fetch_one()
					;
			}
			$this->set( $user );
		}
		return $this;
	}

	public function get()
	{
		return $this->user;
	}

	public function set( $user )
	{
		$this->user = $user;
		return $this;
	}
}