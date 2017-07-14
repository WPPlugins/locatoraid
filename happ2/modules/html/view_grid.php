<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Grid_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $scale = 'sm'; // can be xs, sm, md, lg
	protected $gutter = 0; // from 0 to 4
	protected $right = array();

	protected $children_details = array();

	function add( $child, $child_value = NULL )
	{
		$args = func_get_args();
		if( count($args) == 3 ){
			list( $item, $item_value, $width ) = $args;
			if( is_array($width) ){
				list( $width, $offset ) = $width;
			}
			else {
				$offset = 0;
			}
			// $this->children[$item] = array( $item_value, $width, $offset );
			$this->children[$item] = $item_value;
			$this->children_details[$item] = array( $width, $offset );
		}
		elseif( count($args) == 2 ){
			list( $item_value, $width ) = $args;
			if( is_array($width) ){
				list( $width, $offset ) = $width;
			}
			else {
				$offset = 0;
			}
			// $this->children[] = array( $item_value, $width, $offset );
			$this->children[] = $item_value;
			$ii = count($this->children) - 1;
			$this->children_details[$ii] = array( $width, $offset );
		}
		return $this;
	}

	function scale()
	{
		return $this->scale;
	}
	function set_scale( $scale )
	{
		$this->scale = $scale;
		return $this;
	}

/* from 0 to 3 */
	function set_gutter( $gutter )
	{
		$this->gutter = $gutter;
		return $this;
	}
	function gutter()
	{
		return $this->gutter;
	}

	function set_child_width( $child, $width )
	{
		if( isset($this->children[$child]) ){
			$this->children[$child][1] = $width;
		}
		return $this;
	}

	function set_child_right( $child, $right = 1 )
	{
		$this->right[$child] = $right;
	}

	function render()
	{
		$out = $this->make('view/element')->tag('div');
		$gutter = $this->gutter();

		$out
			->add_attr('class', 'hc-clearfix')
			;
		if( $gutter ){
			$out
				->add_attr('class', 'hc-mxn' . $gutter)
				;
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$scale = $this->scale();
		$items = $this->children();

		foreach( $items as $key => $item ){
			// list( $item, $width, $offset ) = $item;
			list( $width, $offset ) = $this->children_details[$key];
			$right = isset($this->right[$key]) ? $this->right[$key] : 0;

			$slot = $this->make('view/element')->tag('div')
				;

			$css_classes = $this->_get_col_class( $scale, $width, $offset, $gutter, $right );
			foreach( $css_classes as $css_class ){
				$slot
					->add_attr('class', $css_class)
					;
			}

			$slot->add( $item );
			$out->add( $slot );
		}
		return $out;
	}

	protected function _get_col_class( $scale, $width, $offset, $gutter, $right )
	{
		$class = array();

		$manual = FALSE;
		$check_manual = array('%', 'em', 'px', 'rem');
		/* check if width contains %% then we need to set it manually */
		foreach( $check_manual as $check ){
			if( substr($width, -strlen($check)) == $check ){
				$manual = TRUE;
				break;
			}
		}

		switch( $scale ){
			case 'xs':
				$class = array('hc-col');
				if( ! $manual ){
					$class[] = 'hc-col-' . $width;
				}
				if( $right ){
					$class[] = 'hc-col-right';
				}
				break;

			case 'sm':
				$class = array('hc-sm-col');
				if( ! $manual ){
					$class[] = 'hc-sm-col-' . $width;
				}
				if( $right ){
					$class[] = 'hc-sm-col-right';
				}
				if( $gutter ){
					$class[] = 'hc-mb' . $gutter . '-xs';
				}
				break;

			case 'md':
				$class = array('hc-md-col');
				if( ! $manual ){
					$class[] = 'hc-md-col-' . $width;
				}
				if( $right ){
					$class[] = 'hc-md-col-right';
				}
				break;

			case 'lg':
				$class = array('hc-lg-col');
				if( ! $manual ){
					$class[] = 'hc-ld-col-' . $width;
				}
				if( $right ){
					$class[] = 'hc-ld-col-right';
				}
				break;
		}

		if( $manual ){
			// $el->add_attr('style', 'width: ' . $width . ';');
		}
		if( $offset ){
			// $el->add_attr('style', 'margin-left: ' . $offset . ';');
		}

		if( $gutter ){
			$class[] = 'hc-px' . $gutter;
		}

		return $class;
	}
}