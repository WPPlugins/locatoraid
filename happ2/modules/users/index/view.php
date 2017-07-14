<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Index_View_HC_MVC extends _HC_MVC
{
	public function render( $entries )
	{
		$header = $this->run('header');
		$sort = $this->run('sort');

		$rows = array();
		reset( $entries );
		foreach( $entries as $e ){
			$rows[ $e['id'] ] = $this->run('row', $e);
		}

		$out = $this->make('/html/view/container');

		if( $rows ){
			$table = $this->make('/html/view/table-responsive')
				->set_header( $header )
				->set_sort( $sort )
				->set_rows( $rows )
				;
			$out->add( $table );
		}

		return $out;
	}

	public function header()
	{
		$return = array();

		$return['email']	= HCM::__('Email');
		$return['id']		= 'ID';
		$return['roles']	= HCM::__('Roles');

		return $return;
	}

	public function sort()
	{
		$return = array(
			'email'			=> 1,
			'id'			=> 1,
			);
		return $return;
	}

	public function row( $e )
	{
		$row = array();

		$p = $this->make('presenter')
			->set_data($e)
			;

		$row = array();

		$row['email']		= $e['email'];
		$row['id']			= $e['id'];
		$row['id']		= $e['id'];
		$id_view = $this->make('/html/view/element')->tag('span')
			->add_attr('class', 'hc-fs2')
			->add_attr('class', 'hc-muted-2')
			->add( $e['id'] )
			;
		$row['id_view']	= $id_view->run('render');

	// roles
		$role_manager = $this->make('/users/roles');
		$roles = $role_manager->roles();
		$this_roles = $role_manager->get_roles( $e['roles'] );

		if( $this_roles ){
			$this_roles_view = array();
			reset( $this_roles );
			foreach( $this_roles as $tr ){
				$this_roles_view[] = $roles[$tr];
			}
			$this_roles_view = join(', ', $this_roles_view);
		}
		else {
			$this_roles_view = array();
			$this_roles_view[] = $this->make('/html/view/icon')->icon('exclamation')
				->add_attr('class', 'hc-red')
				;
			$this_roles_view[] = HCM::__('No Role');
			$this_roles_view = join('', $this_roles_view);
		}

		$row['roles_view'] = $this_roles_view;

		return $row;
	}
}