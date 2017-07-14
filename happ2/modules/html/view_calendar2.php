<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Calendar2_HC_MVC extends Html_View_Element_HC_MVC
{
	private $date = '';
	private $end_date = '';
	private $content = array();
	private $show_other = FALSE;
	private $show_weekdays = TRUE;
	private $default_date_content = NULL;

	public function _init()
	{
		$t = $this->make('/app/lib')->run('time');
		$t->setNow();
		$this->set_date( $t->formatDate_Db() );
		return $this;
	}
	
	public function set_default_date_content( $c )
	{
		$this->default_date_content = $c;
	}
	public function default_date_content()
	{
		return $this->default_date_content;
	}

	public function set_show_other( $show_other = TRUE )
	{
		$this->show_other = $show_other;
		return $this;
	}
	public function show_other()
	{
		return $this->show_other;
	}

	public function set_show_weekdays( $show_weekdays = TRUE )
	{
		$this->show_weekdays = $show_weekdays;
		return $this;
	}
	public function show_weekdays()
	{
		return $this->show_weekdays;
	}

	function dates()
	{
		$t = $this->make('/app/lib')->run('time');

		$start_date = $this->date();
		$end_date = $this->end_date();

		if( $end_date ){
			$return = array();
			$t->setDateDb( $start_date );
			$rex_date = $t->formatDate_Db();
			while( $rex_date <= $end_date ){
				$return[] = $rex_date;
				$t->modify('+1 day');
				$rex_date = $t->formatDate_Db();
			}
		}
		else {
			$t->setDateDb( $start_date );
			$return = $t->getDates( 'month' );
		}

		return $return;
	}

	function set_date_content( $date, $content )
	{
		$this->content[$date] = $content;
		return $this;
	}
	function date_content( $date )
	{
		return isset($this->content[$date]) ? $this->content[$date] : NULL;
	}

	function set_date( $date )
	{
		$this->date = $date;
		return $this;
	}
	function date()
	{
		return $this->date;
	}

	function set_end_date( $end_date )
	{
		$this->end_date = $end_date;
		return $this;
	}
	function end_date()
	{
		return $this->end_date;
	}

	function render()
	{
		$t = $this->make('/app/lib')->run('time');

		$start_date = $this->date();
		$end_date = $this->end_date();

		$months = array();

		$t->setDateDb( $start_date );
		$t->setStartMonth();
		$months[] = $t->formatDate_Db();
		$t->setEndMonth();
		$rex_date = $t->formatDate_Db();

		while( $rex_date < $end_date ){
			$t->modify('+1 day');
			$t->setStartMonth();
			$months[] = $t->formatDate_Db();
			$t->setEndMonth();
			$rex_date = $t->formatDate_Db();
		}

		$full_out = $this->make('view/list-inline');

		$show_other = $this->show_other();
		$show_weekdays = $this->show_weekdays();

		$ROWS = array();

		foreach( $months as $start_month_day ){
			$t->setDateDb( $start_month_day );
			$month_matrix = $t->getMonthMatrix();

			$t->setDateDb( $start_month_day );
			$start_month = $t->formatDate_Db();
			$t->setEndMonth();
			$end_month = $t->formatDate_Db();

			$out = $this->make('view/table')
				// ->add_attr('style', 'table-layout: fixed; width: 14em;')
				;

			$rid = 0;
			if( $show_weekdays ){
				$row = array();
				$cid = 0;
				foreach( $month_matrix[0] as $date ){
					$t->setDateDb( $date );

					$cell_content = $t->formatWeekDayShort();
					$cell_content = $this->make('view/element')->tag('div')
						->add_attr('class', 'hc-muted-2')
						->add_attr('class', 'hc-fs2')
						->add_attr('class', 'hc-align-center')
						->add_attr('class', 'hc-border-bottom')
						->add( $cell_content )
						;

					$row[] = $cell_content;
					$cid++;
				}
				$rid++;
				$ROWS[] = $row;
			}

			foreach( $month_matrix as $week => $week_dates ){
				$cid = 0;
				$row = array();
				foreach( $week_dates as $date ){
					if( ! $show_other && (($date > $end_month) OR ($date < $start_month)) ){
						$cell_content = '';
						/* empty cell */
						if( ! $show_weekdays ){
							// $out->add_cell_attr( $rid, $cid,
								// array(
									// 'class'	=> array('noborder'),
									// )
								// );
						}
					}
					else {
						$t->setDateDb( $date );

						$cell_content = $this->date_content($date);
						if( $cell_content === NULL ){
							$default_date_content = $this->default_date_content();
							if( is_object($default_date_content) ){
								$cell_content = clone $default_date_content;
							}
							else {
								$cell_content = $default_date_content;
							}
						}

						if( is_object($cell_content) ){
							$cell_content
								->add( $t->getDayShort() )
								;
						}
						elseif( $cell_content !== NULL ){
						}
						else {
							$cell_content = $t->getDayShort();
						}
					}

					// $out->set_cell( $rid, $cid,
						// $cell_content
						// );
					$row[] = $cell_content;
					// $out->add_cell_attr( $rid, $cid,
						// array(
							// 'style'	=> 'width: 14.2857%; white-space: nowrap;',
							// )
						// );

					$cid++;
				}
				$rid++;
				$ROWS[] = $row;
			}

			$out->set_rows( $ROWS );
			
			$month_out = $this->make('view/list')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-rounded')
				->add_attr('class', 'hc-p1')
				;

			$t->setDateDb( $start_month_day );
			$month_label = $t->getMonthName() . ' ' . $t->getYear();

			$month_out->add('label', $month_label );
			$month_out->add('calendar', $out );

			$full_out->add(
				'month_' . $start_month_day,
				$month_out
				);
		}

		return $full_out;
	}
}