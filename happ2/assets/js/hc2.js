var hcapp = {};

function hc2_try_parse_json( jsonString )
{
	try {
		var o = JSON.parse( jsonString );
		/* 
		 * Handle non-exception-throwing cases:
		 * Neither JSON.parse(false) or JSON.parse(1234) throw errors, hence the type-checking,
		 * but... JSON.parse(null) returns 'null', and typeof null === "object", 
		 * so we must check for that, too.
		 */ 
		if ( o && typeof o === "object" && o !== null ) {
			return o;
		}
	}
	catch ( e ) { }
	return false;
}

jQuery(document).on( 'click', '.hcj2-target ul.hcj2-dropdown-menu', function(e)
{
	e.stopPropagation();
//	e.preventDefault();
});

function hc2_set_loader( $el )
{
	var loader = '<div class="hc-loader"></div';
	var shader = '<div class="hc-loader-shader"></div';
	$el.css('position', 'relative'); 
	$el.append( shader );
	$el.append( loader );
}

function hc2_unset_loader( $el )
{
	$el.find('[class="hc-loader-shader"]').remove();
	$el.find('[class="hc-loader"]').remove();
}

jQuery(document).on( 'click', '.hcj2-confirm', function(event)
{
	if( window.confirm("Are you sure?") ){
		return true;
	}
	else {
		event.preventDefault();
		event.stopPropagation();
		return false;
	}
});

jQuery(document).on( 'submit', '.hcj2-alert-dismisser', function(e)
{
	jQuery(this).closest('.hcj2-alert').hide();
	return false;
});

jQuery(document).on( 'click', '.hcj2-as-label', function(e)
{
	jQuery(this).closest('.hcj2-as-label-container').find(':checkbox,:radio').each( function(){
		if( ! jQuery(this).attr('disabled') ){
			jQuery(this)[0].checked = ! jQuery(this)[0].checked;
			jQuery(this).trigger('change');
		}
	});
});

jQuery(document).on( 'click', '.hcj2-action-setter', function(event)
{
	var thisForm = jQuery(this).closest('form');
	var actionFieldName = 'action';
	var actionValue = jQuery(this).attr('name');

	thisForm.find("input[name='" + actionFieldName + "']").each( function(){
		jQuery(this).val( actionValue );
	});
});

/*
this displays more info divs for radio choices
*/
jQuery(document).on( 'change', '.hcj2-radio-more-info', function(event)
{
	// jQuery('.hcj2-radio-info').hide();
	var total_container = jQuery( this ).closest('.hcj2-radio-info-container');
	total_container.find('.hcj2-radio-info').hide();

	var my_container = jQuery( this ).closest('label');
	var my_info = my_container.find('.hcj2-radio-info');
	my_info.show();
});

/* toggle */
jQuery(document).on('click', '.hcj2-toggle', function(e)
{
	var this_target_id = jQuery(this).data('target');
	if( this_target_id.length > 0 ){
		this_target = jQuery(this_target_id);
		if( this_target.is(':visible') ){
			this_target.hide();
		}
		else {
			this_target.show();
		}
	}
	return false;
});

/* collapse next */
jQuery(document).on('click', '.hcj2-collapse-next,[data-toggle=collapse-next]', function(e)
{
	var this_target = jQuery(this).closest('.hcj2-collapse-panel').children('.hcj2-collapse');

	if( this_target.is(':visible') ){
		this_target.hide();
		this_target.removeClass('hcj-open');
		jQuery(this).trigger({
			type: 'hidden.hc.collapse'
		});
	}
	else {
		this_target.show();
		this_target.addClass('hcj-open');
		jQuery(this).trigger({
			type: 'shown.hc.collapse'
		});

		if( jQuery(this).hasClass('hcj2-collapser-hide')){
			// jQuery(this).closest('li').hide();
			jQuery(this).hide();
		}
	}
//	this_target.collapse('toggle');

	if( jQuery(this).attr('type') != 'checkbox' ){
		/* scroll into view */
//		var this_parent = jQuery(this).parents('.collapse-panel');
//		this_parent[0].scrollIntoView();
		return false;
	}
	else {
		return true;
	}
});

