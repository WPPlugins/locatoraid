<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Duration2_HC_MVC extends HC_Form_Input2
{
	protected $allowed_options = array('minutes', 'hours', 'days', 'weeks');

	public function allowed_options()
	{
		return $this->allowed_options;
	}

	public function set_allowed_options( $allowed_options )
	{
		$this->allowed_options = $allowed_options;
		return $this;
	}

	function set_name( $name )
	{
		parent::set_name( $name );
		return $this->do_init();
	}

	public function do_init()
	{
	// already set
		if( isset($this->fields['units']) ){
			return $this;
		}

		$name = $this->name();
		$inputs = array();

		$inputs['units'] = $this
			// ->make('view/radio')
			// ->set_inline()
			->make('view/select')
			->set_name($name . '_units')
			;

		$allowed_options = $this->allowed_options();
		$all_options = array(
			'minutes'	=> HCM::__('Minutes'),
			'hours'		=> HCM::__('Hours'),
			'days'		=> HCM::__('Days'),
			'weeks'		=> HCM::__('Weeks'),
			'months'	=> HCM::__('Months'),
			);

		foreach( $all_options as $opt => $opt_label ){
			if( in_array($opt, $allowed_options) ){
				$inputs['units']->add_option( $opt, $opt_label );
			}
		}

		if( in_array('minutes', $allowed_options) ){
			$inputs['measure_minutes'] = $this
				->make('view/select')
				->set_name($name . '_measure_minutes')
				->set_options_flat(
					array(5,10,15,20,25,30,35,40,45,50,55,60)
					)
				->add_attr('data-hc-observe', $name . '_units=minutes')
				;
		}

		if( in_array('hours', $allowed_options) ){
			$inputs['measure_hours'] = $this
				->make('view/select')
				->set_name($name . '_measure_hours')
				->set_options_flat(
					// array(1,1.5,2,2.5,3,3.5,4,4.5,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24)
					array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24)
					)
				->add_attr('data-hc-observe', $name . '_units=hours')
				;
		}

		if( in_array('days', $allowed_options) ){
			$inputs['measure_days'] = $this
				->make('view/select')
				->set_name($name . '_measure_days')
				->set_options_flat(
					array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30)
					)
				->add_attr('data-hc-observe', $name . '_units=days')
				;
		}

		if( in_array('weeks', $allowed_options) ){
			$inputs['measure_weeks'] = $this
				->make('view/select')
				->set_name($name . '_measure_weeks')
				->set_options_flat(
					array(1,2,3,4,5,6,7,8,9,10)
					)
				->add_attr('data-hc-observe', $name . '_units=weeks')
				;
		}

		if( in_array('months', $allowed_options) ){
			$inputs['measure_months'] = $this
				->make('view/select')
				->set_name($name . '_measure_months')
				->set_options_flat(
					array(1,2,3,4,5,6,7,8,9,10,11,12)
					)
				->add_attr('data-hc-observe', $name . '_units=months')
				;
		}

		foreach( $inputs as $n => $i ){
			$this->fields[$n] = $i;
		}

	// default value
		$unit_opions = array_keys( $inputs['units']->options() );
		$default_unit = array_shift( $unit_opions );

		$qty_opions = array_keys( $inputs['measure_' . $default_unit]->options() );
		$default_qty = array_shift( $qty_opions );

		$multiplies = $this->get_multiplies();
		$multiply = $multiplies[ $default_unit ];

		// $default = $default_qty . ' ' . $default_unit;
		$default = $default_qty * $multiply;
// $default = 14*24*60*60;
		$this->set_value( $default );

		return $this;
	}

	public function render()
	{
		$value = $this->value();
		$values = array();
		if( $value ){
			$multiplies = $this->get_multiplies();
			$value_units = NULL;
			$value_measure = NULL;

		// will try to find the units
			foreach( $multiplies as $k => $m ){
				if( $m > $value ){
					// echo "$m > $value<br>";
					continue;
				}
				if( $value % $m ){
					// echo "$m NOT OK FOR $value<br>";
					continue;
				}

				$value_units = $k;
				$value_measure = $value / $m;
				break;
			}
// echo "VAL_MEASURE = $value_measure, VAL_UNITS = $value_units<br>";
// exit;
			// list( $value_measure, $value_units ) = explode( ' ', $value );
			$values['units'] = $value_units;
			$values['measure_' . $value_units] = $value_measure;
		}

		if( $values ){
			reset( $this->fields );
			foreach( $this->fields as $fn => $f ){
				if( array_key_exists($fn, $values) ){
					$this->fields[$fn]->set_value($values[$fn]);
				}
			}
		}

		$wrap = $this->make('/html/view/list-inline')
			->add_attr('class', 'hc-nowrap')
			;

		$measures = array();
		reset( $this->fields );
		foreach( $this->fields as $fn => $f ){
			if( substr($fn, 0, strlen('measure_')) == 'measure_' ){
				$measures[] = $f;
			}
		}

		$wrap->add( $measures );

		// $wrap->add( '-' );

		$wrap->add( 
			$this->fields['units']
			);

		return $this->decorate( $wrap );
	}

	public function get_multiplies()
	{
		$return = array(
			'weeks'		=> 7*24*60*60,
			'days'		=> 24*60*60,
			'hours'		=> 60*60,
			'minutes'	=> 60,
			);
		return $return;
	}

	public function grab( $post )
	{
		$value = NULL;

		$this->fields['units']->grab( $post );
		$units = $this->fields['units']->value();

		$measure_name = 'measure_' . $units;
		if( isset($this->fields[$measure_name]) ){
			$this->fields[$measure_name]->grab($post);
			$measure = $this->fields[$measure_name]->value();
			$value = $measure . ' ' . $units;

			$multiplies = $this->get_multiplies();
			if( isset($multiplies[$units]) ){
				$multiply = $multiplies[$units];
				$value = $measure * $multiply;
			}
		}

		$this->set_value( $value );
		return $this;
	}
}