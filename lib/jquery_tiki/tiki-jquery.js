// $Id$
// JavaScript glue for jQuery in Tiki
//
// Tiki 6 - $ is now initialised in jquery.js
// but let's keep $jq available too for legacy custom code

var $jq = $,
	$window = $(window),
	$document = $(document);

// Escape a string for use as a jQuery selector value, for example an id or a class
// CSSOM defines an escaping function for CSS. Firefox 31 (2014) offers CSS.escape(), which is probably equivalent.
// jQuery issue #1761 requests the inclusion of an equivalent function in jQuery (open November 2015)
function escapeJquery(str) {
	return str.replace(/([\!"#\$%&'\(\)\*\+,\.\/:;\?@\[\\\]\^`\{\|\}\~=>])/g, "\\$1");
}

// Check / Uncheck all Checkboxes - overriden from tiki-js.js
function switchCheckboxes (tform, elements_name, state, hiddenToo) {
	// checkboxes need to have the same name elements_name
	// e.g. <input type="checkbox" name="my_ename[]">, will arrive as Array in php.
	if (hiddenToo == undefined) {
		hiddenToo = false;
	}
	var closeTag;
	if (hiddenToo) {
		closeTag = '"]';
	} else {
		closeTag = '"]:visible';
	}
	$(tform).contents().find('input[name="' + escapeJquery(elements_name) + closeTag).prop('checked', state).change();
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
		showJQ("#" + foo, jqueryTiki.effect, jqueryTiki.effect_speed, jqueryTiki.effect_direction);
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
	var $foo = $("#" + foo);
	if (style && style !== 'block' || foo === 'help_sections' || foo === 'fgalexplorer' || typeof jqueryTiki === 'undefined') {	// TODO find a better way?
		$foo.toggle();	// inlines don't animate reliably (yet) (also help)
		if ($foo.css('display') === 'none') {
			setSessionVar('show_' + tiki_encodeURIComponent(foo), 'n');
		} else {
			setSessionVar('show_' + tiki_encodeURIComponent(foo), 'y');
		}
	} else {
		if ($foo.css("display") === "none") {
			setSessionVar('show_' + tiki_encodeURIComponent(foo), 'y');
			show(foo);
		}
		else {
			setSessionVar('show_' + tiki_encodeURIComponent(foo), 'n');
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
		$(selector).slideDown(speed);
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


function ajaxSubmitEventHandler(successCallback) {
	return function (e) {
		e.preventDefault();
		var form = this, act;
		act = $(form).attr('action');
		var modal = $(form).closest('.modal-dialog');

		if (! act) {
			act = url;
		}

		/*
		 If confirm-action class is set on the form, it should follow the confirmAction
		 processing and a feedback message should be used. confirmAction deals with processing
		 the ajax service call.
		 */
		if ($(form).hasClass("confirm-action")) {
			confirmAction(this);
			return false;
		}

		if (typeof $(form).valid === "function") {
			if (!$(form).valid()) {
				return false;
			} else if ($(form).validate().pendingRequest > 0) {
				$(form).validate();
				setTimeout(function() {$(form).submit();}, 500);
				return false;
			}
		}

		modal.tikiModal(tr('Loading...'));
		$.ajax(act, {
			type: 'POST',
			dataType: 'json',
			data: $(form).serialize(),
			success: function (data) {
				successCallback.apply(form, [data]);
			},
			error: function (jqxhr) {
				modal.tikiModal();
				$(form).showError(jqxhr);
			},
			complete: function () {
				modal.tikiModal();
			}
		});

		return false;
	};
}

function checkDuplicateRows( button, columnSelector, rowSelector, parentSelector ) {
	if (typeof columnSelector === 'undefined') {
		columnSelector = "td";
	}
	if (typeof rowSelector === 'undefined') {
		rowSelector = "tr:not(:first)";
	}
	if (typeof parentSelector === 'undefined') {
		parentSelector = "table:first";
	}
	var $rows = $(button).parents(parentSelector).find(rowSelector);
	$rows.each(function( ix, el ){
		if ($("input:checked", el).length === 0) {
			var $el = $(el);
			var line = $el.find(columnSelector).text();
			$rows.each(function( ix, el ){
				if ($el[0] !== el && $("input:checked", el).length === 0) {
					if (line === $(el).find(columnSelector).text()) {
						$(":checkbox:first", el).prop("checked", true);
					}
				}
			});
		}
	});
}

$.fn.tiki_popover = function () {
	var list, $container = this;

	/*
	 * Prepare the data so all elements so the data is all in the right format for bootstrap popovers
	 */
	list = $container.find('.tips[title!=""], .tikihelp[title!=""]')
		.each(function () {
			var element = this;

			if ($(this).attr('title')) {
				$.each(['|', ':', '<br/>', '<br>'], function (key, sep) {
					var parts = $(element).attr('title').split(sep);
					if (parts.length > 1) {
						$(element).attr('title', parts.shift());
						$(element).data('content', parts.join(sep));
					}
				});
			} else {
				$(this).attr('title', '');
			}

			if (! $(this).data('trigger')) {
				$(this).data('trigger', 'hover');
			}
		});

	$.merge(list, $container.find("a[data-toggle=popover]:not(.tips[title!='']):not(.tikihelp[title!=''])"));

	list.filter('.bottom').data('placement', 'bottom');
	list.filter('.left').data('placement', 'left');
	list.find('img').attr('title', ''); // Remove the img title to avoid browser tooltip
	list.filter('[data-trigger="click"]')
		.click(function (e) {
			e.preventDefault();
		});
	
	// Handle common cases
	list
		.popover({
			container: 'body',
			html: true,
			delay: { "show": 0, "hide": 10 },
			placement: $.tikiPopoverWhereToPlace
		});

	$container.find('.ajaxtips').each(function() {
		var me = $(this),
			trigger = me.data('trigger') || 'hover';

		$(this).popover({
			trigger: trigger,
			html: true,
			delay: { "show": 0, "hide": 10 },
			content: function () {
				var link = this, content = $(this).data('content');

				if (!content) {
					$.get($(this).data('ajaxtips'), function (data) {
						content = data;

						$(link).data('content', content);
						$(link).popover('show');
					});
				}

				return content;
			}
		});
	});

	// only have one popover showing at a time
	$document.on("show.bs.popover", function ( e ) {
		var event = e;
		$('.popover:visible:not(.tour-tour)').each(function () {
			if (this.previousElementSibling !== event.target) {
				$(this).hide();
			}
		});
	});

	return $container;
};

$.tikiPopoverWhereToPlace = function (pop, el) {
	var pxNum = function(str) {
			return (str || '').replace('px', '') * 1;
		},
		$win = $(window),
		width = $win.width(),
		height = $win.height(),
		$pop = $(pop),
		$el = $(el),
		allowedImgWidth = width * 0.60,
		allowedImgHeight = height * 0.60,
		leftPos = $el.offset().left,
		$img = $pop.find('div[style*="background-image"],img').first(),
		$imgContainer = $img.parent(),
		$imgPopover = $imgContainer.parent(),
		imgWidth = pxNum($img.css('width')),
		imgHeight = pxNum($img.css('height')),
		newImgWidth,
		newImgHeight,
		widthBuffer,
		heightBuffer;
		
	if ($el.data("placement")) {
		return $el.data("placement");	// element already has popover placement set
	}

	if($el.attr('position') == "center"){
		$el.css('display', 'inline');
	}else{
		$el.css('display', 'inline-block');
	}

	//lets check the size of the popover img
	if (imgWidth > allowedImgWidth || imgHeight > allowedImgHeight) {
		widthBuffer = (pxNum($imgContainer.css('padding-left')) + pxNum($imgContainer.css('margin-left')) + pxNum($imgContainer.css('border-left-width'))) * 2;
		heightBuffer = (pxNum($imgContainer.css('padding-top')) + pxNum($imgContainer.css('margin-top')) + pxNum($imgContainer.css('border-top-width'))) * 2;

		// proportionate the image relative to what is allowed
		if(allowedImgWidth/imgWidth > allowedImgHeight/imgHeight){
			newImgWidth = allowedImgWidth;
			newImgHeight = imgHeight*(allowedImgWidth/imgWidth);
		} else {
			newImgWidth = imgWidth*(allowedImgHeight/imgHeight);
			newImgHeight = allowedImgHeight;
		}

		$img.css({
			backgroundSize: newImgWidth + 'px ' + newImgHeight + 'px',
			width: newImgWidth + 'px',
			height: newImgHeight + 'px'
		});

		$imgPopover.css({
			maxWidth: (newImgWidth + widthBuffer) + 'px',
			maxHeight: (newImgHeight + heightBuffer) +'px'
		});
	}

	if (width < 500 || ((width - leftPos) + imgWidth) > width) return 'bottom';
	if (width - leftPos > 400) return 'right';

	return 'left';
};

/*
	keep the popover open while the user is hovering over it
	based on http://jsfiddle.net/raving/2thfaxeu thanks
*/
var originalLeave = $.fn.popover.Constructor.prototype.leave;
$.fn.popover.Constructor.prototype.leave = function(obj){
  var self = obj instanceof this.constructor ?
    obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
  var container, timeout;

  originalLeave.call(this, obj);

  if(self.$tip) {
    container = self.$tip;
    timeout = self.timeout;
    container.one('mouseenter', function(){
      //We entered the actual popover – call off the dogs
      clearTimeout(timeout);
      //Let's monitor popover content instead
      container.one('mouseleave', function(){
        $.fn.popover.Constructor.prototype.leave.call(self, self);
      });
    })
  }
};




$(function() { // JQuery's DOM is ready event - before onload
	if (!window.jqueryTiki) window.jqueryTiki = {};

	// Reflections
	if (jqueryTiki.reflection) {
		$("img.reflect").reflect({});
	}

	if (jqueryTiki.tooltips) {
		$(document).tiki_popover();
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
				if ($(this).data('active')) return;

				$(this)
					.data('active', true)
					.moveToWithinWindow();
			},
			onHide: function(){
				$(this).removeData('active');
			}
		});
		$('ul.cssmenu_vert').superfish({
			animation: {opacity:'show', height:'show'},	// fade-in and slide-down animation
			speed: 'fast',								// faster animation speed
			onShow: function(){
				if ($(this).data('active')) return;

				$(this)
					.data('active', true)
					.moveToWithinWindow();
			},
			onHide: function(){
				$(this).removeData('active');
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
	if (jqueryTiki.colorbox && !jqueryTiki.mobile) {
		$().bind('cbox_complete', function(){	
			$("#cboxTitle").wrapInner("<div></div>");
		});
				
		// Tiki defaults for ColorBox

		//for every link containing 'shadowbox', 'colorbox', 'box', etc. in data-box attribute
		$("a[data-box*='box']").colorbox({
			rel: function(){
				return $(this).attr('data-box');
			},
			transition: "elastic",
			maxHeight:"95%",
			maxWidth:"95%",
			overlayClose: true,
			current: jqueryTiki.cboxCurrent
		});
		
		// now, first let suppose that we want to display images in ColorBox by default:
		
		// this matches data-box attributes containing type=img or no type= specified
		$("a[data-box*='box'][data-box*='type=img'], a[data-box*='box'][data-box!='type=']").colorbox({
			photo: true
		});
		// data-box attributes containing slideshow (this one must be without #col1)
		$("a[data-box*='box'][data-box*='slideshow']").colorbox({
			photo: true,
			slideshow: true,
			slideshowSpeed: 3500,
			preloading: false,
			width: "100%",
			height: "100%"
		});
		// this are the defaults matching all *box links which are not obviously links to images...
		// (if we need to support more, add here... otherwise it is possible to override with type=iframe in data-box attribute of a link)
		//  (from here one to speed it up, matches any link in #col1 only - the main content column)
		$("#col1 a[data-box*='box']:not([data-box*='type=img']):not([href*='display']):not([href*='preview']):not([href*='thumb']):not([data-box*='slideshow']):not([href*='image']):not([href$='\.jpg']):not([href$='\.jpeg']):not([href$='\.png']):not([href$='\.gif'])").colorbox({
			iframe: true,
			width: "95%",
			height: "95%"
		});
		// hrefs starting with ftp(s)
		$("#col1 a[data-box*='box'][href^='ftp://'], #col1 a[data-box*='box'][href^='ftps://']").colorbox({
			iframe: true,
			width: "95%",
			height: "95%"
		});
		// data-box attributes containing type=flash
		$("#col1 a[data-box*='box'][data-box*='type=flash']").colorbox({
			inline: true,
			width: "60%",
			height: "60%",
			href: function () {
				var $el = $("#cb_swf_player");
				if ($el.length === 0) {
					$el = $("<div id='cb_swf_player' />");
					$(document.body).append($("<div />").hide().append($el));
				}
				//$(this).media.swf(el, { width: 400, height: 300, autoplay: true, src: $(this).attr("href") });
				swfobject.embedSWF($(this).attr("href"), "cb_swf_player", "100%", "90%", "9.0.0", "lib/swfobject/expressInstall.swf");
				return $("#cb_swf_player");
			}
		});
		// data-box attributes with type=iframe (if someone needs to override anything above)
		$("#col1 a[data-box*='box'][data-box*='type=iframe']").colorbox({
			iframe: true
		});
		// inline content: hrefs starting with #
		$("#col1 a[data-box*='box'][href^='#']").colorbox({
			inline: true,
			width: "50%",
			height: "50%",
			href: function(){ 
				return $(this).attr('href');
			}
		});
		
		// titles (for captions):
		
		// by default get title from the title attribute of the link (in all columns)
		$("a[data-box*='box'][title]").colorbox({
			title: function(){ 
				return $(this).attr('title');
			}
		});
		// but prefer the title from title attribute of a wrapped image if any (in all columns)
		$("a[data-box*='box'] img[title]").colorbox({
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
		// data-box attributes containing title param overrides title attribute of the link (shadowbox compatible)
		$("#col1 a[data-box*='box'][data-box*='title=']").colorbox({
			title: function () {
				re = /(title=([^;\"]+))/i;
				ret = $(this).attr("rel").match(re);
				return ret[2];
			}
		});
		// data-box attributes containing height param (shadowbox compatible)
		$("#col1 a[data-box*='box'][data-box*='height=']").colorbox({
			height: function () {
				re = /(height=([^;\"]+))/i;
				ret = $(this).attr("rel").match(re);
				return ret[2];
			}
		});
		// data-box attributes containing width param (shadowbox compatible)
		$("#col1 a[data-box*='box'][data-box*='width=']").colorbox({
			width: function () {
				re = /(width=([^;\"]+))/i;
				ret = $(this).attr("rel").match(re);
				return ret[2];
			}
		});	
		
		// links generated by the {COLORBOX} plugin
		if (jqueryTiki.colorbox) {
			$("a[data-box^='shadowbox[colorbox']").each(function () {$(this).attr('savedTitle', $(this).attr('title'));});
			$("a[data-box^='shadowbox[colorbox']").colorbox({
				title: function() {
					return $(this).attr('savedTitle');	// this fix not required is colorbox was disabled
				}
			});
		}
		
	}	// end if (jqueryTiki.colorbox)

	if (jqueryTiki.zoom) {
		$("a[data-box*=zoom]").each(function () {
			$(this)
				.wrap('<span class="img_zoom"></span>')
				.parent()
				.zoom({
					url: $(this).attr("href")
				});
		});
	}

	$.fn.applyChosen = function () {
		if (jqueryTiki.chosen) {
			$("select:not(.allow_single_deselect):not(.nochosen)").tiki("chosen");
		}
	};

	$.applyChosen = function() {
		return $('body').applyChosen();
	};

	if (jqueryTiki.chosen) {
		$.applyChosen();
	}

	$( function() {
		$("#keepOpenCbx").click(function() {
			if (this.checked) {
				setCookie("fgalKeepOpen", "1");
			} else {
				setCookie("fgalKeepOpen", "");
			}
		});
		var keepopen = getCookie("fgalKeepOpen");
		$("#keepOpenCbx").prop("checked", !! keepopen);
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
	if (!$("#keepOpenCbx").prop("checked")) {
		window.close();
	} else {
		window.blur();
		if (window.opener) {
			window.opener.focus();
		}
	}
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
				if (! edit) {
					val = val.replace(/"/g, '\\"');	// escape double quotes
				}
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
		if (container.data("ui-dialog")) {
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
			btns: btns
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
			
			var $row = $('.wikiplugin_edit').find('#param_' + paramName);
			$row.addClass('parent_' + paramValues.parent.name + '_' + paramValues.parent.value);
			
			if ($parent.val() != paramValues.parent.value) {
				if (!$parent.val() && $("input, select", $row).val()) {
					$parent.val(paramValues.parent.value);
				} else {
					$row.hide();
				}
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

function dialogSelectElement( area_id, elementStart, elementEnd ) {
	if (typeof CKEDITOR !== 'undefined' && typeof CKEDITOR.instances[area_id] !== 'undefined') {return;}	// TODO for ckeditor

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

/*
 * JS only textarea fullscreen function (for Tiki 5+)
 */

$(function() {	// if in translation-diff-mode go fullscreen automatically
	if ($("#diff_outer").length && !$.trim($(".wikipreview .wikitext").html()).length) {	// but not if previewing (TODO better)
		toggleFullScreen("editwiki");
	}
});

function sideBySideDiff() {
	if ($('.side-by-side-fullscreen').size()) {
		$('.side-by-side-fullscreen').remove();
		return;
	}

	var $diff = $('#diff_outer').remove(), $zone = $('.edit-zone');
	$zone.after($diff.addClass('side-by-side-fullscreen'));
	$diff.find('#diff_history').height('');
}

function toggleFullScreen(area_id) {

	if ($("input[name=wysiwyg]").val() === "y") {		// quick fix to disable side-by-side translation for wysiwyg
		$("#diff_outer").css({
			position: "inherit",
			height: "400px",
			overflowX: "auto"
		});
		return;
	}

	var textarea = $("#" + area_id);
	
	//codemirror interation and preservation
	var textareaEditor = syntaxHighlighter.get(textarea);
	if (textareaEditor) {
		syntaxHighlighter.fullscreen(textarea);
		sideBySideDiff();
		return;
	}

	var toolbar = $('#editwiki_toolbar'),
		preview = $("#autosave_preview"),
		comment = $("#comment").parents("fieldset:first"),
		screen = $('.TextArea-fullscreen'),
		zone = $('.edit-zone', screen);

	screen.add(textarea).css('height', '');

	//removes wiki command buttons (save, cancel, preview) from fullscreen view
	$('.TextArea-fullscreen .actions').remove();
	if (textarea.parent().hasClass("ui-wrapper")) {
		textarea.resizable("destroy");	// if codemirror is off, jquery-ui resizable messes this up
	}

	var textareaParent = textarea.parents(".tab-content:first").toggleClass('TextArea-fullscreen');

	if (textareaParent.hasClass('TextArea-fullscreen')) {
		$('body').css('overflow', 'hidden');
		$('.tabs,.rbox-title').toggle();

		var win = $window
			.data('cm-resize', true),
			diff = $("#diff_outer"),
			msg = $(".translation_message"),
			actions = $('.actions', textarea.parents("form"));

		//adds wiki command buttons (save, cancel, preview) to fullscreen view
		actions.clone().appendTo('.TextArea-fullscreen');
		actions = $('.actions', $('.TextArea-fullscreen'));

		comment.css({	// fix comments fieldset to bottom and hide others (like contributions)
			position: "absolute",
			bottom: actions.outerHeight() + "px",
			width: "100%"
		}).nextAll("fieldset").hide();

		preview.css({
			position: "absolute",
			top: 0,
			left: 0
		});

		win.resize(function() {
			screen = $('.TextArea-fullscreen');
			actions = $('.actions', screen);
			comment = $("#comment").parents("fieldset:first");
			if (win.data('cm-resize') && screen) {
				screen.css('height', win.height() + 'px');
				var swidth = win.width() + "px";
				var commentMargin = parseInt(comment.css("marginBottom").replace("px", "")) * 2;
				commentMargin += parseInt(comment.css("borderBottomWidth").replace("px", "")) * 2;
				var innerHeight = win.height() - comment.outerHeight() - commentMargin - actions.outerHeight();
             // reducing innerHeight by 85px in prev line makes the "Describe the change you made:" and
             // "Monitor this page:" edit fields visible and usable. Tested in all 22 themes in Tiki-12 r.48429

				if (diff.length) {
					swidth = (screen.width() / 2) + "px";
					innerHeight -= msg.outerHeight();
					msg.css("width", (screen.width() / 2 - msg.css("paddingLeft").replace("px", "") - msg.css("paddingRight").replace("px", "")) + "px");
					diff.css({
						width: swidth,
						height: innerHeight + 'px'
					});
					$('#diff_history').height(innerHeight + "px");
				}
				textarea.css("width", swidth);
				toolbar.css('width', swidth);
				zone.css("width", swidth);
				preview.css("width", swidth);
				textarea.css('height', (innerHeight - toolbar.outerHeight()) + "px");
			}
		});
		setTimeout(function () {$window.resize();}, 500);	// some themes (coelesce) don't show scrollbars unless this is delayed a bit
	} else {
		textarea.css("width", "");
		toolbar.css('width', "");
		zone.css({ width: "", height: ""});
		screen.css("width", "");
		comment.css({ position: "", bottom: "", width: "" }).nextAll("fieldset").show();
		preview.css({ position: "", top: "", left: "" });
		$('body').css('overflow', '');
		$('.tabs,.rbox-title').toggle();
		$window.removeData('cm-resize');
	}

	sideBySideDiff();
}

/* Simple tiki plugin for jQuery
 * Helpers for autocomplete and sheet
 */
var xhrCache = {}, lastXhr;	// for jq-ui autocomplete

$.fn.tiki = function(func, type, options) {
	var opts = {}, opt;
	switch (func) {
		case "autocomplete":
			if (jqueryTiki.autocomplete && jqueryTiki.ui) {
				if (typeof type === 'undefined') { // func and type given
					// setup error - alert here?
					return null;
				}
				options = options || {};
				var requestData = {};

				var url = "";
				switch (type) {
					case "pagename":
						url = "tiki-listpages.php?listonly&initial=" + (options.initial ? options.initial + "&nonamespace" : "");
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
					$(this).autocomplete(opts).blur( function() {
						$(this).removeClass( "ui-autocomplete-loading").change();
					});
				});
			}
			break;
		case "carousel":
			if (jqueryTiki.carousel) {
				opts = {
						imagePath: "vendor/jquery/plugins/infinitecarousel/images/",
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
							buttonText: '',
							dateFormat: jqueryTiki.shortDateFormat,
							timeFormat: jqueryTiki.shortTimeFormat,
							showButtonPanel: true,
							altFormat: "@",
							altFieldTimeOnly: false,
							onClose: function(dateText, inst) {
								$.datepicker._updateAlternate(inst);	// make sure the hidden field is up to date
								var val = $(inst.settings.altField).val(), timestamp;
								if (func === "datetimepicker") {
									val = val.substring(0, val.indexOf(" "));
									timestamp = parseInt(val / 1000, 10);
									if (!timestamp || isNaN(timestamp)) {
										$.datepicker._setDateFromField(inst);	// seems to need reminding when starting empty
										$.datepicker._updateAlternate(inst);
										val = $(inst.settings.altField).val();
										val = val.substring(0, val.indexOf(" "));
										timestamp = parseInt(val / 1000, 10);
									}
									if (timestamp && inst.settings && inst.settings.timepicker) {	// if it's a datetimepicker add on the time
										var time = inst.settings.timepicker.hour * 3600 +
											inst.settings.timepicker.minute * 60 +
											inst.settings.timepicker.second;
										timestamp += time;
									}
								} else {
									timestamp = parseInt(val / 1000, 10);
								}
								$(inst.settings.altField).val(timestamp ? timestamp : "").change();
							}
						};
						break;
					default:
						opts = {
							showOn: "both",
							buttonText: '',
							dateFormat: jqueryTiki.shortDateFormat,
							showButtonPanel: true,
							firstDay: jqueryTiki.firstDayofWeek
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
		case "chosen":
			if (jqueryTiki.chosen) {
				opts = { allow_single_deselect: true, search_contains: true };		// allow_single_deselect happens if first item is empty
				if ($("html").attr("dir") === "rtl") {
					$(this).addClass("chosen-rtl");
				}
				$.map({		// translate the strings
					placeholder_text_multiple: "Select Some Options",
					placeholder_text_single: "Select an Option",
					no_results_text: "No results match"
				}, function (v, k) {
					opts[k] = tr(v);
				});
				$.extend(opts, options);
		 		return this.each(function() {
					var opts2 = $.extend({}, opts);
					if ($(this).is(":hidden") && !opts.width) {
						// from https://github.com/harvesthq/chosen/pull/1580
						var $hiddenElement = $(this).clone().appendTo("body");
						if ($(this).is('.form-control')) {
							opts2.width = '100%';
						} else {
							opts2.width = $hiddenElement.outerWidth();
						}
						$hiddenElement.remove();
					}
					$(this).chosen(opts2);
				});
			}
			break;
	}	// end switch(func)
};


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
			var title = $('h4:first', comment).clone();
			var body = $('.comment-body:first', comment).clone();
			body.find('dt:contains("note")').closest('dl').remove().addClass('panel');

			if( text.length > 0 ) {
				var parents = container.find(':contains("' + text + '")').parent();
				var node = container.find(':contains("' + text + '")').not(parents)
					.addClass('note-editor-text alert-info')
					.each( function() {
						var child = $('dl.note-list',this);
						if( ! child.length ) {
							child = $('<dl class="note-list list-group-item-info"/>')
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
							// update the comments list
							editlink.closest('.comment-container').reload();
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
					var $placeholder = $('span.ui-icon:first', this);
					if ($('ul:first', this).length) {
						var dir = $('ul:first', this).css('display') === 'block' ? 's' : 'e';
						if ($placeholder.length) {
							$placeholder.replaceWith('<span class="flipper ui-icon ui-icon-triangle-1-' + dir + '" style="float: left;margin-top:.2em;"/>');
						} else {
							$(this).prepend('<span class="flipper ui-icon ui-icon-triangle-1-' + dir + '" style="float: left;margin-top:.2em;"/>');
						}
					} else {
						if ($placeholder.length) {
							$placeholder.replaceWith('<span style="float:left;width:16px;height:16px;margin-top:.2em;"/>');
						} else {
							$(this).prepend('<span style="float:left;width:16px;height:16px;margin-top:.2em;"/>');
						}
					}
					if ($('div.checkbox', this).length) {
						$('div.checkbox', this).css("margin-left", "16px");
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

	$.fn.fancy_filter = function (operation, options) {
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
			var height = $this.height() ? $this.height() : Math.ceil( width * 9 / 16 );
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
				if( this.selectionStart) {
					this.selectionStart = start;
					this.selectionEnd = end;
				} else if( this.setSelectionRange ){
					this.setSelectionRange(start, end);
				} else if( this.createTextRange ){
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
				}
			});
		}
		var field = this[0];
		if( field.selectionStart !== undefined) {
			return {
				start: field.selectionStart,
				end: field.selectionEnd
			}
		} else if ( field.createTextRange ) {
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
			var textProperty = range.htmlText ? "htmlText" : "text";	// behaviour changed in IE10 (approx) so htmlText has unix line-ends which works (not 100% sure why)
			var selectionStart = stored_range[textProperty].length - range[textProperty].length;
			var selectionEnd = selectionStart + range[textProperty].length;
			return {
				start: selectionStart,
				end: selectionEnd
			}
		
		}	};

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
			if (location.search.indexOf("comzone=show") > -1) {
				var comButton = this;
				setTimeout(function() {
					$(comButton).click();
				}, 500);
			}
		});

		return this;
	};

	$.fn.comment_load = function (url) {
		$('#top .note-list').remove();

		this.each(function () {
			var comment_container = this;
			if (! comment_container.reload) {
				comment_container.reload = function () {
					$(comment_container).empty().comment_load(url);
				};
			}
			$(this).addClass('comment-container');
			$(this).load(url, function (response, status) {
				$(this).show();
				$('.comment.inline dt:contains("note")', this)
					.closest('.comment')
					.addnotes( $('#top') );

                if(jqueryTiki.useInlineComment) {
				    $('#top').noteeditor($('.comment-form:last a', comment_container), '#note-editor-comment');
                }

				$('.button.comment-form.autoshow a').addClass('autoshown').click().removeClass('autoshown'); // allow autoshowing of comment forms through autoshow css class 

				var match = location.hash.match(/threadId(\d+)/);
				if (match) {
					var $comment = $(".comment[data-comment-thread-id=" + match[1] + "]");
					var top = $comment.offset().top + $comment.height() - ($window.height() / 2);
					$('html, body').animate({
						scrollTop: top
					}, 2000, function () {
						$comment.animate({
							backgroundColor: "#ff8"
						}, 250, function () {
							$comment.animate({
								backgroundColor: ""
							}, 1000);
						});
					});

				}
			});
		});

		return this;
	};

	$(document).on('click', '.comment .confirm-prompt', function (e) {
		var link = this;
		e.preventDefault();
		$(link).doConfirm({
			success: function (data) { 
				if (data.status === 'DONE') {
					$(link).closest('.comment-container').reload();
					$('span.count_comments').each(function () {
						// This is wrong, completely ignores there can be multiple in a page.
						var comment_count = parseInt($(this).text(), 10) - 1;
						$(this).text(comment_count);
					});
				}
			}
		});
	});

	$(document).on('click', '.comment-form.buttons a', function () {
		var comment_container = $(this).closest('.comment-container, .ui-dialog-content')[0];

		$('.comment-form form', comment_container).each(function() {		// remove other forms
			var $p = $(this).parent();
			$p.empty().addClass('button').append($p.data('previous'));
		});
		if (!$(this).hasClass('autoshown')) {
			$(".comment").each(function () {
				$("article > *:not(ol)", this).each(function () {
					$(this).css("opacity", 0.6);
				});
			});
		}
		$(this).parents('.comment:first').find("*").css("opacity", 1);

		var $formContainer = null;
		if ($(this).data('target')) {
			$formContainer = $($(this).data('target'));
		} else {
			$formContainer = $(this).parents('.buttons');
		}
		$(this).parents('.buttons').data('previous', $(this)).empty().removeClass('buttons').removeClass('button');

		// Update buttons if loaded as a modal
		$('.modal.fade.in').trigger('tiki.modal.redraw');

		$formContainer.load($(this).attr('href'), function () {
			var form = $('form', this).submit(function () {
				var errors, current = this;
				$(current).tikiModal(tr("Saving..."));
				$.post($(current).attr('action'), $(current).serialize(), function (data, st) {
					$(current).tikiModal();
					if (data.threadId) {
						$(current).closest('.comment-container').reload();
						$('span.count_comments').each(function () {

						function getUrlVar(key){
							var result = new RegExp(key + "=([^&]*)", "i").exec($(current).attr('action')); 
							return result && unescape(result[1]) || ""; 
						}

						switch (getUrlVar('action')){
							case "post":
							var hasCount = 1;
							var operator = "+";
							break;
							case "remove":
							var hasCount = 1;
							var operator = "-";								
							break;
							case "edit":
							var hasCount = 0;
							var operator = "+";		
							break;	
							case "moderate":
							var hasCount = 0;
							var operator = "+";								
							break;					
						}
						var comment_count = eval(parseInt($(this).text(), 10)+operator+hasCount);
						$(this).text(comment_count);
						});
						if (data.feedback && data.feedback[0]) {
							alert(data.feedback.join("\n"));
						}
					} else {
						errors = $('ol.errors', form).empty();
						if (! errors.length) {
							$(':submit', current).after(errors = $('<ol class="alert-warning"/>'));
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
				window.codeMirrorEditor = [];
				form.find('textarea.wikiedit').flexibleSyntaxHighlighter();
			}
		});
		return false;
	});

	$.fn.input_csv = function (operation, separator, value) {
		this.each(function () {
			var values = $(this).val().split(separator);
			if (values[0] === '') {
				values.shift();
			}

			if (operation === 'add' && -1 === values.indexOf("" + value)) {
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
			append = '?' + $.buildParams(query);
		}

		if (action) {
			return 'tiki-' + controller + '-' + action + append;
		} else {
			return 'tiki-' + controller + '-x' + append;
		}
	};

	$.serviceUrl = function (options) {
		var o = $.extend({}, options), controller = options.controller, action = options.action;
		delete(o.controller);
		delete(o.action);
		return $.service(controller, action, o);
	};

	$.buildParams = function (query, prefix, suffix) {
		prefix = prefix || '';
		suffix = suffix || '';

		return $.map(query, function (v, k) {
			if ($.isPlainObject(v)) {
				return $.buildParams(v, k + '[', ']');
			} else {
				return prefix + k + suffix + '=' + tiki_encodeURIComponent(v);
			}
		}).join('&');
	};

	$.fn.serviceDialog = function (options) {
		this.each(function () {
			var $dialog = $('<div/>'), origin = this, buttons = {};
			$(this).append($dialog).data('serviceDialog', $dialog);

			if (! options.hideButtons) {
				buttons[tr('OK')] = function () {
					$dialog.find('form:visible').submit();
				};
				buttons[tr('Cancel')] = function () {
					$dialog.dialog('close');
					if ($dialog.data('ui-dialog')) {
						$dialog.dialog('destroy');
					}
				};
			}

			$dialog.dialog({
				title: options.title,
				minWidth: options.width ? options.width : 500,
				height: (options.fullscreen ? $window.height() - 20 : (options.height ? options.height : 600)),
				width: (options.fullscreen ? $window.width() - 20 : null),
				close: function () {
					if (options.close) {
						options.close.apply([], this);
					}
					if ($(this).data('ui-dialog')) {
						$(this).dialog('destroy').remove();
					}
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

		this.each(function () {
			if (! this.reload) {
				this.reload = function () {
					$(this).loadService(data, options);
				};
			}
		});

		if (typeof data === "string") {
			data = parseQuery(data);
		}
		if (data && data.controller) {
			controller = data.controller;
		}

		if (data && data.action) {
			action = data.action;
		}

		if (options.origin && $(options.origin).is('a')) {
			url = $(options.origin).attr('href');
		} else if (options.url) {
			url = options.url;
		} else {
			url = $.service(controller, action);
		}

		$dialog.tikiModal(tr("Loading..."));

		$.ajax(url, {
			data: data,
			error: function (jqxhr) {
				$dialog.html(jqxhr.responseText);
			},
			success: function (data) {
				$dialog.html(data);
				$dialog.find('.ajax').click(function (e) {
					$dialog.loadService(null, {origin: this});
					return false;
				});
				$dialog.find('.service-dialog').click(function (e) {
					if ($dialog.data('ui-dialog')) {
						$dialog.dialog('close');
					}
					return true;
				});

				$dialog.find('form .submit').hide();

				$dialog.find('form:not(.no-ajax)').unbind("submit").submit(ajaxSubmitEventHandler(function (data) {
					data = (data ? data : {});
					
					if (data.FORWARD) {
						$dialog.loadService(data.FORWARD, options);
					} else if ($dialog.data('ui-dialog')) {
						$dialog.dialog('destroy').remove();
					}

					if (options.success) {
						options.success.apply(options.origin, [data]);
					}
				}));

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
			},
			complete: function () {
				$dialog.tikiModal();
				if ($dialog.find('form').size() == 0 && $dialog.data('ui-dialog')) {
					// If the result contains no form, skip OK/Cancel, and just allow to close
					var buttons = $dialog.dialog('option', 'buttons'), n = {};
					if (buttons[tr('Cancel')]) {
						n[tr('OK')] = buttons[tr('Cancel')];
						$dialog.dialog('option', 'buttons', n);
					}
				}
			}
		});
	};

	$.fn.requireConfirm = function (options) {
		this.click(function (e) {
			e.preventDefault();
			$(this).doConfirm(options);
			return false;
		});

		return this;
	};

	$.fn.doConfirm = function (options) {
		var message = options.message, link = this;

		if (! message) {
			message = $(this).data('confirm');
		}

		if (confirm (message)) {
			var $this = $(this);
			$this.tikiModal(" ");

			$.ajax($(this).attr('href'), {
				type: 'POST',
				dataType: 'json',
				data: {
					'confirm': 1
				},
				success: function (data) {
					$this.tikiModal();
					options.success.apply(link, [data]);
				},
				error: function (jqxhr) {
					$this.tikiModal();
					$(link).closest('form').showError(jqxhr);
				}
			});
		}
	};

	$.fn.showError = function (message) {
		if (message.responseText) {
			if (message.getResponseHeader("Content-Type").indexOf("text/html") === -1) {
				var data = $.parseJSON(message.responseText);
				message = data.message;
			} else {
				message = $(message.responseText).text();	// can be html
			}
		} else if (typeof message !== 'string') {
			message = "";
		}
		this.each(function () {
			var parts, that = this;
			if (parts = message.match(/^<!--field\[([^\]]+)\]-->(.*)$/)) {
				field = parts[1];
				message = parts[2];

				if (that[field]) {
					that = that[field];
				}
			}

			var validate = false, errors = {}, field, $additional = $('<ul>');

			if (jqueryTiki.validate) {
				validate = $(that).closest('form').validate()
			}

			if (validate) {
				if (! $(that).attr('name')) {
					$(that).attr('name', $(that).attr('id'));
				}

				if (that !== validate.currentForm) {
					field = $(that).attr('name');
				}

				if (field) {
					errors[field] = message;
					validate.showErrors(errors);
				} else {
					// No specific field, assign as form error
					$additional.append($('<li>').text(message));
				}

				setTimeout(function () {
					$('#error_report li').filter(function () {
						return $(this).text() === message;
					}).remove();

					if ($('#error_report ul').is(':empty')) {
						$('#error_report').empty();
					}
				}, 100);
			} else {
				$additional.append($('<li>').text(message));
			}

			if (! $additional.is(':empty')) {
				// Write form errors at the top, please stop removing them
				$('.ajax-errors', this).remove();
				$('<div class="ajax-errors alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>')
					.prependTo(this)
					.append($additional);
			}

			// Style the bootstrap form-group as an error
			$('.form-group').removeClass('has-error')
				.find('label.error:visible')
				.addClass('help-block')
				.prepend('<span class="fa fa-flag"/> ')
				.closest('.form-group').addClass('has-error')
		});

		return this;
	};

	$.fn.clearError = function () {
		this.each(function () {
			$(this).closest('form').find('label.error[for="' + $(this).attr('name') + '"]').remove();
			$(this).closest('form').find('.form-group.has-error').removeClass('has-error');
		});

		return this;
	};

	function loadSelectorData(filter, args, success) {
		if (! $.object_selector_cache) {
			$.object_selector_cache = {};
		}

		if ($.isFunction(args)) {
			success = args;
			args = {};
		}

		var item, url;
		
		url = $.service('search', 'lookup', $.extend(args, {
			filter: filter
		}));

		if (item = $.object_selector_cache[url]) {
			if (item.data) {
				success(item.data);
			} else {
				item.queue.push(success);
			}
		} else {
			item = $.object_selector_cache[url] = {
				data: null,
				queue: [success]
			};
			$.getJSON(url, function (data) {
				item.data = data;
				$.each(item.queue, function (k, f) {
					f(data);
				});
				item.queue = [];
			});
		}
	}
	$._object_selector_add_item = function (type, $select, $results, item, title, selected) {
		var checkname = $select.closest('.object-selector, .object-selector-multi')
			.find('.primary').attr('id') + '_sel';
		$('<option>')
			.val(item)
			.data('label', title)
			.text(title)
			.prop('selected', selected)
			.appendTo($select);

		$('<div class="' + type + '"><label><input type="' + type + '"> </label></div>')
			.find('label').append(title).end()
			.find(':radio, :checkbox')
				.attr('name', checkname)
				.prop('checked', selected)
				.val(item)
			.end()
			.appendTo($results);
	};

	$.fn._object_selector_update_results = function (type, result, initial) {
		var $container = this,
			$results = $container.find('.results'),
			$select = $container.find('select'),
			$noresults = $('.no-result', this),
			selection = [];

		this.find(':radio:checked, :checkbox:checked')
			.not('.protected')
			.each(function () {
				selection.push($(this).val());
			});

		this.find(':radio:not(:checked), :checkbox:not(:checked)')
			.not('.protected')
			.closest('.radio, .checkbox')
			.remove();

		$select
			.find('option:not(:checked)')
			.not('.protected')
			.remove();

		$noresults.toggleClass('hidden', selection.length !== 0);
		$.each(result, function (key, value) {
			var current = value.object_type + ':' + value.object_id;

			if (-1 === selection.indexOf(current)) {
				if (initial) {
					$._object_selector_add_item(type, $select, $([]), current, value.title, false);
				} else {
					$._object_selector_add_item(type, $select, $results, current, value.title, false);
				}
			} else {
				$("option[value='" + current + "']", $select).text(value.title);
			}
		});

		$select.trigger('chosen:updated');
	};

	$.fn.object_selector = function (action, value, title) {
		var args = arguments;

		this.each(function () {
			var input = this
				, $simple = $(this).prev()
				, filter = $(input).data('filters')
				, threshold = $(input).data('threshold')
				, format = $(input).data('format') || ''
				, parentobject = $(input).data('parent')
				, parentkey = $(input).data('parentkey');

			$(input).addClass('primary').hide();
			$simple.hide();

			var $spinner = $(this).parent(),
				$container = $(input).closest('.object-selector'),
				$select = $container.find('select').first(),
				$filter = $container.find(':text.filter').first(),
				$search = $container.find('button.search').first(),
				$panel = $container.find('.panel').first();

			if (action === 'set') {
				$select.val(value);
				if ($select.val() !== value && title) {
					$._object_selector_add_item('radio', $select, $container.find('.results'), value, title, true);

					$select.trigger('chosen:updated');
				}

				$(input)
					.val(value)
					.data('label', title)
					.change();

				return;
			}

			if (action === 'setfilter') {
				filter[args[1]] = args[2];
				$(input).data('filters', filter);
				$container.find('.too-many').hide();
				$search.click();
				return;
			}

			if (parentobject && parentkey) {
				filter[parentkey] = $(parentobject).val();
				$(parentobject).on('change', function () {
					$(input).object_selector('setfilter', parentkey, $(this).val());
				});
			}

			if (threshold !== -1) {
				$spinner.tikiModal(" ");
				loadSelectorData(filter, {maxRecords: threshold, format: format}, function (data) {
					$container._object_selector_update_results('radio', data.resultset.result, true);

					$spinner.tikiModal();

					if (data.resultset.count <= threshold) {
						$select.parent().removeClass('hidden');
					} else {
						$panel.removeClass('hidden');
					}
				});
			} else {
				$panel.removeClass('hidden');
			}
			$panel.on('click', ':radio', function () {
				if ($(this).is(':checked')) {
					$(input).object_selector('set', $(this).val(), $(this).parent().text());
				}
			});

			$(input).change(function () {
				var val = $(this).val(), id = null;
				if (val) {
					id = val.split(':')[1];
				}

				if ($simple.val() != id) {
					$simple.val(id).change();
				}
			});
			$simple.change(function () {
				var target = filter.type + ':' + $(this).val();

				if (filter.type && $(input).val() != target) {
					$(input).val(target).change();
				}
			});
			$select.change(function () {
				if ($(input).val() != $select.val()) {
					$(input).data('label', $select.find('option:selected').text());
					$(input).val($select.val()).change();
				}
			});

			$search.click(function (e) {
				e.preventDefault();

				$spinner = $filter.parent().tikiModal(" ");
				loadSelectorData($.extend(filter, {title: $filter.val()}), {format: format}, function (data) {
					$container._object_selector_update_results('radio', data.resultset.result, false);

					$spinner.tikiModal();
				});
			});

			$filter.keypress(function (e) {
				if (e.which === 13) {
					e.preventDefault();
					$search.click();
				}
			});
		});

		return this;
	};

	$.fn.object_selector_multi = function (action) {
		var args = arguments;
		this.each(function () {
			var $textarea = $(this).hide().addClass('primary')
				, $container = $(this).closest('.object-selector-multi')
				, $select = $container.find('select')
				, $simpleinput = $textarea.prev(':text').hide()
				, $basic = $container.find('.basic-selector')
				, $panel = $container.find('.panel')
				, $search = $container.find('button.search').first()
				, $filter = $container.find(':text.filter').first()
				, filter = $textarea.data('filters')
				, threshold = $textarea.data('threshold')
				, format = $textarea.data('format') || ''
				, parentobject = $textarea.data('parent')
				, parentkey = $textarea.data('parentkey')
				, initialValues = $select.val()
				, separator = $simpleinput.data('separator')
				;

			if (action === 'setfilter') {
				filter[args[1]] = args[2];
				$textarea.data('filters', filter);
				$container.find('.too-many').hide();
				$search.click();
				return;
			}

			if (parentobject && parentkey) {
				filter[parentkey] = $(parentobject).val();
				$(parentobject).on('change', function () {
					$textarea.object_selector_multi('setfilter', parentkey, $(this).val());
				});
			}

			if (threshold !== -1) {
				$container.tikiModal(' ');
				loadSelectorData(filter, {maxRecords: threshold, format: format}, function (data) {
					$container.tikiModal('');
					$container._object_selector_update_results('checkbox', data.resultset.result, true);

					if (data.resultset.count <= threshold) {
						$basic.removeClass('hidden');
					} else {
						$panel.removeClass('hidden');
					}
				});
			} else {
				$panel.removeClass('hidden');
			}
			
			$filter.keypress(function (e) {
				if (e.which === 13) {
					e.preventDefault();
					$search.click();
				}
			});

			$search.click(function (e) {
				e.preventDefault();

				var $spinner = $filter.parent().tikiModal(" ");
				loadSelectorData($.extend(filter, {title: $filter.val()}), {format: format}, function (data) {
					$container._object_selector_update_results('checkbox', data.resultset.result, false);
					$spinner.tikiModal();
				});
			});

			$panel.on('click', ':checkbox', function () {
				var list = $.makeArray($container.find(':checkbox:checked').map(function () {
					return $(this).val();
				}));
				$textarea.val(list.join("\n")).change();
			});
			$select.on('change', function () {
				var list = $(this).val() || [];
				$textarea.val(list.join("\n")).change();
			});

			if (separator) {
				$textarea.on('change', function () {
					var lines = $(this).val().split("\n"), ids = [];
					$.each(lines, function (k, line) {
						var parts = line.split(':');
						if (parts.length === 2) {
							ids.push(parts[1]);
						}
					});
					$simpleinput.val(ids.join(separator));
				});
			}
		});
	};

	$.fn.sortList = function () {
		var list = $(this), items = list.children('li').get();

		items.sort(function(a, b) {
			var compA = $(a).text().toUpperCase();
			var compB = $(b).text().toUpperCase();
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		});

		$.each(items, function(idx, itm) {
			list.append(itm);
		});
	};
	$.localStorage = {
		store: function (key, value) {
			var fullKey = this._build(key);
			if (window.localStorage) {
				if (value) {
					window.localStorage[fullKey] = $.toJSON({
						date: Date.now(),
						data: value
					});
				} else {
					delete window.localStorage[fullKey];
				}
			}
		},
		load: function (key, callback, fetch, duration) {
			var payload, fullKey = this._build(key);

			if (window.localStorage && window.localStorage[fullKey]) {
				payload = $.parseJSON(window.localStorage[fullKey]);

				if (duration) {
					// Expired, refetch
					if (payload.date + duration*1000 < Date.now()) {
						fetch(function (data) {
							$.localStorage.store(key, data);
							callback(data);
						});
						return;
					}
				}

				callback(payload.data);
			} else {
				fetch(function (data) {
					$.localStorage.store(key, data);
					callback(data);
				});
			}
		},
		_build: function (key) {
			// Use an alternate key to ensure old data structure
			// does not collide
			return key + "_2";
		}
	};

	var favoriteList = [];
	$.fn.favoriteToggle = function () {
		this
			.each(function () {
				var type, obj, isFavorite, link = this;
				type = $(this).queryParam('type');
				obj = $(this).queryParam('object');
				

				isFavorite = function () {
					var ret = false;
					$.each(favoriteList, function (k, v) {
						if (v === type + ':' + obj) {
							ret = true;
							return false;
						}
					});

					return ret;
				};

				$(this).find('span').remove(); //removes the previous star icon
				$(this).prepend($('<span />').attr({
					'class' : isFavorite() ? 'fa fa-star fa-fw' : 'fa fa-star-o fa-fw',
					'title' : isFavorite() ? tr('Remove from favorites') : tr('Add to favorites')
				}));

				if (isFavorite()) {
					$(this).addClass( 'favorite_selected' );
					$(this).removeClass( 'favorite_unselected' ); 
				} else {
					$(this).addClass( 'favorite_unselected' );
					$(this).removeClass( 'favorite_selected' );	
				}
				$(this)
					.filter(':not(".register")')
					.addClass('register')
					.click(function () {
						$.post($(this).attr('href'), {
							target: isFavorite() ? 0 : 1
						}, function (data) {
							favoriteList = data.list;
							$.localStorage.store($(link).data('key'), favoriteList);

							$(link).favoriteToggle();
						}, 'json');
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
				list.data('key'),
				function (data) {
					favoriteList = data;
					list
						.favoriteToggle()
						.removeClass('favorite-toggle');
				}, 
				function (recv) {
					$.getJSON($.service('favorite', 'list'), recv);
				},
				3600 // Valid for 1h
			);
		}
	});

	// global ajax event handlers
	$document.ajaxComplete(function () {
		$('.favorite-toggle')
			.favoriteToggle()
			.removeClass('favorite-toggle');
	});

	$document.ajaxError(function (event, jqxhr) {
		$(".modal.fade.in").modal("hide");
		$("#error_report").showError(jqxhr.status + " " + jqxhr.statusText);
	});

	/**
	 * Show a loading spinner on top of a button (or whatever)
	 *
	 * @param $spinner empty or jq object $spinner		if empty, spinner is added and returned and element "disabled"
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

})(jQuery);

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

$.tikiModal = function(msg) {
	return $('body').tikiModal(msg);
};

//Makes modal over window or object so ajax can load and user can't prevent action
$.fn.tikiModal = function(msg) {
	var obj = $(this);
	if (!obj.length) {
		return null;			// happens after search index rebuild in some conditions
	}
	var lastModal = obj.data('lastModal');
	
	if (!lastModal) {
		lastModal = Math.floor(Math.random() * 1000);
		obj.data('lastModal', lastModal);
	}
	var box = {
		top: obj.offset().top,
		left: obj.offset().left,
		// height: obj.height() > $window.height() ? $window.height() : obj.height(),
		// width: obj.width() > $window.width() ? $window.width() : obj.width()
		height: obj.height(),
		width: obj.width()
	};
	var modal = $('body').find('#modal_' + lastModal);
	var spinner = $('<img src="img/spinner.gif" style="vertical-align: top; margin-right: .5em;" />');
	
	if (!msg) {
		modal
			.fadeOut(function() {
				$(this).remove();
			});
		obj.removeData('lastModal');
		return obj;
	}
	
	if (modal.length) {
		modal
			.find('.dialog')
			.empty()
			.html(spinner)
			.append(msg);
		return obj;
	}
	
	modal = $('<div id="modal_' + lastModal + '" class="tiki-modal">' +
		    	'<div class="mask" />' +
		    	'<div class="dialog"></div>' +
			'</div>')
		.appendTo('body');

	var zIndex = 0;
	if (obj.is("body")) {
		zIndex = 2147483646 - 1;	// maximum
		box.top = obj.offset().top + $window.scrollTop();
		box.left = obj.offset().left + $window.scrollLeft();
	} else {
		obj.parents().addBack().each(function () {
			var z = $(this).css("z-index");
			if (z && z !== 'auto' && z > zIndex) {
				zIndex = Number(z);
			}
		});
	}

	//Set height and width to mask to fill up the whole screen or the single element
	modal
		.width(box.width)
		.height(box.height)
		.css('top', 	box.top + 'px')
		.css('left', 	box.left + 'px')
		.find('.mask')
			.height(box.height)
			.fadeTo(1000, 0.6)
		.parent()
		.find('.dialog')
			.append(spinner)
			.append(msg);
	var dialog = modal.find('.dialog');
	dialog.css({
		marginTop: (dialog.height() / -2) + "px",
		marginLeft: (dialog.width() / -2) + "px"
	});

	if (zIndex) {
		modal.css("z-index", zIndex + 1);
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
		var offset_arg = step.data('offset_arg');
		
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
						document.location = url + offset_arg + "=" + newOffset;
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
	}
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

	$.tikiModal(tr('Loading editor'));

	me.serviceDialog({
		title: me.attr('title'),
		data: {
			controller: 'draw',
			action: 'edit',
			fileId: me.data('fileid'),
			galleryId: me.data('galleryid'),
			imgParams: me.data('imgparams'),
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
					imgParams: me.data('imgparams'),
					data: $('#fileData').val()
				})
				.bind('savedDraw', function(e, o) {
					me.data('drawLoaded', false);
					me.drawing.parent().dialog('destroy');
					me.drawing.remove();

					//update the image that did exist in the page with the new one that now exists
					var img = $('.pluginImg' + me.data('fileid')).show();

					if (img.length < 1) document.location = document.location + '';

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

					var src = me.data('src');

					$('<div class=\"svgImage\" />')
						.load(src ? src : 'tiki-download_file.php?fileId=' + o.fileId + '&display', function() {

							$(this)
								.css('position', 'absolute')
								.fadeTo(0, 0.01)
								.prependTo('body')
								.find('img,svg')
								.scaleImg({
									width: w,
									height: h
								});

							img.html($(this).children());

							$(this).remove();
						});

					if (!options.saved) return;

					options.saved(o.fileId);

					me.data('fileid', o.fileId);			// replace fileId on edit button
					if (o.imgParams && o.imgParams.fileId) {
						o.imgParams.fileId = o.fileId;
						me.data('imgparams', o.imgParams);
					}
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

					$.tikiModal();
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

function delayedExecutor(delay, callback)
{
	var timeout;

	return function () {
		var args = arguments;
		if (timeout) {
			clearTimeout(timeout);
			timeout = null;
		}

		timeout = setTimeout(function () {
			callback.apply(this, args)
		}, delay);
	};
}

/**
*   Close (user) sidebar column(s) if no modules are displayed.
*   Modules can be hidden at runtime. So, check after the page/DOM model is loaded.
*/
$(function () {

    // Do final client side adjustment of the sidebars
    /////////////////////////////////////////
    var maincol = 'col1';

    // Leave the sidebars unchanged in the admin modules panel
    if (document.URL.indexOf('tiki-admin_modules.php') >= 0) {
        return;
    }

    // Hide left side panel, if no modules are displayed
    var left_mods = document.getElementById('left_modules');
    if (left_mods != null) {
        if (isEmptyText(left_mods.innerHTML)) {
            var col = document.getElementById('col2');
            if (col != null) {
                col.style.display = "none"
            }
            document.getElementById(maincol).style.marginLeft = '0';

            var toggle = document.getElementById("showhide_left_column");
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
            col = document.getElementById('col3');
            if (col != null) {
                col.style.display = "none"
            }
            document.getElementById(maincol).style.marginRight = '0';

            toggle = document.getElementById("showhide_right_column");
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
	var h = $el.height(),
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
		var h, w, img = $(this),
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
					h = max.height;
					w = Math.ceil(actual.width / actual.height * max.height);
				} else {
					w = max.width;
					h = Math.ceil(actual.height / actual.width * max.width);
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


// Compatibility to old jquery to resolve a bug in fullcalendar
$.curCSS = function (element, property) {
	return $(element).css(property);
};


$.fn.registerFacet = function () {
	this.each(function () {
		var element = this, entries = $($(this).data('for')).val()
			.split(" " + $(this).data('join') + " ")
			.map(function (value) {
				return (value.charAt(0) === '"') ? value.substr(1, value.length - 2) : value;
			});

		function applyFilter(value) {
			if (value) {
				value = $.makeArray(value);
				value = value
					.map(function (value) {
						return (-1 === value.indexOf(' ')) ? value : ('"' + value + '"');
					})
					.join(" " + $(element).data('join') + " ");
			}
			$($(element).data('for')).val(value).change();
		}

		if ($(element).is('select')) {
			$(this)
				.val(entries)
				.trigger("chosen:updated") // for chosen
				.change(function () {
					var value = $(this).val();
					applyFilter(value);
				});
		} else if ($(element).has(':checkbox').length) {
			$(':checkbox', element)
				.each(function () {
					if (-1 !== $.inArray($(this).val(), entries)) {
						$(this).prop('checked', true);
					}
				})
				.on('click', function () {
					applyFilter($(':checked', element).map(function () {
						return $(this).val();
					}));
				});
		}

		var selected = $('option:selected, :checkbox:checked', this).length,
			all = $('option, :checkbox', this).length;

		if (all === 1 && selected === 0) {
			$(this).closest('.facet-hide-group').hide();
		}
	});

	return this;
};

$.fn.reload = function () {
	this.each(function () {
		if (this.reload) {
			this.reload();
		} else if($(this).data('reload')) {
			$(this).loadService({}, {
				url: $(this).data('reload')
			});
		}
	});
	return this;
};

$(document).on('mouseover', '.media[data-href]', function () {
	$(this).css('cursor', 'pointer');
});
$(document).on('mouseout', '.media[data-href]', function () {
	$(this).css('cursor', 'default');
});
$(document).on('click', '.media[data-href]', function () {
	document.location.href = $(this).data('href');
});

$(document).on('hidden.bs.modal', '.footer-modal.fade', function () {
	// Required for bootstrap to allow changing the content of a modal
	$(this).removeData('bs.modal').find('.modal-content').empty();

	// With multiple stacked modals, which is not officially supported by bootstrap,
	// focus on the last modal would be lost.
	$('body').toggleClass('modal-open', $('.modal.fade.in').length > 0);
});

$(document).on('submit', '.modal-body form:not(.no-ajax)', ajaxSubmitEventHandler(function (data) {
	//if FORWARD is set in the returned data, load the passed service into the modal
	// rather than close the modal and refresh the page.
	if (data.FORWARD) {
		$(this).children().remove();
		$(this).loadService(data.FORWARD, {origin: this});
		$(this).closest('.modal').trigger('tiki.modal.redraw');
	} else {
		// reload() causes a request to update the browser cache - similar to pressing the reload button.
		// so we must not reload() but set the href. This behaves simililar to clicking a link - which keeps the browser cache.
		// The difference is: NOT loading about 50+ js / css files!
		//document.location.reload();
		document.location.href = document.location.href.replace(/#.*$/, "");	// remove the hash from the URL if there is one otherwise the page doesn't reload
	}
}));

// When data-size is set on the toggle-link, alter the size of the modal
$(document).on('click', '[data-toggle=modal][data-size]', function () {
	var target = $(this).data('target'), size = $(this).data('size');
	
	$(target)
		.one('hidden.bs.modal', function () {
			$('.modal-dialog', this).removeClass(size);
		})
		.find('.modal-dialog').addClass(size)
		;
});

$(document).on('click', '[data-toggle=modal][data-modal-title]', function () {
	var target = $(this).data('target'), title = $(this).data('modal-title');
	
	$(target)
		.one('loaded.bs.modal', function () {
			$('.modal-title', this).text(title);
		})
		;
});

$(document).on('loaded.bs.modal', '.modal.fade', function () {
	$(this).trigger('tiki.modal.redraw');
});

$(document).on('shown.bs.modal', '.modal', function () {
	$('.modal-body :input', this).first().focus();
	$('.modal-backdrop', this).slice(1).remove(); // Bootstrap keeps adding more of these

	if ($.applyChosen) {
		$(this).applyChosen();
	}
});

$(document).on('tiki.modal.redraw', '.modal.fade', function () {
	var modal = this, $button;

	// On Modal show, find all buttons part of a .submit block and create
	// proxies of them in the modal footer
	$('.modal-footer .auto-btn', modal).remove();
	$('div.submit .btn', modal).each(function () {
		var $submit = $(this);
		if ($submit.is('a:not(.custom-handling)')) {
			$button = $submit;
		} else {
			$submit.hide();
			$button = $('<button>')
				.text($submit.val() || $submit.text())
				.attr('class', $submit.attr('class'))
				.addClass('auto-btn')
				.click(function () {
					$submit.click();
				});
		}
		$('.modal-footer', modal).append($button);
	});

	if ($.fn.flexibleSyntaxHighlighter) {
		$('textarea', modal).flexibleSyntaxHighlighter();
	}

	$(".nav-tabs", this).each(function () {
		if ($(".active", this).length === 0) {
			$("li:first-child a", this).tab("show");
		}
	});

	if ($.applyChosen) {
		$(this).applyChosen();
	}
});

$(function () {
	var $tabs = $('a[data-toggle=tab][href="' + document.location.hash + '"]'),
		tabShown = false,
		notShown = [];

	if (document.location.search.match(/cookietab=/)) {
		tabShown = true;
	} else if (document.location.hash && $tabs.length) {
		$tabs.tab('show');
		tabShown = true;
	} else {
		$(".tabs").each(function () {
			var name = $(this).data("name"),
				t = getCookie(name, "tabs", "notfound");

			var $tab = $('.active > a[data-toggle=tab]', this);	// class "active" set serverside from $cookietab var
			if (t && $tab.length === 0) {
				$tab = $('a[data-toggle=tab][href="' + t + '"]');
			}

			if ($tab.length) {
				$tab.tab('show');
				tabShown = true;
			} else if (name) {
				notShown.push(name);
			}
		});
	}
	if (typeof $().tab === "function") {
		if (!tabShown && !notShown.length) {
			$("a[data-toggle=tab]:first").tab("show");
		} else if (notShown.length) {
			for (var i = 0; i < notShown.length; i++) {
				$(".tabs[data-name=" + notShown[i] + "] a[data-toggle=tab]:first").tab("show");
			}
		}
	}

	$('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
		if ($(this).parents(".tab-content").length === 0) {
			document.location.hash = $(e.target).attr("href");
		}
		setCookieBrowser($(this).parents(".tabs:first").data("name"), $(e.target).attr("href"), "tabs");
	}).click(function () {
		var scroll = $window.scrollTop();	// prevent window jumping to tabs on click
		$(this).tab('show');
		$window.scrollTop(scroll);
	});
});

$.openModal = function (options) {
	var href = options.remote, $target;

	if (-1 === href.indexOf('?')) {
		href += '?modal=1';
	} else {
		href += '&modal=1';
	}

	$target = $('.modal.fade:not(.in)').first()
		// Bind a single event to trigger as soon as the form appears
		.one('loaded.bs.modal', options.open || function () {})
		.one('hidden.bs.modal', options.close || function () {})
		// Make the form appear
		.modal({
			remote: href
		});
	
	if (options.title) {
		$target.one('loaded.bs.modal', function () {
			$('.modal-title', this).text(options.title);
		});
	}

	if (options.size) {
		$target.find('.modal-dialog').addClass(options.size);
		$target.one('hidden.bs.modal', function () {
			$('.modal-dialog', this).removeClass(options.size);
		});
	}
};

$.closeModal = function (options) {
	options = options || {};
	var done = options.done;
	if (done) {
		done = function () {
			// Wait until the event loop ends before considering really done
			setTimeout(options.done, 0);
		};
	}

	$('.modal.fade.in').last()
		.one('hidden.bs.modal', done || function () {})
		.modal('hide');
};

$.fn.clickModal = function (options) {
	this.click($.clickModal(options));

	return this;
};
$.clickModal = function (options, href) {
	return function (e) {
		var control = this, url;
		if (! href) {
			url = $(this).attr('href');
		} else {
			url = href;
		}
		if ($.isFunction(e.preventDefault)) {
			e.preventDefault();
		}

		$.openModal({
			title: options.title,
			size: options.size,
			remote: url,
			open: function () {
				if (options.open) {
					options.open.apply(this, []);
				}

				$('form:not(.no-ajax)', this)
					.addClass('no-ajax') // Remove default ajax handling, we replace it
					.submit(ajaxSubmitEventHandler(function (data) {
						if (options.success) {
							options.success.apply(control, [data]);
						}
					}));
			}
		});
	};
};

$.fn.clickInline = function (container, options) {
	this.click($.clickInline(container, options));

	return this;
};
$.clickInline = function (container, options, href) {
	return function (e) {
		var control = this, url;
		if (! href) {
			url = $(this).attr('href');
		} else {
			url = href;
		}
		if ($.isFunction(e.preventDefault)) {
			e.preventDefault();
		}

		$this = $(this);

		$this.hide();

		$.get(url, function(data){

			if(container instanceof jQuery){
				container.html("");
				container.append(data);

				if (options.open) {
					options.open.apply(this, []);
				}
			}


			// Bind "submit" listener to an element
			// that is not a FORM, because nested FORMS
			// are invalid. The submit event is still
			// triggered for code portability
			$this.parent().find('.inline.file-uploader')
				.submit(function (e, data) {
					e.preventDefault();

					if (options.success) {
						options.success.apply(control, [data]);
					}
					return false;
				});		
		});
	};
};

window.regCapsLock = function () {};

// Avoid that jquery appends a cachebuster to scripts loaded via a regular script tag when the base content was loaded from an xhr call
// I.e xhr call loads same html boilerplate and that boilerplate contains a script tag that loads some .js script.
// In this case, jquery would add a cachebuster to the js request, and no cache would be work.
$.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
	if ( options.dataType == 'script' || originalOptions.dataType == 'script' ) {
		options.cache = true;
	}
});


function objectLockToggle(icon) {

	var $this = $(icon).tikiModal(" "),
		action = $this.data("is_locked") ? "unlock" : "lock";

	$.post($.service(
		"object",
		action,
		{
			type: $this.data("type"),
			object: $this.data("object"),
			value: $this.data("is_locked") ? "" : jqueryTiki.username
		}
		), function (data) {
			if (data && data.locked) {
				$this.find(".icon").setIcon("lock");
				$this.data("is_locked", "1")
					.attr("title", tr("Locked by " + jqueryTiki.userRealName))
					.parent().find("input[name=locked]").val(jqueryTiki.username);
			} else {
				$this.find(".icon").setIcon("unlock");
				$this.data("is_locked", "")
					.attr("title", "")
					.parent().find("input[name=locked]").val("");
			}
		},
		"json").done(function () {
		$this.tikiModal();
	});

	return false;
};
