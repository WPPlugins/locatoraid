<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Duration_HC_MVC extends Form_View_Select_HC_MVC
{
	function __construct( $name = '' )
	{
		parent::__construct( $name );
		$step = 15 * 60;

		$start_with = $step;
		$end_with = 24 * 60 * 60;

		if( $end_with < $start_with ){
			$end_with = $start_with;
		}

		$options = array();

		$t = $this->make('/app/lib')->run('time');
		$t->setDateDb( 20130118 );

		if( $start_with )
			$t->modify( '+' . $start_with . ' seconds' );

		$no_of_steps = ( $end_with - $start_with) / $step;
		for( $ii = 0; $ii <= $no_of_steps; $ii++ ){
			$sec = $start_with + $ii * $step;
			// $options[ $sec ] = $t->formatPeriod( $sec - $start_with );
			// $options[ $sec ] = $t->formatPeriodShort( $sec - $start_with, 'hour' );
			$options[ $sec ] = $t->formatPeriodShort( $sec, 'hour' );
			$t->modify( '+' . $step . ' seconds' );
		}

		$this->set_options( $options );
	}
}