/* collapse other */
jQuery(document).on('click', '.hcj2-collapser', function(e)
{
	// var targetUrl = jQuery(this).attr('href');
	var targetUrl = jQuery(this).data('target');
	if(
		( targetUrl.length > 0 ) &&
		( targetUrl.charAt(targetUrl.length-1) == '#' )
		){
		return false;
	}

	if( targetUrl.charAt(0) != '#' ){
		targetUrl = '#' + targetUrl
	}

	var this_target = jQuery(targetUrl);

	if( this_target.is(':visible') ){
		this_target.hide();
		this_target.removeClass('hcj-open');
		jQuery(this).trigger({
			type: 'hidden.hc.collapse'
		});
	}
	else {
		this_target.show();
		this_target.addClass('hcj-open');
		jQuery(this).trigger({
			type: 'shown.hc.collapse'
		});
	}
//	this_target.collapse('toggle');
	if( jQuery(this).attr('type') != 'checkbox' ){
		return false;
	}
	else {
		return true;
	}
});

/* collapse other */
jQuery(document).on('click', '.hcj2-collapse-closer', function(e)
{
	var this_target = jQuery(this).closest('.hcj2-collapse');

	if( this_target.is(':visible') ){
		this_target.hide();
		this_target.removeClass('in');
		jQuery(this).trigger({
			type: 'hidden.hc.collapse'
		});
	}
	else {
		this_target.show();
		this_target.addClass('in');
		jQuery(this).trigger({
			type: 'shown.hc.collapse'
		});
	}

	if( jQuery(this).attr('type') != 'checkbox' ){
		return false;
	}
	else {
		return true;
	}
});

jQuery(document).on('click', '.hcj2-dropdown-menu select', function()
{
	return false;
});

jQuery(document).on( 'click', 'a.hcj2-toggler', function(event)
{
	jQuery('.hcj2-toggled').toggle();
	return false;
});

jQuery(document).on('change', '.hcj2-collector-wrap input.hcj2-collect-me', function(event){
	var my_val = jQuery(this).val();
	var me_remove = ( jQuery(this).is(":checked") ) ? 0 : 1;
	var input_name = jQuery(this).attr('name');

	/* find an input of the same name in the collector form */
	var collector_form = jQuery(this).closest('.hcj2-collector-wrap').find('form.hcj2-collector-form');
	var collector_input = collector_form.find("input[name^='" + input_name + "']");

	if( collector_input.length ){
		var current_value = collector_input.val();
		if( current_value.length ){
			current_value = current_value.split('|');
		}
		else {
			current_value = [];
		}

		var my_pos = jQuery.inArray(my_val, current_value);

	/* remove */
		if( me_remove ){
			if( my_pos != -1 ){
				current_value.splice(my_pos, 1);
			}
		}
	/* add */
		else {
			if( my_pos == -1 ){
				current_value.push(my_val);
			}
		}

		current_value = current_value.join('|');
		collector_input.val( current_value );
	}
});

jQuery(document).on( 'click', '.hcj2-all-checker', function(event)
{
	var thisLink = jQuery( this );
	var firstFound = false;
	var whatSet = true;

	var moreCollect = thisLink.data('collect');
	if( moreCollect ){
		var myParent = thisLink.closest('.hcj2-collector-wrap');
		if( myParent.length > 0 )
			myParent.first();
		else
			myParent = jQuery('#nts');

		myParent.find("input[name^='" + moreCollect + "']").each( function()
		{
			if( 
				( jQuery(this).attr('type') == 'checkbox' )
				){
				if( ! firstFound ){
					whatSet = ! this.checked;
					firstFound = true;
				}
				// this.checked = whatSet;
				jQuery(this)
					.prop("checked", whatSet)
					.change()
					;
			}
		});
	}

	if(
		( thisLink.prop('tagName').toLowerCase() == 'input' ) &&
		( thisLink.attr('type').toLowerCase() == 'checkbox' )
		){
		return true;
	}
	else {
		return false;
	}
});

/* color picker */
jQuery(document).on('click', 'a.hcj2-color-picker-selector', function(event)
{
	var my_value = jQuery(this).data('color');

	var my_form = jQuery(this).closest('.hcj2-color-picker');
	my_form.find('.hcj2-color-picker-value').val( my_value );
	my_form.find('.hcj2-color-picker-display').css('background-color', my_value);

	/* close collapse */
	return false;
});

/* icon picker */
jQuery(document).on('click', 'a.hcj2-icon-picker-selector', function(event)
{
	var my_value = jQuery(this).data('icon');

	var my_form = jQuery(this).closest('.hcj2-icon-picker');
	my_form.find('.hcj2-icon-picker-value').val( my_value );
	my_form.find('.hcj2-icon-picker-display').html( jQuery(this).html() );

	/* close collapse */
	return false;
});

