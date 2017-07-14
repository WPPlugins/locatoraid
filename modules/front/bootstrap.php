<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_Bootstrap_LC_HC_MVC extends _HC_MVC
{
	public function run()
	{
		// $controller = $this->make('controller');
		$view = $this->make('view');

		$app_name = $this->app_name();
		$shortcode = 'locatoraid';
		add_shortcode( $shortcode, array($this, 'view'));
	}

	public function view( $shortcode_atts )
	{
		$params = array();

		if( $shortcode_atts && is_array($shortcode_atts) ){
			foreach( $shortcode_atts as $k => $v ){
				$params[$k] = $v;
			}
		}

		$view = $this->make('view');
		return $view->run('render', $params);

		// $controller = $this->make('controller');
		// return $controller->run('route-index', $params);
	}
}