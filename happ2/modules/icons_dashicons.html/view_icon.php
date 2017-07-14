<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Icons_Dashicons_Html_View_Icon_HC_MVC extends Html_View_Element_HC_MVC
{
	private $convert = array(
		'networking'	=> 'networking',
		'star-o'	=> 'star-empty',
		// 'plus'		=> 'plus-alt', // simple plus appears off center vertically
		'cog'		=> 'admin-generic',
		'user'		=> 'admin-users',
		'group'		=> 'groups',
		'times'		=> 'dismiss',
		'check'		=> 'yes',
		'status'	=> 'post-status',
		'list'		=> 'editor-ul',
		'history'	=> 'book',
		'exclamation'	=> 'warning',
		'printer'		=> 'media-text',
		'home'			=> 'admin-home',
		'star'			=> 'star-filled',

		'purchase'		=> 'products',
		'sale'			=> 'cart',
		'inventory'		=> 'admin-page',
		'copy'			=> 'admin-page',
		'chart'			=> 'chart-bar',
		'message'		=> 'email',
		'holidays'		=> 'palmtree',
		'connection'	=> 'admin-links',
		'view'			=> 'visibility',
	);

	public function extend_render($icon, $params, $src)
	{
	// <span class="oi oi-icon-name" title="icon name" aria-hidden="true"></span>

		$icon = isset($this->convert[$icon]) ? $this->convert[$icon] : $icon;
		if( $icon && strlen($icon) ){
			if( substr($icon, 0, 1) == '&' ){
				$return = $this->make('/html/view/element')->tag('span')
					->add( $icon )
					->add_attr('class', 'hc-mr1')
					->add_attr('class', 'hc-ml1')
					->add_attr('class', 'hc-char')
					;
			}
			else {
				$return = $this->make('/html/view/element')->tag('i');
				$return
					->add_attr('class', array('dashicons', 'dashicons-' . $icon, 'hc-dashicons'))
					;
			}
		}

		$attr = $src->attr();
		foreach( $attr as $k => $v ){
			$return->add_attr( $k, $v );
		}
// echo $return;
// exit;
		return $return;
	}
}