// $Id$
// JavaScript glue for jQuery (1.3.2 - 1.4.3) in TikiWiki (3.0+)
//
// Tiki 6 - $ is now initialised in jquery.js
// but let's keep $jq available too for legacy custom code

var $jq = $;

// Check / Uncheck all Checkboxes - overriden from tiki-js.js
function switchCheckboxes (tform, elements_name, state) {
  // checkboxes need to have the same name elements_name
  // e.g. <input type="checkbox" name="my_ename[]">, will arrive as Array in php.
	$(tform).contents().find('input[name="' + elements_name + '"]:visible').attr('checked', state).change();
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
	if (f) { setCookie(foo, "o", section); }
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
			showJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
		}
		else {
			setSessionVar('show_' + escape(foo), 'n');
			hideJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
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
		$(selector).show(effect.match(/(.*)_ui$/)[1], {direction: dir }, speed);
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
		$(selector).hide(effect.match(/(.*)_ui$/)[1], {direction: dir }, speed);
	} else {
		$(selector).hide();
	}
}

// override overlib
function convertOverlib(element, tip, params) {	// process modified overlib event fn to cluetip from {popup} smarty func
	
	if (element.processed || typeof $(element).cluetip != "function") { return false; }
	if (typeof params == "undefined") { params = []; }
	
	var options = {};
	options.clickThrough = true;
	for (var param = 0; param < params.length; param++) {
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
		
		if ($el.width() > $(window).width()) {
			$el.width($(window).width() * 0.8);
		}
		options.width = $el.width();
		$el.remove();
		
		element.tipWidth = options.width;
	}
	
	prefix = "|";
	$(element).attr('title', prefix + tip);
	
	element.processed = true;
	
	//options.sticky = true; //useful for css work
	$(element).cluetip(options);

	if (options.activation === 'click') {
		$(element).trigger('click');
	} else {
		$(element).trigger('mouseover');
	}
	setTimeout(function () { $("#cluetip").show(); }, 200);	// IE doesn't necessarily display
	$(element).attr("title", "");	// remove temporary title attribute to avoid built in browser tips
	return false;
}

function nd() {
	$("#cluetip").hide();
}

// ajax loading fns moved from tiki-ajax.js as not only used with xajax

