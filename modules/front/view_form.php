<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class Front_View_Form_LC_HC_MVC extends _HC_MVC
{
	public function render( $params = array() )
	{
		$out = $this->make('/html/view/container');

		$form = $this->make('form');

		if( isset($params['where-product']) && $params['where-product'] ){
			$form
				->unset_input('product')
				;
		}

		$search_form_id = 'hclc_search_form';

		$link = $this->make('/html/view/link')
			->to('search')
			->ajax()
			->href()
			;

		if( isset($params['start']) && ($params['start'] != 'no') ){
			$form->set_value( 'search', $params['start'] );
		}

		$link_params = array(
			'search'	=> '_SEARCH_',
			'product'	=> '_PRODUCT_',
			'lat'		=> '_LAT_',
			'lng'		=> '_LNG_',
			);

		if( isset($params['limit']) ){
			$link_params['limit'] = $params['limit'];
		}

		if( isset($params['radius']) && (count($params['radius']) <= 1) ){
			$link_params['radius'] = $params['radius'];
		}

		if( isset($params['sort']) ){
			if( substr($params['sort'], -strlen('-reverse')) == '-reverse' ){
				$link_params['sort'] = array( substr($params['sort'], 0, -strlen('-reverse')), 0);
			}
			else {
				$link_params['sort'] = $params['sort'];
			}
		}

		reset( $params );
		foreach( $params as $k => $v ){
			if( ! (substr($k, 0, strlen('where-')) == 'where-') ){
				continue;
			}
			$k = substr( $k, strlen('where-') );
			$link_params[$k] = $v;
		}

		if( ! $link_params['product'] ){
			$link_params['product'] = '_PRODUCT_';
		}

		$link = $this->make('/html/view/link')
			->to('/search', $link_params )
			->ajax()
			->href()
			;

	// radius link which will give us links to results
		$radius_link = '';
		if( isset($params['radius']) && (count($params['radius']) > 1) ){
			$radius_link_params = $link_params;

			$radius_link_params['radius'] = $params['radius'];
			unset( $radius_link_params['sort'] );
			// unset( $radius_link_params['limit'] );

			$radius_link = $this->make('/html/view/link')
				->to('/search/radius', $radius_link_params )
				->ajax()
				->href()
				;
		}

		$display_form = $this->make('/html/view/form')
			->add_attr('id', $search_form_id)
			->add_attr('action', $link )
			->add_attr('data-radius-link', $radius_link )
			->set_form( $form )
			->add_attr('class', 'hc-mb2')
			;

		if( isset($params['start']) && ($params['start'] != 'no') ){
			$display_form
				->add_attr('data-start', $params['start'])
				;
		}

		$where_param = array();

		reset( $params );
		$take_where = array('where-country', 'where-zip', 'where-state', 'where-city');
		foreach( $params as $k => $v ){
			if( ! in_array($k, $take_where) ){
				continue;
			}
			if( ! strlen($v) ){
				continue;
			}

			$short_k = substr($k, strlen('where-'));
			$where_param[] = $short_k . ':' . $v;
		}

		if( $where_param ){
			$where_param = join(' ', $where_param);
			$display_form
				->add_attr('data-where', $where_param)
				;
		}

		$inputs_view = $this->make('/html/view/element')->tag('div')
			->add_attr('id', 'locatoraid-search-form-inputs')
			;
		$inputs = $form->inputs();
		foreach( $inputs as $k => $input ){
			$input_view = $this->make('/html/view/element')->tag('div')
				->add_attr('id', 'locatoraid-search-form-' . $k)
				->add( $input )
				;

			$inputs_view
				->add( $input_view )
				;
		}

		$buttons = $this->make('/html/view/container');
		$buttons->add(
			$this->make('/html/view/element')->tag('input')
				->add_attr('type', 'submit')
				->add_attr('title', HCM::__('Search') )
				->add_attr('value', HCM::__('Search') )
				->add_attr('class', 'hc-block')
				->add_attr('id', 'locatoraid-search-form-button')
			);

		$form_view = $this->make('/html/view/grid')
			->set_gutter(2)
			;

		$form_view
			->add( $inputs_view, 8 )
			->add( $buttons, 4 )
			;

	// more results link
		$more_results_link = $this->make('/html/view/element')->tag('a')
			->add_attr('class', 'hcj2-more-results')
			->add_attr('id', 'locatoraid-search-more-results')
			->add( HCM::__('More Results') )
			->add_attr('style', 'display: none; cursor: pointer;')
			;

		$form_view = $this->make('/html/view/list-div')
			->set_gutter(2)
			->add( $form_view )
			->add( $more_results_link )
			;

		$display_form
			->add( $form_view )
			;

		$out
			->add( $display_form )
			;

		return $out;
	}
}