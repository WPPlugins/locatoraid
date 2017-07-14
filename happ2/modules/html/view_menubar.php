<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
include_once( dirname(__FILE__) . '/view_list_inline.php' );
class Html_View_Menubar_HC_MVC extends Html_View_List_Inline_HC_MVC
{
	public function render()
	{
		$items = $this->children();

		$keys = array_keys($items);
		$out = $this->make('/html/view/list-inline')
			->set_gutter(2)
			;

		foreach( $keys as $k ){
			if( is_object($items[$k]) ){
				if( method_exists($items[$k], 'add_attr') ){
					$items[$k]
						->add_attr('class', 'hc-theme-tab-link')
						;
				}
				if( method_exists($items[$k], 'admin') ){
					$items[$k]
						->admin()
						;
				}
			}
		}
		$out->set_children( $items );

		return $out;
	}
}