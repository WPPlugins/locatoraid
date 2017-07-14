<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Html_View_Select_Links_HC_MVC extends Html_View_Element_HC_MVC
{
	protected $options = array();
	protected $selected = NULL;
	protected $option_groups = array();
	protected $options_to_groups = array();

	public function add_option( $key, $label, $link, $optgroup_id = NULL )
	{
		$this->options[ $key ] = array( $label, $link );

		if( $this->option_groups && $optgroup_id && isset($this->option_groups[$optgroup_id]) ){
			if( ! isset($this->options_to_groups[$optgroup_id]) ){
				$this->options_to_groups[$optgroup_id] = array();
			}
			$this->options_to_groups[$optgroup_id][] = $key;
		}

		return $this;
	}
	public function options()
	{
		return $this->options;
	}

	public function set_option_groups( $option_groups )
	{
		$this->option_groups = $option_groups;
	}
	public function option_groups()
	{
		return $this->option_groups;
	}

	public function set_selected( $selected )
	{
		$this->selected = $selected;
		return $this;
	}
	public function selected()
	{
		return $this->selected;
	}

	public function render_readonly()
	{
		$return = NULL;

		$options = $this->options();
		$selected = $this->selected();
		if( $selected === NULL ){
			$keys = array_keys($options);
			$selected = array_shift($keys);
		}

		if( isset($options[$selected]) ){
			$option = $options[$selected];
			list( $label, $link ) = $option;
			$return = $this->make('view/element')->tag('span')
				->add( $label )
				->add_attr('class', 'hc-p2')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-rounded')
				->add_attr('class', 'hc-inline-block')
				;
		}

		return $return;
	}

	public function render()
	{
		$readonly = $this->readonly();
		if( $readonly ){
			return $this->run('render-readonly');
		}

		$options = $this->options();
		if( count($options) <= 1 ){
			return $this->run('render-readonly');
		}

		$return = $this->make('/html/view/element')->tag('select')
			->add_attr('class', 'hc-field')
			->add_attr('onchange', 'if (this.value) window.location.href=this.value')
			;

		$selected = $this->selected();

		if( $this->option_groups && $this->options_to_groups ){
			// if anything in no group
			$orphan_options = array();
			reset( $options );
			foreach( $options as $key => $option ){
				$is_orphan = TRUE;
				reset( $this->options_to_groups );
				foreach( $this->options_to_groups as $optgroup_id => $this_options ){
					if( in_array($key, $this_options) ){
						$is_orphan = FALSE;
						break;
					}
				}
				if( $is_orphan ){
					$orphan_options[] = $key;
				}
			}
			if( $orphan_options ){
				foreach( $orphan_options as $key ){
					$option = $options[$key];
					list( $label, $link ) = $option;

					$option = $this->make('/html/view/element')->tag('option');
					$option->add_attr( 'value', $link );
					$option->add( $label );

					if( $selected == $key ){
						$option->add_attr( 'selected', 'selected' );
					}
					$return->add( $option );
				}
			}

			reset( $this->options_to_groups );
			foreach( $this->options_to_groups as $optgroup_id => $this_options ){
				$optgroup = $this->make('/html/view/element')->tag('optgroup');
				$optgroup->add_attr( 'label', $this->option_groups[$optgroup_id] );

				foreach( $this_options as $key ){
					$option = $options[$key];
					list( $label, $link ) = $option;

					$option = $this->make('/html/view/element')->tag('option');
					$option->add_attr( 'value', $link );
					$option->add( $label );

					if( $selected == $key ){
						$option->add_attr( 'selected', 'selected' );
					}
					$optgroup->add( $option );
				}
				$return->add( $optgroup );
			}
		}
		else {
			reset( $options );
			foreach( $options as $key => $option ){
				list( $label, $link ) = $option;

				$option = $this->make('/html/view/element')->tag('option');
				$option->add_attr( 'value', $link );
				$option->add( $label );

				if( $selected == $key ){
					$option->add_attr( 'selected', 'selected' );
				}
				$return->add( $option );
			}
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$return->add_attr($k, $v);
		}
		return $return;
	}
}