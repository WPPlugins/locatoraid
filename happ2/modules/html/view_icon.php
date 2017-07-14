<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Icon_HC_MVC extends Html_View_Element_HC_MVC
{
	private $icon = NULL;

	public function icon( $icon = NULL )
	{
		$config_loader = $this->make('/app/lib/config-loader');

		$icon_for = $config_loader->get('icons');
		if( isset($icon_for[$icon]) ){
			$icon = $icon_for[$icon];
		}
		$this->icon = $icon;
		return $this;
	}

	public function render()
	{
		switch( $this->icon ){
			case 'spinner':
				$return = $this->make('view/icon')->icon('spin')
					->run('render')
					->add_attr('class', 'hc-m0')
					->add_attr('class', 'hc-p0')
					;

				$return = $this->make('view/element')->tag('div')
					->add( $return )
					->add_attr('class', 'hc-spin')
					->add_attr('class', 'hc-inline-block')
					->add_attr('class', 'hc-m0')
					->add_attr('class', 'hc-p0')
					;

				// echo $return;
				// exit;
				break;

			default:
				$return = $this->icon;
		}

	// should be extended by concrete icon modules
		return $return;
	}
}
