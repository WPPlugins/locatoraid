<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Date_Nav_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $param_names = array('date_from' => 'datefrom', 'date_to' => 'dateto');
	protected $params = array('date_from' => NULL, 'date_to' => NULL);

	public function set_param_name( $param, $name )
	{
		$this->param_names[$param] = $name;
		return $this;
	}
	public function param_name( $param )
	{
		return $this->param_names[$param];
	}

	public function set_param( $param, $value )
	{
		$this->params[$param] = $value;
		return $this;
	}
	public function param( $param )
	{
		if( isset($this->params[$param]) ){
			$return = $this->params[$param];
		}
		else {
			$return = $this->default_value($param);
		}
		return $return;
	}

	public function default_value( $param )
	{
		$return = NULL;

		switch( $param ){
			case 'date_from':
				$t = $this->make('/app/lib')->run('time');
				$t->setNow();
				$return = $t->formatDateDb();
				break;

			case 'date_to':
				$date_from = $this->param('date_from');
				$t = $this->make('/app/lib')->run('time');
				$t->setDateDb( $date_from );
				$t->modify('+6 days');
				$return = $t->formatDateDb();
				break;
		}
		return $return;
	}

	public function _init()
	{
		$params = array('date_from', 'date_to');

		$post = $this->make('/input/lib')->post();
		if( $post ){
			$form = $this->form();
			$form->grab( $post );
			$values = $form->values();

			$args = array();
			reset( $params );
			foreach( $params as $p ){
				$pname = $this->param_name($p);
				if( isset($values[$pname]) ){
					$args[$pname] = $values[$pname];
				}
			}

			$redirect_to = $this->make('/html/view/link')
				->to( '-', $args )
				->href()
				;

			echo $this->make('/http/view/response')
				->set_redirect($redirect_to) 
				;
			exit;
		}

		$uri = $this->make('/http/lib/uri');
		$args = array();

		foreach( $params as $p ){
			$pname = $this->param_name($p);
			if( $uri->arg($pname) ){
				$this->set_param( $p, $uri->arg($pname) );
			}
		}

		return $this;
	}

	public function form()
	{
		$return = new _HC_Form;

		$return
			->set_input( 
				$this->param_name('date_from'), 
				$this->make('/datepicker/input')
					->set_value( $this->param('date_from') )
				)

			->set_input( 
				$this->param_name('date_to'), 
				$this->make('/datepicker/input')
					->set_value( $this->param('date_to') )
				)
			;

		return $return;
	}

	public function render()
	{
		$t = $this->make('/app/lib')->run('time');

		$date_from = $this->param('date_from');
		$date_to = $this->param('date_to');

		$date_range_view = $t->formatDateRange( $date_from, $date_to, TRUE );
		$date_range_view = $this->make('/html/view/element')->tag('div')
			->add( $date_range_view )
			// ->add_attr('class', 'hc-btn')
			->add_attr('class', 'hc-p2')
			;

	// form view
		$form = $this->form();

		$link = $this->make('/html/view/link')
			->to('-')
			->href()
			;
		$display_form = $this->make('/html/view/form')
			->add_attr('action', $link )
			->set_form( $form )
			;

		$date_inputs = $this->make('/html/view/list')
			;
		$btn_save = $this->make('/html/view/element')->tag('input')
			->add_attr('type', 'submit')
			->add_attr('title', HCM::__('OK') )
			->add_attr('value', HCM::__('OK') )
			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
			->add_attr('class', 'hc-block-xs')
			;

		$date_inputs
			->add( 
				$this->make('/html/view/label-input')
					->set_label( HCM::__('From Date') )
					->set_content( $form->input( $this->param_name('date_from') ) )
				)
			->add( 
				$this->make('/html/view/label-input')
					->set_label( HCM::__('To Date') )
					->set_content( $form->input( $this->param_name('date_to') ) )
				)
			->add( $btn_save )
			;
		$display_form
			->add( $date_inputs )
			;

	// finally range view
		$date_range = $this->make('/html/view/collapse')
			->set_title( $date_range_view )
			->set_content( $display_form )
			;

	// arrows
		$start_ts = $t->setDateDb($date_from)->getTimestamp();
		$end_ts = $t->setDateDb($date_to)->getTimestamp();
		$duration = $end_ts - $start_ts;

		$t->setDateDb($date_to)
			->modify('+1 day')
			;
		$next_date_from = $t->formatDateDb();

	// check if it's full month
		$full_month = FALSE;
		$t->setDateDb($date_from);
		$month1 = $t->getMonth();
		$t->modify('-1 day');
		$month2 = $t->getMonth();
		if( $month1 != $month2 ){
			$t->setDateDb($date_to);
			$month3 = $t->getMonth();
			$t->modify('+1 day');
			$month4 = $t->getMonth();
			if( $month3 != $month4 ){
				$rex_date = $t->setDateDb($date_from)
					->modify('+1 month')
					->formatDateDb()
					;
				$full_month = 1;
				while( $rex_date <= $date_to ){
					$full_month++;
					$rex_date = $t
						->modify('+1 month')
						->formatDateDb()
						;
				}
			}
		}

		$t->setDateDb($next_date_from);
		if( $full_month ){
			$t->modify('+' . $full_month . ' months');
			$t->modify('-1 day');
		}
		else {
			$t->modify('+' . $duration . ' seconds');
		}
		$next_date_to = $t->formatDateDb();

		$t->setDateDb($date_from)
			->modify('-1 day')
			;
		$prev_date_to = $t->formatDateDb();

		if( $full_month ){
			$t->modify('-' . $full_month . ' months');
			$t->modify('+1 day');
		}
		else {
			$t->modify('-' . $duration . ' seconds');
		}
		$prev_date_from = $t->formatDateDb();

		$arrows = $this->make('/html/view/list-inline')
			->add_attr('class', 'hc-mt1')
			;
		$arrow_next = $this->make('/html/view/link')
			->to('-', 
				array(
					$this->param_name('date_from')	=> $next_date_from,
					$this->param_name('date_to')	=> $next_date_to,
					)
				)
			->add( '&gt;' )

			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
		// imitate wordpress
			// ->add_attr('class', 'page-title-action')

			// ->add_attr('class', 'hc-btn')
			// ->add_attr('class', 'hc-py2')
			// ->add_attr('class', 'hc-px3')
			// ->add_attr('class', 'hc-rounded')
			// ->add_attr('class', 'hc-mr1')
			// ->add_attr('class', 'hc-align-center')
			// ->add_attr('class', 'hc-bg-silver')
			;

		$arrow_prev = $this->make('/html/view/link')
			->to('-', 
				array(
					$this->param_name('date_from')	=> $prev_date_from,
					$this->param_name('date_to')	=> $prev_date_to,
					)
				)
			->add( '&lt;' )

			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
		// imitate wordpress
			// ->add_attr('class', 'page-title-action')

			// ->add_attr('class', 'hc-btn')
			// ->add_attr('class', 'hc-py2')
			// ->add_attr('class', 'hc-px3')
			// ->add_attr('class', 'hc-rounded')
			// ->add_attr('class', 'hc-mr1')
			// ->add_attr('class', 'hc-align-center')
			// ->add_attr('class', 'hc-bg-silver')
			;

		$arrows
			->add( $arrow_prev )
			->add( $arrow_next )
			;

		$out = $this->make('/html/view/list')
			;

		$out2 = $this->make('/html/view/list-inline')
			->set_gutter(2)
			->add( $date_range->render_trigger() )
			->add( $arrows )
			;

		$out
			->add( $out2 )
			->add( $date_range->render_content() )
			;

		return $out;
	}
}