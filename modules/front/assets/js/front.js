(function($) {
var self = this;

var observers = function()
{
	var observers = [];

	this.add = function( item )
	{
		observers.push( item );
	}
	this.notify = function( what, payload )
	{
		for( var ii = 0; ii < observers.length; ii++ ){
			observers[ii].trigger( what, payload );
		}
	}
}

this.form = function( html_id )
{
	var self = this;
	this.observers = new observers;
	var $this = jQuery( '#' + html_id );

	this.next_links = [];
	this.add_my_bias = true;

	this.more_results = function()
	{
		hc2_set_loader( $this );
		var next_link = self.next_links.shift();
		if( next_link ){
			self.do_search( next_link, '' );
		}
		else {
			hc2_unset_loader( $this );
		}
	}

	this.more_results_link = $this.find('.hcj2-more-results');
	this.more_results_link.on('click', function(e){
		self.more_results();
	});

	this.radius_search = function( search )
	{
		hc2_set_loader( $this );
		self.next_links = [];

		var search_url = $this.data('radius-link');

		var search_string = search.search;
		search_string = search_string + '';

		for( var k in search ){
			var to_replace = '_' + k.toUpperCase() + '_';
			var replace_to = search[k];
			if( Array.isArray(replace_to) ){
				replace_to = replace_to.join('|');
			}
			search_url = search_url.replace( to_replace, replace_to );
		}

		var where = $this.data('where');
		if( where && self.add_my_bias ){
			search_string = search_string + ' ' + where;
		}

		if( ! search_string.length ){
			search_url = search_url.replace( '_LAT_', '' );
			search_url = search_url.replace( '_LNG_', '' );

			self.do_radius_search( search_url );
		}
		else {
		// now try to geocode the search
			var try_this = {
				'address': search_string
			};
			if( hc2_lc_front_vars['search_bias_country'] ){
				try_this['componentRestrictions'] = {
					country: hc2_lc_front_vars['search_bias_country'],
				};
			}

			hc2_geocode(
				try_this,
				function( success, results, return_status )
				{
					if( success ){
						search_url = search_url.replace( '_LAT_', results.lat );
						search_url = search_url.replace( '_LNG_', results.lng );

						self.observers.notify( 'get-search', [results.lat, results.lng, search_string] );
					}
					else {
						search_url = search_url.replace( '_LAT_', '' );
						search_url = search_url.replace( '_LNG_', '' );
					}
					self.do_radius_search( search_url );
				}
			);
		}
	}

	this.do_radius_search = function( search_url )
	{
		console.log( search_url );

		jQuery.ajax({
			type: 'GET',
			url: search_url,
			dataType: "json",
			success: function(data, textStatus){
				// hc2_unset_loader( $this );

			// search links
				if( data.length ){
					for( var ii = 0; ii < data.length; ii++ ){
						var this_link = data[ii];
						self.next_links.push( this_link[0] );
					}
				}

				var next_link = self.next_links.shift();
				if( next_link ){
					self.do_search( next_link, '' );
				}
				else {
					self.observers.notify( 'get-results', {} );
					hc2_unset_loader( $this );
				}
			}
			})
			.fail( function(jqXHR, textStatus, errorThrown){
				hc2_unset_loader( $this );
				alert( 'Ajax Error: ' + search_url );
				alert( jqXHR.responseText );
				})
			;
	}

	this.search = function( search )
	{
		hc2_set_loader( $this );
		var search_string = search.search;
		search_string = search_string + '';
		var search_url = $this.attr('action');

		for( var k in search ){
			var to_replace = '_' + k.toUpperCase() + '_';
			var replace_to = search[k];
			if( Array.isArray(replace_to) ){
				replace_to = replace_to.join('|');
			}
			search_url = search_url.replace( to_replace, replace_to );
		}

		// search_url = search_url.replace( '_SEARCH_', search_string );

		var where = $this.data('where');
		if( where && self.add_my_bias ){
			search_string = search_string + ' ' + where;
		}

		if( ! search_string.length ){
			search_url = search_url.replace( '_LAT_', '' );
			search_url = search_url.replace( '_LNG_', '' );

			self.do_search( search_url, search_string );
		}
		else {
		// now try to geocode the search
			var try_this = {
				'address': search_string
			};
			if( hc2_lc_front_vars['search_bias_country'] ){
				try_this['componentRestrictions'] = {
					country: hc2_lc_front_vars['search_bias_country'],
				};
			}

			hc2_geocode(
				try_this,
				function( success, results, return_status )
				{
					if( success ){
						search_url = search_url.replace( '_LAT_', results.lat );
						search_url = search_url.replace( '_LNG_', results.lng );

						self.observers.notify( 'get-search', [results.lat, results.lng, search_string] );
					}
					else {
						search_url = search_url.replace( '_LAT_', '' );
						search_url = search_url.replace( '_LNG_', '' );
					}
					self.do_search( search_url, search_string );
				}
			);
		}
	}

	this.do_search = function( search_url, search_string )
	{
		console.log( search_url );

		jQuery.ajax({
			type: 'GET',
			url: search_url,
			dataType: "json",
			success: function(data, textStatus){
				self.observers.notify( 'get-results', data );
				hc2_unset_loader( $this );

			// more results link
				if( self.next_links.length ){
					self.more_results_link.show();
				}
				else {
					self.more_results_link.hide();
				}
			}
			})
			.fail( function(jqXHR, textStatus, errorThrown){
				hc2_unset_loader( $this );
				alert( 'Ajax Error: ' + search_url );
				alert( jqXHR.responseText );
				})
			;
	}

	this.submit = function( event )
	{
		event.stopPropagation();
		event.preventDefault();

		this_data = {};
		var this_form_array = $this.find('select, textarea, input, checkbox').serializeArray();
		for( var ii = 0; ii < this_form_array.length; ii++ ){
			var name = this_form_array[ii]['name'];
			name = name.substr(3); // strip 'hc-'

			if( name.substr(-2) == '[]' ){
				name = name.substr(0, name.length-2);
				if( ! this_data[name] ){
					this_data[name] = [];
				}
				this_data[name].push( this_form_array[ii]['value'] );
			}
			else {
				this_data[name] = this_form_array[ii]['value'];
			}
		}

		var radius_search_url = $this.data('radius-link');
		var search_string = this_data.search;
		search_string = search_string + '';

		if( search_string.length && radius_search_url.length ){
			self.radius_search( this_data );
		}
		else {
			self.search( this_data );
		}
	}

	$this.on('submit', this.submit );

	// var default_search = $this.find('input[name=hc-search]').val();
	var where = $this.data('where');
	var start = $this.data('start');

	// if( default_search || where ){
	if( where || (start != null) ){
		var radius_search_url = $this.data('radius-link');
		if( start.length && radius_search_url.length ){
			this.radius_search( {'search': start} );
		}
		else {
			this.search( {'search': start} );
		}
	}
}

this.list = function( html_id )
{
	var self = this;
	this.observers = new observers;
	var $this = jQuery( '#' + html_id );

	this.params = {
		'group'	:	$this.data('group'),
		'sort' 	:	$this.data('sort')
	};

	self.template = jQuery( '#' + html_id + '_template' ).html();
	self.template_no_results = jQuery( '#' + html_id + '_template_no_results' ).html();

	this.entries = {};

	this.trigger = function( what, payload )
	{
		if( ! $this.length ){
			return;
		}

		switch( what ){
			case 'get-results':
				this.render( payload );
				break;
			case 'select-location':
				this.highlight( payload );
				this.scroll_to( payload );
				break;
		}
	}

	this.render = function( results )
	{
		if( ! $this.is(":visible") ){
			$this.show();
		}

		self.entries = {};

		var entries = [];
		if( results.hasOwnProperty('results') ){
			entries = results['results'];
		}

		$this.html('');

		var group_by = this.params['group'];
		var groups = {};

		if( group_by ){
			for( var ii = 0; ii < entries.length; ii++ ){
				var this_loc = entries[ii];
				var this_group_label = this_loc[group_by];

				if( ! groups.hasOwnProperty(this_group_label) ){
					groups[this_group_label] = [];
				}
				groups[this_group_label].push(ii);
			}
		}
		else {
			var this_group_label = '';
			groups[this_group_label] = [];
			for( var ii = 0; ii < entries.length; ii++ ){
				groups[this_group_label].push(ii);
			}
		}

		var group_labels = Object.keys( groups );
		group_labels.sort(function(a, b){
			return a.localeCompare(b);
		})

		for( var kk = 0; kk < group_labels.length; kk++ ){
			var group_label = group_labels[kk];

			if( group_label.length ){
				var group_label_view = '<h4>' + group_label + '</h4>';
				$this.append( group_label_view );
			}

			for( var jj = 0; jj < groups[group_label].length; jj++ ){
				var ii = groups[group_label][jj];
				var this_loc = entries[ii];

				var template = new Hc2Template( self.template );
				var template_vars = this_loc;
				var this_loc_view = template.render(template_vars);

				// var $this_loc_view = jQuery( this_loc_view );
				var $this_loc_view = jQuery('<div>').html( this_loc_view );
				$this_loc_view
					.data( 'location-id', this_loc['id'] )
					;

				self.entries[ this_loc['id'] ] = $this_loc_view;
				$this.append( $this_loc_view );

				$this_loc_view.on('click', function(e){
					var location_id = jQuery(this).data('location-id');
					self.highlight( location_id );
					self.observers.notify( 'select-location', location_id );
				});
			}
		}

		if( ! entries.length ){
			var no_results_view = self.template_no_results;
			var $no_results_view = jQuery('<div>').html( no_results_view );
			$this.append( $no_results_view );
		}
	}

	this.scroll_to = function( id )
	{
		var $container = self.entries[id];
		var new_top = $this.scrollTop() + $container.position().top;
		$this.scrollTop( new_top );
	}

	this.highlight = function( id )
	{
		var hl_class = 'hc-outlined';

		for( var iid in self.entries ){
			self.entries[iid].removeClass( hl_class );
		}

		var container = self.entries[id];
		container.addClass( hl_class );
	}
}

this.map = function( html_id )
{
	var self = this;
	this.observers = new observers;
	var $this = jQuery( '#' + html_id );
	self.template = jQuery( '#' + html_id + '_template' ).html();
	this.markers = {};
	this.entries = {};
	$this.map = null;

	this.max_zoom = 12;
	this.max_zoom_no_entries = 4;

	this.infowindow = new google.maps.InfoWindow({
		});

	this.trigger = function( what, payload )
	{
		if( ! $this.length ){
			return;
		}

		switch( what ){
			case 'get-search':
				this.render_search( payload );
				break;
			case 'get-results':
				this.render( payload );
				break;
			case 'select-location':
				this.render_info( payload );
				break;
		}
	}

	this.init_map = function( html_id )
	{
		if( ! this.map ){
			this.map = hc2_init_gmaps( html_id );
			jQuery(document).trigger('hc2-lc-map-init', this.map);
		}
	}

	this.render_search = function( coord )
	{
		if( ! $this.is(":visible") ){
			$this.show();
		}
		this.init_map( html_id );

		for( var id in this.markers ){
			this.markers[id].setMap(null);
		}
		this.markers = {};

		var search_coordinates = new google.maps.LatLng(coord[0], coord[1]);

		var searched_marker = new google.maps.Marker({
			position: search_coordinates,
			icon: {
				path: google.maps.SymbolPath.CIRCLE,
				scale: 6
				},
			// icon: "//maps.google.com/mapfiles/arrow.png",
			draggable: false,
			map: this.map,
			title: coord[2],
		});

		self.markers[-1] = searched_marker;

		// this.map.setCenter( search_coordinates );
		// if( this.map.getZoom() > this.max_zoom_no_entries ){
			// this.map.setZoom(this.max_zoom_no_entries);
		// }
		// this.map.fitBounds( bound );
		// this.map.setZoom(6);
	}

	this.render = function( results )
	{
		if( ! $this.is(":visible") ){
			$this.show();
		}
		this.init_map( html_id );

		for( var id in this.markers ){
			if( id > 0 ){
				this.markers[id].setMap(null);
			}
		}

		var entries = [];
		if( results.hasOwnProperty('results') ){
			entries = results['results'];
		}

		for( var ii = 0; ii < entries.length; ii++ ){
			var id = entries[ii]['id'];
			self.entries[id] = entries[ii];
		}

	// place locations on map
		for( var ii = 0; ii < entries.length; ii++ ){
			var this_loc = entries[ii];
			var id = entries[ii]['id'];

			var location_position = new google.maps.LatLng( this_loc['latitude'], this_loc['longitude'] );

			var location_marker = new google.maps.Marker( {
				map: self.map,
				position: location_position,
				title: this_loc['name'],
				draggable: false,
				visible: true,
				animation: google.maps.Animation.DROP,
				location_id: id,
				});

			if( this_loc['mapicon'] && this_loc['mapicon'].length ){
				location_marker.setIcon( this_loc['mapicon'] );
			}

			location_marker.addListener( 'click', function(){
				self.render_info( this.location_id );
				self.observers.notify( 'select-location', this.location_id );
			});

			self.markers[id] = location_marker;
		}

	// zoom map accordingly
		var bound = new google.maps.LatLngBounds();
		for( var id in this.markers ){
			bound.extend( this.markers[id].position );
		}
		this.map.fitBounds( bound );
		this.map.setCenter( bound.getCenter() );

	// prepare zoom
		if( entries.length ){
			if( this.map.getZoom() > this.max_zoom ){
				this.map.setZoom(this.max_zoom);
			}
		}
		else {
			if( this.map.getZoom() > this.max_zoom_no_entries ){
				this.map.setZoom(this.max_zoom_no_entries);
			}
		}
	}

	this.render_info = function( this_id )
	{
		var this_marker = self.markers[this_id];
		var this_loc = self.entries[this_id];

		var template = new Hc2Template( self.template );
		var template_vars = this_loc;
		var this_loc_view = template.render(template_vars);

		this.infowindow.setContent( this_loc_view );
		this.infowindow.open( self.map, this_marker );
	}
}

jQuery(document).on('hc2-gmaps-loaded', function()
{
	var form = new self.form( 'hclc_search_form' );
	var list = new self.list( 'hclc_list' );
	var map = new self.map( 'hclc_map' );

	form.observers.add( list );
	form.observers.add( map );

	list.observers.add( map );
	map.observers.add( list );
});

}());
