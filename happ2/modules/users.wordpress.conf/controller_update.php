<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Users_Wordpress_Conf_Controller_Update_HC_MVC extends _HC_Form
{
	public function before_route_index( $args, $src )
	{
		$my_args = hc2_parse_args( $args );
		$tab = isset($my_args['tab']) ? $my_args['tab'] : '';
		if( $tab != 'wordpress-users' ){
			return;
		}

	// do the default stuff
		$posted = $src
			->run('grab', $tab, $args)
			;

		$src->run('save', $posted);

	// update wordpress capabilities
		$users_model = $this->make('/users/model');
		$roles = $users_model->wp_roles_mapping();

		$app_short_name = $this->app_short_name();

		$all_hc_roles = array();
		reset( $roles );

		foreach( $roles as $role => $hc_role ){
			if( is_array($hc_role) ){
				continue;
			}
			$all_hc_roles[ $hc_role ] = 1;
		}
		$all_hc_roles = array_keys($all_hc_roles);

		reset( $roles );
		foreach( $roles as $role => $hc_role ){
			$wp_role = get_role( $role );
			if( ! $wp_role ){
				continue;
			}

			$add_roles = array();
			if( $hc_role != 'none' ){
				$add_roles[] = $hc_role;
			}
			$remove_roles = array_diff( $all_hc_roles, array($hc_role, 'none') );

			foreach( $remove_roles as $r ){
				$this_cap = $app_short_name . '_' . $r;
				// echo "REMOVING CAP: '$this_cap' FOR '$role'<br>";
				$wp_role->remove_cap( $this_cap );
			}

			foreach( $add_roles as $r ){
				$this_cap = $app_short_name . '_' . $r;
				// echo "ADDING CAP: '$this_cap' FOR '$role'<br>";
				$wp_role->add_cap( $this_cap );
			}
		}
// exit;
		$redirect_to = $this->make('/html/view/link')
			->to('-referrer-')
			->href()
			;
		return $this->make('/http/view/response')
			->set_redirect($redirect_to) 
			;
	}
}