function ajaxLoadingShow(destName) {
	var $dest, $loading, pos, x, y, w, h;
	
	if (typeof destName === 'string') {
		$dest = $('#' + destName);
	} else {
		$dest = $(destName);
	}
	if ($dest.length === 0) {
		return;
	}
	$loading = $('#ajaxLoading');

	// find area of destination element
	pos = $dest.offset();
	// clip to page
	if (pos.left + $dest.width() > $(window).width()) {
		w = $(window).width() - pos.left;
	} else {
		w = $dest.width();
	}
	if (pos.top + $dest.height() > $(window).height()) {
		h = $(window).height() - pos.top;
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

function setUpClueTips() {
	var ctOptions = { splitTitle: '|', cluezIndex: 400, width: 'auto', fx: { open: 'fadeIn', openSpeed: 'fast' }, clickThrough: true };
	$.cluetip.setup({ insertionType: 'insertBefore', insertionElement: '#main' });
	
	$('.tips[title!=""]').cluetip($.extend(ctOptions, {}));
	$('.titletips[title!=""]').cluetip($.extend(ctOptions, {}));
	$('.tikihelp[title!=""]').cluetip($.extend(ctOptions, { splitTitle: ':' })); // , width: '150px'
	$('.stickytips').cluetip($.extend(ctOptions, { showTitle: false, sticky: false, local: true, hideLocal: true, activation: 'click', cluetipClass: 'fullhtml' }));
	
	// repeats for "tiki" buttons as you cannot set the class and title on the same element with that function (it seems?)
	//$('span.button.tips a').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400, fx: {open: 'fadeIn', openSpeed: 'fast'}, clickThrough: true});
	//$('span.button.titletips a').cluetip({splitTitle: '|', cluezIndex: 400, fx: {open: 'fadeIn', openSpeed: 'fast'}, clickThrough: true});
	// TODO after 5.0 - these need changes in the {button} Smarty fn
}

$(document).ready( function() { // JQuery's DOM is ready event - before onload
	
	// tooltip functions and setup
	if (jqueryTiki.tooltips) {	// apply "cluetips" to all .tips class anchors
		
		setUpClueTips();
		
	}	// end cluetip setup
	
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
				superFishPosition(this);
			}
		});
		$('ul.cssmenu_vert').superfish({
			animation: {opacity:'show', height:'show'},	// fade-in and slide-down animation
			speed: 'fast',								// faster animation speed
			onShow: function(){
				superFishPosition(this);
			}
		});
		// try and reposition the menu ul within the browser window
		var superFishPosition = function( el ) {
			var $el = $(el);
			var h = $el.height();
			var w = $el.width();
			var o = $el.offset();
			var po = $el.parent().offset();
			var st = $(window).scrollTop();
			var sl = $(window).scrollLeft();
			var wh = $(window).height();
			var ww = $(window).width();
			if (w + o.left > sl + ww) {
				$el.animate({'left': sl + ww - w - po.left}, 'fast');
			}
			if (h + o.top > st + wh) {
				$el.animate({'top': st + wh - h - po.top}, 'fast');
			} else if (o.top < st) {
				$el.animate({'top': st - po.top}, 'fast');
			}
		};
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
			$("a[rel^='shadowbox[colorbox']").each(function () { $(this).attr('savedTitle', $(this).attr('title')); });
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
	
	if (jqueryTiki.sheet) {
		
		// override saveSheet on jQuery.sheet for tiki specific export
		$.sheet.saveSheet = function( redirect ) {
			$( $.sheet.instance ).each( function( i ){
				if (typeof redirect === 'undefined') { redirect = false; }
				// not set to 0 by default in case AJAX has caused a spurious one to appear
	
				this.evt.cellEditDone();
				
				var s = $.sheet.get_sheet_json(this);
				
				s = "s=" + $.toJSON(s)	// convert to JSON
					.replace(/\+/g,"%2B")	// replace +'s with 0x2B hex value
					.replace(/\&/g,"%26");	// and replace &'s with 0x26
				
				var setDirty = this.setDirty;
				$.ajax({
					url: this.s.urlSave,
					type: "POST",
					data: s,
					//contentType: "application/json; charset=utf-8",
					dataType: 'html',
					beforeSend: function() { window.showFeedback("Saving", 10000); }, 
					success: function(data) {
						setDirty(false);
						window.showFeedback(data, 2000, redirect);
					}
				});
			});
		};
		
		$.sheet.get_sheet_json = function(sheetInstance) {	// diverged from jQuery.sheet 1.1 / Tiki 6
			var sheetClone = sheetInstance.sheetDecorateRemove(true);
			var documents = []; //documents
			
			$(sheetClone).each(function() {
				var document = {}; //document
				document.metadata = {};
				document.data = {};
				
				//This preserves the width for postback, very important for styles
				//<DO_NOT_REMOVE>
				var table = $(this);
				var trFirst = table.find('tr:first');
				table.find('col').each(function(i){
					//because css isn't always set correctly, we need to check the width attribute as well
					//we also sanitize width string here
					var w = parseInt((jQuery(this).css('width') + '').replace('px',''), 10);
					var w2 = parseInt((jQuery(this).attr('width') + '').replace('px',''), 10);
					
					w = (w > w2 ? w : w2);
					
					trFirst.find('td').eq(i)
						.css('width', w + 'px')
						.attr('width', w);
				});
				//</DO_NOT_REMOVE>
				
				var trs = table.find('tr');
				var rowCount = trs.length;
				var colCount = 0;
				var col_widths = '';
				
				trs.each(function(i) {
					var tr = $(this);
					var tds = tr.find('td');
					colCount = tds.length;
					
					document.data['r' + i] = {};
					
					var h = tr.css('height');
					document.data['r' + i].height = (h ? h : tr.attr('height'));
					
					tds.each(function(j) {
						var td = jQuery(this);
						var colSpan = td.attr('colspan');
						colSpan = (colSpan > 1 ? colSpan : null);

						document.data['r' + i]['c' + j] = {
							value: td.html(),
							formula: td.attr('formula'),
							stl: td.attr('style'),
							colspan: colSpan,
							cl: td.attr('class')
						};
						
						var sp = td.attr('colSpan');
						if (sp > 1) {
							doc.data['r' + i]['c' + j].width = sp;
						}
						sp = td.attr('rowSpan');	// TODO in .sheet
						if (sp > 1) {
							doc.data['r' + i]['c' + j].height = sp;
						}
					});
				});
					
				var id = table.attr('rel');
				id = id ? id.match(/sheetId(\d+)/) : null;
				id = id && id.length > 0 ? id[1] : 0;

				document.metadata = {
					"columns": parseInt(colCount, 10), //length is 1 based, index is 0 based
					"rows": parseInt(rowCount, 10), //length is 1 based, index is 0 based
					"title": table.attr('title'),
					"col_widths": {},
					"sheetId": id
				};
				
				table.find('colgroup').children().each(function(i) {
					document.metadata.col_widths['c' + i] = ($(this).attr('width') + '').replace('px', '');
				});
				
				documents.push(document); //append to documents
			});
			return documents;
		};	
	}	// end sheet
	
	// moved from tiki-list_file_gallery.tpl in tiki 6
	checkClose = function() {
		if (!$("#keepOpenCbx").attr("checked")) {
			window.close();
		} else {
			window.blur();
		}
	};
	$(document).ready( function() {
		$("#keepOpenCbx").click(function() {
			if (this.checked) {
				setCookie("fgalKeepOpen", "1");
			} else {
				setCookie("fgalKeepOpen", "");
			}
		}).attr("checked", getCookie("fgalKeepOpen") ? "checked" : "");
	});
	// end fgal fns


	
});		// end $(document).ready


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
function popupPluginForm(area_id, type, index, pageName, pluginArgs, bodyContent, edit_icon){
    if (!$.ui) {
		alert("dev notice: no jq.ui here?");
        return popup_plugin_form(area_id, type, index, pageName, pluginArgs, bodyContent, edit_icon); // ??
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
    
    var form = build_plugin_form(type, index, pageName, pluginArgs, bodyContent);
    $(form).find('tr input[type=submit]').remove();
    
    container.append(form);
    document.body.appendChild(container[0]);
	
    handlePluginFieldsHierarchy(type);

	var pfc = container.find('table tr').length;	// number of rows (plugin form contents)
	var t = container.find('textarea:visible').length;
	if (t) { pfc += t * 3; }
	if (pfc > 9) { pfc = 9; }
	if (pfc < 2) { pfc = 2; }
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
            element = form.elements[i].name;
            
            var matches = element.match(/params\[(.*)\]/);
            
            if (matches === null) {
                // it's not a parameter, skip 
                continue;
            }
            var param = matches[1];
            
            var val = form.elements[i].value;
            
            // check if fields that are required and visible are not empty
            if (meta.params[param].required) {
            	if (val == '' && $(form.elements[i]).is(':visible')) {
	            	$(form.elements[i]).css('border-color', 'red');
	            	if ($(form.elements[i]).next('.required_param').length == 0) {
	            		$(form.elements[i]).after('<div class="required_param" style="font-size: x-small; color: red;">(required)</div>');
	            	}
	            	emptyRequiredParam = true;
	            } else {
	            	// remove required feedback if present
	            	$(form.elements[i]).css('border-color', '');
	            	$(form.elements[i]).next('.required_param').remove();
	            }
            }
            
            if (val !== '') {
                params.push(param + '="' + val + '"');
            }
        }

        if (emptyRequiredParam) {
        	return false;
        }
       
		var blob
		if (typeof form.content != 'undefined' && form.content.value.length > 0) {
			blob = '{' + type.toUpperCase() + '(' + params.join(' ') + ')}' + form.content.value + '{' + type.toUpperCase() + '}';
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
		width: $(window).width() * 0.6,
		height: $(window).height() * pfc,
		zIndex: 10000,
		title: heading.text(),
		autoOpen: false,
		close: function() {
			$('div.plugin input[name="type"][value="' + type + '"]').parent().parent().remove();		

			var ta = $('#' + area_id);
			if (ta) { ta.focus(); }
		}
	}).dialog('option', 'buttons', btns).dialog("open");
   
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
			parent = $('.wikiplugin_edit').find('[name$="params[' + paramValues.parent.name + ']"]');
			
			$('.wikiplugin_edit').find('#param_' + paramName).addClass('parent_' + paramValues.parent.name + '_' + paramValues.parent.value);
			
			if (parent.val() != paramValues.parent.value) {
				$('.wikiplugin_edit').find('#param_' + paramName).hide();
			}
			
			if (!parents[paramValues.parent.name]) {
				parents[paramValues.parent.name] = {};
				parents[paramValues.parent.name]['children'] = [];
				parents[paramValues.parent.name]['parentElement'] = parent;
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

var fullScreenState = [];

$(document).ready(function() {	// if in translation-diff-mode go fullscreen automatically
	if ($("#diff_outer").length && !$(".wikipreview").length) {	// but not if previewing (TODO better)
		toggleFullScreen("editwiki");
	}
});

function toggleFullScreen(area_id) {
	var $ta = $("#" + area_id);
	var $diff = $("#diff_outer"), $edit_form, $edit_form_innards;	// vars for translation diff elements if present

	if (fullScreenState[area_id]) {	// leave full screen - fullScreenState[area_id] contains info about previous page DOM state when fullscreen
		if ($diff.length) {
			$("#fs_grippy_" + area_id).remove();
			$diff.css("float", fullScreenState[area_id]["diff"]["float"]).width(fullScreenState[area_id]["diff"]["width"]).height(fullScreenState[area_id]["diff"]["height"]);
			$("#diff_history").height(fullScreenState[area_id]["diff_history"]["height"])
								.width(fullScreenState[area_id]["diff_history"]["width"]);
			for(var i = 0; i < fullScreenState[area_id]["edit_form_innards"].length; i++) {
				$(fullScreenState[area_id]["edit_form_innards"][i]["el"])
						.css("left", fullScreenState[area_id]["edit_form_innards"][i]["left"])
						.width(fullScreenState[area_id]["edit_form_innards"][i]["width"])
						.height(fullScreenState[area_id]["edit_form_innards"][i]["height"]);
			}	
			$edit_form = $(fullScreenState[area_id]["edit_form"]["el"]);	// hmmm?
			$edit_form.css("position", fullScreenState[area_id]["edit_form"]["position"])
						.css("left", fullScreenState[area_id]["edit_form"]["left"])
						.width(fullScreenState[area_id]["edit_form"]["width"]).height(fullScreenState[area_id]["edit_form"]["height"]);
		}
		$ta.css("float", fullScreenState[area_id]["ta"]["float"]).width(fullScreenState[area_id]["ta"]["width"]).height(fullScreenState[area_id]["ta"]["height"]);
		$ta.resizable({minWidth: fullScreenState[area_id]["resizable"]["minWidth"], minHeight: fullScreenState[area_id]["resizable"]["minHeight"]});
		
		for(i = 0; i < fullScreenState[area_id]["hidden"].length; i++) {
			fullScreenState[area_id]["hidden"][i].show();
		}
		
		for (i = 0; i < fullScreenState[area_id]["changed"].length; i++) {
			var $el = $(fullScreenState[area_id]["changed"][i]["el"]);
			$el.css("margin-left", fullScreenState[area_id]["changed"][i]["margin-left"])
				.css("margin-right", fullScreenState[area_id]["changed"][i]["margin-right"])
				.css("margin-top", fullScreenState[area_id]["changed"][i]["margin-top"])
				.css("margin-bottom", fullScreenState[area_id]["changed"][i]["margin-bottom"])
				.css("padding-left", fullScreenState[area_id]["changed"][i]["padding-left"])
				.css("padding-right", fullScreenState[area_id]["changed"][i]["padding-right"])
				.css("padding-top", fullScreenState[area_id]["changed"][i]["padding-top"])
				.css("padding-bottom", fullScreenState[area_id]["changed"][i]["padding-bottom"])
				.width(fullScreenState[area_id]["changed"][i]["width"])
				.height(fullScreenState[area_id]["changed"][i]["height"]);
		}
		
		$(".fs_clones").remove();
		$(document.documentElement).css("overflow","auto");
		
		fullScreenState[area_id] = false;
		
	} else {		// go full screen
		$(window).scrollTop(0);
		$(document.documentElement).css("overflow","hidden");
		
		fullScreenState[area_id] = [];
		fullScreenState[area_id]["hidden"] = [];
		fullScreenState[area_id]["changed"] = [];
		fullScreenState[area_id]["resizable"] = [];
		fullScreenState[area_id]["resizable"]["minWidth"] = $ta.resizable("option", "minWidth");
		fullScreenState[area_id]["resizable"]["minHeight"] = $ta.resizable("option", "minHeight");
		
		$ta.resizable("destroy");
		var h = $(window).height();
		var w = $(window).width();
		
		if ($diff.length) {	// translation diff there so split the screen down the middle (for now)
			w = Math.floor(w / 2) - 5;
		}
		
		// store & hide anything not in col1 
		fullScreenState[area_id]["hidden"].push($("#header, #col2, #col3, #footer"));
		$("#header, #col2, #col3, #footer").hide();
		
		// store & reset margins, padding and size for all the textarea parents, and hide siblings
		$ta.parents().each(function() {
			fullScreenState[area_id]["hidden"].push($(this).siblings(":visible:not('#diff_outer, .translation_message')"));
			var ob = [];
			ob["el"] = this;
			ob["margin-left"] = $(this).css("margin-left");	// this is for IE - it fails using margin or padding as a single setting
			ob["margin-right"] = $(this).css("margin-right");
			ob["margin-top"] = $(this).css("margin-top");
			ob["margin-bottom"] = $(this).css("margin-bottom");
			ob["padding-left"] = $(this).css("padding-left");
			ob["padding-right"] = $(this).css("padding-right");
			ob["padding-top"] = $(this).css("padding-top");
			ob["padding-bottom"] = $(this).css("padding-bottom");
			ob["width"] = $(this).css("width");
			ob["height"] = $(this).css("height");
			fullScreenState[area_id]["changed"].push(ob);
		});
		$ta.parents().each(function() {
			$(this).siblings(":visible:not('#diff_outer, .translation_message')").hide();
			$(this).css("margin", 0).css("padding", 0).width(w).height(h);
		});
		
		// store & resize translation diff divs etc
		if ($diff.length) {
			fullScreenState[area_id]["diff"] = [];
			fullScreenState[area_id]["diff"]["width"] = $diff.width();
			fullScreenState[area_id]["diff"]["height"] = $diff.height();
			fullScreenState[area_id]["diff"]["float"] = $diff.css("float");
			fullScreenState[area_id]["diff_history"] = [];
			fullScreenState[area_id]["diff_history"]["height"] = $("#diff_history").height();
			fullScreenState[area_id]["diff_history"]["width"] = $("#diff_history").width();
			$edit_form = $diff.next();
			$edit_form_innards = $edit_form.find("#edit-zone, table.normal, textarea, fieldset");
			fullScreenState[area_id]["edit_form"] = [];
			fullScreenState[area_id]["edit_form"]["el"] = $edit_form[0];	// store this element for easy access later
			fullScreenState[area_id]["edit_form"]["height"] = $edit_form.height();
			fullScreenState[area_id]["edit_form"]["width"] = $edit_form.width();
			fullScreenState[area_id]["edit_form"]["left"] = $edit_form.css("left") !== 'auto' ? $edit_form.css("left") : 0;
			fullScreenState[area_id]["edit_form"]["position"] = $edit_form.css("position");
			fullScreenState[area_id]["edit_form_innards"] = [];
			$edit_form_innards.each(function() {
				var ob = [];
				ob["el"] = this;
				ob["width"] = $(this).css("width");
				ob["height"] = $(this).css("height");
				ob["left"] = $(this).css("left");
				fullScreenState[area_id]["edit_form_innards"].push(ob);
			});
			
			$diff.parents().each(function() {			// shares some parents with the textarea
				$(this).width($(window).width());	// so make room for both
			});
		}
		
		// resize the actual textarea
		fullScreenState[area_id]["ta"] = [];
		fullScreenState[area_id]["ta"]["width"] = $ta.width();
		fullScreenState[area_id]["ta"]["height"] = $ta.height();
		fullScreenState[area_id]["ta"]["float"] = $ta.css("float");
		
		var b = 0;
		if ($ta.css("border-left-width")) {
		b = $ta.css("border-left-width").replace("px","");
		}
		
		$ta.width(w - b * 2).height($ta.parent().height() - $(".textarea-toolbar").height() - $(".translation_message").height() - 60 - b * 2);
		
		// add grippy resize bar to translation diff page
		if ($diff.length) {
			var grippy_width = 10;
			$diff.width(w).height(h).css("float", "left").next().css("float", "right");
			var vh = $("#diff_versions").css("overflow", "auto").height() + 18;
			if (vh > h * 0.15) {
				vh = h * 0.15;
			}
			$("#diff_versions").height(vh);
			$("#diff_history").height(h - vh).width(w).css("left", w + grippy_width);
			$edit_form.css("position","absolute").css("left", w + grippy_width).width(w - grippy_width);
			
			$grippy = $("<div id='fs_grippy_" + area_id +"' />").css({"background-image": "url(pics/icons/shading.png)",
											"background-repeat": "repeat-y",
											"background-position": -3,
											"position": "absolute",
											"left": w + "px",
											"top": 0,
											"cursor": "col-resize"})
									.width(grippy_width).height(h).draggable({ axis: 'x', drag: function(event, ui) {
										$diff.find("div,table").width(ui.offset.left - grippy_width);
										$edit_form.css("left", ui.offset.left + grippy_width).find("#edit-zone, table.normal, textarea, fieldset")
												.width($(window).width() - ui.offset.left);
									} });
			$diff.after($grippy);
			
		}
		
		// copy and add the action buttons (preview, save etc)
		if ($("div.top_actions").length) {
			$ta.parent().append($("div.top_actions > .wikiaction").clone(true).addClass("fs_clones"));
		} else {
			$ta.parent().append($("#editpageform td > .wikiaction").clone(true).addClass("fs_clones"));
		}

		// show action buttons and reapply cluetip options
		if (jqueryTiki.tooltips) {
			$(".fs_clones").cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400, fx: {open: 'fadeIn', openSpeed: 'fast'}, clickThrough: true}).show();
		} else {
			$(".fs_clones").show();
		}

	}
}

