<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Time_HC_MVC extends Form_View_Select_HC_MVC
{
	protected static $time_options = array();

	function set_name( $name )
	{
		parent::set_name( $name );
		return $this->do_init();
	}

	function do_init()
	{
		$app_settings = $this->make('/app/lib/settings');
		$start_with = 0;
		$end_with = 24 * 60 * 60;

		$time_min = $app_settings->run('get', 'time_min');
		if( $time_min !== NULL ){
			$this->set_conf('min', $time_min);
		}
		else {
			$this->set_conf('min', 0);
		}

		$time_max = $app_settings->run('get', 'time_max');
		if( $time_max !== NULL ){
			$this->set_conf('max', $time_max);
		}
		else {
			$this->set_conf('max', 24*60*60);
		}

		if( ! self::$time_options ){
			$start_with = $this->conf('min');
			$end_with = $this->conf('max');

			if( $end_with < $start_with ){
				$end_with = $start_with;
			}

			$step = 5 * 60;
			$options = array();

			$t = $this->make('/app/lib')->run('time');
			$t->setDateDb( 20130118 );

			if( $start_with ){
				$t->modify( '+' . $start_with . ' seconds' );
			}

			$no_of_steps = ( $end_with - $start_with) / $step;
			for( $ii = 0; $ii <= $no_of_steps; $ii++ ){
				$sec = $start_with + $ii * $step;
				$options[ $sec ] = $t->formatTime();
				$t->modify( '+' . $step . ' seconds' );
			}
			self::$time_options = $options;
		}

		$this->set_options( self::$time_options );

		$options = $this->options();
		$options = array_keys( $options );
		$value = array_shift( $options );
		$this->set_value( $value );

		return $this;
	}
}