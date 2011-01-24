/* $Id$
 *
 * Include JS file for Tiki 7 Theme Generator
 */

$(document).ready( function() {
	// colorpicker for the colour swatches
	$(".tgItem > div", "#tg_section_colors").tiki("colorpicker", "", {
		colorLabel: "",
		colorSelector: ".colorSelector > div",
		colorInput: "input"
	});
	
	// checkboxes to select items
	$(".tgItems :checkbox").click(function(e, flip) {
		if ($(this).attr("checked") && !flip) { // flip trickery to get trigger to toggle the right classes
			$(this).parent().addClass("selected");
		} else if ((!$(this).attr("checked") && !flip) || flip) {
			$(this).parent().removeClass("selected");
		}
	});
	
	// reset button fn
	$(".tgResetSection").click(function() {
		var container = $(this).parent().nextAll("ul");
		$(".tgItem :checkbox:checked", container).each(function() {
			$(this).parent().find("input.tgValue").val($(this).val());
			$(this).parent().find(".colorLabel").text($(this).val());
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
		$(".themegenerator").tabs();
	}
	
	// disable for now
	$("input[type=checkbox].tgLivePreview", "#tg_section_typeography")
		.attr("checked", "")
		.attr("disabled", "disabled");

	
});
