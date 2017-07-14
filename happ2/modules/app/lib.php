<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class App_Lib_HC_MVC extends _HC_MVC
{
	public function is_me()
	{
		return TRUE;
	}

	public function time()
	{
		if( ! class_exists('HC_Time') ){
			$this->make('/time/lib');
		}

		static $return = NULL;
		if( $return !== NULL ){
			return $return;
		}

		$return = new HC_Time;
		$conf = $this->make('lib/settings');

		if( $conf ){
			$week_starts_on = $conf->get('datetime:week_starts');
			if( $week_starts_on !== NULL ){
				$return->weekStartsOn = $week_starts_on;
			}

			$time_format = $conf->get('datetime:time_format');
			if( $time_format ){
				$return->timeFormat = $time_format;
			}

			$date_format = $conf->get('datetime:date_format');
			if( $date_format ){
				$return->dateFormat = $date_format;
			}

			// $tz = $conf->get('timezone');
			$tz = 'UTC';
			if( $tz ){
				$return->setTimezone( $tz );
			}
		}

		return $return;
	}
}
