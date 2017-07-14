<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Conf_Index_Controller_HC_MVC extends _HC_MVC
{
	function route_index()
	{
		$argso = $this->make('/app/lib/args')->run('parse', func_get_args());
		$args = $argso->get();

		$tab = isset($args['tab']) ? $args['tab'] : '';

		$config_loader = $this->make('/app/lib/config-loader');
		$fields = $config_loader->get('settings');
		$tabs = $this->run('get-tabs', $fields);

		$tab_keys = array_keys($tabs);
		if( ! $tab ){
			$tab = $tab_keys[0];
		}

		$form = $this
			->run('form', $tab, $args)
			;

		$defaults = $this->run('defaults', $tab);
		$form->set_values( $defaults );

		return $this->run('prepare-view', $tab, $form, $args);
	}

	public function defaults( $tab )
	{
		$defaults = array();

		$config_loader = $this->make('/app/lib/config-loader');
		$app_settings = $this->make('/app/lib/settings');

		$fields = $config_loader->get('settings');
		$tabs = $this->run('get-tabs', $fields);
		$this_fields = $tabs[$tab];

		foreach( $this_fields as $fk => $fn ){
			if( $fk == '_label'){
				continue;
			}
			$value = $app_settings->run('get', $fn);
			$defaults[$fn] = $value;
		}

		return $defaults;
	}

	public function form( $tab )
	{
		$config_loader = $this->make('/app/lib/config-loader');
		$app_settings = $this->make('/app/lib/settings');

		$fields = $config_loader->get('settings');
		$tabs = $this->run('get-tabs', $fields);
// _print_r( $tabs );
// unset($tabs['wordpress-users']);
		$this_fields = $tabs[$tab];
		$form = $this->make('form');

		foreach( $this_fields as $fk => $fn ){
			if( $fk == '_label'){
				continue;
			}
			$f = $fields[ $fk ];
			$input = $this->make('/form/view/' . $f['type']);

			switch( $f['type'] ){
				case 'dropdown':
					$input->set_options( $f['options'] );
					if( count($f['options']) < 2 ){
						$input->set_readonly();
					}
					break;

				case 'radio':
					$input->set_options( $f['options'] )->set_inline();
					if( count($f['options']) < 2 ){
						$input->set_readonly();
					}
					break;

				case 'checkbox_set':
					$input->set_options( $f['options'] );
					if( isset($f['inline']) ){
						$input->set_inline($f['inline']);
					}
					if( count($f['options']) < 2 ){
						$input->set_readonly();
					}
					break;
			}

			if( isset($f['label']) ){
				$input->set_label($f['label']);
			}

			if( isset($f['attr']) && is_array($f['attr']) ){
				foreach( $f['attr'] as $k => $v ){
					$input
						->add_attr($k, $v)
						;
				}
			}

			$form->set_input( 
				$fn,
				$input
				);
		}

		return $form;
	}

	public function prepare_view( $tab, $form, $args )
	{
		$config_loader = $this->make('/app/lib/config-loader');
		$fields = $config_loader->get('settings');
		$tabs = $this->run('get-tabs', $fields);

		$view = $this->make('index/view')
			->run('render', $form, $args)
			;
		$view = $this->make('index/view/layout')
			->run('render', $view, $tabs, $tab)
			;
		$view = $this->make('/layout/view/body')
			->set_content($view)
			;
		return $this->make('/http/view/response')
			->set_view($view)
			;
	}

	public function get_tabs( $fields ){
		$tabs = array();
		foreach( $fields as $fn => $f ){
			$this_tab = 'core';
			if( strpos($fn, ':') !== FALSE ){
				list( $this_tab, $this_short_fn ) = explode( ':', $fn );
			}
			$this_tab = str_replace('_', '-', $this_tab);

			if( ! isset($tabs[$this_tab])){
				$tabs[$this_tab] = array();
			}

			if( $this_short_fn == '_label' ){
				$tabs[$this_tab][$this_short_fn] = $f;
			}
			else {
				$tabs[$this_tab][$fn] = $fn;
			}
		}

	// remove those without labels
		$check = array_keys($tabs);
		foreach( $check as $tab ){
			if( ! isset($tabs[$tab]['_label']) ){
				unset( $tabs[$tab] );
			}
		}

		return $tabs;
	}
}