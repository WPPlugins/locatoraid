<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class WordPress_Auth_View_Index_HC_MVC extends _HC_MVC
{
	public function extend_prepare_header( $return, $args, $src )
	{
		$my_return = array(
			'username' => array(
				'label'				=> HCM::__('Username'),
				'sortable'			=> 1,
				'default_sort_asc'	=> 0,
				),
			);
		$return = array_merge( $return, $my_return );
		return $return;
	}

	public function extend_prepare_row( $return, $args, $src )
	{
		$e = array_shift($args);

		$my_return = array(
			'username'	=> $e['username']
			);
		$return = array_merge( $return, $my_return );

		if( array_key_exists('display_name_view', $return) && is_object($return['display_name_view']) ){
			$return['display_name_view']->set_readonly();
		}
		return $return;
	}
}