<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Acl_Manager_HC_MVC extends _HC_MVC
{
	protected $user = NULL;

	const WHO_USER		= 'user';
	const WHO_GROUP		= 'group';

	const TYPE_OBJECT	= 'object';
	const TYPE_TABLE	= 'table';
	const TYPE_GLOBAL	= 'global';

	public function _init()
	{
		$user = $this->make('/auth/model/user')
			->get()
			;
		$this->set_user( $user );
		return $this;
	}

	public function user()
	{
		return $this->user;
	}

	public function set_user( $user )
	{
		$this->user = $user;
		return $this;
	}

	public function groups()
	{
		$return = array(
			'root'		=> pow(2, 0),
			'everyone'	=> pow(2, 1),
			);
		return $return;
	}

	/* 
	$what is like location@edit
	*/
	public function can( $what, $on = array() )
	{
		$return = FALSE;

		$which = $this->run( 'which', $what );

		$comparator = $this->make('/app/lib/compare');
		$return = $comparator->is_valid($on, $which);

		return $return;
	}

	public function which( $what )
	{
		$return = array();
		return $return;
	}

	public function get_all_actions( $table = NULL )
	{
		$return = array();
		return $return;
	}

	public function after_link_check( $return, $args )
	{
		$slug = array_shift( $return );
		$params = array_shift( $return );

		$return = $this->run('link-check', $slug, $params);
		if( ! $return ){
			$return = array('', array());
		}
		return $return;
	}

	public function link_check( $slug, $params = array() )
	{
		$return = array( $slug, $params );
		return $return;
	}

// this compares an object array to a set of conditions, compatible with those have for SQL queries
}