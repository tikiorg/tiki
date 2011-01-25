/* $Id$
 *
 * Include JS file for Tiki 7 Theme Generator
 */

$(document).ready( function() {
	// colorpicker for the colour swatches
	
	{	// closure for colorpicker code
		// expose options for event fns
		var colorSelector = ".colorSelector > div";
		var colorInput = "input.tgValue";

		var colorPickerListItem = null;		// still sort of globals really
		var colorPickerPreviews = null;		// need to know what was clicked from "inside" colorpicker

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
					colorPickerPreviews = [];
					var zall = $(":visible");
					for (var t in m) {
						for (var i = 0; i < zall.length; i++) {
							var $el = $(zall[i]);
							if ($el.length && $el.css(m[t]) && $.Color($el.css(m[t])).toHEX() == c) {
								colorPickerPreviews.push( [ m[t], $el[0] ] );
							}
						}
					}
				} else {
					colorPickerPreviews = null;
				}
			},
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
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
					$(colorPickerPreviews).each(function () {
						$(this[1]).css( this[0], '#' + hex);
					});
				}
			}
		};	// end opts for colorpicker

		$(".tgItem > div", "#tg_section_colors").each(function() {
			var colorItem = this;
			$(this).ColorPicker(opts);			
		});

	}

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
	
	// float button fn
	$(".tgFloatDialog").click(function() {
		openThemeGenDialog($("select[name=themegenerator_theme]").val());
		return false;
	});
	
	function openThemeGenDialog( themename ) {
		var l = getCookie("left", "themegen");
		var t = getCookie("top", "themegen");
		var w = getCookie("width", "themegen", 660);
		var h = getCookie("height", "themegen", 500);
		var p = "center"
		
		if (l !== null && t !== null) {
			p = [ parseInt(l, 10), parseInt(t, 10) ];
		}
		
		$("#themegenerator_content").dialog({
			bgiframe: true,
			width: w,
			height: h,
			position: p,
			modal: false,
			title: tr("Theme Generator:") + " " + themename,
			buttons: {
				Cancel: function () {
					$(this).dialog('close');
				},
				'Save': function() {
					var bValid = true;

					if (bValid) {
						
						$("#themegenerator_content form").submit();
						$(this).dialog('close');
					}
				},
				Preview: function () {
					
					$("#themegenerator_content form").append($("<input type='hidden' name='tg_preview' value='dialog'"));
					$("#themegenerator_content form").submit();
					$(this).dialog('close');
				}
			},
			close: function () {
				$(this).find('input[type=text]').val('').removeClass('ui-state-error');
			},
			open: function( event, ui ) {
				if (!$("form", this).length) {
					$(this).wrapInner($("<form target='tiki-admin.php&page=look&cookietab=6' method='POST' />"));
				}
				$("label", this).each( function() {	// labels go 100% width in dialog
					$(this).css("width", ($(this).text().length * 0.6) + "em");
				});
				$("input[name=tg_preview]", this).hide();
				
				setCookie("state", "open", "themegen");
			},
			dragStop: function(event, ui) {
				setCookie("left", ui.position.left, "themegen");
				setCookie("top", ui.position.top, "themegen");
			},
			resizeStop: function(event, ui) {
				setCookie("width", ui.size.width, "themegen");
				setCookie("height", ui.size.top, "themegen");
			}
		});
		
	}
	
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
		$(".themegenerator").tabs();
	}
	
	// disable for now
	$("input[type=checkbox].tgLivePreview", "#tg_section_typeography")
		.attr("checked", "")
		.attr("disabled", "disabled");

	
});
