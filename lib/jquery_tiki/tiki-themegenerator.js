/* $Id$
 *
 * Include JS file for Tiki 7 Theme Generator
 */

$(document).ready( function() {
	// colorpicker for the colour swatches
	$(".colorItem > div").tiki("colorpicker", "", {
		colorLabel: ".colorLabel",
		colorSelector: ".colorSelector > div",
		colorInput: "input"
	});
	
	// checkboxes to select colours
	$(".colorItem :checkbox").click(function(e, flip) {
		if ($(this).attr("checked") && !flip) { // flip trickery to get trigger to toggle the right classes
			$(this).parent().addClass("selected");
		} else if ((!$(this).attr("checked") && !flip) || flip) {
			$(this).parent().removeClass("selected");
		}
	});
	
	// reset button fn
	$("#resetColors").click(function() {
		$(".colorItem :checkbox:checked").each(function() {
			$(this).parent().find("input[type=hidden]").val($(this).val());
			$(this).parent().find(".colorLabel").text($(this).val());
			$(this).parent().find(".colorSelector > div").css("background-color", $(this).val());
			$(this).click();
			$(this).parent().removeClass("selected");
		});
		return false;
	});
	
	// select modified colours
	$("#toggleChangedColors").click(function() {
		$(".colorItem").each(function() {
			if ($("input[type=hidden]", this).val() !== $("input[type=checkbox]", this).val()) {
				$(this).find("input[type=checkbox]").trigger("click", [true]);
			}
		});
	});
	
	// toggle selection
	$("#toggleColors").click(function() {
		$(".colorItem input[type=checkbox]").trigger("click", [true]);
	});
	
});
