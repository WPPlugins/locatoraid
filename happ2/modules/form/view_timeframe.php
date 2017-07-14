<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Timeframe_HC_MVC extends HC_Form_Input_Composite2
{
	function set_name( $name )
	{
		parent::set_name( $name );
		return $this->do_init();
	}

	public function do_init()
	{
	// already set
		if( isset($this->fields['start']) && isset($this->fields['end']) ){
			return $this;
		}
		$name = $this->name();
		$this->fields['start'] = $this->make('view/time')->set_name($name . '_start');
		$this->fields['end'] = $this->make('view/time')->set_name($name . '_end');

		return $this;
	}

	public function remove_option( $where, $what )
	{
		if( isset($this->fields[$where]) ){
			$this->fields[$where]->remove_option($what);
		}
		return $this;
	}

	public function render()
	{
		$wrap = $this->make('/html/view/list-inline')
			->add_attr('class', 'hc-nowrap')
			;
		$wrap->add( 
			$this->fields['start']
			);

		$wrap->add( '-' );
		$wrap->add( 
			$this->fields['end']
			);

		return $this->decorate( $wrap );
	}
}