/*
 * tiki brosho code
 */

function toggle_brosho() {
	$.fn.brosho({                     //call to the brosho plugin
		stylesheet:         "lib/jquery_tiki/brosho/tiki.brosho.css", //path of custom brosho stylesheet
		position:           "left",           //initial position of the editor ("top", "bottom", "left", "right")
		elementHoverClass:  "custom-hover",   //a custom hover class
		editorOpacity:      0.8                 //full opacity on editor
	});
	$("#brosho-selector").prepend($("<img src='pics/icons/close.png' style='float:right;' />").click(function(){
		$('#brosho-wrapper, #brosho-overlay-wrapper').remove(); //remove old stuff
		$('body *').unbind(); //unbind the previous hover event handler on every element within the body
	}));
	$("#brosho-controls").append($("<li id='brosho-copy' />").text("Copy to Custom CSS: ").append($("<a href='#'>Copy</a>")).click(function(){
		$("textarea[name=header_custom_css]").val($("textarea[name=header_custom_css]").val() + "\n" + brosho_get_code());
	}));
	$("#brosho-generate, #brosho-position").remove();
	if ($.ui) {
		var h = $("#brosho-wrapper").height();
		$("#brosho-wrapper").resizable({maxHeight:h,minHeight:h}).draggable();
	}
	return false;
}

function brosho_get_code() {
	var full_css = ""; //store the generated css code here
	$('body *').each(function() { //check every element for changed css
		var el = $(this); //used several times
		if (el.length) {
			var brosho_css = el.attr('brosho-css'); //store the attribute value
			  
			if(brosho_css !== undefined || brosho_css) { //does this element have changed css
				var temp_css = $.fn.brosho.extractCssSelectorPath(el) + ' {\n'; //generate css selector path
				var properties = brosho_css.split(";"); //split the properties
				    
				for(var i = 0; i < properties.length; i++) { //loop through every property
					if (properties[i].length) {
						temp_css += '\t' + $.trim(properties[i]) + ';\n';
					} //trim and add the property
				}
				    
				temp_css += '}\n\n'; //close the selector
				    
				if (full_css.indexOf(temp_css) == -1) {
					full_css += temp_css;
				} //make sure we dont have the snippet yet and append it to the full css string
			}
		}
	});
	return full_css;
}