/* observe forms */
function hc_observe_input( this_input )
{
	// var my_form = this_input.closest('form');
	var my_form = this_input.closest('.hcj2-observe');

	my_form.find('[data-hc-observe]').each( function(){
		var my_this = jQuery(this);
		var whats = my_this.data('hc-observe').toString().split(' ');

		var my_holder = my_this.closest('.hcj2-input-holder');
		if( my_holder.length ){
			my_holder.hide();
		}
		else {
			my_this.hide();
		}

		for( var ii = 0; ii < whats.length; ii++ ){
			var what_parts = whats[ii].split('=');
			var what_param = what_parts[0];
			var what_value = what_parts[1];
// alert( this_input.attr('name') + 'observe: ' + what_param + ' = ' + what_value + '?' );

			var show_this = false;

			var search_name = what_param;
			if( what_param.substring(0,3) != 'hc-' ){
				search_name = 'hc-' + search_name;
			}
			search_name = search_name.replace(':', '\\:');

			var find_this = '[name="' + search_name + '"]';
			// trigger_input = my_form.find('[name="' + search_name + '"]');
			trigger_input = my_form.find(find_this);

			// if( ! trigger_input ){
			if( ! trigger_input.length ){
				search_name = search_name + '\[\]';
				var find_this = '[name="' + search_name + '"]';
				trigger_input = my_form.find(find_this);
				if( ! trigger_input.length ){
					continue;
				}
			}

			if( trigger_input.prop('type') == 'select-one' ){
				trigger_val = trigger_input.val();
			}
			else if( trigger_input.prop('type') == 'radio' ){
				// trigger_val = my_form.find('[name=' + search_name + ']:checked').val();
				trigger_val = my_form.find(find_this + ':checked').val();
			}
			else if( trigger_input.prop('type') == 'checkbox' ){
				trigger_val = my_form.find(find_this + ':checked').val();
			}
			else {
				trigger_val = trigger_input.val();
			}

// alert( trigger_input.prop('type') + '=' + trigger_val );
// alert( 'search_name = ' + search_name + ', trigger_val = ' + trigger_val + ', what_val = ' + what_value );

			if( what_value.substr(0,1) == '!' ){
				what_value = what_value.substr(1);
				if( what_value != trigger_val ){
					show_this = true;
				}
			}
			else {
				if( what_value == trigger_val ){
					show_this = true;
				}
				else if( what_value == '*' && trigger_val ){
					alert( trigger_val );
					show_this = true;
				}
			}

			if( show_this ){
				if( my_holder.length ){
					my_holder.show();
					my_this.show();
				}
				else {
					my_this.show();
				}
				break;
			}
		}
		// alert( jQuery(this).data('hc-observe') );
	});
	return false;
}

jQuery(document).on('change', '.hcj2-observe input, select', function(event)
{
	return hc_observe_input( jQuery(this) );
});

function hc2_init_page( where )
{
	if( typeof where !== 'undefined' ){
	}
	else {
		if( jQuery(document.body).find("#nts").length ){
			where = jQuery("#nts");
		}
		else {
			where = jQuery(document.body);
		}
	}

	where.find('.hcj2-observe input, select').each( function(){
		hc_observe_input( jQuery(this) );
	});

	where.find('.hcj2-radio-more-info:checked').each( function(){
		var my_container = jQuery( this ).closest('label');
		var my_info = my_container.find('.hcj2-radio-info');
		my_info.show();
	});

	if( where.find('.hc-datepicker2').length ){
		where.find('.hc-datepicker2').hc_datepicker2({
			})
			.on('changeDate', function(ev)
				{
				var dbDate = 
					ev.date.getFullYear() 
					+ "" + 
					("00" + (ev.date.getMonth()+1) ).substr(-2)
					+ "" + 
					("00" + ev.date.getDate()).substr(-2);

			// remove '_display' from end
				var display_id = jQuery(this).attr('id');
				var display_suffix = '_display';
				var value_id = display_id.substr(0, (display_id.length - display_suffix.length) );

				jQuery(this).closest('form').find('#' + value_id)
					.val(dbDate)
					.trigger('change')
					;
				});
	}
}

jQuery(document).ready( function()
{
	hc2_init_page();

	/* add icon for external links */
	// jQuery('#nts a[target="_blank"]').append( '<i class="fa fa-fw fa-external-link"></i>' );

	jQuery('#nts a[target="_blank"]').each(function(index){
		var my_icon = '<i class="fa fa-fw fa-external-link"></i>';
		var common_link_parent = jQuery(this).closest('.hcj2-common-link-parent');
		if( common_link_parent.length > 0 ){
			// common_link_parent.prepend(my_icon);
		}
		else {
			jQuery(this).append(my_icon);
		}
	});

	/* scroll into view */
	if ( typeof nts_no_scroll !== 'undefined' ){
		// no scroll
	}
	else {
		// document.getElementById("nts").scrollIntoView();	
	}

	/* auto dismiss alerts */
	jQuery('.hcj2-auto-dismiss').delay(4000).slideUp(200, function(){
		// jQuery('.hcj2-auto-dismiss .alert').alert('close');
	});
});

