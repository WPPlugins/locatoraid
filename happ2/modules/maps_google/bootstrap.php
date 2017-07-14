<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Maps_Google_Bootstrap_HC_MVC extends _HC_MVC
{
	public function run()
	{
		$is_me = $this->make('/app/lib')
			->run('is-me')
			;
		if( ! $is_me ){
			return;
		}

		$app_settings = $this->make('/app/lib/settings');
		$api_key = $app_settings->run('get', 'maps_google:api_key');
		if( is_array($api_key) ){
			$api_key = array_shift($api_key);
		}

		if( strlen($api_key) ){
			return;
		}

		$uri = $this->make('/http/lib/uri');

		$slug = $uri->slug();
		
		if( substr($slug, 0, strlen('setup')) == 'setup' ){
			return;
		}

		if( in_array($slug, array('conf/update')) ){
			return;
		}

		if( in_array($slug, array('conf')) ){
			$args = $uri->args();
			if( isset($args['tab']) && ($args['tab'] == 'maps-google') ){
				return;
			}
		}

	// redirect to field edit
		$uri = $this->make('/http/lib/uri')
			->admin()
			->url('conf', array('-tab' => 'maps-google'))
			;	
		$view = $this->make('/http/view/response')
			->set_redirect($uri) 
			;
		echo $view;
		exit;
	}
}