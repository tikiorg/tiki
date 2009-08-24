// $Id$
// JavaScript glue for JQuery (1.3.2) in TikiWiki (3.0)

var $jq = jQuery.noConflict();

$jq(document).ready( function() { // JQuery's DOM is ready event - before onload
	
	// Check / Uncheck all Checkboxes - overriden from tiki-js.js
	switchCheckboxes = function (tform, elements_name, state) {
	  // checkboxes need to have the same name elements_name
	  // e.g. <input type="checkbox" name="my_ename[]">, will arrive as Array in php.
		$jq(tform).contents().find('input[name="' + elements_name + '"]:visible').attr('checked', state);
	}


	// override existing show/hide routines here

	var jqNoAnimElements = ['help_sections', 'ajaxLoading'];

	show = function (foo,f,section) {
		if (jQuery.inArray(foo, jqNoAnimElements) > -1) {		// exceptions that don't animate reliably
			$jq("#" + foo).show();
		} else if ($jq("#" + foo).hasClass("tabcontent")) {		// different anim prefs for tabs
			showJQ("#" + foo, jqueryTiki.effect_tabs, jqueryTiki.effect_tabs_speed, jqueryTiki.effect_tabs_direction);
		} else {
			showJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		}
		if (f) { setCookie(foo, "o", section); }
	};
	
	hide = function (foo,f, section) {
		if (jQuery.inArray(foo, jqNoAnimElements) > -1) {		// exceptions
			$jq("#" + foo).hide();
		} else if ($jq("#" + foo).hasClass("tabcontent")) {
			hideJQ("#" + foo, jqueryTiki.effect_tabs, jqueryTiki.effect_tabs_speed, jqueryTiki.effect_tabs_direction);
		} else {
			hideJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		}
		if (f) {
			var wasnot = getCookie(foo, section, 'x') == 'x';
			setCookie(foo, "c", section);
			if (wasnot) {
				history.go(0);	// ik!
			}
		}
	};
	
	// flip function... unfortunately didn't use show/hide (ay?)
	flip = function (foo,style) {
		if (style && style != 'block' || foo == 'help_sections' || foo == 'fgalexplorer') {	// TODO find a better way?
			$jq("#" + foo).toggle();	// inlines don't animate reliably (yet) (also help)
			if ($jq("#" + foo).css('display') == 'none') {
				setSessionVar('show_' + escape(foo), 'n');
			} else {
				setSessionVar('show_' + escape(foo), 'y');
			}
		} else {
			if ($jq("#" + foo).css("display") == "none") {
				setSessionVar('show_' + escape(foo), 'y');
				showJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
			}
			else {
				setSessionVar('show_' + escape(foo), 'n');
				hideJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
			}
		}
	};

	// handle JQ effects
	showJQ = function (selector, effect, speed, dir) {
		if (effect == 'none') {
			$jq(selector).show();
		} else if (effect == '' || effect == 'normal') {
			$jq(selector).show(speed);
		} else if (effect == 'slide') {
			$jq(selector).slideDown(speed);
		} else if (effect == 'fade') {
			$jq(selector).fadeIn(speed);
		} else if (effect.match(/(.*)_ui$/).length > 1) {
			$jq(selector).show(effect.match(/(.*)_ui$/)[1], {direction: dir }, speed);
		} else {
			$jq(selector).show();
		}
	};
	
	hideJQ = function (selector, effect, speed, dir) {
		if (effect == 'none') {
			$jq(selector).hide();
		} else if (effect == '' || effect == 'normal') {
			$jq(selector).hide(speed);
		} else if (effect == 'slide') {
			$jq(selector).slideUp(speed);
		} else if (effect == 'fade') {
			$jq(selector).fadeOut(speed);
		} else if (effect.match(/(.*)_ui$/).length > 1) {
			$jq(selector).hide(effect.match(/(.*)_ui$/)[1], {direction: dir }, speed);
		} else {
			$jq(selector).hide();
		}
	};
	
	// tooltip functions and setup
	if (jqueryTiki.tooltips) {	// apply "cluetips" to all .tips class anchors
		$jq('.tips').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400, fx: {open: 'fadeIn', openSpeed: 'fast'}});
		$jq('.titletips').cluetip({splitTitle: '|', cluezIndex: 400});
		$jq('.tikihelp').cluetip({splitTitle: ':', width: '150px', cluezIndex: 400, fx: {open: 'fadeIn', openSpeed: 'fast'}});
		$jq('.stickytips').cluetip({ showTitle: false, width: 'auto', cluezIndex: 400, sticky: false, local: true, hideLocal: true, activation: 'click', cluetipClass: 'fullhtml', fx: {open: 'fadeIn', openSpeed: 'fast'}});
		
		// override overlib - TODO optimise JQuery calls
		convertOverlib = function (element, tip, options) {	// process overlib event fn to cluetip
			
			tip = tip.replace(/return overlib\((.*)\).*/gi, '$1');	// remove function wrapper 
			var prefix = "";
			var strStart = 0;
			var strEnd = tip.length;
			
			if (tip.substring(0,1) == "'") {				// overlib is called with a string, then a few "constant" params
				strStart = 1;								// this bit extracts the first string (couldn't get a regexp to work reliably)
				strEnd = tip.lastIndexOf("'");
			} else if (tip.substring(0,1) == '"') {
				strStart = 1;
				strEnd = tip.lastIndexOf('"');
			}
			
			// process parameters
			params = tip.substring(strEnd-1, tip.length).toLowerCase();	// possibly: sticky,autostatus,autostatuscap,fullhtml,hauto,vauto,closeclick,wrap,followmouse,mouseoff,compatmode
			if (params.indexOf('sticky') > -1) { options.sticky = true; } else { options.sticky = false; }
			//if (params.indexOf('mouseoff') > -1) {}	// TODO?
			if (params.indexOf('fullhtml') > -1) { options.cluetipClass = 'fullhtml'; } else { options.cluetipClass = 'default'; }
			options.splitTitle = '|';
			options.showTitle = false;
			options.width = 'auto';		// this don't work :(
			options.cluezIndex = 400;
			options.dropShadow = true;
			options.fx = {open: 'fadeIn', openSpeed: 'fast'};
			options.closeText = 'x';
			options.closePosition = 'title';
			options.mouseOutClose = true;
			
			// attach new tip
			tip = tip.substring(strStart, strEnd);		// trim quotes
			tip = tip.replace(/\\n/g, '');			// remove newlines
			tip = tip.replace(/\\/g, '');				// remove backslashes
			
			// hack to calculate div width
			var el = document.createElement('DIV');
			$jq(el).css('position', 'absolute').css('visibility', 'hidden');
			document.body.appendChild(el);
			if (tip.length > 1500) {
				tip = tip.substring(0, 1500);	// setting html to anything bigger seems to blow jquery away :(
			}
			$jq(el).html(tip);
			if ($jq(el).width() > $jq(window).width()) {
				$jq(el).width($jq(window).width() * 0.8);
			}
			options.width = $jq(el).width();
			document.body.removeChild(el);

			prefix = "|";
			$jq(element).attr('title', prefix + tip);
			
			// options.sticky = true; useful for css work
			$jq(element).cluetip(options);
		}
		
		$jq("[onmouseover*='overlib(']").each(function() {
			tip = $jq(this)[0].getAttribute('onmouseover');			// use the html call to get the text, not the fn
			convertOverlib(this, tip, {});
		});
		
		$jq("[onclick*='overlib(']").each(function() {			// TODO refactor!!!!
			tip = $jq(this)[0].getAttribute('onclick');			// use the html call to get the text, not the fn
			convertOverlib(this, tip, {activation: 'click'});
		});
		
		// nobble overlib funcs
		overlib = function() {};
		nd = function() {};
	}
	
	// superfish setup (CSS menu effects)
	if (jqueryTiki.superfish) {
		$jq('ul.cssmenu_horiz').superfish({
			animation: {opacity:'show', height:'show'},	// fade-in and slide-down animation
			speed: 'fast'								// faster animation speed
		});
		$jq('ul.cssmenu_vert').superfish({
			animation: {opacity:'show', height:'show'},	// fade-in and slide-down animation
			speed: 'fast'								// faster animation speed
		});
	}
	
	// tablesorter setup (sortable tables?)
	if (jqueryTiki.tablesorter) {
		$jq('.sortable').tablesorter({
			widthFixed: true							// ??
//			widgets: ['zebra'],							// stripes (coming soon)
		});
	}
	
	// colorbox setup (shadowbox replacement)
	if (jqueryTiki.colorbox) {
		// for every link containing 'shadowbox'
		$jq("a[rel*='shadowbox']").colorbox({
			transition:"elastic",
			height:"95%",
			overlayClose: true,
			title: true
		});
		// rel containg type=img
		$jq("a[rel*='shadowbox'][rel*='type=img']").colorbox({
			photo: true
		});
		// rel containg type=flash
		$jq("a[rel*='shadowbox'][rel*='type=flash']").colorbox({
			flash: true				
		});
		// rel containg slideshow
		$jq("a[rel*='shadowbox'][rel*='slideshow']").colorbox({
			slideshow: true,
			preloading: false,
			height: "100%"
		});
		// href starting with http(s)
		$jq("a[rel*='shadowbox'][href^='http://'], a[rel*='shadowbox'][href^='https://']").colorbox({
			iframe: true,
			width: "95%"
		});
		// href starting with ftp(s)
		$jq("a[rel*='shadowbox'][href^='ftp://'], a[rel*='shadowbox'][href^='ftps://']").colorbox({
			iframe: true,
			width: "95%"
		});
	}
	
});		// end $jq(document).ready


/* Autocomplete assistants */

function parseAutoJSON(data) {
	var parsed = [];
	return $jq.map(data, function(row) {
		return {
			data: row,
			value: row,
			result: row
		}
	});
}