jQuery(document).on( 'keypress', '.hcj2-ajax-form input', function(e){
	if( (e.which && e.which == 13) || (e.keyCode && e.keyCode == 13) ){
		var this_form = jQuery(this).closest('.hcj2-ajax-form');
		this_form.trigger('hc2-submit');
		return false;
	}
	else {
		return true;
	}
});

var hc2 = {};

var hc2_spinner = '<span class="hc-m0 hc-p0 hc-fs5 hc-spin hc-inline-block">&#9788;</span>';
var hc2_absolute_spinner = '<div class="hc-fs5 hc-spin hc-inline-block hc-m0 hc-p0" style="position: absolute; top: 45%;"><span class="hc-m0 hc-p0">&#9788;</span></div>';
var hc2_full_spinner = '<div class="hcj2-full-spinner hc-bg-silver hc-muted-2 hc-align-center" style="z-index: 1000; width: 100%; height: 100%; position: absolute; top: 0; left: 0;">' + hc2_absolute_spinner + '</div>';

jQuery(document).on( 'click', '.hcj2-action-trigger', function(event)
{
	var receiver = jQuery(this).closest('.hcj2-action-receiver');
	receiver.trigger( 'receive', jQuery(this).data() );
	return false;
});

jQuery(document).on( 'click', '.hcj2-ajax-loader', function(event)
{
	var me = jQuery(this);
	var ajax_url = me.attr('href');
	if( ! ajax_url ){
		return false;
	}

	var my_parent = me.closest('.hcj2-ajax-parent');
	if( my_parent.length ){
		var target_container = my_parent.find('.hcj2-ajax-container').filter(":first");
	}
	else {
		var target_container = me.closest('.hcj2-ajax-container');
	}

	if( target_container.length ){
		// already loaded? then close
		var current_url = target_container.data('src');
		if( current_url == ajax_url ){
			if( target_container.is(':visible') ){
				// target_container.data('src', '');
				// target_container.html('');
				target_container.hide();
			}
			else {
				target_container.show();
			}
		}
		else {
			hc2_ajax_load( ajax_url, target_container );
			target_container.data('src', ajax_url);
		}

		return false;
	}
});

jQuery(document).on( 'click', '.hcj2-ajax-form:not(.hcj2-custom-handled) input[type="submit"]', function(event)
{
	/* stop form from submitting normally */
	event.preventDefault(); 
	/* get some values from elements on the page: */
	var this_form = jQuery(this).closest('.hcj2-ajax-form');
	var this_form_data = this_form.find('select, textarea, input').serializeArray();

	var target_container = jQuery(this).closest('.hcj2-ajax-container');
	if( target_container.length ){
		var this_referer = target_container.data('src');
		this_form_data.push( {name: "hc-referrer", value: this_referer} );
	}

	var current_html = this_form.html();
	this_form.prepend( hc2_full_spinner );

	var target_url = this_form.attr('action');

	jQuery.ajax({
		type: 'POST',
		url: target_url,
//		dataType: "json",
		dataType: "text",
		data: this_form_data,
		success: function(data, textStatus){
			var is_json = true;
			try {
				var json_data = jQuery.parseJSON( data );
			}
			catch( err ){
				is_json = false;
			}

			if( is_json ){
				this_form.trigger('hc2-json-received', json_data );
				this_form.find('.hcj2-full-spinner').remove();
				// this_form.html( current_html );
			}
			else {
			// html returned
				if( target_container.length ){
					target_container.html(data);
				}
				else {
					this_form.html(data);
				}
			}
		}
		})
		.fail( function(jqXHR, textStatus, errorThrown){
			alert( 'Ajax Error: ' + target_url );
			alert( jqXHR.responseText );
			this_form.html( current_html );
			})
		;
	return false;
});

function hc2_ajax_load( ajax_url, target_container )
{
	if( ! target_container.is(':visible') ){
		target_container.show();
	}

	target_container.prepend( hc2_full_spinner );
	target_container.load( ajax_url, function(){
		hc2_init_page( target_container );
		target_container.data('src', ajax_url);
	});
}

