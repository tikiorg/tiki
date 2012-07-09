// $Id$
// JavaScript glue for jQuery in Tiki
//
// Tiki 6 - $ is now initialised in jquery.js
// but let's keep $jq available too for legacy custom code

var $jq = $,
$window = $(window)
$document = $(document);

// Escape a string for use as a jQuery selector value, for example an id or a class
function escapeJquery(str) {
	return str.replace(/([\!"#\$%&'\(\)\*\+,\.\/:;\?@\[\\\]\^`\{\|\}\~=>])/g, "\\$1");
}

// Check / Uncheck all Checkboxes - overriden from tiki-js.js
function switchCheckboxes (tform, elements_name, state) {
	// checkboxes need to have the same name elements_name
	// e.g. <input type="checkbox" name="my_ename[]">, will arrive as Array in php.
	$(tform).contents().find('input[name="' + escapeJquery(elements_name) + '"]:visible').attr('checked', state).change();
}


// override existing show/hide routines here

// add id's of any elements that don't like being animated here
var jqNoAnimElements = ['help_sections', 'ajaxLoading'];

function show(foo, f, section) {
	if ($.inArray(foo, jqNoAnimElements) > -1 || typeof jqueryTiki === 'undefined') {		// exceptions that don't animate reliably
		$("#" + foo).show();
	} else if ($("#" + foo).hasClass("tabcontent")) {		// different anim prefs for tabs
		showJQ("#" + foo, jqueryTiki.effect_tabs, jqueryTiki.effect_tabs_speed, jqueryTiki.effect_tabs_direction);
	} else {
		if ($.browser.webkit && !jqueryTiki.effect && $("#role_main #" + foo).length) {	// safari/chrome does strange things with default amination in central column
			showJQ("#" + foo, "slide", jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		} else {
			showJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		}
	}
	if (f) {setCookie(foo, "o", section);}
}

function hide(foo, f, section) {
	if ($.inArray(foo, jqNoAnimElements) > -1 || typeof jqueryTiki === 'undefined') {		// exceptions
		$("#" + foo).hide();
	} else if ($("#" + foo).hasClass("tabcontent")) {
		hideJQ("#" + foo, jqueryTiki.effect_tabs, jqueryTiki.effect_tabs_speed, jqueryTiki.effect_tabs_direction);
	} else {
		hideJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
	}
	if (f) {
//		var wasnot = getCookie(foo, section, 'x') == 'x';
		setCookie(foo, "c", section);
//		if (wasnot) {
//			history.go(0);	// used to reload the page with all menu items closed - broken since 3.x
//		}
	}
}

// flip function... unfortunately didn't use show/hide (ay?)
function flip(foo, style) {
	if (style && style !== 'block' || foo === 'help_sections' || foo === 'fgalexplorer' || typeof jqueryTiki === 'undefined') {	// TODO find a better way?
		$("#" + foo).toggle();	// inlines don't animate reliably (yet) (also help)
		if ($("#" + foo).css('display') === 'none') {
			setSessionVar('show_' + escape(foo), 'n');
		} else {
			setSessionVar('show_' + escape(foo), 'y');
		}
	} else {
		if ($("#" + foo).css("display") === "none") {
			setSessionVar('show_' + escape(foo), 'y');
			show(foo);
		}
		else {
			setSessionVar('show_' + escape(foo), 'n');
			hide(foo);
		}
	}
}

// handle JQ effects
function showJQ(selector, effect, speed, dir) {
	if (effect === 'none') {
		$(selector).show();
	} else if (effect === '' || effect === 'normal') {
		$(selector).show(400);	// jquery 1.4 no longer seems to understand 'nnormal' as a speed
	} else if (effect == 'slide') {
		// With jquery 1.4.2 (and less) and IE7, the function slidedown is buggy
		// See: http://dev.jquery.com/ticket/3120
		if ($.browser.msie && parseInt($.browser.version, 10) == 7)	{
			$(selector).show(speed);
		} else {
			$(selector).slideDown(speed);
		}
	} else if (effect === 'fade') {
		$(selector).fadeIn(speed);
	} else if (effect.match(/(.*)_ui$/).length > 1) {
		$(selector).show(effect.match(/(.*)_ui$/)[1], {direction: dir}, speed);
	} else {
		$(selector).show();
	}
}

function hideJQ(selector, effect, speed, dir) {
	if (effect === 'none') {
		$(selector).hide();
	} else if (effect === '' || effect === 'normal') {
		$(selector).hide(400);	// jquery 1.4 no longer seems to understand 'nnormal' as a speed
	} else if (effect === 'slide') {
		$(selector).slideUp(speed);
	} else if (effect === 'fade') {
		$(selector).fadeOut(speed);
	} else if (effect.match(/(.*)_ui$/).length > 1) {
		$(selector).hide(effect.match(/(.*)_ui$/)[1], {direction: dir}, speed);
	} else {
		$(selector).hide();
	}
}

// override overlib
function convertOverlib(element, tip, params) {	// process modified overlib event fn to cluetip from {popup} smarty func
	
	if ($(element).data('processed') || typeof $(element).cluetip != "function") {return false;}
	if (typeof params == "undefined") {params = [];}
	
	var options = {};
	options.clickThrough = true;
	for (var param = 0; param < params.length; param++) {
		var val = "", pam;
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
				options.clickThrough = false;
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
	
	if (element.tipWidth) {
		options.width = element.tipWidth;
	} else if (!options.width || options.width === 'auto') {
		// hack to calculate div width
		var $el = $("<div />")
			.css('display', 'none')
			.insertBefore("#main")
			.html(tip);
		
		if ($el.width() > $window.width()) {
			$el.width($window.width() * 0.8);
		}
		options.width = $el.width() + 26;	// add space for css padding
		$el.remove();
		
		element.tipWidth = options.width;
	}
	
	var prefix = "|";
	$(element).attr('title', prefix + tip);

	if (options.activation !== 'click') {
		options.hoverIntent = {sensitivity: 3, interval: 50, timeout: 0};
	}
	$(element).data('processed', true);	

//options.sticky = true; //useful for css work
	$(element).cluetip(options);

	if (options.activation === 'click') {
		$(element).trigger('click');
	} else {
		$(element).trigger('mouseover');
	}
	setTimeout(function () {$("#cluetip").show();}, 200);	// IE doesn't necessarily display
	$(element).attr("title", "");	// remove temporary title attribute to avoid built in browser tips
	return false;
}

function nd() {
	$("#cluetip").hide();
}

// ajax loading indicator

function ajaxLoadingShow(destName) {
	var $dest, $loading, pos, x, y, w, h;
	
	if (typeof destName === 'string') {
		$dest = $('#' + destName);
	} else {
		$dest = $(destName);
	}
	if ($dest.length === 0 || $dest.parents(":hidden").length > 0) {
		return;
	}
	$loading = $('#ajaxLoading');

	// find area of destination element
	pos = $dest.offset();
	// clip to page
	if (pos.left + $dest.width() > $window.width()) {
		w = $window.width() - pos.left;
	} else {
		w = $dest.width();
	}
	if (pos.top + $dest.height() > $window.height()) {
		h = $window.height() - pos.top;
	} else {
		h = $dest.height();
	}
	x = pos.left + (w / 2) - ($loading.width() / 2);
	y = pos.top + (h / 2) - ($loading.height() / 2);
	

	// position loading div
	$loading.css('left', x).css('top', y);
	// now BG
	x = pos.left + ccsValueToInteger($dest.css("margin-left"));
	y = pos.top + ccsValueToInteger($dest.css("margin-top"));
	w = ccsValueToInteger($dest.css("padding-left")) + $dest.width() + ccsValueToInteger($dest.css("padding-right"));
	h = ccsValueToInteger($dest.css("padding-top")) + $dest.height() + ccsValueToInteger($dest.css("padding-bottom"));
	$('#ajaxLoadingBG').css('left', pos.left).css('top', pos.top).width(w).height(h).fadeIn("fast");
	
	show('ajaxLoading');

	
}

function ajaxLoadingHide() {
	hide('ajaxLoading');
	$('#ajaxLoadingBG').fadeOut("fast");
}


function checkDuplicateRows( button, columnSelector, rowSelector ) {
	if (typeof columnSelector === 'undefined') {
		columnSelector = "td";
	}
	if (typeof rowSelector === 'undefined') {
		rowSelector = "table:first tr:not(:first)";
	}
	var $rows = $(button).parents(rowSelector);
	$rows.each(function( ix, el ){
		if ($("input:checked", el).length === 0) {
			var $el = $(el);
			var line = $el.find(columnSelector).text();
			$rows.each(function( ix, el ){
				if ($el[0] !== el && $("input:checked", el).length === 0) {
					if (line === $(el).find(columnSelector).text()) {
						$(":checkbox:first", el).attr("checked", true);
					}
				}
			});
		}
	});
}

function setUpClueTips() {
	var ctOptions = {splitTitle: '|', cluezIndex: 2000, width: 'auto', fx: {open: 'fadeIn', openSpeed: 'fast'},
		clickThrough: true, hoverIntent: {sensitivity: 3, interval: 100, timeout: 0}};

	$.cluetip.setup = {insertionType: 'insertBefore', insertionElement: '#main'};
	
	$('.tips[title!=""]').cluetip($.extend(ctOptions, {}));
	$('.titletips[title!=""]').cluetip($.extend(ctOptions, {}));
	$('.tikihelp[title!=""]').cluetip($.extend(ctOptions, {splitTitle: ':'})); // , width: '150px'
	
	// unused?
	$('.stickytips').cluetip($.extend(ctOptions, {showTitle: false, sticky: false, local: true, hideLocal: true, activation: 'click', cluetipClass: 'fullhtml'}));
	
	// repeats for "tiki" buttons as you cannot set the class and title on the same element with that function (it seems?)
	//$('span.button.tips a').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400, fx: {open: 'fadeIn', openSpeed: 'fast'}, clickThrough: true});
	//$('span.button.titletips a').cluetip({splitTitle: '|', cluezIndex: 400, fx: {open: 'fadeIn', openSpeed: 'fast'}, clickThrough: true});
	// TODO after 5.0 - these need changes in the {button} Smarty fn
}

$(function() { // JQuery's DOM is ready event - before onload
	if (!window.jqueryTiki) window.jqueryTiki = {};

	// tooltip functions and setup
	if (jqueryTiki.tooltips) {	// apply "cluetips" to all .tips class anchors
		
		setUpClueTips();
		
	}	// end cluetip setup
	
	// Reflections
	if (jqueryTiki.replection) {
		$("img.reflect").reflect({});
	}

	// superfish setup (CSS menu effects)
	if (jqueryTiki.superfish) {
		$('ul.cssmenu_horiz').supersubs({ 
            minWidth:    11,   // minimum width of sub-menus in em units 
            maxWidth:    20,   // maximum width of sub-menus in em units 
            extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                               // due to slight rounding differences and font-family 
		});
		$('ul.cssmenu_vert').supersubs({ 
            minWidth:    11,   // minimum width of sub-menus in em units 
            maxWidth:    20,   // maximum width of sub-menus in em units 
            extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                               // due to slight rounding differences and font-family 
		});
		$('ul.cssmenu_horiz').superfish({
			animation: {opacity:'show', height:'show'},	// fade-in and slide-down animation
			speed: 'fast',								// faster animation speed
			onShow: function(){
				$(this).moveToWithinWindow();
			}
		});
		$('ul.cssmenu_vert').superfish({
			animation: {opacity:'show', height:'show'},	// fade-in and slide-down animation
			speed: 'fast',								// faster animation speed
			onShow: function(){
				$(this).moveToWithinWindow();
			}
		});
	}
	
	// tablesorter setup (sortable tables?)
	if (jqueryTiki.tablesorter) {
		$('.sortable').tablesorter({
			widthFixed: true							// ??
//			widgets: ['zebra'],							// stripes (coming soon)
		});
	}
	
	// ColorBox setup (Shadowbox, actually "<any>box" replacement)
	if (jqueryTiki.colorbox) {
		$().bind('cbox_complete', function(){	
			$("#cboxTitle").wrapInner("<div></div>");
		});
				
		// Tiki defaults for ColorBox
		
		// for every link containing 'shadowbox' or 'colorbox' in rel attribute
		$("a[rel*='box']").colorbox({
			transition: "elastic",
			maxHeight:"95%",
			maxWidth:"95%",
			overlayClose: true,
			title: true,
			current: jqueryTiki.cboxCurrent
		});
		
		// now, first let suppose that we want to display images in ColorBox by default:
		
		// this matches rel containg type=img or no type= specified
		$("a[rel*='box'][rel*='type=img'], a[rel*='box'][rel!='type=']").colorbox({
			photo: true
		});
		// rel containg slideshow (this one must be without #col1)
		$("a[rel*='box'][rel*='slideshow']").colorbox({
			photo: true,
			slideshow: true,
			slideshowSpeed: 3500,
			preloading: false,
			width: "100%",
			height: "100%"
		});
		// this are the defaults matching all *box links which are not obviously links to images...
		// (if we need to support more, add here... otherwise it is possible to override with type=iframe in rel attribute of a link)
		//  (from here one to speed it up, matches any link in #col1 only - the main content column)
		
		$("#col1 a[rel*='box']:not([rel*='type=img']):not([href*='display']):not([href*='preview']):not([href*='thumb']):not([rel*='slideshow']):not([href*='image']):not([href$='\.jpg']):not([href$='\.jpeg']):not([href$='\.png']):not([href$='\.gif'])").colorbox({
			iframe: true,
			width: "95%",
			height: "95%"
		});
		// hrefs starting with ftp(s)
		$("#col1 a[rel*='box'][href^='ftp://'], #col1 a[rel*='box'][href^='ftps://']").colorbox({
			iframe: true,
			width: "95%",
			height: "95%"
		});
		// rel containg type=flash
		$("#col1 a[rel*='box'][rel*='type=flash']").colorbox({
			flash: true,
			iframe: false
		});
		// rel with type=iframe (if someone needs to override anything above)
		$("#col1 a[rel*='box'][rel*='type=iframe']").colorbox({
			iframe: true
		});
		// inline content: hrefs starting with #
		$("#col1 a[rel*='box'][href^='#']").colorbox({
			inline: true,
			width: "50%",
			height: "50%",
			href: function(){ 
				return $(this).attr('href');
			}
		});
		
		// titles (for captions):
		
		// by default get title from the title attribute of the link (in all columns)
		$("a[rel*='box'][title]").colorbox({
			title: function(){ 
				return $(this).attr('title');
			}
		});
		// but prefer the title from title attribute of a wrapped image if any (in all columns)
		$("a[rel*='box'] img[title]").colorbox({
			title: function(){ 
				return $(this).attr('title');
			},
			photo: true,				// and if you take title from the image you need photo 
			href: function(){			// and href as well (for colobox 1.3.6 tiki 5.0)
				return $(this).parent().attr("href");
			}
		});
		
		/* Shadowbox params compatibility extracted using regexp functions */
		var re, ret;
		// rel containg title param overrides title attribute of the link (shadowbox compatible)
		$("#col1 a[rel*='box'][rel*='title=']").colorbox({
			title: function () {
				re = /(title=([^;\"]+))/i;
				ret = $(this).attr("rel").match(re);
				return ret[2];
			}
		});
		// rel containg height param (shadowbox compatible)
		$("#col1 a[rel*='box'][rel*='height=']").colorbox({
			height: function () {
				re = /(height=([^;\"]+))/i;
				ret = $(this).attr("rel").match(re);
				return ret[2];
			}
		});
		// rel containg width param (shadowbox compatible)
		$("#col1 a[rel*='box'][rel*='width=']").colorbox({
			width: function () {
				re = /(width=([^;\"]+))/i;
				ret = $(this).attr("rel").match(re);
				return ret[2];
			}
		});	
		
		// links generated by the {COLORBOX} plugin
		if (jqueryTiki.colorbox) {
			$("a[rel^='shadowbox[colorbox']").each(function () {$(this).attr('savedTitle', $(this).attr('title'));});
			if (jqueryTiki.tooltips) {
				$("a[rel^='shadowbox[colorbox']").cluetip({
					splitTitle: '<br />', 
					cluezIndex: 400, 
					width: 'auto', 
					fx: {open: 'fadeIn', openSpeed: 'fast'}, 
					clickThrough: true
				});
			}
			$("a[rel^='shadowbox[colorbox']").colorbox({
				title: function() {
					return $(this).attr('savedTitle');	// this fix not required is colorbox was disabled
				}
			});
		}
		
	}	// end if (jqueryTiki.colorbox)

	// make all selects into ui selectmenus depending on $prefs['jquery_ui_selectmenu_all']
	if (jqueryTiki.selectmenu) {
		var $smenus, hidden = [];
		if (jqueryTiki.selectmenuAll) {
			$smenus = $("select")
		} else {
			$smenus = $("select.selectmenu");
		}
		if ($smenus.length) {
			$smenus.each ( function () {
				$.merge( hidden, $(this).parents("fieldset:hidden:last, div:hidden:last"));
			});
			hidden = $.unique($(hidden));
			hidden.show();
			$smenus.tiki("selectmenu");
			hidden.hide();
		}
	}
	
	$( function() {
		$("#keepOpenCbx").click(function() {
			if (this.checked) {
				setCookie("fgalKeepOpen", "1");
			} else {
				setCookie("fgalKeepOpen", "");
			}
		})
		var keepopen = getCookie("fgalKeepOpen");
		if (keepopen) {
			$("#keepOpenCbx").attr("checked", "checked");
		} else {
			$("#keepOpenCbx").removeAttr("checked");
		}
	});
	// end fgal fns


	$.paginationHelper();	
});		// end $document.ready

//For ajax/custom search
$document.bind('pageSearchReady', function() {
	$.paginationHelper();
});

// moved from tiki-list_file_gallery.tpl in tiki 6
function checkClose() {
	if (!$("#keepOpenCbx").attr("checked")) {
		window.close();
	} else {
		window.blur();
		if (window.opener) {
			window.opener.focus();
		}
	}
};


/* Autocomplete assistants */

function parseAutoJSON(data) {
	var parsed = [];
	return $.map(data, function(row) {
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
function popupPluginForm(area_id, type, index, pageName, pluginArgs, bodyContent, edit_icon, selectedMod){
    if (!$.ui) {
		alert("dev notice: no jq.ui here?");
        return popup_plugin_form(area_id, type, index, pageName, pluginArgs, bodyContent, edit_icon); // ??
    }
	if ($("#" + area_id).length && $("#" + area_id)[0].createTextRange) {	// save selection for IE
		storeTASelection(area_id);
	}

    var container = $('<div class="plugin"></div>');

    if (!index) {
        index = 0;
    }
    if (!pageName) {
        pageName = '';
    }
	var textarea = $('#' + area_id)[0];
	var replaceText = false;
	
	if (!pluginArgs && !bodyContent) {
		pluginArgs = {};
		bodyContent = "";
		
		dialogSelectElement( area_id, '{' + type.toUpperCase(), '{' + type.toUpperCase() + '}' ) ;
		var sel = getTASelection( textarea );
		if (sel && sel.length > 0) {
			sel = sel.replace(/^\s\s*/, "").replace(/\s\s*$/g, "");	// trim
			//alert(sel.length);
			if (sel.length > 0 && sel.substring(0, 1) === '{') { // whole plugin selected
				var l = type.length;
				if (sel.substring(1, l + 1).toUpperCase() === type.toUpperCase()) { // same plugin
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
							for (var i = 0; i < pm.length; i++) {
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
				if (type == 'mouseover') { // For MOUSEOVER, we want the selected text as label instead of body
					bodyContent = '';
					pluginArgs = {};
					pluginArgs['label'] = sel;
				} else {
					bodyContent = sel;
				}
				replaceText = true;
			}
		} else {	// no selection
			replaceText = false;
		}
    }
    var form = build_plugin_form(type, index, pageName, pluginArgs, bodyContent, selectedMod);
    
    //with PluginModule, if the user selects another module while the edit form is open
    //replace the form with a new one with fields to match the parameters for the module selected
	$(form).find('tr select[name="params[module]"]').change(function() {
		var npluginArgs = $.parseJSON($(form).find('input[name="args"][type="hidden"]').val());
		//this is the newly selected module
		var selectedMod = $(form).find('tr select[name="params[module]"]').val();
		$('div.plugin input[name="type"][value="' + type + '"]').parent().parent().remove();
		popupPluginForm(area_id, type, index, pageName, npluginArgs, bodyContent, edit_icon, selectedMod);
	});
    var $form = $(form).find('tr input[type=submit]').remove();
    
    container.append(form);
    document.body.appendChild(container[0]);
	
    handlePluginFieldsHierarchy(type);

	var pfc = container.find('table tr').length;	// number of rows (plugin form contents)
	var t = container.find('textarea:visible').length;
	if (t) {pfc += t * 3;}
	if (pfc > 9) {pfc = 9;}
	if (pfc < 2) {pfc = 2;}
	pfc = pfc / 10;			// factor to scale dialog height
	
	var btns = {};
	var closeText = tr("Close");
	btns[closeText] = function() {
		$(this).dialog("close");
	};
	
	btns[replaceText ? tr("Replace") : edit_icon ? tr("Submit") : tr("Insert")] = function() {
        var meta = tiki_plugins[type];
        var params = [];
        var edit = edit_icon;
        // whether empty required params exist or not
        var emptyRequiredParam = false;
        
        for (var i = 0; i < form.elements.length; i++) {
            var element = form.elements[i].name;
            
            var matches = element.match(/params\[(.*)\]/);
            
            if (matches === null) {
                // it's not a parameter, skip 
                continue;
            }
            var param = matches[1];
            
            var val = form.elements[i].value;
            
            // check if fields that are required and visible are not empty
			if (meta.params[param]) {
				if (meta.params[param].required) {
					if (val === '' && $(form.elements[i]).is(':visible')) {
						$(form.elements[i]).css('border-color', 'red');
						if ($(form.elements[i]).next('.required_param').length === 0) {
							$(form.elements[i]).after('<div class="required_param" style="font-size: x-small; color: red;">(required)</div>');
						}
						emptyRequiredParam = true;
					}
					else {
						// remove required feedback if present
						$(form.elements[i]).css('border-color', '');
						$(form.elements[i]).next('.required_param').remove();
					}
				}
			}
			
            if (val !== '') {
                params.push(param + '="' + val + '"');
            }
        }

        if (emptyRequiredParam) {
        	return false;
        }
       
		var blob, pluginContentTextarea = $("[name=content]", form), pluginContentTextareaEditor = syntaxHighlighter.get(pluginContentTextarea);
		var cont = (pluginContentTextareaEditor ? pluginContentTextareaEditor.getValue() : pluginContentTextarea.val());
		
		if (cont.length > 0) {
			blob = '{' + type.toUpperCase() + '(' + params.join(' ') + ')}' + cont + '{' + type.toUpperCase() + '}';
		} else {
			blob = '{' + type.toLowerCase() + ' ' + params.join(' ') + '}';
		}
        
        if (edit) {
            container.children('form').submit();
        } else {
            insertAt(area_id, blob, false, false, replaceText);
        }
		$(this).dialog("close");
		$('div.plugin input[name="type"][value="' + type + '"]').parent().parent().remove();
	        
		return false;
    };

	var heading = container.find('h3').hide();

	try {
		if (container.dialog) {
			container.dialog('destroy');
		}
	} catch( e ) {
		// IE throws errors destroying a non-existant dialog
	}
	container.dialog({
		width: $window.width() * 0.6,
		height: $window.height() * pfc,
		zIndex: 10000,
		title: heading.text(),
		autoOpen: false,
		close: function() {
			$('div.plugin input[name="type"][value="' + type + '"]').parent().parent().remove();		

			var ta = $('#' + area_id);
			if (ta) {ta.focus();}
		}
	}).dialog('option', 'buttons', btns).dialog("open");
	
	
	//This allows users to create plugin snippets for any plugin using the jQuery event 'plugin_#type#_ready' for document
	$document
		.trigger({
			type: 'plugin_' + type + '_ready',
			container: container,
			arguments: arguments,
			btns: btns
		})
		.trigger({
			type: 'plugin_ready',
			container: container,
			arguments: arguments,
			btns: btns,
			type: type
		});
}

/*
 * Hides all children fields in a wiki-plugin form and
 * add javascript events to display them when the appropriate
 * values are selected in the parent fields. 
 */
function handlePluginFieldsHierarchy(type) {
	var pluginParams = tiki_plugins[type]['params'];
	
	var parents = {};
	
	$.each(pluginParams, function(paramName, paramValues) {
		if (paramValues.parent) {
			var $parent = $('[name$="params[' + paramValues.parent.name + ']"]', '.wikiplugin_edit');
			
			$('.wikiplugin_edit').find('#param_' + paramName).addClass('parent_' + paramValues.parent.name + '_' + paramValues.parent.value);
			
			if ($parent.val() != paramValues.parent.value) {
				$('.wikiplugin_edit').find('#param_' + paramName).hide();
			}
			
			if (!parents[paramValues.parent.name]) {
				parents[paramValues.parent.name] = {};
				parents[paramValues.parent.name]['children'] = [];
				parents[paramValues.parent.name]['parentElement'] = $parent;
			}
			
			parents[paramValues.parent.name]['children'].push(paramName);
		}
	});
	
	$.each(parents, function(parentName, parent) {
		parent.parentElement.change(function() {
			$.each(parent.children, function() {
				$('.wikiplugin_edit #param_' + this).hide();
			});
			$('.wikiplugin_edit .parent_' + parentName + '_' + this.value).show();
		});
	}); 
}

/*
 * JS only textarea fullscreen function (for Tiki 5+)
 */

$(function() {	// if in translation-diff-mode go fullscreen automatically
	if ($("#diff_outer").length && !$.trim($(".wikipreview .wikitext").html()).length) {	// but not if previewing (TODO better)
		toggleFullScreen("editwiki");
	}
});

function toggleFullScreen(area_id) {
	var textarea = $("#" + area_id);
	
	//codemirror interation and preservation
	var textareaEditor = syntaxHighlighter.get(textarea);
	if (textareaEditor) {
		syntaxHighlighter.fullscreen(textarea);
		return;
	}

	$('.TextArea-fullscreen').add(textarea).css('height', '');

	//removes wiki command buttons (save, cancel, preview) from fullscreen view
	$('.TextArea-fullscreen .wikiaction').remove();

	var textareaParent = textarea.parent().parent().toggleClass('TextArea-fullscreen');
	$('body').toggleClass('noScroll');
	$('.tabs,.rbox-title').toggle();

	if (textareaParent.hasClass('TextArea-fullscreen')) {
		var win = $window
			.data('cm-resize', true),
			screen = $('.TextArea-fullscreen'),
			toolbar = $('#editwiki_toolbar');

		win.resize(function() {
			if (win.data('cm-resize') && screen) {
				screen.css('height', win.height() + 'px');

				textarea
					.css('height', ((win.height() - toolbar.height()) - 30) + 'px');
			}
		})
			.resize();

		//adds wiki command buttons (save, cancel, preview) from fullscreen view
		$('#role_main input.wikiaction').clone().appendTo('.TextArea-fullscreen');
	} else {
		$window.removeData('cm-resize');
	}
}

/* Simple tiki plugin for jQuery
 * Helpers for autocomplete and sheet
 */
var xhrCache = {}, lastXhr;	// for jq-ui autocomplete

$.fn.tiki = function(func, type, options) {
	var opts = {}, opt;
	switch (func) {
		case "autocomplete":
			if (jqueryTiki.autocomplete) {
				if (typeof type === 'undefined') { // func and type given
					// setup error - alert here?
					return null;
				}
				options = options || {};
				var requestData = {};

				var url = "";
				switch (type) {
					case "pagename":
						url = "tiki-listpages.php?listonly";
						break;
					case "groupname":
						url = "tiki-ajax_services.php?listonly=groups";
						break;
					case "username":
						url = "tiki-ajax_services.php?listonly=users";
						break;
					case "usersandcontacts":
						url = "tiki-ajax_services.php?listonly=usersandcontacts";
						break;
					case "userrealname":
						url = "tiki-ajax_services.php?listonly=userrealnames";
						break;
					case "tag":
						url = "tiki-ajax_services.php?listonly=tags&separator=+";
						break;
					case "icon":
						url = "tiki-ajax_services.php?listonly=icons&max=" + (opts.max ? opts.max: 10);
						opts.formatItem = function(data, i, n, value) {
							var ps = value.lastIndexOf("/");
							var pd = value.lastIndexOf(".");
							return "<img src='" + value + "' /> " + value.substring(ps + 1, pd).replace(/_/m, " ");
						};
						opts.formatResult = function(data, value) {
							return value;
						};
						break;
					case 'trackername':
						url = "tiki-ajax_services.php?listonly=trackername";
						break;
					case 'trackervalue':
						if (typeof options.fieldId === "undefined") {
							// error
							return null;
						}
						$.extend( requestData, options );
						options = {};
						url = "list-tracker_field_values_ajax.php";
						break;
				}
				$.extend( opts, {		//  default options for autocompletes in tiki
					minLength: 2,
					source: function( request, response ) {
						if (options.tiki_replace_term) {
							request.term = options.tiki_replace_term.apply(null, [request.term]);
						}
						var cacheKey = "ac." + type + "." + request.term;
						if ( cacheKey in xhrCache ) {
							response( xhrCache[ cacheKey ] );
							return;
						}
						request.q = request.term;
						$.extend( request, requestData );
						lastXhr = $.getJSON( url, request, function( data, status, xhr ) {
							xhrCache[ cacheKey ] = data;
							if ( xhr === lastXhr ) {
								response( data );
							}
						});
					}
				});
				$.extend(opts, options);

		 		return this.each(function() {
					$(this).autocomplete(opts);
//					.click( function () {
//						$(".ac_results").hide();	// hide the drop down if input clicked on again
//					});
				});
			}
			break;
		case "carousel":
			if (jqueryTiki.carousel) {
				opts = {
						imagePath: "lib/jquery/infinitecarousel/images/",
						autoPilot: true
					};
				$.extend(opts, options);
		 		return this.each(function() {
					$(this).infiniteCarousel(opts);
				});
			}
			break;
		case "datepicker":
		case "datetimepicker":
			if (jqueryTiki.ui) {
				switch (type) {
					case "jscalendar":	// replacements for jscalendar
										// timestamp result goes in the options.altField
						if (typeof options.altField === "undefined") {
							alert("jQuery.ui datepicker jscalendar replacement setup error: options.altField not set for " + $(this).attr("id"));
							debugger;
						}
						opts = {
							showOn: "both",
							buttonImage: "img/icons/calendar.png",
							buttonImageOnly: true,
							dateFormat: "yy-mm-dd",
							showButtonPanel: true,
							altFormat: "@",
							onClose: function(dateText, inst) {
								$.datepicker._updateAlternate(inst);	// make sure the hidden field is up to date
								var timestamp = parseInt($(inst.settings.altField).val() / 1000, 10);
								if (!timestamp) {
									$.datepicker._setDateFromField(inst);	// seems to need reminding when starting empty
									$.datepicker._updateAlternate(inst);
									timestamp = parseInt($(inst.settings.altField).val() / 1000, 10);
								}
								if (timestamp && inst.settings && inst.settings.timepicker) {	// if it's a datetimepicker add on the time
									var time = inst.settings.timepicker.hour * 3600 +
											   inst.settings.timepicker.minute * 60 +
											   inst.settings.timepicker.second;
									timestamp += time;
								}
								$(inst.settings.altField).val(timestamp ? timestamp : "");
							}
						};
						break;
					default:
						opts = {
							showOn: "both",
							buttonImage: "img/icons/calendar.png",
							buttonImageOnly: true,
							dateFormat: "yy-mm-dd",
							showButtonPanel: true
						};
						break;
				}
				$.extend(opts, options);
				if (func === "datetimepicker") {
					return this.each(function() {
							$(this).datetimepicker(opts);
						});
				} else {
					return this.each(function() {
						$(this).datepicker(opts);
					});
				}
			}
			break;
		case "accordion":
			if (jqueryTiki.ui) {
				opts = {
						autoHeight: false,
						collapsible: true,
						navigation: true
//						change: function(event, ui) {
//							// sadly accordion active property is broken in 1.7, but fix is coming in 1.8 so TODO 
//							setCookie(ui, ui.options.active, "accordion");
//						}
					};
				$.extend(opts, options);
		 		return this.each(function() {
					$(this).accordion(opts);			
				});
			}
			break;
		case "selectmenu":
			if (jqueryTiki.selectmenu) {
				opts = {
						style: 'dropdown',
						wrapperElement: "<span />"
					};
				$.extend(opts, options);
		 		return this.each(function() {
					$(this).selectmenu(opts);
				});
			}
			break;
	}	// end switch(func)
};

/******************************
 * Functions for dialog tools *
 ******************************/

// shared

window.dialogData = [];
var dialogDiv;

function displayDialog( ignored, list, area_id ) {
	var i, item, el, obj, tit = "";

	var $is_cked =  $('#cke_contents_' + area_id).length !== 0;

	if (!dialogDiv) {
		dialogDiv = document.createElement('div');
		document.body.appendChild( dialogDiv );
	}
	$(dialogDiv).empty();
	
	for( i = 0; i < window.dialogData[list].length; i++ ) {
		item = window.dialogData[list][i];
		if (item.indexOf("<") === 0) {	// form element
			el = $(item);
			$(dialogDiv).append( el );
		} else if (item.indexOf("{") === 0) {
			try {
				//obj = JSON.parse(item);	// safer, but need json2.js lib
				obj = eval("("+item+")");
			} catch (e) {
				alert(e.name + ' - ' + e.message);
			}
		} else if (item.length > 0) {
			tit = item;
		}
	}
	
	// Selection will be unavailable after context menu shows up - in IE, lock it now.
	if ( typeof CKEDITOR !== "undefined" && CKEDITOR.env.ie ) {
		var editor = CKEDITOR.instances[area_id];
		var selection = editor.getSelection();
		if (selection) {selection.lock();}
	} else if ($("#" + area_id)[0].createTextRange) {	// save selection for IE
		storeTASelection(area_id);
	}
	
	if (!obj) { obj = {}; }
	if (!obj.width) {obj.width = 210;}
	obj.bgiframe = true;
	obj.autoOpen = false;
	obj.zIndex = 10000;
	try {
		if ($(dialogDiv).dialog) {
			$(dialogDiv).dialog('destroy');
		}
	} catch( e ) {
		// IE throws errors destroying a non-existant dialog
	}
	$(dialogDiv).dialog(obj).dialog('option', 'title', tit).dialog('open');

	return false;
}

window.pickerData = [];
var pickerDiv = {};

function displayPicker( closeTo, list, area_id, isSheet, styleType ) {
	$('div.toolbars-picker').remove();	// simple toggle
	var $closeTo = $(closeTo);
	
	if ($closeTo.hasClass('toolbars-picker-open')) {
		$('.toolbars-picker-open').removeClass('toolbars-picker-open');
		return false;
	}
	
	$closeTo.addClass('toolbars-picker-open');
	var textarea = $('#' +  area_id);
	
	var coord = $closeTo.offset();
	coord.bottom = coord.top + $closeTo.height();
	
	pickerDiv = $('<div class="toolbars-picker ' + list + '" />')
		.css('left', coord.left + 'px')
		.css('top', (coord.bottom + 8) + 'px')
		.appendTo('body');

	var prepareLink = function(ins, disp ) {
		disp = $(disp);
		
		var link = $( '<a href="#" />' ).append(disp);
			
		if (disp.attr('reset') && isSheet) {
			var bgColor = $('div.tiki_sheet:first').css(styleType);
			var color = $('div.tiki_sheet:first').css(styleType == 'color' ? 'background-color' : 'color');
			disp
				.css('background-color', bgColor)
				.css('color', color);
			
			link
				.addClass('toolbars-picker-reset');
		}
		
		if ( isSheet ) {
			link
				.click(function() {
					var I = $(closeTo).attr('instance');
					I = parseInt( I ? I : 0, 10 );
					
					if (disp.attr('reset')) {
						$.sheet.instance[I].cellChangeStyle(styleType, '');
					} else {
						$.sheet.instance[I].cellChangeStyle(styleType, disp.css('background-color'));
					}
					
					$closeTo.click();
					return false;
				});
		} else {
			link.click(function() {				
				insertAt(area_id, ins);
				
				var textarea = $('#' + area_id);
				// quick fix for Firefox 3.5 losing selection on changes to popup
				if (typeof textarea.selectionStart != 'undefined') {
					var tempSelectionStart = textarea.selectionStart;
					var tempSelectionEnd = textarea.selectionEnd;
				}
				
				$closeTo.click();
				
				// quick fix for Firefox 3.5 losing selection on changes to popup
				if (typeof textarea.selectionStart != 'undefined' && textarea.selectionStart != tempSelectionStart) {
					textarea.selectionStart = tempSelectionStart;
				}
				if (typeof textarea.selectionEnd != 'undefined' && textarea.selectionEnd != tempSelectionEnd) {
					textarea.selectionEnd = tempSelectionEnd;
				}
				
				return false;
			});
		}
		return link;
	};
	var chr, $a;
	for( var i in window.pickerData[list] ) {
		chr = window.pickerData[list][i];
		if (list === "specialchar") {
			chr = $("<span>" + chr + "</span>");
		}
		$a = prepareLink( i, chr );
		if ($a.length) {
			pickerDiv.append($a);
		}
	}
	
	return false;
}


function dialogSelectElement( area_id, elementStart, elementEnd ) {
	if ($('#cke_contents_' + area_id).length !== 0) {return;}	// TODO for ckeditor
	
	var $textarea = $('#' + area_id);
	var textareaEditor = syntaxHighlighter.get($textarea);
	var val = ( textareaEditor ? textareaEditor.getValue() : $textarea.val() );
	var pairs = [], pos = 0, s = 0, e = 0;
	
	while (s > -1 && e > -1) {	// positions of start/end markers
		s = val.indexOf(elementStart, e);
		if (s > -1) {
			e = val.indexOf(elementEnd, s + elementStart.length);
			if (e > -1) {
				e += elementEnd.length;
				pairs[pairs.length] = [ s, e ];
			}
		}
	}
	
	(textareaEditor ? textareaEditor : $textarea[0]).focus();
	
	var selection = ( textareaEditor ? syntaxHighlighter.selection(textareaEditor, true) : $textarea.selection() );

	s = selection.start;
	e = selection.end;
	var st = $textarea.attr('scrollTop');

	for (var i = 0; i < pairs.length; i++) {
		if (s >= pairs[i][0] && e <= pairs[i][1]) {
			setSelectionRange($textarea[0], pairs[i][0], pairs[i][1]);
			break;
		}
	}
	
}


function dialogSharedClose( area_id, dialog ) {
	$(dialog).dialog("close");
}

// Internal Link

function dialogInternalLinkOpen( area_id ) {
	$("#tbWLinkPage").tiki("autocomplete", "pagename");
	dialogSelectElement( area_id, '((', '))' ) ;
	var s = getTASelection($('#' + area_id)[0]);
	var m = /\((.*)\(([^\|]*)\|?([^\|]*)\|?([^\|]*)\|?\)\)/g.exec(s);
	if (m && m.length > 4) {
		if ($("#tbWLinkRel")) {
			$("#tbWLinkRel").val(m[1]);
		}
		$("#tbWLinkPage").val(m[2]);
		if (m[4]) {
			if ($("#tbWLinkAnchor")) {
				$("#tbWLinkAnchor").val(m[3]);
			}
			$("#tbWLinkDesc").val(m[4]);
		} else {
			$("#tbWLinkDesc").val(m[3]);
		}
	} else {
		$("#tbWLinkDesc").val(s);
		if ($("#tbWLinkAnchor")) {
			$("#tbWLinkAnchor").val("");
		}
	}
}

function dialogInternalLinkInsert( area_id, dialog ) {
	if (!$("#tbWLinkPage").val()) {
		alert(tr("Please enter a page name"));
		return;
	}
	var s = "(";
	if ($("#tbWLinkRel") && $("#tbWLinkRel").val()) {
		s += $("#tbWLinkRel").val();
	}
	s += "(" + $("#tbWLinkPage").val();
	if ($("#tbWLinkAnchor") && $("#tbWLinkAnchor").val()) {
		s += "|" + ($("#tbWLinkAnchor").val().indexOf("#") !== 0 ? "#" : "") + $("#tbWLinkAnchor").val();
	}
	if ($("#tbWLinkDesc").val()) {
		s += "|" + $("#tbWLinkDesc").val();
	}
	s += "))";
	insertAt(area_id, s, false, false, true);
	
	dialogSharedClose( area_id, dialog );
	
}

// External Link

function dialogExternalLinkOpen( area_id ) {
	$("#tbWLinkPage").tiki("autocomplete", "pagename");
	dialogSelectElement( area_id, '[', ']' ) ;
	var s = getTASelection($('#' + area_id)[0]);
	var m = /\[([^\|]*)\|?([^\|]*)\|?([^\|]*)\]/g.exec(s);
	if (m && m.length > 3) {
		$("#tbLinkURL").val(m[1]);
		$("#tbLinkDesc").val(m[2]);
		if (m[3]) {
			if ($("#tbLinkNoCache") && m[3] == "nocache") {
				$("#tbLinkNoCache").attr("checked", "checked");
			} else {
				$("#tbLinkRel").val(m[3]);
			}
		} else {
			$("#tbWLinkDesc").val(m[3]);
		}
	} else {
		if (s.match(/(http|https|ftp)([^ ]+)/ig) == s) { // v simple URL match
			$("#tbLinkURL").val(s);
		} else {
			$("#tbLinkDesc").val(s);
		}
	}
	if (!$("#tbLinkURL").val()) {
		$("#tbLinkURL").val("http://");
	}
}

function dialogExternalLinkInsert(area_id, dialog) {

	var s = "[" + $("#tbLinkURL").val();
	if ($("#tbLinkDesc").val()) {
		s += "|" + $("#tbLinkDesc").val();
	}
	if ($("#tbLinkRel").val()) {
		s += "|" + $("#tbLinkRel").val();
	}
	if ($("#tbLinkNoCache") && $("#tbLinkNoCache").attr("checked")) {
		s += "|nocache";
	}
	s += "]";
	insertAt(area_id, s, false, false, true);
	
	dialogSharedClose( area_id, dialog );
	
}

// Table

function dialogTableOpen(area_id, dialog) {

	dialogSelectElement( area_id, '||', '||' ) ;

	dialog = $(dialog);

	var s = getTASelection($('#' + area_id)[0]);
	var m = /\|\|([\s\S]*?)\|\|/mg.exec(s);
	var vals = [], rows = 3, cols = 3, c, r, i, j;
	if (m) {
		m = m[1];
		m = m.split("\n");
		rows = 0;
		cols = 1;
		for (i = 0; i < m.length; i++) {
			var a2 = m[i].split("|");
			var a = [];
			for (j = 0; j < a2.length; j++) { // links can have | chars in
				if (a2[j].indexOf("[") > -1 && a2[j].indexOf("[[") == -1 && a2[j].indexOf("]") == -1) { // external link
					a[a.length] = a2[j];
					j++;
					var k = true;
					while (j < a2.length && k) {
						a[a.length - 1] += "|" + a2[j];
						if (a2[j].indexOf("]") > -1) { // closed
							k = false;
						} else {
							j++;
						}
					}
				} else if (a2[j].search(/\(\S*\(/) > -1 && a2[j].indexOf("))") == -1) {
					a[a.length] = a2[j];
					j++;
					k = true;
					while (j < a2.length && k) {
						a[a.length - 1] += "|" + a2[j];
						if (a2[j].indexOf("))") > -1) { // closed
							k = false;
						} else {
							j++;
						}
					}
				} else {
					a[a.length] = a2[j];
				}
			}
			vals[vals.length] = a;
			if (a.length > cols) {
				cols = a.length;
			}
			if (a.length) {
				rows++;
			}
		}
	}
	for (r = 1; r <= rows; r++) {
		for (c = 1; c <= cols; c++) {
			var v = "";
			if (vals.length) {
				if (vals[r - 1] && vals[r - 1][c - 1]) {
					v = vals[r - 1][c - 1];
				} else {
					v = "   ";
				}
			} else {
				v = "   "; //row " + r + ",col " + c + "";
			}
			var el = $("<input type=\"text\" id=\"tbTableR" + r + "C" + c + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" style=\"width:" + (90 / cols) + "%\" />")
				.val($.trim(v))
				.appendTo(dialog);
		}
		if (r == 1) {
			el = $("<img src=\"img/icons/add.png\" />")
				.click(function() {
					dialog.data("cols", dialog.data("cols") + 1);
					for (r = 1; r <= dialog.data("rows"); r++) {
						v = "";
						var el = $("<input type=\"text\" id=\"tbTableR" + r + "C" + dialog.data("cols") + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" style=\"width:" + (90 / dialog.data("cols")) + "%\" />")
							.val(v);
						$("#tbTableR" + r + "C" + (dialog.data("cols") - 1)).after(el);
					}
					dialog.find("input").width(90 / dialog.data("cols") + "%");
				})
				.appendTo(dialog);
		}
		dialog.append("<br />");
	}
	el = $("<img src=\"img/icons/add.png\" />")
		.click(function() {
			dialog.data("rows", dialog.data("rows") + 1);
			for (c = 1; c <= dialog.data("cols"); c++) {
				v = "";
				var el = $("<input type=\"text\" id=\"tbTableR" + dialog.data("rows") + "C" + c + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" value=\"" + v + "\" style=\"width:" + (90 / dialog.data("cols")) + "%\" />")
					.insertBefore(this);
			}
			$(this).before("<br />");
			dialog.dialog("option", "height", (dialog.data("rows") + 1) * 1.2 * $("#tbTableR1C1").height() + 130);
		})
		.appendTo(dialog);
	
	dialog
			.data('rows', rows)
			.data('cols', cols)
			.dialog("option", "width", (cols + 1) * 120 + 50)
			.dialog("option", "position", "center");

	$("#tbTableR1C1").focus();
}

function dialogTableInsert(area_id, dialog) {
	var s = "||", rows, cols, c, r, rows2 = 1, cols2 = 1;
	dialog = $(dialog);

	rows = dialog.data('rows') || 3;
	cols = dialog.data('cols') || 3;
	for (r = 1; r <= rows; r++) {
		for (c = 1; c <= cols; c++) {
			if ($.trim($("#tbTableR" + r + "C" + c).val())) {
				if (r > rows2) {
					rows2 = r;
				}
				if (c > cols2) {
					cols2 = c;
				}
			}
		}
	}
	for (r = 1; r <= rows2; r++) {
		for (c = 1; c <= cols2; c++) {
			var tableData = $("#tbTableR" + r + "C" + c).val();
			s += tableData;
			if (c < cols2) {
				s += (tableData ? '|' : ' | ');
			}
		}
		if (r < rows2) {
			s += "\n";
		}
	}
	s += "||";
	insertAt(area_id, s, false, false, true);
	
	dialogSharedClose( area_id, dialog );
}

// Find

function dialogFindOpen(area_id) {
	
	var s = getTASelection($('#' + area_id)[0]);
	$("#tbFindSearch").val(s).focus();			  
}

function dialogFindFind( area_id ) {
	var ta = $('#' + area_id);
	var findInput = $("#tbFindSearch").removeClass("ui-state-error");
	
	var $textareaEditor = syntaxHighlighter.get(ta); //codemirror functionality
	if ($textareaEditor) {
		syntaxHighlighter.find($textareaEditor, findInput.val());
	}
	else { //standard functionality
		var s, opt, str, re, p = 0, m;
		s = findInput.val();
		opt = "";
		if ($("#tbFindCase").attr("checked")) {
			opt += "i";
		}
		str = ta.val();
		re = new RegExp(s, opt);
		p = getCaretPos(ta[0]);
		if (p && p < str.length) {
			m = re.exec(str.substring(p));
		}
		else {
			p = 0;
		}
		if (!m) {
			m = re.exec(str);
			p = 0;
		}
		if (m) {
			setSelectionRange(ta[0], m.index + p, m.index + s.length + p);
		}
		else {
			findInput.addClass("ui-state-error");
		}
	}
}

// Replace

function dialogReplaceOpen(area_id) {

	var s = getTASelection($('#' + area_id)[0]);
	$("#tbReplaceSearch").val(s).focus();
	  		  
}

function dialogReplaceReplace( area_id ) {
	var findInput = $("#tbReplaceSearch").removeClass("ui-state-error");
	var s = findInput.val();
	var r = $("#tbReplaceReplace").val();
	var opt = "";
	if ($("#tbReplaceAll").attr("checked")) {
		opt += "g";
	}
	if ($("#tbReplaceCase").attr("checked")) {
		opt += "i";
	}
	var ta = $('#' + area_id);
	var str = ta.val();
	var re = new RegExp(s,opt);
	
	var textareaEditor = syntaxHighlighter.get(ta); //codemirror functionality
	if (textareaEditor) {
		syntaxHighlighter.replace(textareaEditor, s, r);
	}
	else { //standard functionality
		ta.val(str.replace(re, r));
	}

}


(function($) {
	/**
	 * Adds annotations to the content of text in ''container'' based on the
	 * content found in selected dts.
	 *
	 * Used in comments.tpl
	 */
	$.fn.addnotes = function( container ) {
		return this.each(function(){
			var comment = this;
			var text = $('dt:contains("note")', comment).next('dd').text();
			var title = $('h6:first', comment).clone();
			var body = $('.body:first', comment).clone();
			body.find('dt:contains("note")').closest('dl').remove();

			if( text.length > 0 ) {
				var parents = container.find(':contains("' + text + '")').parent();
				var node = container.find(':contains("' + text + '")').not(parents)
					.addClass('note-editor-text')
					.each( function() {
						var child = $('dl.note-list',this);
						if( ! child.length ) {
							child = $('<dl class="note-list"/>')
								.appendTo(this)
								.hide();

							$(this).click( function() {
								child.toggle();
							} );
						}

						child.append( title )
							.append( $('<dd/>').append(body) );
					} );
			}
		});
	};

	/**
	 * Convert a zone to a note editor by attaching handlers on mouse events.
	 */
	$.fn.noteeditor = function (editlink, link) {
		var hiddenParents = null;
		var annote = $(link)
			.click( function( e ) {
				e.preventDefault();

				var $block = $('<div/>');
				var annotation = $(this).attr('annotation');
				$(this).fadeOut(100);

				$block.load(editlink.attr('href'), function () {
					var msg = "";
					if (annotation.length < 20) {
						msg = tr("The text you have selected is quite short. Select a longer piece to ensure the note is associated with the correct text.") + "<br />";
					}

					msg = "<p class='description comment-info'>" + msg + tr("Tip: Leave the first line as it is, starting with \";note:\". This is required") + "</p>";
					$block.prepend($(msg));
					$('textarea', this)
						.val(';note:' + annotation + "\n\n").focus();

					$('form', this).submit(function () {
						$.post($(this).attr('action'), $(this).serialize(), function () {
							$block.dialog('destroy');
						});
						return false;
					});

					$block.dialog({
						modal: true,
						width: 500,
						height: 400
					});
				});
			} )
			.appendTo(document.body);

			$(this).mouseup(function( e ) {
				var range;
				if( window.getSelection && window.getSelection().rangeCount ) {
					range = window.getSelection().getRangeAt(0);
				} else if( window.selection ) {
					range = window.selection.getRangeAt(0);
				}

				if( range ) {
					var str = $.trim( range.toString() );

					if( str.length && -1 === str.indexOf( "\n" ) ) {
						annote.attr('annotation', str);
						annote.fadeIn(100).position( {
							of: e,
							at: 'bottom left',
							my: 'top left',
							offset: '20 20'
						} );
					} else {
						if (annote.css("display") != "none") {
							annote.fadeOut(100);
						}
						if ($("form.comments").css("display") == "none") {
							$("form.comments").show();
						}
						if (hiddenParents) {
							hiddenParents.hide();
							hiddenParents = null;
						}
					}
				}
			});
	};

	$.fn.browse_tree = function () {
		this.each(function () {
			$('.treenode:not(.done)', this)
				.addClass('done')
				.each(function () {
					if (getCookie($('ul:first', this).attr('data-id'), $('ul:first', this).attr('data-prefix')) !== 'o') {
						$('ul:first', this).css('display', 'none');
					}
					if ($('ul:first', this).length) {
						var dir = $('ul:first', this).css('display') === 'block' ? 's' : 'e';
						$(this).prepend('<span class="flipper ui-icon ui-icon-triangle-1-' + dir + '" style="float: left;"/>');
					} else {
						$(this).prepend('<span style="float:left;width:16px;height:16px;"/>');
					}
				});

			$('.flipper:not(.done)')
				.addClass('done')
				.css('cursor', 'pointer')
				.click(function () {
					var body = $(this).parent().find('ul:first');
					if ('block' === body.css('display')) {
						$(this).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
						body.hide('fast');
						setCookie(body.data("id"), "", body.data("prefix"));
					} else {
						$(this).removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
						body.show('fast');
						setCookie(body.data("id"), "o", body.data("prefix"));
					}
				});
		});

		return this;
	};

	var fancy_filter_create_token = function(value, label) {
		var close, token;

		close = $('<span class="ui-icon ui-icon-close"/>')
			.click(function () {
				var ed = $(this).parent().parent();
				$(this).parent().remove();
				ed.change();
				return false;
			});

		token = $('<span class="token"/>')
			.attr('data-value', value)
			.text(label)
			.attr('contenteditable', false)
			.disableSelection()
			.append(close);

		return token[0];
	};

	var fancy_filter_build_init = function(editable, str, options) {
		if (str === '') {
			str = '&nbsp;';
		}

		editable.html(str.replace(/(\d+)/g, '<span>$1</span>'));

		if (options && options.map) {
			editable.find('span').each(function () {
				var val = $(this).text();
				$(this).replaceWith(fancy_filter_create_token(val, options.map[val] ? options.map[val] : val));
			});
		}
	};

	$jq.fn.fancy_filter = function (operation, options) {
		this.each(function () {
			switch (operation) {
			case 'init':
				var editable = $('<div class="fancyfilter"/>'), input = this;

				if (editable[0].contentEditable !== null) {
					fancy_filter_build_init(editable, $(this).val(), options);
					editable.attr('contenteditable', true);
					$(this).after(editable).hide();
				}

				editable
					.keyup(function() {
						$(this).change();
						$(this).mouseup();
					})
					.change(function () {
						$(input).val($('<span/>')
							.html(editable.html())
							.find('span').each(function() {
								$(this).replaceWith(' ' + $(this).attr('data-value') + ' ');
							})
							.end().text().replace(/\s+/g, ' '));
					})
					.mouseup(function () {
						input.lastRange = window.getSelection().getRangeAt(0);
					});

				break;
			case 'add':
				var node = fancy_filter_create_token(options.token, options.label);
				if (this.lastRange) {
					this.lastRange.deleteContents();
					this.lastRange.insertNode(node);
					this.lastRange.insertNode(document.createTextNode(options.join));
				} else {
					$(this).next().append(options.join).append(node);
				}
				$(this).next().change();
				break;
			}
		});

		return this;
	};

	$.fn.drawGraph = function () {
		this.each(function () {
			var $this = $(this);
			var width = $this.width();
			var height = Math.ceil( width * 9 / 16 );
			var nodes = $this.data('graph-nodes');
			var edges = $this.data('graph-edges');

			var g = new Graph;
			$.each(nodes, function (k, i) {
				g.addNode(i);
			});
			$.each(edges, function (k, i) {
				var style = { directed: true };
				if( i.preserve ) {
					style.color = 'red';
				}
				g.addEdge( i.from, i.to, style );
			});

			var layouter = new Graph.Layout.Spring(g);
			layouter.layout();
			
			var renderer = new Graph.Renderer.Raphael($this.attr('id'), g, width, height );
			renderer.draw();
		});

		return this;
	};

	/**
	 * Handle textarea and input text selections
	 * Code from:
	 *
	 * jQuery Autocomplete plugin 1.1
	 * Copyright (c) 2009 Jörn Zaefferer
	 *
	 * Dual licensed under the MIT and GPL licenses:
	 *   http://www.opensource.org/licenses/mit-license.php
	 *   http://www.gnu.org/licenses/gpl.html
	 *
	 * Now deprecated and replaced in Tiki 7 by jquery-ui autocomplete
	 */
	$.fn.selection = function(start, end) {
		if (start !== undefined) {
			if (end === undefined) {
				end = start;
			}
			return this.each(function() {
				if( this.createTextRange ){
					var selRange = this.createTextRange();
					if (start == end) {
						selRange.move("character", start);
						selRange.select();
					} else {
						selRange.collapse(true);
						selRange.moveStart("character", start);
						selRange.moveEnd("character", end - start);	// moveEnd is relative
						selRange.select();
					}
				} else if( this.setSelectionRange ){
					this.setSelectionRange(start, end);
				} else if( this.selectionStart ){
					this.selectionStart = start;
					this.selectionEnd = end;
				}
			});
		}
		var field = this[0];
		if ( field.createTextRange ) {
			// from http://the-stickman.com/web-development/javascript/finding-selection-cursor-position-in-a-textarea-in-internet-explorer/
			// The current selection
			var range = document.selection.createRange();
			// We'll use this as a 'dummy'
			var stored_range = range.duplicate();
			// Select all text
			stored_range.moveToElementText( field );
			// Now move 'dummy' end point to end point of original range
			stored_range.setEndPoint( 'EndToEnd', range );
			// Now we can calculate start and end points
			var selectionStart = stored_range.text.length - range.text.length;
			var selectionEnd = selectionStart + range.text.length;
			return {
				start: selectionStart,
				end: selectionEnd
			}
		
		} else if( field.selectionStart !== undefined ){
			return {
				start: field.selectionStart,
				end: field.selectionEnd
			}
		}
	};

	$.fn.comment_toggle = function () {
		this.each(function () {
			var $target = $(this.hash);
			$target.hide();

			$(this).click(function () {
				if ($target.is(':visible')) {
					$target.hide(function () {
						$(this).empty();
					});
				} else {
					$target.comment_load($(this).attr('href'));
				}

				return false;
			});
		});

		return this;
	};

	$.fn.comment_load = function (url) {
		$('#top .note-list').remove();

		this.each(function () {
			var comment_container = this;
			$(this).load(url, function (response, status) {
				$(this).show();
				$('.comment.inline dt:contains("note")', this)
					.closest('.comment')
					.addnotes( $('#top') );

				$('#top').noteeditor($('.comment-form:last a', comment_container), '#note-editor-comment');

				$('.comment-form a', this).click(function () {
					$(this).parent().empty().removeClass('button').load($(this).attr('href'), function () {
						var form = $('form', this).submit(function () {
							var errors;
							$.post(form.attr('action'), $(this).serialize(), function (data, st) {
								if (data.threadId) {
									$(comment_container).empty().comment_load(url);
								} else {
									errors = $('ol.errors', form).empty();
									if (! errors.length) {
										$(':submit', form).after(errors = $('<ol class="errors"/>'));
									}
									
									$.each(data.errors, function (k, v) {
										errors.append($('<li/>').text(v));
									});
								}
							}, 'json');
							return false;
						});

						//allow syntax highlighting
						if ($.fn.flexibleSyntaxHighlighter) {
							console.log(form.find('textarea.wikiedit'));
							form.find('textarea.wikiedit').flexibleSyntaxHighlighter();
						}
					});
					return false;
				});

				$('.button.comment-form.autoshow a').click(); // allow autoshowing of comment forms through autoshow css class 

				$('.confirm-prompt', this).requireConfirm({
					success: function (data) { 
						if (data.status === 'DONE') {
							$(comment_container).empty().comment_load(url);
						}
					}
				});
			});
		});

		return this;
	};

	$.fn.input_csv = function (operation, separator, value) {
		this.each(function () {
			var values = $(this).val().split(separator);
			if (values[0] === '') {
				values.shift();
			}

			if (operation === 'add') {
				values.push(value);
			} else if (operation === 'delete') {
				value = String(value);
				while (-1 !== $.inArray(value, values)) {
					values.splice($.inArray(value, values), 1);
				}
			}

			$(this).val(values.join(separator));
		});

		return this;
	};

	$.service = function (controller, action, query) {
		var append = '';

		if (query) {
			append = '?' + $.map(query, function (v, k) {
				return k + '=' + escape(v);
			}).join('&');
		}

		if (action) {
			return 'tiki-' + controller + '-' + action + append;
		} else {
			return 'tiki-' + controller + append;
		}
	};

	$.fn.serviceDialog = function (options) {
		this.each(function () {
			var $dialog = $('<div/>'), origin = this, buttons = {};
			$(this).append($dialog).data('serviceDialog', $dialog);

			buttons[tr('OK')] = function () {
				$dialog.find('form:visible').submit();
			};
			buttons[tr('Cancel')] = function () {
				$dialog
					.dialog('close')
					.dialog('destroy');
			};

			$dialog.dialog({
				title: options.title,
				minWidth: 500,
				height: (options.fullscreen ? $window.height() - 20 : 600),
				width: (options.fullscreen ? $window.width() - 20 : null),
				close: function () {
					if (options.close) {
						options.close.apply([], this);
					}
					$(this).dialog('destroy');
				},
				buttons: buttons,
				modal: options.modal,
				zIndex: options.zIndex
			});

			$dialog.loadService(options.data, $.extend(options, {origin: origin}));
		});

		return this;
	};
	$.fn.loadService =  function (data, options) {
		var $dialog = this, controller = options.controller, action = options.action, url;

		if (data && data.controller) {
			controller = data.controller;
		}

		if (data && data.action) {
			action = data.action;
		}

		if (options.origin && $(options.origin).is('a')) {
			url = $(options.origin).attr('href');
		} else {
			url = $.service(controller, action);
		}

		$dialog.load(url, data, function (data) {
			$dialog.find('form .submit').hide();

			$dialog.find('form:not(.no-ajax)').submit(function (e) {
				var form = this, act;
				act = $(form).attr('action');
	
				if (! act) {
					act = url;
				}

				$.ajax(act, {
					type: 'POST',
					dataType: 'json',
					data: $(form).serialize(),
					success: function (data) {
						data = (data ? data : {});
						
						if (data.FORWARD) {
							$dialog.loadService(data.FORWARD, options);
						} else {
							$dialog.dialog('destroy');
						}

						if (options.success) {
							options.success.apply(options.origin, [data]);
						}
					},
					error: function (jqxhr) {
						$(form.name).showError(jqxhr);
					}
				});

				return false;
			});

			if (options.load) {
				options.load.apply($dialog[0], [data]);
			}

			$('.confirm-prompt', this).requireConfirm({
				success: function (data) {
					if (data.FORWARD) {
						$dialog.loadService(data.FORWARD, options);
					} else {
						$dialog.loadService(options.data, options);
					}
				}
			});
		});
	};

	$.fn.requireConfirm = function (options) {
		this.click(function (e) {
			e.preventDefault();
			var message = options.message, link = this;

			if (! message) {
				message = $(this).data('confirm');
			}

			if (confirm (message)) {
				$.ajax($(this).attr('href'), {
					type: 'POST',
					dataType: 'json',
					data: {
						'confirm': 1
					},
					success: function (data) {
						options.success.apply(link, [data]);
					},
					error: function (jqxhr) {
						$(link).closest('form').showError(jqxhr);
					}
				});
			}
			return false;
		});

		return this;
	};

	$.fn.showError = function (message) {
		if (message.responseText) {
			var data = $.parseJSON(message.responseText);
			message = data.message;
		}
		this.each(function () {
			var validate = $(this).closest('form').validate(), errors = {}, field;

			if (validate) {
				if (! $(this).attr('name')) {
					$(this).attr('name', $(this).attr('id'));
				}

				field = $(this).attr('name');

				var parts;
				if (parts = message.match(/^<!--field\[([^\]]+)\]-->(.*)$/)) {
					field = parts[1];
					message = parts[2];
				}
				
				errors[field] = message;
				validate.showErrors(errors);

				setTimeout(function () {
					$('#error_report li').filter(function () {
						return $(this).text() === message;
					}).remove();

					if ($('#error_report ul').is(':empty')) {
						$('#error_report').empty();
					}
				}, 100);
			}
		});

		return this;
	};

	$.fn.clearError = function () {
		this.each(function () {
			$(this).closest('form').find('label.error[for="' + $(this).attr('name') + '"]').remove();
		});

		return this;
	};

	$.fn.object_selector = function (filter, threshold) {
		var input = this;
		this.each(function () {
			var $spinner = $(this).parent().modal(" ");
			$.getJSON('tiki-searchindex.php', {
				filter: filter
			}, function (data) {
				var $select = $('<select/>'), $autocomplete = $('<input type="text"/>');
				$(input).wrap('<div class="object_selector"/>');
				$(input).hide();
				$select.append('<option/>');
				$.each(data, function (key, value) {
					$select.append($('<option/>').attr('value', value.object_type + ':' + value.object_id).text(value.title));
				});

				$(input).after($select);
				$select.change(function () {
					$(input).data('label', $select.find('option:selected').text());
					$(input).val($select.val()).change();
				});

				if (jqueryTiki.selectmenu) {
					var $hidden = $select.parents("fieldset:hidden:last").show();
					$select.css("font-size", $.browser.webkit ? "1.4em" : "1.1em")	// not sure why webkit is so different, it just is :(
						.attr("id", input.attr("id") + "_sel")						// bug in selectmenu when no id
						.tiki("selectmenu");
					$hidden.hide();
				}
				$spinner.modal();

				if ($select.children().length > threshold) {
					var filterField = $('<input type="text"/>').width(120).css('marginRight', '1em');

					$(input).after(filterField);
					filterField.wrap('<label/>').before(tr('Search:')+ ' ');

					filterField.keypress(function (e) {
						var field = this;

						if (e.which === 0 || e.which === 13) {
							return false;
						}

						if (this.searching) {
							clearTimeout(this.searching);
							this.searching = null;
						}

						if (this.ajax) {
							this.ajax.abort();
							this.ajax = null;
							$spinner.modal();
						}

						if (jqueryTiki.selectmenu) {
							$select.selectmenu('open');
							$(field).focus();
						}

						this.searching = setTimeout(function () {
							var loaded = $(field).val();
							if (!loaded || loaded === " ") {	// let them get the whole list back?
								loaded = "*";
							}
							if ((loaded === "*" || loaded.length >= 3) && loaded !== $select.data('loaded')) {
								$spinner = $(field).parent().modal(" ");
								field.ajax = $.getJSON('tiki-searchindex.php', {
									filter: $.extend(filter, {autocomplete: loaded})
								}, function (data) {
									$select.empty();
									$select.data('loaded', loaded);
									$select.append('<option/>');
									$.each(data, function (key, value) {
										$select.append($('<option/>').attr('value', value.object_type + ':' + value.object_id).text(value.title));
									});
									if (jqueryTiki.selectmenu) {
										$select.tiki("selectmenu");
										$select.selectmenu('open');
										$(field).focus();
									}
									$spinner.modal();
								});
							}
						}, 500);
					});
				}
			});
		});

		return this;
	};

	$.fn.sortList = function () {
		var list = $(this), items = list.children('li').get();

		items.sort(function(a, b) {
			var compA = $(a).text().toUpperCase();
			var compB = $(b).text().toUpperCase();
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		})

		$.each(items, function(idx, itm) {
			list.append(itm);
		});
	};
	$.localStorage = {
		store: function (key, value) {
			if (window.localStorage) {
				window.localStorage[key] = $.toJSON(favoriteList);
			}
		},
		load: function (key, callback, fetch) {
			if (window.localStorage && window.localStorage[key]) {
				callback($.parseJSON(window.localStorage[key]));
			} else {
				fetch(function (data) {
					window.localStorage[key] = $.toJSON(data);
					callback(data);
				});
			}
		}
	};

	var favoriteList = [];
	$.fn.favoriteToggle = function () {
		this.find('a')
			.each(function () {
				var type, obj, isFavorite, link = this;
				type = $(this).queryParam('type');
				obj = $(this).queryParam('object');
				

				function isFavorite() {
					var ret = false;
					$.each(favoriteList, function (k, v) {
						if (v === type + ':' + obj) {
							ret = true;
							return false;
						}
					});

					return ret;
				}

				$(this).empty();
				$(this).append(tr('Favorite'));
				$(this).prepend($('<img style="vertical-align: top; margin-right: .2em;" />').attr('src', isFavorite() ? 'img/icons/star.png' : 'img/icons/star_grey.png'));
				// Toggle class of closest surrounding div for css customization
				if (isFavorite()) {
					$(this).closest('div').addClass( 'favorite_selected' );
					$(this).closest('div').removeClass( 'favorite_unselected' ); 
				} else {
					$(this).closest('div').addClass( 'favorite_unselected' );
					$(this).closest('div').removeClass( 'favorite_selected' );	
				}
				$(this)
					.filter(':not(".register")')
					.addClass('register')
					.click(function () {
						$.getJSON($(this).attr('href'), {
							target: isFavorite() ? 0 : 1
						}, function (data) {
							favoriteList = data.list;
							$.localStorage.store('favorites', favoriteList);

							$(link).parent().favoriteToggle();
						});
						return false;
					});
			});

		return this;
	};

	$.fn.queryParam = function (name) {
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
		var results = regex.exec(this[0].href);

		if(results == null) {
			return "";
		} else {
			return decodeURIComponent(results[1].replace(/\+/g, " "));
		}
	};

	$(function () {
		var list = $('.favorite-toggle');

		if (list.length > 0) {
			$.localStorage.load(
				'favorites',
				function (data) {
					favoriteList = data;
					list
						.favoriteToggle()
						.removeClass('favorite-toggle');
				}, 
				function (recv) {
					$.getJSON($.service('favorite', 'list'), recv);
				}
			);
		}
	});

	$.ajaxSetup({
		complete: function () {
			$('.favorite-toggle')
				.favoriteToggle()
				.removeClass('favorite-toggle');
		}
	});

	/**
	 * Show a loading spinner on top of a button (or whatever)
	 *
	 * @param empty or jq object $spinner		if empty, spinner is added and returned and element "disabled"
	 * 											if spinner then spinner is removed and element returned to normal
	 *
	 * @return jq object $spinner being shown or null when removing
	 */

	$.fn.showBusy = function( $spinner ) {
		if (!$spinner) {
			var pos = $(this).position();
			$spinner = $("<img src='img/spinner.gif' alt='" + tr("Wait") + "' class='ajax-spinner' />").
					css({
						"position": "absolute",
						"top": pos.top + ($(this).height() / 2),
						"left": pos.left + ($(this).width() / 2) - 8
					}).data("target", this);
			$(this).parent().find(".ajax-spinner").remove();
			$(this).parent().append($spinner);
			$(this).attr("disabled", true).css("opacity", 0.5);
			return $spinner;
		} else {
			$($spinner.data("target")).attr("disabled", false).css("opacity", 1);
			$spinner.remove();
			return null;
		}
	}

})($jq);

// Prevent memory leaks in IE
// Window isn't included so as not to unbind existing unload events
// More info:
//	- http://isaacschlueter.com/2006/10/msie-memory-leaks/
if ( window.attachEvent && !window.addEventListener ) {
	window.attachEvent("onunload", function() {
		for ( var id in jQuery.cache ) {
			var item = jQuery.cache[ id ];
			if ( item.handle ) {
				if ( item.handle.elem === window ) {
					for ( var type in item.events ) {
						if ( type !== "unload" ) {
							// Try/Catch is to handle iframes being unloaded, see #4280
							try {
								jQuery.event.remove( item.handle.elem, type );
							} catch(e) {}
						}
					}
				} else {
					// Try/Catch is to handle iframes being unloaded, see #4280
					try {
						jQuery.event.remove( item.handle.elem );
					} catch(e) {}
				}
			}
		}
	});
}

$.modal = function(msg) {
	return $('body').modal(msg, {
		isEverything: true
	});
};

//Makes modal over window or object so ajax can load and user can't prevent action
$.fn.modal = function(msg, s) {
	var obj = $(this);
	var lastModal = obj.attr('lastModal');
	
	if (!lastModal) {
		lastModal = Math.floor(Math.random() * 1000);
		obj.attr('lastModal', lastModal);
	}

	var mid = obj.height() / 2 + obj.offset().top;
	if (mid > $window.height()) {
		mid = ($window.height() - obj.offset().top) / 2 + obj.offset().top;
	}
	s = $.extend({
		isEverything: false,
		top: 	obj.offset().top,
		left: 	obj.offset().left,
		height: obj.height(), //we try to get height here
		width: 	obj.width(),
		middle: (mid),
		center: (obj.width() / 2 + obj.offset().left)
	}, s);
	
	var modal = $('body').find('#modal_' + lastModal);
	var spinner = $('<img src="img/spinner.gif" />');
	
	if (!msg) {
		modal
			.fadeOut(function() {
				$(this).remove();
			});
		obj.removeAttr('lastModal');
		return;
	}
	
	if (modal.length) {
		modal
			.find('.dialog')
			.html(spinner)
			.append(msg);
		return;
	}
	
	modal = $('<div>' + 
		    '<div class="dialog"></div>' +
		    '<div class="mask"></div>' +
		'</div>')
		.addClass('boxes')
		.attr('id', 'modal_' + lastModal)
		.prependTo('body');
	
	var size = {};
	
	if (s.isEverything) { //if the modal includes the whole page
		s = $.extend(s, {
			top: 	0,
			left:	0,
			height: $document.height(),
			width: 	$window.width(),
			middle: $window.height() / 2,
			center: $window.width() / 2
		});
	}
	 
	//Set height and width to mask to fill up the whole screen or the single element
	modal.find('.mask')
		.css('width', 	s.width + 'px')
		.css('height', 	s.height + 'px')
		.css('top', 	s.top + 'px')
		.css('left', 	s.left + 'px')
		.css('position', 'absolute')
		.fadeTo(1,0.01)
		.fadeTo(1000,0.8);
	
	var dialog = modal.find('.dialog');
	dialog
		.append(spinner)
		.append(msg)
		.css('top',  (s.middle - dialog.height() / 2) + 'px')
		.css('left', (s.center - dialog.width() / 2) + 'px')
		.css('position', 'absolute');

	if (s.isEverything) {
		dialog
			.css('top', (s.middle - dialog.height() / 2) + 'px')
			.css('left', (s.center - dialog.width() / 2) + 'px')
			.css('position', 'fixed');
	}
	
	return obj;
};

//makes the width of an input change to the value
$.fn.valWidth = function() {
	var me = $(this);
	return me.ready(function() {
		var h = me.height();
		if (!h) {
			h = me.offsetParent().css("font-size");
			if (h) {
				h = parseInt(h.replace("px", ""));
			}
		}
		me.keyup(function() {
			var width = me.val().length * h;

			me
				.stop()
				.animate({
					width: (width > h ? width : h)
				}, 200);
		})
		.keyup();
	});
};

//For making pagination have the ability to enter page/offset number and go
$.paginationHelper = function() {
	$('.pagenums').each(function() {
		var me = $(this);
		var step = me.find('input.pagenumstep');
		var endOffset = (me.find('input.pagenumend').val() - 1) * step.data('step');
		var url = step.data('url');
		var offset_jsvar = step.data('offset_jsvar');
		
		me.find('span.pagenumstep').replaceWith(
			$('<input type="text" style="font-size: inherit; " />')
				.val(step.val())
				.change(function() {
					var newOffset = step.data('step') * ($(this).val() - 1);
					
					if (newOffset >= 0) {
						//make sure the offset isn't too high
						newOffset = (newOffset > endOffset ? endOffset : newOffset);
						
						//THis is for custom/ajax search handling
						window[offset_jsvar] = newOffset;
						if (step[0]) {
							if (step.attr('onclick')) {
								step[0].onclick();
								return;
							}
						}
						
						//if the above behavior isn't there, we update location
						document.location = url + "offset=" + newOffset;
					}
				})
				.keyup(function(e) {
					switch(e.which) {
						case 13: $(this).blur();
					}
				})
				.valWidth()
		);
	});
};

//a sudo "onvisible" event
$.fn.visible = function(fn, isOne) {
	if (fn) {
		$(this).each(function() {
			var me = $(this);
			if (isOne) {
				me.one('visible', fn);
			} else {
				me.bind('visible', fn);
			}
			
			function visibilityHelper() {
				if (!me.is(':visible')) {
					setTimeout(visibilityHelper, 500);
				} else {
					me.trigger('visible');
				}
			}
			
			visibilityHelper();
		});
	} else {
		$(this).trigger('visible');
	}
	
	return this;
};

$.download = function(url, data, method){
	//url and data options required
	if( url && data ){ 
		//data can be string of parameters or array/object
		data = typeof data == 'string' ? data : jQuery.param(data);
		//split params into form inputs
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		//send request
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>')
		.appendTo('body').submit().remove();
	};
};

$.uiIcon = function(type) {
	return $('<div style="width: 1.4em; height: 1.4em; margin: .2em; display: inline-block; cursor: pointer;">' +
		'<span class="ui-icon ui-icon-' + type + '">&nbsp;</span>' + 
	'</div>')
	.hover(function(){
		$(this).addClass('ui-state-highlight');
	}, function() {
		$(this).removeClass('ui-state-highlight');
	});
};

$.uiIconButton = function(type) {
	return $.uiIcon(type).addClass('ui-state-default ui-corner-all');
};

$.rangySupported = function(fn) {
	if (window.rangy) {
		rangy.init();
		var cssClassApplierModule = rangy.modules.CssClassApplier;
		return fn();
	}
};

$.fn.rangy = function(fn) {
	var me = $(this);
	$.rangySupported(function() {
		$document.mouseup(function(e) {
			if (me.data('rangyBusy')) return;
			
			var selection = rangy.getSelection();
			var html = selection.toHtml();
			var text = selection.toString();
			
			if (text.length > 3 && rangy.isUnique(me[0], text)) {
					if (fn)
						if ($.isFunction(fn))
							fn({
								text: text,
								x: e.pageX,
								y: e.pageY
							});
			}
		});
	});
	return this;
};

$.fn.rangyRestore = function(phrase, fn) {
	var me = $(this);
	$.rangySupported(function() {
		phrase = rangy.setPhrase(me[0], phrase);
		
		if (fn)
			if ($.isFunction(fn))
				fn(phrase);
	});
	return this;
};

$.fn.rangyRestoreSelection = function(phrase, fn) {
	var me = $(this);
	$.rangySupported(function() {
		phrase = rangy.setPhraseSelection(me[0], phrase);
		
		if (fn)
			if ($.isFunction(fn))
				fn(phrase);
	});
	return this;
};

$.fn.realHighlight = function() {
	var o = $(this);
	$.rangySupported(function() {
		rangy.setPhraseBetweenNodes(o.first(), o.last(), document);
	});
	return this;
};

$.fn.ajaxEditDraw = function(options) {
	var me = $(this).attr('href', 'tiki-ajax_services.php');

	//defaults
	options = $.extend({
		saved: function() {},
		closed: function() {}
	}, options);

	$.modal(tr('Loading editor'));

	me.serviceDialog({
		title: me.attr('title'),
		data: {
			controller: 'draw',
			action: 'edit',
			fileId: me.data('fileid'),
			galleryId: me.data('galleryid'),
			raw: true
		},
		modal: true,
		zIndex: 9999,
		fullscreen: true,
		load: function (data) {
			//prevent from happeneing over and over again
			if (me.data('drawLoaded')) return false;
			
			me.data('drawLoaded', true);

			me.drawing = $('#tiki_draw')
				.loadDraw({
					fileId: me.data('fileid'),
					galleryId: me.data('galleryid'),
					name: me.data('name'),
					data: $('#fileData').val()
				})
				.bind('savedDraw', function(e, o) {
					me.data('drawLoaded', false);
					me.drawing.parent().dialog('destroy');
					me.drawing.remove();

					//update the image that did exist in the page with the new one that now exists
					var img = $('.pluginImg' + me.data('fileid')).show();
					var w = img.width(), h = img.height();

					if (img.hasClass('regImage')) {
						var replacement = $('<div />')
							.attr('class', img.attr('class'))
							.attr('style', img.attr('style'))
							.attr('id', img.attr('id'))
							.insertAfter(img);

						img.remove();
						img = replacement;
					}

					$('<div class=\"svgImage\" />')
						.load('tiki-download_file.php?fileId=' + o.fileId + '&display', function() {

							$(this)
								.css('position', 'absolute')
								.fadeTo(0, 0.01)
								.prependTo('body')
								.find('img,svg')
								.scaleImg({
									width: w,
									height: h
								});

							img.html($(this).html());

							$(this).remove();
						});

					if (!options.saved) return;

					options.saved(o.fileId);
				})
				.submit(function() {
					me.drawing.saveDraw();
					return false;
				})
				.bind('loadedDraw', function() {
					//kill the padding around the dialog so it looks like svg-edit is one single box
					me.drawing
						.parent()
						.css('padding', '0px');

					var serviceDialog = me.data('serviceDialog');
					if (serviceDialog) {
						var drawFrame = $('#svgedit');
						serviceDialog
							.bind('dialogresize', function() {
								drawFrame.height(serviceDialog.height() - 4);
							})
							.trigger('dialogresize');
					}

					$.modal();
				});

			me.drawing.find('#drawMenu').remove();
		},
		close: function() {
			if (me.data('drawLoaded')) {
				me.data('drawLoaded', false);
				me.drawing.remove();

				if (!options.closed) return;
				options.closed(me);
			}
		}
	});
	
	return false;
};

$.notify = function(msg, settings) {
	settings = $.extend({
		speed: 10000
	},settings);
	
	var notify = $('#notify');
	
	if (!notify.length) {
		notify = $('<div id="notify" />')
			.css('top', '5px')
			.css('right', '5px')
			.css('position', 'fixed')
			.css('z-index', 9999999)
			.css('padding', '5px')
			.width($window.width() / 5)
			.prependTo('body');
	}
	
	var note = $('<div class="notify ui-state-error ui-corner-all ui-widget ui-widget-content" />')
		.append(msg)
		.css('padding', '5px')
		.css('margin', '5px')
		.mousedown(function() {
			return false;
		})
		.hover(function() {
			$(this)
				.stop()
				.fadeTo(500, 0.3)
		}, function() {
			$(this)
				.stop()
				.fadeTo(500, 1)
		})
		.prependTo(notify);
	
	setTimeout(function() {
		note
			.fadeOut()
			.slideUp();
		
		//added outside of fadeOut to ensure removal 
		setTimeout(function() {
			note.remove();
		}, 1000);
		
	}, settings.speed);
};

/**
*   Close (user) sidebar column(s) if no modules are displayed.
*   Modules can be hidden at runtime. So, check after the page/DOM model is loaded.
*/
$(function () {

    // Do final client side adjustment of the sidebars
    /////////////////////////////////////////
    var maincol = 'col1';

    // Hide left side panel, if no modules are displayed
    var left_mods = document.getElementById('left_modules');
    if (left_mods != null) {
        if (isEmptyText(left_mods.innerHTML)) {
            var col = document.getElementById('col2');
            if (col != null) {
                col.style.display = "none"
            }
            document.getElementById(maincol).style.marginLeft = '0';

            var toggle = document.getElementById("showhide_left_column")
            if (toggle != null) {
                toggle.style.display = "none";
            }
        }
    }

    // Hide right side panel, if no modules are displayed
    var right_mods = document.getElementById('right_modules');
    if (right_mods != null) {

        //        alert("right_mods.innerHTML=" + right_mods.innerHTML);
        //alert("right_mods.innerText=" + right_mods.innerText);

        if (isEmptyText(right_mods.innerHTML)) {
            var col = document.getElementById('col3');
            if (col != null) {
                col.style.display = "none"
            }
            document.getElementById(maincol).style.marginRight = '0';

            var toggle = document.getElementById("showhide_right_column")
            if (toggle != null) {
                toggle.style.display = "none";
            }
        }
    }

    // FF does not support obj.innerText. So, analyze innerHTML, which all browsers seem to support
    function isEmptyText(html) {

        // Strip HTML tags
        /////////////////////////
        var strInputCode = html;

        // Replace coded-< with <, and coded-> with >
        strInputCode = strInputCode.replace(/&(lt|gt);/g, function (strMatch, p1) {
            return (p1 == "lt") ? "<" : ">";
        });
        // Strip tags
        var strTagStrippedText = strInputCode.replace(/<\/?[^>]+(>|$)/g, "");

        // Trim whitespace
        var text = strTagStrippedText.replace(/^\s+|\s+$/g, "");

        return text == null || text.length == 0;
    }
});

// try and reposition the menu ul within the browser window
$.fn.moveToWithinWindow = function() {
	var $el = $(this);
	h = $el.height(),
	w = $el.width(),
	o = $el.offset(),
	po = $el.parent().offset(),
	st = $window.scrollTop(),
	sl = $window.scrollLeft(),
	wh = $window.height(),
	ww = $window.width();

	if (w + o.left > sl + ww) {
		$el.animate({'left': sl + ww - w - po.left}, 'fast');
	}
	if (h + o.top > st + wh) {
		$el.animate({'top': st + wh - (h > wh ? wh : h) - po.top}, 'fast');
	} else if (o.top < st) {
		$el.animate({'top': st - po.top}, 'fast');
	}
};

$.fn.scaleImg = function (max) {
	$(this).each(function() {
		//Here we want to make sure that the displayed contents is the right size
		var img = $(this),
		actual = {
			height: img.height(),
			width: img.width()
		},
		original = $(this).clone(),
		parent = img.parent();

		var winner = '';

		if (actual.height > max.height) {
			winner = 'height';
		} else if (actual.width > max.width) {
			winner = 'width';
		}

		//if there is no winner, there is no need to resize
		if (winner) {
			//we resize both images and svg, we check svg first
			var g = img.find('g');
			if (g.length) {
				img
					.attr('preserveAspectRatio', 'xMinYMin meet');

				parent
					.css('overflow', 'hidden')
					.width(max.width)
					.height(max.height);

				g.attr('transform', 'scale( ' + (100 / (actual[winner] / max[winner]) * 0.01)  + ' )');
			} else {
				//now we resize regular images
				if (actual.height > actual.width) {
					var h = max.height;
					var w = Math.ceil(actual.width / actual.height * max.height);
				} else {
					var w = max.width;
					var h = Math.ceil(actual.height / actual.width * max.width);
				}
				img.css({ height: h, width: w });
			}

			img
				.css('cursor', "url(img/icons/zoom.gif),auto")
				.click(function () {
					$('<div/>').append(original).dialog({
						modal: true,
						width: Math.min($(window).width(), actual.width + 20),
						height: Math.min($(window).height(), actual.height + 50)
					});
					return false;
				});
		}
	});

	return this;
};


$.openEditHelp = function (num) {
	var edithelp_pos = getCookie("edithelp_position"),
		opts = {
			width: 460,
			height: 500,
			title: tr("Help"),
			autoOpen: false,
			beforeclose: function(event, ui) {
				var off = $(this).offsetParent().offset();
				setCookie("edithelp_position", parseInt(off.left,10) + "," + parseInt(off.top,10) + "," + $(this).offsetParent().width() + "," + $(this).offsetParent().height());
			}
		};

	if (edithelp_pos) {
		edithelp_pos = edithelp_pos.split(",");
	}

	if (edithelp_pos && edithelp_pos.length) {
		opts["position"] = [parseInt(edithelp_pos[0],10), parseInt(edithelp_pos[1],10)];
		opts["width"] = parseInt(edithelp_pos[2],10);
		opts["height"] = parseInt(edithelp_pos[3],10);
	}

	try {
		if ($("#help_sections").dialog) {
			$("#help_sections").dialog("destroy");
		}
	} catch( e ) {
		// IE throws errors destroying a non-existant dialog
	}

	var help_sections = $("#help_sections")
		.dialog(opts)
		.dialog("open")
		.tabs("select", num);

	$document.trigger('editHelpOpened', help_sections);
};