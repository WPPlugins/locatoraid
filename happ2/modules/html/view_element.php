<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Element_HC_MVC extends _HC_MVC
{
	protected $readonly = FALSE;

	protected $tag = 'input';
	protected $children = array();
	protected $attr = array();

	protected $observe = NULL;
	protected $children_order = array();

	function __construct()
	{
	}

	public function set_child_order( $child_key, $order )
	{
		$this->children_order[ $child_key ] = $order;
		return $this;
	}

	public function set_readonly( $readonly = TRUE )
	{
		$this->readonly = $readonly;
		return $this;
	}
	public function readonly()
	{
		return $this->readonly;
	}

	function set_observe( $observe )
	{
		$this->observe = $observe;
		return $this;
	}
	function observe()
	{
		return $this->observe;
	}

	function tag( $set = NULL )
	{
		if( $set === NULL ){
			return $this->tag;
		}
		else {
			$this->tag = $set;
			return $this;
		}
	}

	function add( $child, $child_value = NULL )
	{
		if( count(func_get_args()) == 1 ){
			$this->children[] = $child;
		}
		else {
			$this->children[$child] = $child_value;
		}
		return $this;
	}

	public function prepend( $child, $child_value = NULL )
	{
		if( count(func_get_args()) == 1 ){
			array_unshift( $this->children, $child );
		}
		else {
			$this->children = array_merge( array($child => $child_value), $this->children );
		}
		return $this;
	}

	function remove_children()
	{
		$this->children = array();
		return $this;
	}
	public function remove( $key )
	{
		return $this->remove_child( $key );
	}
	public function remove_child( $key )
	{
		unset($this->children[$key]);
		return $this;
	}

	function child( $key )
	{
		return isset($this->children[$key]) ? $this->children[$key] : NULL;
	}

	function prepend_child( $child )
	{
		array_unshift( $this->children, $child );
		return $this;
	}
	function set_children( $children )
	{
		$this->children = $children;
		return $this;
	}
	function children()
	{
		if( ! $this->children_order ){
			return $this->children;
		}
		else {
			$sort = array();
			$rex_order = 1;
			foreach( $this->children as $k => $child ){
				if( isset($this->children_order[$k]) ){
					$this_order = $this->children_order[$k];
				}
				else {
					$this_order = $rex_order++;
				}
				$sort[ $k ] = $this_order;
			}
			asort($sort);
			$return = array();
			foreach( array_keys($sort) as $k ){
				$return[ $k ] = $this->children[$k];
			}
			return $return;
		}
	}

	protected function _prepare_children()
	{
		$return = '';
		$children = $this->children();

		if( $children ){
			reset( $children );
			foreach( $children as $key => $child ){
//				$return .= "\n";
				if( is_array($child) ){
					foreach( $child as $subchild ){
						$return .= $subchild;
					}
				}
				elseif( is_object($child) ){
					$return .= $child->run('render');
				}
				else {
					$return .= $child;
				}
			}
		}
		return $return;
	}

	function render()
	{
		$return = '';
		$return .= '<' . $this->tag();

		if( $observe = $this->observe() ){
			$this
				->add_attr('data-hc-observe', $observe)
				;
		}

		$add_newline = FALSE;
		if( in_array($this->tag(), array('script', 'meta', 'link', 'head', 'body')) ){
			$add_newline = TRUE;
		}

		$children_return = $this->_prepare_children();
		// if( strlen($children_return) ){
		// 	$already_title = $this->attr('title');
		// 	if( ! $already_title ){
		// 		$this->add_attr('title', $children_return);
		// 	}
		// }

		$attr = $this->attr();
		if( $attr ){
			foreach( $attr as $key => $val ){
				switch( $key ){
					case 'class':
						$val = array_unique($val);
						break;

					case 'value':
						for( $ii = 0; $ii < count($val); $ii++ ){
							$val[$ii] = htmlspecialchars( $val[$ii] );
							$val[$ii] = str_replace( array("'", '"'), array("&#39;", "&quot;"), $val[$ii] );
						}
						break;
				}

				$val = join(' ', $val);
				if( strlen($val) OR ( substr($key, 0, strlen('data-')) == 'data-') ){
					$return .= ' ' . $key . '="' . $val . '"';
				}
			}
		}

		if( strlen($children_return) ){
			$return .= '>';
			if( $add_newline ){
				$return .= "\n";
			}
			$return .= $children_return;
			if( $add_newline ){
				$return .= "\n";
			}
			$return .= '</' . $this->tag() . '>';
		}
		else {
			if( in_array($this->tag(), array('br', 'input', 'link', 'meta')) ){
				$return .= '/>';
			}
			else {
				$return .= '>';
				// if( $add_newline ){
					// $return .= "\n";
				// }
				$return .= '</' . $this->tag() . '>';
			}
		}

		if( $add_newline ){
			$return .= "\n";
		}

		return $return;
	}

	// attribute related functions
	public function attr( $key = '' )
	{
		if( $key === '' ){
			$return = $this->attr;
		}
		elseif( isset($this->attr[$key]) ){
			$return = $this->attr[$key];
		}
		else {
			$return = array();
		}
		return $return;
	}

	public function reset_attr( $key )
	{
		unset($this->attr[$key]);
		return $this;
	}

	public function add_attr( $key, $value = NULL )
	{
		if( count(func_get_args()) == 1 ){
			// supplied as array
			foreach( $key as $key => $value ){
				$this->add_attr( $key, $value );
			}
		}
		else {
			$args = func_get_args();
			if( count($args) > 2 ){
				$value = array();
				array_shift($args);
				while( $args ){
					$value[] = array_shift($args);
				}
			}

			if( is_array($value) ){
				foreach( $value as $v ){
					$this->add_attr( $key, $v );
				}
			}
			else {
				switch( $key ){
					case 'title':
						if( is_string($value) ){
							$value = strip_tags($value);
							$value = trim($value);
						}
						break;

					case 'id':
						if( isset($this->attr[$key]) ){
							unset($this->attr[$key]);
						}
						break;

					case 'class':
					// wordpress?
						if( defined('WPINC') ){
							if( is_admin() ){
								switch( $value ){
									case 'hc-theme-btn-primary':
										$value = 'button-primary';
										break;
								}
							}
						}

						break;
				}

				if( ! is_array($value) )
					$value = array( $value ); 

				if( in_array($key, array('alt', 'value', 'title')) ){
					for( $ii = 0; $ii < count($value); $ii++ ){
						$value[ $ii ] = HC_lib2::esc_attr( $value[ $ii ] );
					}
				}

				if( isset($this->attr[$key]) ){
					$this->attr[$key] = array_merge($this->attr[$key], $value);
				}
				else {
					$this->attr[$key] = $value;
				}
			}
		}
		return $this;
	}
}

class Html_View_Container_HC_MVC extends Html_View_Element_HC_MVC
{
	function render()
	{
		$out = '';

		$args = func_get_args();
		if( count($args) ){
			$items = array_shift($args);
		}
		else {
			$items = $this->children();
		}

		foreach( $items as $item ){
			$out .= $item;
		}
		return $out;
	}
}