/*
template engine
from https://github.com/jasonmoo/t.js

Simple interpolation: {{=value}}
Scrubbed interpolation: {{%unsafe_value}}
Name-spaced variables: {{=User.address.city}}
If/else blocks: {{value}} <<markup>> {{:value}} <<alternate markup>> {{/value}}
If not blocks: {{!value}} <<markup>> {{/!value}}
Object/Array iteration: {{@object_value}} {{=_key}}:{{=_val}} {{/@object_value}}
*/

(function() {
	var blockregex = /\{\{(([@!]?)(.+?))\}\}(([\s\S]+?)(\{\{:\1\}\}([\s\S]+?))?)\{\{\/\1\}\}/g,
		valregex = /\{\{([=%])(.+?)\}\}/g;

	function Hc2Template(template) {
		this.Hc2Template = template;
	}

	function scrub(val) {
		return new Option(val).innerHTML.replace(/"/g,"&quot;");
	}

	function get_value(vars, key) {
		var parts = key.split('.');
		while (parts.length) {
			if (!(parts[0] in vars)) {
				return false;
			}
			vars = vars[parts.shift()];
		}
		return vars;
	}

	function render(fragment, vars) {
		return fragment
			.replace(blockregex, function(_, __, meta, key, inner, if_true, has_else, if_false) {

				var val = get_value(vars,key), temp = "", i;

				if (!val) {

					// handle if not
					if (meta == '!') {
						return render(inner, vars);
					}
					// check for else
					if (has_else) {
						return render(if_false, vars);
					}

					return "";
				}

				// regular if
				if (!meta) {
					return render(if_true, vars);
				}

				// process array/obj iteration
				if (meta == '@') {
					// store any previous vars
					// reuse existing vars
					_ = vars._key;
					__ = vars._val;
					for (i in val) {
						if (val.hasOwnProperty(i)) {
							vars._key = i;
							vars._val = val[i];
							temp += render(inner, vars);
						}
					}
					vars._key = _;
					vars._val = __;
					return temp;
				}

			})
			.replace(valregex, function(_, meta, key) {
				var val = get_value(vars,key);

				if (val || val === 0) {
					return meta == '%' ? scrub(val) : val;
				}
				return "";
			});
	}

	Hc2Template.prototype.render = function (vars) {
		return render(this.Hc2Template, vars);
	};

	window.Hc2Template = Hc2Template;
})();

function hc2_print_r( thing )
{
	var out = '';
	for( var i in thing ){
		if( typeof thing[i] == 'object' ){
			out += i + ": ";
			for( var j in thing[i] ){
				out += j + ": " + thing[i][j] + ";\n";
			}
			out += "\n";
		}
		else {
			out += i + ": " + thing[i] + "\n";
		}
	}
	alert(out);	
}

// various php functions in javascript taken from locutus.io
function hc2_php_number_format (number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
	var n = !isFinite(+number) ? 0 : +number
	var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
	var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
	var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
	var s = ''

	var toFixedFix = function (n, prec) {
		var k = Math.pow(10, prec)
		return '' + (Math.round(n * k) / k)
		.toFixed(prec)
	}

	// @todo: for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || ''
		s[1] += new Array(prec - s[1].length + 1).join('0')
	}
	return s.join(dec)
}

function hc2_submit_form( target_url, form_data, this_form )
{
	jQuery.ajax({
		type: 'POST',
		url: target_url,
		dataType: "text",
		data: form_data,
		success: function(data, textStatus){
			var is_json = true;
			try {
				var json_data = jQuery.parseJSON( data );
			}
			catch( err ){
				is_json = false;
			}

			if( is_json ){
				this_form.trigger('hc2-json-received', json_data );
				this_form.find('.hcj2-full-spinner').remove();
			}
			else {
			// html returned
				this_form.html(data);
			}
		}
		})
		.fail( function(jqXHR, textStatus, errorThrown){
			var error_msg = 'Ajax error: ' + target_url + ', ' + jqXHR.responseText + '<br/>';
			this_form.find('.hcj2-full-spinner').remove();
			this_form.prepend( error_msg );
			})
		;
}

jQuery(document).on( 'click', '.hcj2-insert-code', function(e)
{
	return false;
});
jQuery(document).on( 'mousedown', '.hcj2-insert-code', function(e)
{
	var $txt = jQuery('textarea:focus');
	if( $txt.length ){
		var txtToAdd = jQuery(this).html();
		var caretPos = $txt[0].selectionStart;
		var textAreaTxt = $txt.val();
		$txt.val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
	}
	else {
		console.log('Please focus on a textarea to add this code to');
	}
	return false;
});
