<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Locations_Presenter_LC_HC_MVC extends _HC_MVC_Model_Presenter
{
	public function present_front()
	{
		$return = $this->data();
// _print_r( $e );
// exit;
		// $return = $this->run('export');

		// if( isset($e['distance']) ){
			// $return['distance'] = $e['distance'];
		// }

		// $return['id'] = $e['id'];
		$return['address'] = $this->run('present-address');
		$return['name'] = $this->run('present-title');

	// process to show urls and emails
		foreach( array_keys($return) as $k ){
			$return[$k] = $this->run( 'prepare-front', $k, $return[$k] );
		}

		return $return;
	}

	public function prepare_front( $key, $value )
	{
		$return = $value;

		switch( $key ){
			case 'website':
				$ok = FALSE;
				$value = trim($value);
				if( ! strlen($value) ){
					return;
				}

				$href = $value;
				$prfx = array('http://', 'https://', '//');
				foreach( $prfx as $prf ){
					if( substr($href, 0, strlen($prf)) == $prf ){
						$ok = TRUE;
						break;
					}
				}

				if( ! $ok ){
					// $href = 'http://' . $href;
					$href = '//' . $href;
				}

				$app_settings = $this->make('/app/lib/settings');
				$this_pname = 'fields:website:label';
				$this_label = $app_settings->run('get', $this_pname);
				$this_label = strlen($this_label) ? $this_label : $value;

				$return = '<a href="' . $href . '" target="_blank">' . $this_label . '</a>';
				break;

			default:
				$email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';

				if(
					preg_match('/^misc/', $key) &&
					preg_match('/(\.jpg|\.png|\.gif|\.svg)$/i', $value)
					){
					$return = '<img src="' . $value . '" style="max-width: 95%;">';
				}
				elseif(
					preg_match('/^misc/', $key) &&
					(
					preg_match('/^https?\:\/\//', $key) OR
					preg_match('/^\/\//', $value)
					)
					){
					$return = '<a href="' . $value . '" target="_blank">' . $value . '</a>';
				}
				elseif(
					preg_match('/^misc/', $key) &&
					preg_match($email_regex, $value)
					){
					// $field_view = '<a href="mailto:' . $e[$f['name']] . '" target="_blank">' . $f['title'] . '</a>';
					// $field_view = '<a href="mailto:' . $e[$f['name']] . '" target="_blank">' . $f['title'] . '</a>';
					$return = '<a href="mailto:' . $value . '" target="_blank">' . $value . '</a>';
				}
				break;
		}

		return $return;
	}

	public function present_title()
	{
		$return = $this->data('name');
		return $return;
	}

	public function present_icon()
	{
		$icon_url = $this->run('present-icon-url');
		if( ! $icon_url ){
			$icon_url = '//maps.google.com/mapfiles/ms/micons/red-dot.png';
		}

		$return = $this->make('/html/view/element')->tag('img')
			->add_attr('src', $icon_url)
			->add_attr('style', 'max-width:100%;')
			;
		return $return;
	}

	public function present_icon_url()
	{
		$return = NULL;
		return $return;
	}

	public function present_distance()
	{
		$return = $this->data('distance');
		if( ! $return ){
			return $return;
		}

		$app_settings = $this->make('/app/lib/settings');
		$measure = $app_settings->run('get', 'core:measure');

		if( $return < 1 ){
			$return = ceil( $return * 100 ) / 100;
		}
		elseif( $return < 100 ){
			$return = ceil( $return * 10 ) / 10;
		}
		else {
			$return = ceil( $return );
		}
		$return = $return . ' ' . $measure;

		return $return;
	}

	public function present_address()
	{
		$parts = array();
		$take = array( 'street1', 'street2', 'city', 'state', 'zip', 'country' );
		foreach( $take as $t ){
			$part = $this->data( $t );
			if( strlen($part) ){
				$parts[$t] = $part;
			}
			else {
				$parts[$t] = '';
			}
		}

		$app_settings = $this->make('/app/lib/settings');
		$template = $app_settings->run('get', 'locations_address:format');

		$template = trim($template);

		if( strlen($template) ){
			if( isset($parts['street2']) && strlen($parts['street2']) ){
				$parts['street'] = $parts['street1'] . "\n" . $parts['street2'];
			}
			else {
				$parts['street'] = $parts['street1'];
			}

			$return = $template;
			foreach( $parts as $k => $v ){
				$return = str_replace( '{' . strtoupper($k) . '}', $v, $return );
			}
		}
		else {
			$return = join(', ', $parts);
		}

		$return = trim( $return );
		$return = nl2br( $return );
		return $return;
	}

	public function database_fields()
	{
		$return = array('name', 'street1', 'street2', 'city', 'state', 'zip', 'country', 'phone', 'website', 'latitude', 'longitude');
		return $return;
	}

	public function fields()
	{
		$return = array(
			'name'			=> HCM::__('Name'),
			'address'		=> HCM::__('Address'),
			'distance'		=> HCM::__('Distance'),
			'phone'			=> HCM::__('Phone'),
			'website'		=> HCM::__('Website'),
			);
		return $return;
	}

	public function fields_labels()
	{
		$return = $this->run('fields');
		$app_settings = $this->make('/app/lib/settings');
		$always_show = array('name', 'address');

		$keys = array_keys($return);
		foreach( $keys as $k ){
			if( ! in_array($k, $always_show) ){
				$this_field_pname = 'fields:' . $k  . ':use';
				$this_field_conf = $app_settings->run('get', $this_field_pname);
				if( ! $this_field_conf ){
					unset( $return[$k] );
					continue;
				}
			}

			$this_field_pname = 'fields:' . $k  . ':label';
			$this_label = $app_settings->run('get', $this_field_pname);
			if( strlen($this_label) ){
				$return[ $k ] = $this_label;
			}
		}
		return $return;
	}
}