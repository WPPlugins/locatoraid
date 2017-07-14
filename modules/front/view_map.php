<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_View_Map_LC_HC_MVC extends _HC_MVC
{
	public function render( $params = array() )
	{
		$out = $this->make('/html/view/container');

		$style = array_key_exists('map-style', $params) ? $params['map-style'] : NULL;
		$map_id = 'hclc_map';
		$div = $this->make('/html/view/element')->tag('div')
			->add_attr('id', $map_id)
			->add_attr('class', 'hc-mb3-xs')
			->add_attr('class', 'hc-border')

			->add_attr('style', $style)
			;

		$div
			// ->add_attr('style', 'display: none;')
			;

		$app_settings = $this->make('/app/lib/settings');
		$template = $app_settings->run('get', 'front_map:template');

		$template = $this->make('/html/view/element')->tag('script')
			->add_attr('type', 'text/template')
			->add_attr('id', 'hclc_map_template')
			->add( $template )
			;

		$out
			->add( $div )
			->add( $template )
			;

		return $out;
	}
}