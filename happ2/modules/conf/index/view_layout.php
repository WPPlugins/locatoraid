<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Conf_Index_View_Layout_HC_MVC extends _HC_MVC
{
	public function header( $tabs, $current_tab = NULL )
	{
		$return = HCM::__('Settings');
		if( isset($tabs[$current_tab]) && isset($tabs[$current_tab]['_label']) ){
			$this_tab_label = $tabs[$current_tab]['_label'];
			$return .= ': ' . $this_tab_label;

			// $return = $this->make('/html/view/container')
				// ->add( $this->make('/html/view/icon')->icon('cog') )
				// ->add( $this_tab_label )
				// ;
		}
		else {
			$return = HCM::__('Settings');
		}

		return $return;
	}

	public function menubar( $tabs, $current_tab = NULL )
	{
		$menubar = $this->make('/html/view/container');

		foreach( $tabs as $this_tab => $tab_props ){
			$this_tab_label = isset($tabs[$this_tab]['_label']) ? $tabs[$this_tab]['_label'] : $this_tab;

			$link = $this->make('/html/view/link')
				->to('', array('tab' => $this_tab))
				->set_persist( FALSE )
				->add( $this_tab_label )
				;
			if( $this_tab == $current_tab ){
				$link
					->add_attr('class', 'hc-theme-btn-submit')
					->add_attr('class', 'hc-theme-btn-primary')
					;
			}

			$menubar->add(
				$this_tab,
				$link
				);
		}

		return $menubar;
	}

	public function render( $content, $tabs, $current_tab = NULL )
	{
		$header = $this->run('header', $tabs, $current_tab);
		$menubar = $this->run('menubar', $tabs, $current_tab);

		$out = $this->make('/layout/view/content-header-menubar')
			->set_content( $content )
			->set_header( $header )
			->set_menubar( $menubar )
			;

		return $out;
	}
}