/* Simple tiki plugin for jQuery
 * Helpers for autocomplete and sheet
 */

$.fn.tiki = function(func, type, options) {
	var opts;
	switch (func) {
		case "autocomplete":
			if (jqueryTiki.autocomplete) {
				if (typeof type !== 'undefined') { // func and type given
					options = options || {};		// some default options for autocompletes in tiki
					opts = {extraParams: {"httpaccept": "text/javascript"},
								dataType: "json",
								parse: parseAutoJSON,
								formatItem: function(row) { return row; },
								selectFirst: false,
								max: 15
							};
					for(opt in options) {
						opts[opt] = options[opt];
					}
				}
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
					case "userrealname":
						data = "tiki-ajax_services.php?listonly=userrealnames";
						break;
					case "tag":
						data = "tiki-ajax_services.php?listonly=tags&separator=+";
						break;
					case "icon":
						data = "tiki-ajax_services.php?listonly=icons&max=" + opts.max;
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
						data = "tiki-ajax_services.php?listonly=trackername";
						break;
				}
		 		return this.each(function() {
					$(this).autocomplete(data, opts).click( function () {
						$(".ac_results").hide();	// hide the drop down if input clicked on again
					});
		
				});
			}
			break;

		case "sheet":
			if (jqueryTiki.sheet) {
				options = options || {};	// some default options for sheets in tiki
				
				//ensure that sheet instance exists, otherwise problems getting current instance number;
				var I = 0;
				if ( $.sheet.instance ) {
					I = $.sheet.instance.length; //we use length here because we haven't yet created sheet, it will append 1 to this number thus making this the effective instance number
				} else {
					$.sheet.instance = [];
				}				
				
				var inlineMenu =  $("#sheetTools").html();
				inlineMenu = jQuery(
							(inlineMenu ? inlineMenu : "").replace(/sheetInstance/g, "jQuery.sheet.instance[" + I + "]")
				);
				
				inlineMenu.find('.qt-picker').attr('instance', I);
				
				opts = $.extend({
							urlMenu: 		"lib/jquery_tiki/jquery.sheet/menu.html",	/* not working currently due to missing menu plugin */
							urlGet: "",
							buildSheet: true,
							autoFiller: true,
							inlineMenu: inlineMenu,
							colMargin: 20, //beefed up colMargin because the default size was too small for font
							height: $(window).height() * 0.8
				}, options);
				
		 		return this.each(function() {
					var sh;
		 			if (jqueryTiki.ui) {
						if ($(this).attr('style') && $(this).attr('style').toLowerCase().indexOf('height') > -1) {
							$(this).height($(this).find("table:first").height() + 6); //a little extra padding?
						} else {
							$(this).height($(this).height() + 0);
						}
		 				sh = $(this).sheet(opts);
		 				if (typeof ajaxLoadingShow === 'function') {
		 					ajaxLoadingHide();
		 				}
		 			} else {
		 				sh = $(this).sheet(opts);
		 			}
				});
			}
			break;
		case "s5":
			if (jqueryTiki.jqs5) {
				$(this).s5($.extend({
					menu: function() {
						return (
							'<a href="#" onclick="jQuery.s5.go(\'first\'); return false;" title="First"><img src="lib/jquery/jquery.s5/images/resultset_first.png" alt="First" /></a> ' + 
							'<a href="#" onclick="jQuery.s5.go(\'prev\'); return false;" title="Prev"><img src="lib/jquery/jquery.s5/images/resultset_previous.png" alt="Prev" /></a> ' + 
							'<a href="#" onclick="jQuery.s5.go(\'next\'); return false;" title="Next"><img src="lib/jquery/jquery.s5/images/resultset_next.png" alt="Next" /></a> ' + 
							'<a href="#" onclick="jQuery.s5.go(\'last\'); return false;" title="Last"><img src="lib/jquery/jquery.s5/images/resultset_last.png" alt="Last" /></a> ' +
							'<a href="#" onclick="jQuery.s5.listSlideTitles(); return false;" title="Jump To Slide" class="listSlideTitlesAnchor"><img src="lib/jquery/jquery.s5/images/layers.png" alt="Jump To Slide" /></a> ' +
							'<a href="#" onclick="jQuery.s5.autoPlay(true); return false;" title="Play"><img src="lib/jquery/jquery.s5/images/control_play_blue.png" alt="Play" /></a> ' +
							'<a href="#" onclick="jQuery.s5.s.pause = true; return false;" title="Pause"><img src="lib/jquery/jquery.s5/images/control_pause_blue.png" alt="Pause" /></a> ' +
							'<a href="#" onclick="jQuery.s5.s.pause = true; go(\'first\'); return false;" title="Stop"><img src="lib/jquery/jquery.s5/images/control_stop_blue.png" alt="Stop" /></a> ' +
							'<a href="#" onclick="jQuery.s5.getNote(); return false;" title="Notes"><img src="lib/jquery/jquery.s5/images/note.png" alt="Notes" /></a> ' +
							'<a href="#" onclick="jQuery.s5.toggleLoop(); return false;" title="Toggle Loop"><img src="lib/jquery/jquery.s5/images/arrow_rotate_clockwise.png" alt="Toggle Loop" /></a>'
						);
					},
					slideDuration: 10000 //10 seconds
				},options));
			}
			break;
		case "carousel":
			if (jqueryTiki.carousel) {
				opts = {
						imagePath: "lib/jquery/infinitecarousel/images/"
					};
				for(opt in options) {
					opts[opt] = options[opt];
				}
		 		return this.each(function() {
					$(this).infiniteCarousel(opts);			
				});
			}
			break;
		case "datepicker":
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
							buttonImage: "pics/icons/calendar.png",
							buttonImageOnly: true,
							dateFormat: "yy-mm-dd",
							showButtonPanel: true,
							altFormat: "@",
							onSelect: function(dateText, inst) {
								$(inst.settings.altField).val(parseInt($(inst.settings.altField).val() / 1000, 10));
							}
						};
						break;
					default:
						opts = {
							showOn: "both",
							buttonImage: "pics/icons/calendar.png",
							buttonImageOnly: true,
							dateFormat: "yy-mm-dd",
							showButtonPanel: true
						};
						break;
				}
				$.extend(opts, options);
		 		return this.each(function() {
					$(this).datepicker(opts);			
				});
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
				for(opt in options) {
					opts[opt] = options[opt];
				}
		 		return this.each(function() {
					$(this).accordion(opts);			
				});
			}
	}
};

