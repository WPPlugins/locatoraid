<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Pager_HC_MVC extends _HC_MVC
{
	protected $total_count;
	protected $per_page = 10;
	protected $current_page = 1;
	protected $link_template = NULL;

	public function set_link_template( $link_template )
	{
		$this->link_template = $link_template;
		return $this;
	}
	public function link_template()
	{
		if( ! $this->link_template ){
			$this->link_template = $this->make('/html/view/link')
				->always_show()
				->add_attr('class', 'hc-theme-btn-submit', 'hc-theme-btn-secondary')
				;
		}
		return clone $this->link_template;
	}
	
	public function render()
	{
		$out = $this->make('/html/view/list-inline')
			->set_gutter(1)
			;

		$parts = array();
		$disable = array();

		$parts['first'] = $this->link_template()
			->to('-', array('-page' => 1))
			->add( '&lt;&lt;' )
			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
			->add_attr('class', 'hc-mt1')
			// ->add_attr('class', 'hc-p2')
			// ->add_attr('class', 'hc-m0')
			;

		$parts['previous'] = $this->link_template()
			->to('-', array('-page' => ($this->current_page() - 1)))
			->add( '&lt;' )
			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
			->add_attr('class', 'hc-mt1')
			// ->add_attr('class', 'hc-theme-btn-submit', 'hc-theme-btn-secondary')
			// ->add_attr('class', 'hc-p2')
			// ->add_attr('class', 'hc-m0')
			;

		if( $this->current_page() == 1 ){
			$disable[] = 'first';
			$disable[] = 'previous';
		}

		if( $this->current_page() == 2 ){
			$disable[] = 'first';
		}

		$parts['current'] = $this->make('/html/view/element')->tag('span')
			->add( $this->current_page() . ' / ' . $this->number_of_pages() )
			->add_attr('class', 'hc-inline-block')
			->add_attr('class', 'hc-btn')
			->add_attr('class', 'hc-p2')
			->add_attr('class', 'hc-m0')
			// ->add_attr('class', 'hc-border')
			;

		$parts['next'] = $this->link_template()
			->to('-', array('-page' => ($this->current_page() + 1)))
			->add( '&gt;' )
			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
			->add_attr('class', 'hc-mt1')
			// ->add_attr('class', 'hc-p2')
			// ->add_attr('class', 'hc-m0')
			;

		$parts['last'] = $this->link_template()
			->to('-', array('-page' => $this->number_of_pages()))
			->add( '&gt;&gt;' )
			->add_attr('class', 'hc-theme-btn-submit')
			->add_attr('class', 'hc-theme-btn-secondary')
			->add_attr('class', 'hc-mt1')
			// ->add_attr('class', 'hc-p2')
			// ->add_attr('class', 'hc-m0')
			;

		if( $this->current_page() == $this->number_of_pages() ){
			$disable[] = 'next';
			$disable[] = 'last';
		}
		if( $this->current_page() == ($this->number_of_pages() - 1) ){
			$disable[] = 'last';
		}

		reset( $disable );
		foreach( $disable as $d ){
			$parts[$d]
				->add_attr('class', 'hc-muted-3')
				->set_readonly()
				;
		}

		reset( $parts );
		foreach( $parts as $p ){
			$out->add( $p );
		}

		return $out;
	}

	public function number_of_pages()
	{
		if( ($this->per_page() == 0) || ($this->total_count() == 0) ){
			$return = 1;
		}
		else {
			$return = ceil( $this->total_count() / $this->per_page() );
		}

		return $return;
	}

	public function set_total_count( $total_count )
	{
		$this->total_count = $total_count;
		return $this;
	}
	public function total_count()
	{
		return $this->total_count;
	}
	public function set_per_page( $per_page )
	{
		$this->per_page = $per_page;
		return $this;
	}
	public function per_page()
	{
		return $this->per_page;
	}
	public function set_current_page( $current_page )
	{
		$this->current_page = $current_page;
		return $this;
	}
	public function current_page()
	{
		return $this->current_page;
	}
}