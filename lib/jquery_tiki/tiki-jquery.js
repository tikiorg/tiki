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
		
		// override overlib
		convertOverlib = function (element, tip, params) {	// process modified overlib event fn to cluetip from {popup} smarty func
			
			if (element.processed) { return; }
			
			var options = new Object();
			for (param in params) {
				var val = "";
				var i = params[param].indexOf("=");
				if (i > -1) {
					var arr = params[param].split("=", 2);
					pam = params[param].substring(0, i).toLowerCase();
					val = params[param].substring(i+1);
				} else {
					pam = params[param].toLowerCase();
				}
				switch (pam) {
					case "sticky":
						options.sticky = true;
						break;
					case "fullhtml":
						options.cluetipClass = 'fullhtml';
						break;
					case "background":
						options.cluetipClass = 'fullhtml';
						tip = '<div style="background-image: url(' + val + '); height:' + options.height + 'px">' + tip + '</div>';
						break;
					case "onclick":
						options.activation = 'click';
						break;
					case "width":
						options.width = val;
						break;
					case "height":
						options.height = val;
						break;
					default:
						break;
				}
			}
			
			options.splitTitle = '|';
			options.showTitle = false;
			options.cluezIndex = 400;
			options.dropShadow = true;
			options.fx = {open: 'fadeIn', openSpeed: 'fast'};
			options.closeText = 'x';
			options.closePosition = 'title';
			options.mouseOutClose = true;
			
			// attach new tip
			//tip = tip.substring(strStart, strEnd);		// trim quotes
			tip = tip.replace(/\\n/g, '');			// remove newlines
			tip = tip.replace(/\\/g, '');				// remove backslashes
			
			if (element.tipWidth) {
				options.width = element.tipWidth;
			} else if (!options.width) {
				// hack to calculate div width
				var el = document.createElement('DIV');
				$jq(el).css('position', 'absolute').css('visibility', 'hidden');
				document.body.appendChild(el);
				if (tip.length > 2000) {
					tip = tip.substring(0, 2000); // setting html to anything bigger seems to blow jquery away :(
				}
				$jq(el).html(tip);
				if ($jq(el).width() > $jq(window).width()) {
					$jq(el).width($jq(window).width() * 0.8);
				}
				options.width = $jq(el).width();
				document.body.removeChild(el);
				
				element.tipWidth = options.width;
			}
			
			prefix = "|";
			$jq(element).attr('title', prefix + tip);
			
			element.processed = true;
			
			// options.sticky = true; useful for css work
			$jq(element).cluetip(options);

			if (options.activation == "click") {
				$jq(element).trigger('click');
			} else {
				$jq(element).trigger('mouseover');
			}
		}
	}	// end cluetip setup
	
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
		$jq().bind('cbox_complete', function(){	
			$jq("#cboxTitle").wrapInner("<div></div>");
		});
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
		/* shadowbox params compatibility functions called below (TODO: please combine in one if you know how) */
		getrelgallery = function () {
			re = /(shadowbox\[([^\]]+)\])/i
			ret = $jq(this).attr("rel").match(re);
			return "'"+ret[2]+"'"
		}
		getrelheight = function () {
			re = /(height=([^;\"]+))/i
			ret = $jq(this).attr("rel").match(re);
			return ret[2]
		}
		getreltitle = function () {
			re = /(title=([^;\"]+))/i
			ret = $jq(this).attr("rel").match(re);
			return ret[2]
		}
		getrelwidth = function () {
			re = /(width=([^;\"]+))/i
			ret = $jq(this).attr("rel").match(re);
			return ret[2]
		}
		// rel containg shadowbox[foo] to group objects in "galleries" (shadowbox compatible)
		$jq('a[rel*="shadowbox\["]').colorbox({
			rel: getrelgallery
		});
		// rel containg height param (shadowbox compatible)
		$jq("a[rel*='shadowbox'][rel*='height']").colorbox({
			height: getrelheight
		});
		// rel containg title param (shadowbox compatible)
		$jq("a[rel*='shadowbox'][rel*='title']").colorbox({
			title: getreltitle
		});
		// rel containg width param (shadowbox compatible)
		$jq("a[rel*='shadowbox'][rel*='width']").colorbox({
			width: getrelwidth
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

/* Find caret position in textarea */

function textarea_cursor_offset(input) {
  if (document.selection) {
  
  	var r = document.selection.createRange();
  	
  	var i;
  	
  	if (input.nodeName == 'TEXTAREA') {
  		var x = r.offsetLeft - r.boundingLeft;
  		var y = r.offsetTop - r.boundingTop;
  	} else {
  		var x = r.offsetLeft;
  		var y = r.offsetTop;
  	}
  	
  	return {
  		left: x,
  		top: y
  	};
  	
  } else if (typeof input.setSelectionRange != 'undefined') {

	var elementName = $jq(input).attr('id') + '_tcodiv'
  	var n, i;

	n = document.getElementById(elementName);
  	if (!n) {
		n = document.createElement('div');
		$jq(n).attr('id', elementName).css('wrap', 'hard').css('whiteSpace', 'pre').css('position', 'absolute').css('z-index', -1).css('overflow', 'auto');
		
		if (input.parentNode.position != 'absolute' && input.parentNode.position != 'relative') {
			input.parentNode.position = 'relative';
		}
		
		
//		var s = document.defaultView.getComputedStyle(input,null);
//		
//		for(i in s) {
//			if( i.indexOf('font') > -1 || i.indexOf('padding') > -1 || i.indexOf('margin') > -1 || i.indexOf('line') > -1 || i.indexOf('letter') > -1) {
//				if (s[i]) {
//					//n.style[i] = s[i];
//					$jq(n).css(i, s[i]);
//				}
//			}
//		}
			
		if ($jq(input).css('font')) { $jq(n).css('font', $jq(input).css('font')); }
		if ($jq(input).css('font-size')) { $jq(n).css('font-size', $jq(input).css('font-size')); }
		if ($jq(input).css('font-family')) { $jq(n).css('font-family', $jq(input).css('font-family')); }
		if ($jq(input).css('line-height')) { $jq(n).css('line-height', $jq(input).css('line-height')); }
		if ($jq(input).css('letter-spacing')) { $jq(n).css('letter-spacing', $jq(input).css('letter-spacing')); }
		if ($jq(input).css('padding-left')) { $jq(n).css('padding-left', $jq(input).css('padding-left')); }
		if ($jq(input).css('padding-top')) { $jq(n).css('padding-top', $jq(input).css('padding-top')); }
		if ($jq(input).css('padding-right')) { $jq(n).css('padding-right', $jq(input).css('padding-right')); }
		if ($jq(input).css('padding-bottom')) { $jq(n).css('padding-bottom', $jq(input).css('padding-bottom')); }
		if ($jq(input).css('margin-left')) { $jq(n).css('margin-left', $jq(input).css('margin-left')); }
		if ($jq(input).css('margin-top')) { $jq(n).css('margin-top', $jq(input).css('margin-top')); }
		if ($jq(input).css('margin-right')) { $jq(n).css('margin-right', $jq(input).css('margin-right')); }
		if ($jq(input).css('margin-bottom')) { $jq(n).css('margin-bottom', $jq(input).css('margin-bottom')); }
		
		//$jq(n).width($jq(input).width()).height($jq(input).height());
		//var p = $jq(input).offsetParent().offset();
		//$jq(n).css('left', p.left).css('top', p.top);
			
		n.style.width = input.offsetWidth+'px';
		n.style.height = input.offsetHeight+'px';
		n.style.left = input.offsetLeft+'px';
		n.style.top = input.offsetTop+'px';
		//n.style.overflow = 'auto';
		
		
		input.parentNode.appendChild(n);
		n = document.getElementById(elementName);
	}
	
	n.innerHTML = input.value;
	
	n.scrollLeft = input.scrollLeft;
	n.scrollTop = input.scrollTop;
	//$jq(n).scrollLeft($jq(input).scrollLeft()).scrollTop($jq(input).scrollTop());
		
	var r = document.createRange();
    var e = document.createElement('span');

    r.setStart(n.firstChild, input.selectionStart);
    r.setEnd(n.firstChild, input.selectionStart);
    r.surroundContents(e);

    var obj = { 
		left : e.offsetLeft + n.offsetLeft,
		top : e.offsetTop + n.offsetTop
    };

    return obj; 
  }
}