/******************************
 * Functions for dialog tools *
 ******************************/

// shared

window.dialogData = [];
var dialogDiv;

function displayDialog( ignored, list, area_id ) {
	var i, item, el, obj, tit = "";

	$is_cked =  $('#cke_contents_' + area_id).length !== 0;

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
		if (selection) { selection.lock(); }
	}
	
	if (!obj) { obj = {}; }
	if (!obj.width) { obj.width = 210; }
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
var pickerDiv;

function displayPicker( closeTo, list, area_id, isSheet, styleType ) {
	if (pickerDiv) {
		$('div.toolbars-picker').remove();	// simple toggle
		pickerDiv = false;
		return;
	}
	textarea = $('#' +  area_id);
	
	pickerDiv = document.createElement('div');
	document.body.appendChild( pickerDiv );

	var coord = $(closeTo).offset();
	coord.bottom = coord.top + $(closeTo).height();

	pickerDiv.className = 'toolbars-picker';
	pickerDiv.style.left = coord.left + 'px';
	pickerDiv.style.top = (coord.bottom + 8) + 'px';

	var prepareLink = function( link, ins, disp ) {
		if (!link) { return; }
		
		link.innerHTML = disp.replace('\/', '/');
		link.href = 'javascript:void(0)';
		
		if ( isSheet ) {
			link.onclick = function() {
				var o = $(link);
				var I = $(closeTo).attr('instance');
				I = parseInt( I ? I : 0, 10 );
				$.sheet.instance[ I ].cellChangeStyle(styleType, o.children().first().css('background-color'));
				
				$('div.toolbars-picker').remove();
				pickerDiv = false;
				
				return false;
			};
		} else {
			link.onclick = function() {
				insertAt( area_id, ins );
		
				textarea = $('#' +  area_id);	
				// quick fix for Firefox 3.5 losing selection on changes to popup
				if (typeof textarea.selectionStart != 'undefined') {
					var tempSelectionStart = textarea.selectionStart;
					var tempSelectionEnd = textarea.selectionEnd;	
				}
	
				$('div.toolbars-picker').remove();
				pickerDiv = false;
	
				// quick fix for Firefox 3.5 losing selection on changes to popup
	        	if (typeof textarea.selectionStart != 'undefined' && textarea.selectionStart != tempSelectionStart) {
	                textarea.selectionStart = tempSelectionStart;
	     		}
				if (typeof textarea.selectionEnd != 'undefined' && textarea.selectionEnd != tempSelectionEnd) {
	            	textarea.selectionEnd = tempSelectionEnd;
	       		}
	
				return false;
			};
		}
	};

	for( var i in window.pickerData[list] ) {
		var chr = window.pickerData[list][i];
		var link = document.createElement( 'a' );

		//pickerDiv.appendChild( document.createTextNode(' ') );
		prepareLink( link, i, chr );
		pickerDiv.appendChild( link );
	}
}


