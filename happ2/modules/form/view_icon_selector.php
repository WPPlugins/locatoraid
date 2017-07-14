<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Form_View_Icon_Selector_HC_MVC extends Form_View_Hidden_HC_MVC
{
	public function render()
	{
		$value = $this->value();
		$name = $this->name();
		$readonly = $this->readonly();

		$im = $this->make('/html/view/icon');

		if( $readonly ){
			$out = $this->make('/html/view/element')->tag('div')
				->add('&nbsp;')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-p1')
				->add_attr('style', 'background-color: ' . $value . ';')
				->add_attr('style', 'width: 2em;')
				;
		}
		else {
			$icons = $im->run('get-icons');
			if( ! $icons ){
				return;
			}

			$hidden = $this->make('view/hidden')
				->set_name( $name )
				->set_value( $value )
				->add_attr('class', 'hcj2-icon-picker-value')
				;


			if( $value ){
				$current_view = $im->run('icon', $value)->run('render');
			}
			else {
				$current_view = '&nbsp;';
				$current_view = '&nbsp;-&nbsp;';
			}

			$title = $this->make('/html/view/element')->tag('a')
				->add_attr('class', 'hc-btn')
				->add_attr('class', 'hc-inline-block')
				->add_attr('class', 'hc-border')
				->add_attr('class', 'hc-rounded')
				->add_attr('class', 'hc-m1')
				->add_attr('class', 'hc-p1')

				->add_attr('class', 'hcj2-icon-picker-display')

				->add($current_view)
				;

			$options = $this->make('/html/view/list-inline')
				->add_attr('class', 'hc-mt2')
				->add_attr('class', 'hc-py2')
				->add_attr('class', 'hc-border-top')
				;

			foreach( $icons as $icn ){
				$this_view = $im->run('icon', $icn)->run('render');

				$option = $this->make('/html/view/element')->tag('a')
					->add_attr('class', 'hc-btn')
					->add_attr('class', 'hc-inline-block')
					->add_attr('class', 'hc-border')
					->add_attr('class', 'hc-rounded')
					->add_attr('class', 'hc-m1')
					->add_attr('class', 'hc-p1')

					->add_attr('data-icon', $icn)

					->add_attr('class', 'hcj2-icon-picker-selector')
					->add_attr('class', 'hcj2-collapse-closer')

					->add( $this_view )
					;

				$options->add( $option );
			}

			$display = $this->make('/html/view/collapse')
				->set_title( $title )
				->set_content( $options )
				;

			$out = $this->make('/html/view/element')->tag('div')
				->add_attr('class', 'hcj2-icon-picker')
				->add( $hidden )
				->add( $display )
				;
		}

		return $this->decorate( $out );
	}
}