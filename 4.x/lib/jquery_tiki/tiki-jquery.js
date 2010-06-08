// $Id$
// JavaScript glue for JQuery (1.3.2) in TikiWiki (3.0)

var $jq = jQuery.noConflict();

$jq(document).ready( function() { // JQuery's DOM is ready event - before onload
	
	// Check / Uncheck all Checkboxes - overriden from tiki-js.js
	switchCheckboxes = function (tform, elements_name, state) {
	  // checkboxes need to have the same name elements_name
	  // e.g. <input type="checkbox" name="my_ename[]">, will arrive as Array in php.
		$jq(tform).contents().find('input[name="' + elements_name + '"]:visible').attr('checked', state).change();
	};


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
		} else if (effect === '' || effect == 'normal') {
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
		} else if (effect === '' || effect == 'normal') {
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
			
			if (element.processed) { return false; }
			tip = decodeURIComponent(unescape(tip.replace(/\+/g,'%20')));
			
			var options = {};
			for (var param in params) {
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
			//options.positionBy = 'mouse';	// TODO - add a param for this one if desired
			
			// attach new tip
			//tip = tip.substring(strStart, strEnd);		// trim quotes
			tip = tip.replace(/[\n\r\t]/g, '');			// remove newlines etc
			tip = tip.replace(/\\/g, '');				// remove backslashes
			
			if (element.tipWidth) {
				options.width = element.tipWidth;
			} else if (!options.width) {
				// hack to calculate div width
				var el = document.createElement('DIV');
				$jq(el).css('position', 'absolute').css('visibility', 'hidden');
				document.body.appendChild(el);
/* sept_7 : seems unnecessary AND dangerous
				if (tip.length > 2000) {
					tip = tip.substring(0, 2000); // setting html to anything bigger seems to blow jquery away :(
				}
*/
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
			
			//options.sticky = true; //useful for css work
			$jq(element).cluetip(options);

			if (options.activation == "click") {
				$jq(element).trigger('click');
			} else {
				$jq(element).trigger('mouseover');
			}
			setTimeout(function () { $jq("#cluetip").show(); }, 200);	// IE doesn't necessarily display
			$jq(element).attr("title", "");	// remove temporary title attribute to avoid built in browser tips
			return false;
		};
		
		nd = function() {
			$jq("#cluetip").hide();
		}
	}	// end cluetip setup
	
	// superfish setup (CSS menu effects)
	if (jqueryTiki.superfish) {
		$jq('ul.cssmenu_horiz').supersubs({ 
            minWidth:    11,   // minimum width of sub-menus in em units 
            maxWidth:    20,   // maximum width of sub-menus in em units 
            extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                               // due to slight rounding differences and font-family 
		});
		$jq('ul.cssmenu_vert').supersubs({ 
            minWidth:    11,   // minimum width of sub-menus in em units 
            maxWidth:    20,   // maximum width of sub-menus in em units 
            extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                               // due to slight rounding differences and font-family 
		});
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
	
	// ColorBox setup (Shadowbox, actually "<any>box" replacement)
	if (jqueryTiki.colorbox) {
		$jq().bind('cbox_complete', function(){	
			$jq("#cboxTitle").wrapInner("<div></div>");
		});
				
		// Tiki defaults for ColorBox (to speed it up, matches any link in #col1 only - the main content column):
		
		// for every link containing 'shadowbox' or 'colorbox' in rel attribute
		$jq("#col1 a[rel*='box']").colorbox({
			transition: "elastic",
			maxHeight:"95%",
			maxWidth:"95%",
			overlayClose: true,
			title: true,
			current: jqueryTiki.cboxCurrent
		});
		
		// now, first let suppose that we want to display images in ColorBox by default:
		
		// this matches rel containg type=img or no type= specified
		$jq("#col1 a[rel*='box'][rel*='type=img'], #col1 a[rel*='box'][rel!='type=']").colorbox({
			photo: true
		});
		// rel containg slideshow (this one must be without #col1)
		$jq("a[rel*='box'][rel*='slideshow']").colorbox({
			photo: true,
			slideshow: true,
			slideshowSpeed: 3500,
			preloading: false,
			width: "100%",
			height: "100%"
		});
		// this are the defaults matching all *box links which are not obviously links to images...
		// (if we need to support more, add here... otherwise it is possible to override with type=iframe in rel attribute of a link)
		$jq("#col1 a[rel*='box']:not([rel*='type=img']):not([href*='display']):not([href*='preview']):not([href*='thumb']):not([rel*='slideshow']):not([href*='image']):not([href$='\.jpg']):not([href$='\.jpeg']):not([href$='\.png']):not([href$='\.gif'])").colorbox({
			iframe: true,
			width: "95%",
			height: "95%"
		});
		// hrefs starting with ftp(s)
		$jq("#col1 a[rel*='box'][href^='ftp://'], #col1 a[rel*='box'][href^='ftps://']").colorbox({
			iframe: true,
			width: "95%",
			height: "95%"
		});
		// rel containg type=flash
		$jq("#col1 a[rel*='box'][rel*='type=flash']").colorbox({
			flash: true,
			iframe: false
		});
		// rel with type=iframe (if someone needs to override anything above)
		$jq("#col1 a[rel*='box'][rel*='type=iframe']").colorbox({
			iframe: true
		});
		// inline content: hrefs starting with #
		$jq("#col1 a[rel*='box'][href^='#']").colorbox({
			inline: true,
			width: "50%",
			height: "50%",
			href: function(){ 
				return $jq(this).attr('href');
			}
		});
		
		// titles (for captions):
		
		// by default get title from the title attribute of the link
		$jq("#col1 a[rel*='box'][title]").colorbox({
			title: function(){ 
				return $jq(this).attr('title');
			}
		});
		// but prefer the title from title attribute of a wrapped image if any
		$jq("#col1 a[rel*='box'] img[title]").colorbox({
			title: function(){ 
				return $jq(this).attr('title');
			}
		});
		
		/* Shadowbox params compatibility extracted using regexp functions */
		
		// rel containg title param overrides title attribute of the link (shadowbox compatible)
		$jq("#col1 a[rel*='box'][rel*='title=']").colorbox({
			title: function () {
				re = /(title=([^;\"]+))/i;
				ret = $jq(this).attr("rel").match(re);
				return ret[2];
			}
		});
		// rel containg height param (shadowbox compatible)
		$jq("#col1 a[rel*='box'][rel*='height=']").colorbox({
			height: function () {
				re = /(height=([^;\"]+))/i;
				ret = $jq(this).attr("rel").match(re);
				return ret[2];
			}
		});
		// rel containg width param (shadowbox compatible)
		$jq("#col1 a[rel*='box'][rel*='width=']").colorbox({
			width: function () {
				re = /(width=([^;\"]+))/i;
				ret = $jq(this).attr("rel").match(re);
				return ret[2];
			}
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
		};
	});
}

/// jquery ui dialog replacements for popup form code
/// need to keep the old non-jq version in tiki-js.js as jq-ui is optional (Tiki 4.0)
/// TODO refactor for 4.n

/* wikiplugin editor */
function popupPluginForm(area_name, type, index, pageName, pluginArgs, bodyContent, edit_icon){
    if (!$jq.ui) {
        return popup_plugin_form(area_name, type, index, pageName, pluginArgs, bodyContent, edit_icon); // ??
    }
    var container = $jq('<div class="plugin"></div>');
    var tempSelectionStart, tempSelectionEnd;

    if (!index) {
        index = 0;
    }
    if (!pageName) {
        pageName = '';
    }
	var textarea = getElementById(area_name);	// use weird version of getElementById in tiki-js.js (also gets by name)
	var replaceText = false;
	
	// quick fix for Firefox 3.5 losing selection on changes to popup
    if (typeof textarea != 'undefined' && typeof textarea.selectionStart != 'undefined') {
		tempSelectionStart = textarea.selectionStart;
    	tempSelectionEnd = textarea.selectionEnd;
	}

   if (!pluginArgs && !bodyContent) {
	    pluginArgs = {};
	    bodyContent = "";
		
		var sel = getSelection( textarea );
		if (sel.length > 0) {
			sel = sel.replace(/^\s\s*/, "").replace(/\s\s*$/g, "");	// trim
			//alert(sel.length);
			if (sel.length > 0 && sel.substring(0, 1) == "{") { // whole plugin selected
				var l = type.length;
				if (sel.substring(1, l + 1).toUpperCase() == type.toUpperCase()) { // same plugin
					var rx = new RegExp("{" + type + "[\\(]?([\\s\\S^\\)]*?)[\\)]?}([\\s\\S]*){" + type + "}", "mi"); // using \s\S matches all chars including lineends
					var m = sel.match(rx);
					if (!m) {
						rx = new RegExp("{" + type + "[\\(]?([\\s\\S^\\)]*?)[\\)]?}([\\s\\S]*)", "mi"); // no closing tag
						m = sel.match(rx);
					}
					if (m) {
						var paramStr = m[1];
						bodyContent = m[2];
						
						var pm = paramStr.match(/([^=]*)=\"([^\"]*)\"\s?/gi);
						if (pm) {
							for (i in pm) {
								var ar = pm[i].split("=");
								if (ar.length) { // add cleaned vals to params object
									pluginArgs[ar[0].replace(/^[,\s\"\(\)]*/g, "")] = ar[1].replace(/^[,\s\"\(\)]*/g, "").replace(/[,\s\"\(\)]*$/g, "");
								}
							}
						}
					}
					replaceText = sel;
				} else {
					if (!confirm("You appear to have selected text for a different plugin, do you wish to continue?")) {
						return false;
					}
					bodyContent = sel;
					replaceText = true;
				}
			} else { // not (this) plugin
				bodyContent = sel;
				replaceText = true;
			}
		} else {	// no selection
			replaceText = false;
		}
    }
    
    var form = build_plugin_form(type, index, pageName, pluginArgs, bodyContent);
    $jq(form).find('tr input[type=submit]').remove();
    
    container.append(form);
    document.body.appendChild(container[0]);
	
	var pfc = container.find('table tr').length;	// number of rows (plugin form contents)
	var t = container.find('textarea:visible').length;
	if (t) { pfc += t * 3; }
	if (pfc > 9) { pfc = 9; }
	if (pfc < 2) { pfc = 2; }
	pfc = pfc / 10;			// factor to scale dialog height
	
	var btns = {};
	var closeText = "Close";
	btns[closeText] = function() {
		
		$jq(this).dialog("close");

		// quick fix for Firefox 3.5 losing selection on changes to popup
		if (tempSelectionStart) {
        	if (typeof textarea.selectionStart != 'undefined' && textarea.selectionStart != tempSelectionStart) {
        		textarea.selectionStart = tempSelectionStart;
        	}
        	if (typeof textarea.selectionEnd != 'undefined' && textarea.selectionEnd != tempSelectionEnd) {
				textarea.selectionEnd = tempSelectionEnd;
			}
		}
		var ta = getElementById(area_name);
		if (ta) { ta.focus(); }
	};
	
	btns[replaceText ? "Replace" : edit_icon ? "Submit" : "Insert"] = function() {
        var meta = tiki_plugins[type];
        var params = [];
        var edit = edit_icon;
        
        for (var i = 0; i < form.elements.length; i++) {
            element = form.elements[i].name;
            
            var matches = element.match(/params\[(.*)\]/);
            
            if (matches === null) {
                // it's not a parameter, skip 
                continue;
            }
            var param = matches[1];
            
            var val = form.elements[i].value;
            
            if (val !== '') {
                params.push(param + '="' + val + '"');
            }
        }
        
        var blob = '{' + type.toUpperCase() + '(' + params.join(',') + ')}' + (typeof form.content != 'undefined' ? form.content.value : '') + '{' + type.toUpperCase() + '}';
        
        if (edit) {
            container.children('form').submit();
        } else {
//			getElementById(area_name).focus(); // unsuccesfull attempt to get Fx3.5/win to keep selection info
            insertAt(area_name, blob, false, false, replaceText);
        }

		$jq(this).dialog("close");
	        
		// quick fix for Firefox 3.5 losing selection on changes to popup
		if (tempSelectionStart) {
			if (textarea.selectionStart != tempSelectionStart) {
				textarea.selectionStart = tempSelectionStart;
			}
			if (textarea.selectionEnd != tempSelectionEnd) {
				textarea.selectionEnd = tempSelectionEnd;
			}
		}
        return false;
    };

	container.dialog('destroy').dialog({
		width: $jq(window).width() * 0.6,
		height: $jq(window).height() * pfc,
		autoOpen: false }).dialog('option', 'buttons', btns).dialog("open");
   
	// quick fix for Firefox 3.5 losing selection on changes to popup
	if (tempSelectionStart) {
		if (typeof textarea.selectionStart != 'undefined' && textarea.selectionStart != tempSelectionStart) {
			textarea.selectionStart = tempSelectionStart;
		}
		if (typeof textarea.selectionEnd != 'undefined' && textarea.selectionEnd != tempSelectionEnd) {
			textarea.selectionEnd = tempSelectionEnd;
		}
	}
// getElementById(area_name).focus(); // unsuccesfull attempt to get Fx3.5/win
// to keep selection info
}

// temporary debug helper
//setInterval(function () {
//	$jq("#qe-1").val( $jq("#editwiki")[0].selectionStart + "," + $jq("#editwiki")[0].selectionEnd );
//}, 200);

/* Simple tiki plugin for jQuery
 * Initially for autocomplete helpers
 */

$jq.fn.tiki = function(func, type, options) {
	if (typeof type != 'undefined') { // func and type given
		options = options || {};
		var opts = {extraParams: {"httpaccept": "text/javascript"},
				dataType: "json",
				parse: parseAutoJSON,
				formatItem: function(row) { return row; },
				selectFirst: false
				};
		for(opt in options) {
			opts[opt] = options[opt];
		}

		switch (func) {
			case "autocomplete":
			if (jqueryTiki.autocomplete) {
				var data = "";
				switch (type) {
					case "pagename":
						data = "tiki-listpages.php?listonly";
						break;
					case "groupname":
						data = "tiki-ajax_services.php?listonly=groups";
						break;
					case "username":
						data = "tiki-ajax_services.php?listonly=users";
						break;
				}
		 		return this.each(function() {
					$jq(this).autocomplete(data, opts);
		
				});
			}
			break;
		}
	}
};