function dialogSelectElement( area_id, elementStart, elementEnd ) {
	if ($('#cke_contents_' + area_id).length !== 0) { return; }	// TODO for ckeditor
	
	var $textarea = $('#' + area_id);
	var val = $textarea.val();
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
	
	$textarea[0].focus();
	var selection = $textarea.selection();

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
			var el = $("<input type=\"text\" id=\"tbTableR" + r + "C" + c + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" value=\"" + v + "\" style=\"width:" + (90 / cols) + "%\" />");
			$(dialog).append(el);
		}
		if (r == 1) {
			el = $("<img src=\"pics/icons/add.png\" />");
			$(dialog).append(el);
			el.click(function() {
				$(dialog).attr("cols", $(dialog).attr("cols") + 1);
				for (r = 1; r <= $(dialog).attr("rows"); r++) {
					v = "   ";
					var el = $("<input type=\"text\" id=\"tbTableR" + r + "C" + $(dialog).attr("cols") + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" value=\"" + v + "\" style=\"width:" + (90 / $(dialog).attr("cols")) + "%\" />");
					$("#tbTableR" + r + "C" + ($(dialog).attr("cols") - 1)).after(el);
				}
				$(dialog).find("input").width(90 / $(dialog).attr("cols") + "%");
			});
		}
		$(dialog).append($("<br />"));
	}
	el = $("<img src=\"pics/icons/add.png\" />");
	$(dialog).append(el);
	el.click(function() {
		$(dialog).attr("rows", $(dialog).attr("rows") + 1);
		for (c = 1; c <= $(dialog).attr("cols"); c++) {
			v = "   ";
			var el = $("<input type=\"text\" id=\"tbTableR" + $(dialog).attr("rows") + "C" + c + "\" class=\"ui-widget-content ui-corner-all\" size=\"10\" value=\"" + v + "\" style=\"width:" + (90 / $(dialog).attr("cols")) + "%\" />");
			$(this).before(el);
		}
		$(this).before("<br />");
		$(dialog).dialog("option", "height", ($(dialog).attr("rows") + 1) * 1.2 * $("#tbTableR1C1").height() + 130);
	});
	
	dialog.rows = rows;
	dialog.cols = cols;
	$(dialog).dialog("option", "width", (cols + 1) * 120 + 50);
	$(dialog).dialog("option", "position", "center");
	$("#tbTableR1C1").focus();
}

