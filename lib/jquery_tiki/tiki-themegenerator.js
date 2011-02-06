/* $Id$
 *
 * Include JS file for Tiki 7 Theme Generator
 */

$(document).ready( function() {
	
	// float button fn
	$(".tgFloatDialog").click(function() {
		openThemeGenDialog($("select[name=themegenerator_theme]").val());
		return false;
	});
});
	
function initThemeGenDialog() {	// closure for colorpicker code
	// 
	// colorpicker for the colour swatches
	// expose options for event fns
	var colorSelector = ".colorSelector > div";
	var colorInput = "input.tgValue";

	var colorPickerListItem = null;		// still sort of globals really
	var tgLivePreviews = [];		// need to know what was clicked from "inside" colorpicker

	var opts = {
		color: '#888',
		onBeforeShow: function () {
			colorPickerListItem = $(this).parent();	 // store for later (maybe there's a better way?)

			var c = $.trim($(colorInput, colorPickerListItem).val());
			var m = c.match(/^#([0-9A-F]{3})$/i);	// check for only 3 hex chars
			if (m) {
				c = c[0]+c[1]+c[1]+c[2]+c[2]+c[3]+c[3];	// double 'em
			}
			$(this).ColorPickerSetColor(c);

			if ($(".tgLivePreview:checked", $(colorPickerListItem).parent().parent()).length) {
				tgLivePreviews = []
				m = $(colorInput, colorPickerListItem).attr("name");
				m = m.match(/\[(.*?)\]/);	// colour type
				if (m) {m = m[1];}
				if (m === "fgcolors") {
					m = ["color"];
				} else if (m === "bgcolors") {
					m = ["background-color"];
				} else if (m === "bordercolors") {
					m = ["border-top-color", "border-right-color", "border-bottom-color", "border-left-color", "border-color", "border"];
				}
				var zall = $(":visible");
				for (var t in m) {
					for (var i = 0; i < zall.length; i++) {
						var $el = $(zall[i]);
						if ($el.length && $el.css(m[t]) && $.Color($el.css(m[t])).toHEX() == c) {
							tgLivePreviews.push( [ m[t], $el[0] ] );
						}
					}
				}
			}
		},
		onShow: function (colpkr) {
			$(document.body).css("user-select", "none");
			$(document.body).css("-webkit-user-select", "none");
			$(document.body).css("-moz-user-select", "none");
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(document.body).css("user-select", "");
			$(document.body).css("-webkit-user-select", "");
			$(document.body).css("-moz-user-select", "");
			colorPickerListItem = null;
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			if (colorSelector) {
				$(colorSelector, colorPickerListItem).css('backgroundColor', '#' + hex);
			}
			if (colorInput) {
				$(colorInput, colorPickerListItem).val('#' + hex);
			}
			$(colorPickerListItem).addClass('changed');

			if ($(".tgLivePreview:checked", $(colorPickerListItem).parent().parent()).length) {
				$(tgLivePreviews).each(function () {
					$(this[1]).css( this[0], '#' + hex);
				});
			}
		},
		onSubmit: function (hsb, hex, rgb, el) {
			this.onChange(hsb, hex, rgb);
		}
	};	// end opts for colorpicker

	$(".colorSelector").each(function() {
		var colorItem = this;
		$(this).parent().ColorPicker(opts);			
	});

	// checkboxes to select items
	$(".tgItems :checkbox").click(function(e, flip) {
		if (flip) {
			if (!$(this).attr("checked")) { // flip trickery to get trigger to toggle the right classes
				$(this).parent().addClass("selected");
			} else {
				$(this).parent().removeClass("selected");
			}
		} else {
			if ($(this).attr("checked")) { // flip trickery to get trigger to toggle the right classes
				$(this).parent().addClass("selected");
			} else {
				$(this).parent().removeClass("selected");
			}
		}
	});

	// reset button fn
	$(".tgResetSection").click(function() {
		var container = $(this).parent().nextAll("ul");
		$(".tgItem :checkbox:checked", container).each(function() {
			$(this).parent().find("input.tgValue").val($(this).val());
			$(this).parent().find(".colorSelector > div").css("background-color", $(this).val());
			$(this).trigger("click", [true]);
			//$(this).parent().removeClass("selected");
		});
		return false;
	});

	// select modified colours
	$(".tgToggleChanged").click(function() {
		var container = $(this).parent().nextAll("ul");
		$(".tgItem", container).each(function() {
			if ($("input.tgValue", this).val() !== $("input[type=checkbox]", this).val()) {
				$(this).find("input[type=checkbox]").trigger("click", [true]);
			}
		});
	});

	// toggle selection
	$(".tgToggle").click(function() {
		var container = $(this).parent().nextAll("ul");
		$("input[type=checkbox]", container).trigger("click", [true]);
	});

	if (jqueryTiki.ui) {
		$(function() {
			var $div;
			$(".tgSize", "#themegenerator_container").each (function () {
			}).focus( function () {

				if ($div) { $div.fadeOut("fast").remove(); }
				var $input = $(this);
				var unit = getUnit($input);
				var val = getNumber($input);
				var pxVal = val;
				if (unit == "em") {
					pxVal = $(parseFloat(val)).toPx();
				} else if (unit == "%") {
					pxVal = $(parseFloat(val / 100.0)).toPx();
				}
				var options = {
//					range: "min",
					min: -2,
					max: val * 20,
					step: 1,
					value: val,
					slide: function () {
						setNumber($input, $(this).slider("value"));
//					},
//					change: function () {
//						setNumber($input, $(this).slider("value"));
						if ($(".tgLivePreview:checked", $(this).parents(".tgItems").parent()).length) {
							$(tgLivePreviews).each(function () {
								$(this[1]).css( this[0], $input.val());
							});
						}
					}
				}
				if ("em|ex|in|cm|mm".indexOf(unit) > -1 ) {
					options.min = 0;
					options.max = val * 5;
					options.step = 0.01;
				} else if ("%".indexOf(unit) > -1 ) {
					options.min = val * 0.8;
					options.max = val * 1.2;
				}

				if ($(".tgLivePreview:checked", $(this).parents(".tgItems").parent()).length) {
					tgLivePreviews = [];
					var m = $(this).attr("name");
					m = m.match(/\[(.*?)\]/);	// size type
					if (m) {m = m[1];}
					if (m === "fontsize") {
						m = ["font-size"];
					} else if (m === "borderwidths") {
						m = ["border-top-width", "border-right-width", "border-bottom-width", "border-left-width"];
					} else if (m === "lineheight") {
						m = ["line-height"];
					} else if (m === "borderradii") {
						m = ["border-radius", "-webkit-border-radius", "-moz-border-radius",
								"border-top-left-radius", "-webkit-border-top-left-radius", "-moz-border-radius-topleft",
								"border-top-right-radius", "-webkit-border-top-right-radius", "-moz-border-radius-topright",
								"border-bottom-left-radius", "-webkit-border-bottom-left-radius", "-moz-border-radius-bottomleft",
								"border-bottom-right-radius", "-webkit-border-bottom-right-radius", "-moz-border-radius-bottomright",
								];
					}
					var zall = $(":visible:not(#themegenerator_container *)");
					for (var t in m) {
						for (var i = 0; i < zall.length; i++) {
							var $el = $(zall[i]);
							if ($el.length && $el.css(m[t])) {
								var  s = $el.css(m[t]).match(/([\d\.\-+]+)(?:px|em|ex|%|in|cm|mm|pt|pc)?/);
								if (s && s[1] && Math.round(s[1]) + "px" == pxVal) {
									tgLivePreviews.push( [ m[t], $el[0] ] );
								}
							}
						}
					}
				}


				$div = $("<div style='position:absolute;width:" +  $input.parent().width() + "px; height:11px;right:0;" +
						"top:" + $input.height() * 2 + "px;display:none;' />");
				$div.slider(options);
				$input.parent().append($div);
				$div.show("fast");
			}).blur( function () {
				//if ($div) { $div.remove(); }
			});
			var getNumber = function ($el) {
				var m = $el.val().match(/([\d\.\-+]+)(?:px|em|ex|%|in|cm|mm|pt|pc)?/);
				if (m) {
					return m[1];
				} else {
					return 0;
				}
			}
			var setNumber = function ($el, val) {
				$el.val(val + getUnit($el));
			}
			var getUnit = function ($el) {
				var m = $el.val().match(/[\d\.\-+]+(px|em|ex|%|in|cm|mm|pt|pc)?/);
				if (m) {
					return m[1];
				} else {
					return "";
				}
			}
		});
	}

//	// disable for now
//	$("input[type=checkbox].tgLivePreview", "#tg_section_typography")
//		.attr("checked", "")
//		.attr("disabled", "disabled");

	$("label", "#themegenerator_container").each( function() {	// labels go 100% width in dialog
		$(this).css("width", ($(this).text().length * 0.6) + "em");
	});
	
	$(".themegenerator", "#themegenerator_container").tabs({
		select: function(event, ui) {
			setCookie("tab", ui.index, "themegen");
		},
		selected: getCookie("tab", "themegen", 0)
	});
	
	setUpClueTips();
	
	$("#tg_css_file", "#themegenerator_container").change(function() {
		openThemeGenDialog();
	});
};


function openThemeGenDialog( themename ) {
	if (themename) {
		setCookie("name", themename, "themegen");
	} else {
		themename = getCookie("name", "themegen");
	}
	var l = getCookie("left", "themegen");
	var t = getCookie("top", "themegen");
	var w = getCookie("width", "themegen", 660);
	var h = getCookie("height", "themegen", 500);
	var p = "center"

	if (l !== null && t !== null) {
		p = [ parseInt(l, 10), parseInt(t, 10) ];
	}

	if ($("#themegenerator_container").length === 0) {
		$("body").append($("<div id='themegenerator_container'><form action='tiki-admin.php?page=look&cookietab=6' method='post'></form></div>"));
	}

	$.post("tiki-admin.php?page=look", {tg_open_dialog: true, tg_css_file: $("#tg_css_file", "#themegenerator_container").val()}, function(data) {
			$('#themegenerator_container form').html(data);
			initThemeGenDialog();
			ajaxLoadingHide();
		},
	"html");

	$("#themegenerator_container").dialog({
		width: w,
		height: h,
		position: p,
		modal: false,
		title: tr("Theme Generator:") + " " + themename,
		buttons: {
			Cancel: function () {
				$(this).dialog('close');
				setCookie("themegen", "");
				deleteCookie("themegen");
			},
			'Save': function() {
				var bValid = true;

				if (bValid) {

					$("#themegenerator_container form").submit();
					$(this).dialog('close');
				}
			},
			Preview: function () {

				$("#themegenerator_container form").append($("<input type='hidden' name='tg_preview' value='dialog' />"));
				$("#themegenerator_container form").submit();
				//$(this).dialog('close');
			}
		},
		close: function () {
			setCookie("state", "closed", "themegen");
			$(this).find('input[type=text]').val('').removeClass('ui-state-error');
		},
		open: function( event, ui ) {
			setCookie("state", "open", "themegen");
		},
		dragStop: function(event, ui) {
			setCookie("left", parseInt(ui.position.left, 10), "themegen");
			setCookie("top", parseInt(ui.position.top, 10), "themegen");
		},
		resizeStop: function(event, ui) {
			setCookie("width", parseInt(ui.size.width, 10), "themegen");
			setCookie("height", parseInt(ui.size.height, 10), "themegen");
		}
	});

	ajaxLoadingShow('themegenerator_container');
}


	
