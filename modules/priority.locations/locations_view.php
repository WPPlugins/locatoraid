<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Priority_Locations_Locations_View_LC_HC_MVC extends _HC_MVC
{
	public function after_prepare_header( $return )
	{
		$app_settings = $this->make('/app/lib/settings');
		$this_field_pname = 'fields:' . 'priority'  . ':use';
		$this_field_conf = $app_settings->run('get', $this_field_pname);
		if( ! $this_field_conf ){
			return $return;
		}

		$return['priority'] = HCM::__('Priority');
		return $return;
	}

	public function after_prepare_row( $return, $args, $src )
	{
		$app_settings = $this->make('/app/lib/settings');
		$this_field_pname = 'fields:' . 'priority'  . ':use';
		$this_field_conf = $app_settings->run('get', $this_field_pname);
		if( ! $this_field_conf ){
			return $return;
		}

		$e = array_shift( $args );

		$p = $this->make('/priority/presenter');
		$options = $p->run('present-options');

		if( isset($options[$e['priority']]) ){
			$this_view = $options[$e['priority']];
		}
		else {
			$this_view = $options[1];
		}

		$return['priority'] = $this_view;

		return $return;
	}
}