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
			
			var options = {};
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
			//options.positionBy = 'mouse';	// TODO - add a param for this one if desired
			
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
			return false;
		};
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
		// for every link containing 'shadowbox' or 'colorbox'
		$jq("a[rel*='box']").colorbox({
			transition: "elastic",
			height:"95%",
			overlayClose: true,
			title: true
		});
		// inline content: hrefs starting with #
		$jq("a[rel*='box'][href^='#']").colorbox({
			inline: true,
			href: function(){ 
				return $(this).attr('href');
			}
		});
		// rel containg type=img
		$jq("a[rel*='box'][rel*='type=img']").colorbox({
			photo: true
		});
		// rel containg type=flash
		$jq("a[rel*='box'][rel*='type=flash']").colorbox({
			flash: true				
		});
		// rel containg slideshow
		$jq("a[rel*='box'][rel*='slideshow']").colorbox({
			slideshow: true,
			preloading: false,
			height: "100%"
		});
		// href starting with http(s)
		$jq("a[rel*='box'][href^='http://'], a[rel*='box'][href^='https://']").colorbox({
			iframe: true,
			width: "95%"
		});
		// href starting with ftp(s)
		$jq("a[rel*='box'][href^='ftp://'], a[rel*='box'][href^='ftps://']").colorbox({
			iframe: true,
			width: "95%"
		});
		/* shadowbox params compatibility functions called below (TODO: please combine in one if you know how) */
		getrelgallery = function () {
			re = /(box\[([^\]]+)\])/i;
			ret = $jq(this).attr("rel").match(re);
			return "'"+ret[2]+"'";
		};
		getrelheight = function () {
			re = /(height=([^;\"]+))/i;
			ret = $jq(this).attr("rel").match(re);
			return ret[2];
		};
		getreltitle = function () {
			re = /(title=([^;\"]+))/i;
			ret = $jq(this).attr("rel").match(re);
			return ret[2];
		};
		getrelwidth = function () {
			re = /(width=([^;\"]+))/i;
			ret = $jq(this).attr("rel").match(re);
			return ret[2];
		};
		// rel containg shadowbox[foo] to group objects in "galleries" (shadowbox compatible)
		$jq('a[rel*="box\["]').colorbox({
			rel: getrelgallery
		});
		// rel containg height param (shadowbox compatible)
		$jq("a[rel*='box'][rel*='height']").colorbox({
			height: getrelheight
		});
		// rel containg title param (shadowbox compatible)
		$jq("a[rel*='box'][rel*='title']").colorbox({
			title: getreltitle
		});
		// rel containg width param (shadowbox compatible)
		$jq("a[rel*='box'][rel*='width']").colorbox({
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
		};
	});
}

/* Find caret position in textarea */

function textarea_cursor_offset(input) {
  if (document.selection) {

	// TODO - untested for IE
  	var r = document.selection.createRange();
  	var i;
  	
  	if (input.nodeName == 'TEXTAREA') {
  		var x = r.offsetLeft - r.boundingLeft;
  		var y = r.offsetTop - r.boundingTop;
  	} else {
  		x = r.offsetLeft;
  		y = r.offsetTop;
  	}
  	
  	return {
  		left: x,
  		top: y
  	};
  	
  } else if (typeof input.setSelectionRange != 'undefined') {

	var elementName = $jq(input).attr('id') + '_tcodiv';
  	var newDiv;

	newDiv = document.getElementById(elementName);
  	if (!newDiv) {
		newDiv = document.createElement('div');
		$jq(newDiv).attr('id', elementName).css('wrap', 'hard').css('whiteSpace', 'pre').css('position', 'absolute').css('z-index', -1);
		
		if (input.parentNode.position != 'absolute' && input.parentNode.position != 'relative') {
			input.parentNode.position = 'relative';
		}
				
		var selectors = ['font', 'font-size', 'font-family', 'line-height', 'letter-spacing',
								'padding-left', 'padding-top', 'padding-right', 'padding-bottom',
								'margin-left', 'margin-top', 'margin-right', 'margin-bottom',
								'text-align', 'vertical-align', 'overflow',
								'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width',
								'border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style',
								'border-top-color', 'border-right-color', 'border-bottom-color', 'border-left-color'
								];
		
		for (d in selectors) {
			var s = selectors[d];
			if ($jq(input).css(s)) { $jq(newDiv).css(s, $jq(input).css(s)); }
		}
		
		$jq(newDiv).width( $jq(input).width() ).height( $jq(input).height() );
		var p = $jq(input).offset();
		var q = $jq(input).offsetParent().offset();
		$jq(newDiv).css('left', p.left).css('top', p.top);
			
		document.body.appendChild(newDiv);
		newDiv = document.getElementById(elementName);
	}
	
	newDiv.innerHTML = input.value;
	
	newDiv.scrollLeft = input.scrollLeft;
	newDiv.scrollTop = input.scrollTop;
	//$jq(newDiv).scrollLeft($jq(input).scrollLeft()).scrollTop($jq(input).scrollTop());
		
	r = document.createRange();
    var e = document.createElement('span');

    r.setStart(newDiv.firstChild, input.selectionStart);
    r.setEnd(newDiv.firstChild, input.selectionStart);
    r.surroundContents(e);

    var obj = { 
		left : e.offsetLeft + newDiv.offsetLeft,
		top : e.offsetTop + newDiv.offsetTop
    };

    return obj; 
  }
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
       
    
    if (!index) {
        index = 0;
    }
    if (!pageName) {
        pageName = '';
    }
    if (!pluginArgs) {
        pluginArgs = {};
    }
    if (!bodyContent) {
        if (document.getSelection) {
            bodyContent = document.getSelection();
        } else if (window.getSelection) {
            bodyContent = window.getSelection();
        } else if (document.selection) {
            bodyContent = document.selection.createRange().text;
        } else {
            bodyContent = '';
        }
    }
    
    var form = build_plugin_form(type, index, pageName, pluginArgs, bodyContent);
    $jq(form).find('tr input[type=submit]').remove();
    
    container.append(form);
    document.body.appendChild(container[0]);
	
	var pfc = container.find('table tr').length;	// number of rows
	var t = container.find('textarea:visible').length;
	if (t) { pfc += t * 3; }
	if (pfc > 9) { pfc = 9; }
	if (pfc < 2) { pfc = 2; }
	pfc = pfc / 10;			// factor to scale dialog height
	
	container.dialog({
		width: $jq(window).width() * 0.6,
		height: $jq(window).height() * pfc,
		buttons: { "Cancel": function() { $jq(this).dialog("close"); },
				   "Insert": function() {
        var meta = tiki_plugins[type];
        var params = [];
        var edit = edit_icon;
        
        for (i = 0; i < form.elements.length; i++) {
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
            insertAt(area_name, blob);
        }
		$jq(this).dialog("close");
        return false;
    }}});
    
}