function dialogTableInsert(area_id, dialog) {
	var s = "||", rows, cols, c, r, rows2 = 1, cols2 = 1;
	rows = dialog.rows ? dialog.rows : 3;
	cols = dialog.cols ? dialog.cols : 3;
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
			s += $("#tbTableR" + r + "C" + c).val();
			if (c < cols2) {
				s += "|";
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
	
	var s, opt, ta, str, re, p = 0, m;
	s = $("#tbFindSearch").removeClass("ui-state-error").val();
	opt = "";
	if ($("#tbFindCase").attr("checked")) {
		opt += "i";
	}
	ta = $('#' + area_id);
	str = ta.val();
	re = new RegExp(s,opt);
	p = getCaretPos(ta[0]);
	if (p && p < str.length) {
		m = re.exec(str.substring(p));
	} else {
		p = 0;
	}
	if (!m) {
		m = re.exec(str);
		p = 0;
	}
	if (m) {
		setSelectionRange(ta[0], m.index + p, m.index + s.length + p);
	} else {
		$("#tbFindSearch").addClass("ui-state-error");
	}

}

// Replace

function dialogReplaceOpen(area_id) {

	var s = getTASelection($('#' + area_id)[0]);
	$("#tbReplaceSearch").val(s).focus();
	  		  
}

function dialogReplaceReplace( area_id ) {
	
	var s = $("#tbReplaceSearch").val();
	var r = $("#tbReplaceReplace").val();
	var opt = "";
	if ($("#tbReplaceAll").attr("checked")) {
		opt += "g";
	}
	if ($("#tbReplaceCase").attr("checked")) {
		opt += "i";
	}
	var str = $('#' + area_id).val();
	var re = new RegExp(s,opt);
	$('#' + area_id).val(str.replace(re,r));

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
			var title = $('.postbody-title', comment).clone();
			var author = $('.author', comment).clone();
			var body = $('.postbody-content', comment).clone();
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

						child.append( title.append(author) )
							.append( $('<dd/>').append(body) );
					} );
			}
		});
	};

	/**
	 * Convert a zone to a note editor by attaching handlers on mouse events.
	 */
	$.fn.noteeditor = function (textarea, link) {
		var hiddenParents = null;
		var annote = $(link)
			.click( function( e ) {
				e.preventDefault();
				var annotation = $(this).attr('annotation');
				$(this).fadeOut(100);
				$("form.comments").hide();
				$(textarea).parent().find(".comment-info").remove();
				var msg = "";
				if (annotation.length < 20) {
					msg = tr("The text you have selected is quite short. Select a longer piece to ensure the note is associated with the correct text.") + "<br />";
				}
				msg = "<p class='description comment-info'>" + msg + tr("Tip: Leave the first line as it is, starting with \";note:\". This is required") + "</p>";
				$(textarea).parent().append($(msg));
				
				hiddenParents = $(textarea).parents(":hidden");
				$(textarea).parents().fadeIn(100);
				$(textarea).val(';note:' + annotation + "\n\n").focus().scroll();
				if (typeof $(textarea).selection == 'function') {	// only there if autocomplete enabled
					var len = $(textarea).val().length;
					$(textarea).selection(len, len);
				}
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
					var string = $.trim( range.toString() );

					if( string.length && -1 === string.indexOf( "\n" ) ) {
						annote.attr('annotation', string);
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

	$.fn.categ_browse_tree = function () {
		this.each(function () {
			$('.treenode:not(.done)', this)
				.addClass('done')
				.each(function () {
					if ($('ul:first', this).hide().length) {
						$(this).prepend('<span class="flipper ui-icon ui-icon-plus" style="float: left;"/>');
					} else {
						$(this).prepend('<span class="ui-icon ui-icon-triangle-1-e" style="float: left;"/>');
					}
				});

			$('.flipper:not(.done)')
				.addClass('done')
				.css('cursor', 'pointer')
				.click(function () {
					var body = $(this).parent().find('ul:first');
					if ('block' === body.css('display')) {
						$(this).removeClass('ui-icon-minus').addClass('ui-icon-plus');
						body.hide('fast');
					} else {
						$(this).removeClass('ui-icon-plus').addClass('ui-icon-minus');
						body.show('fast');
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
					this.lastRange.insertNode(document.createTextNode(' or '));
				} else {
					$(this).next().append(' or ').append(node);
				}
				$(this).next().change();
				break;
			}
		});

		return this;
	};

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
