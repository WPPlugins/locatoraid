<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_Conf_Mode_Controller_LC_HC_MVC extends _HC_Form
{
	public function route_index()
	{
		$args = $this->make('/app/lib/args')->parse( func_get_args() );
		$what = $args->get('what');
		$to = $args->get('to');

		$app_settings = $this->make('/app/lib/settings');
		switch( $what ){
			case 'front-in-list':
				if( $to == 'reset' ){
					$this_field_pname = 'front_list:template';
					$new_value = '';
					$this_field_conf = $app_settings->run('set', $this_field_pname, $new_value);
				}
				else {
					$this_field_pname = 'front_list:advanced';
					$new_value = ($to == 'advanced') ? 1 : 0;
					$this_field_conf = $app_settings->run('set', $this_field_pname, $new_value);
				}
				break;

			case 'front-on-map':
				if( $to == 'reset' ){
					$this_field_pname = 'front_map:template';
					$new_value = '';
					$this_field_conf = $app_settings->run('set', $this_field_pname, $new_value);
				}
				else {
					$this_field_pname = 'front_map:advanced';
					$new_value = ($to == 'advanced') ? 1 : 0;
					$this_field_conf = $app_settings->run('set', $this_field_pname, $new_value);
				}
				break;
		}

		$redirect_to = $this->make('/html/view/link')
			->to('-referrer-')
			->href()
			;

		return $this->make('/http/view/response')
			->set_redirect($redirect_to) 
			;
	}
}