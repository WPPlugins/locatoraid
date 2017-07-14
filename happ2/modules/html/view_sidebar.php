<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
include_once( dirname(__FILE__) . '/view_list.php' );
class Html_View_Sidebar_HC_MVC extends Html_View_List_HC_MVC
{
	public function render()
	{
		$items = $this->children();
		$keys = array_keys($items);
		foreach( $keys as $k ){
			if( is_object($items[$k]) ){
				if( method_exists($items[$k], 'add_attr') ){
					$items[$k]
						->admin()
						->add_attr('class', 'hc-theme-block-link')
						->add_attr('class', 'hc-mb1')
						;
				}
				if( method_exists($items[$k], 'admin') ){
					$items[$k]
						->admin()
						;
				}
			}
		}

		$this->set_children( $items );
		return parent::render();
	}
}