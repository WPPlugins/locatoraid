<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Maps_Google_Conf_Input_Map_Style_HC_MVC extends HC_Form_Input2
{
	protected $input = NULL;

	public function _init()
	{
		$this->input = $this->make('/form/view/textarea')
			->set_name( $this->name() )
			->add_attr('class', 'hc-block')
			->add_attr('rows', '10')
			->add_attr('class', 'hcj2-map-style')
			;

		return $this;
	}

	public function set_value( $value )
	{
		parent::set_value( $value );
		$this->input
			->set_value( $value )
			;
		return $this;
	}

	public function set_name( $name )
	{
		$this->input
			->set_name( $name )
			;
		return $this;
	}

	public function render()
	{
	// add javascript
		$this->make('/app/enqueuer')
			->run('register-script', 'hc-maps-google-style-preview', 'happ2/modules/maps_google.conf/assets/js/preview.js')
			->run('enqueue-script', 'hc-maps-google-style-preview')
			;

		$input = $this->input
			->run('render')
			;

		$preview_button = $this->make('/html/view/element')->tag('button')
			->add( HCM::__('Map Style Preview') )
			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
			->add_attr('class', 'hc-block')
			->add_attr('class', 'hcj2-map-preview')
			;

	// map preview
		$map_id = 'hclc_map';
		$map = $this->make('/html/view/element')->tag('div')
			->add_attr('id', $map_id)
			->add_attr('class', 'hc-border')
			->add_attr('style', 'height: 14rem;')
			;

		$out = $this->make('/html/view/grid')
			->set_gutter(2)
			;

		$out
			->add( $input, 5 )
			->add( $preview_button, 2 )
			->add( $map, 5 )
			;

		return $out;
	}

	public function grab( $post )
	{
		$this->input
			->grab( $post )
			;
		$value = $this->input
			->value()
			;

		$this->set_value( $value );
		return $this;
	}
}