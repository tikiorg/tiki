// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* Include for tiki-admin_toolbars.php
 * 
 * Selector vars set up in tiki-admin_toolbars.php:
 * 
 * var toolbarsadmin_rowStr = '#row-1,#row-2,#row-3... etc'
 * var toolbarsadmin_fullStr = '#full-list-w,#full-list-p,#full-list-c';
 * var toolbarsadmin_delete_text = tra('Are you sure you want to delete this custom tool?')
 */


$(document).ready(function () {
	
	$(toolbarsadmin_rowStr).sortable({
		connectWith: toolbarsadmin_fullStr + ', .row',
		forcePlaceholderSize: true,
		forceHelperSize: true,
		placeholder: 'toolbars-placeholder',
		stop: function (event, ui) {
			var elx = 0, lastLeftX = 0, firstRightX = 0;	// find the gap between left and right aligned tools
			$(ui.item).parent().children().each(function () {
				if ($(this).css("float") === "right") {
					if (!lastLeftX) { lastLeftX = elx; }
					firstRightX = $(this).position().left;
				}
				elx = $(this).position().left;
			});
			if (!lastLeftX) { lastLeftX = elx; }
			if (!firstRightX) { firstRightX = $(ui.item).parent().width(); }
			var midPoint = lastLeftX + (firstRightX - lastLeftX) / 2;
			if (ui.offset.left > midPoint) {
				$(ui.item).css("float", "right");
			} else {
				$(ui.item).css("float", "left");
			}
		},
		start: function (event, ui) {
			if ($(ui.item).css("float") === "right") {
				$(ui.placeholder).css('float', "right");
			} else {
				$(ui.placeholder).css('float', "left");
			}
		},
		receive: function(event, ui) {
			var a = 1;
		}
	});
	$(toolbarsadmin_fullStr).sortable({
		connectWith: '.row, #full-list-c',
		forcePlaceholderSize: true,
		forceHelperSize: true,
		placeholder: 'toolbars-placeholder',
		remove: function (event, ui) {	// special handling for separator to allow duplicates
			if ($(ui.item).text() === '-') {
				$(this).prepend($(ui.item).clone());	// leave a copy at the top of the full list
			}
		},
		receive: function (event, ui) {
			$(ui.item).css('float', '');
			if ($(ui.item).text() === '-') {
				$(this).children().remove('.qt--');				// remove all seps
				$(this).prepend($(ui.item).clone());			// put one back at the top
	
			} else if ($(this).attr('id') === 'full-list-c') {	// dropped in custom list
				$(ui.item).dblclick(function () { showToolEditForm(ui.item); });
				$(ui.item).trigger('dblclick');
			}
			sortList(this);
		},
		stop: function (event, ui) {
			sortList(this);
		}
	});
	var sortList = function (list) {
		var arr = $(list).children().get(), item, labelA, labelB;
		arr.sort(function(a, b) {
			labelA = $(a).text().toUpperCase();
			labelB = $(b).text().toUpperCase();
			if (labelA < labelB) { return -1; }
			if (labelA > labelB) { return 1; }
			return 0;
		});
		$(list).empty();
		for (item = 0; item < arr.length; item++) {
			$(list).append(arr[item]);
		}
		if ($(list).attr("id") === "full-list-c") {
			$('.qt-custom').dblclick(function () { showToolEditForm(this); });
		}
	};
	$('.qt-custom').dblclick(function () { showToolEditForm(this); });
	
	// show edit form dialogue
	var showToolEditForm = function (item) {

		if (item) {
			$('#toolbar_edit_div #tool_name').val($(item).text()); //.attr('disabled','disabled');
			$('#toolbar_edit_div #tool_label').val($(item).children('img').attr('title'));
			if ($(item).children('img').attr('src') !== 'img/icons/shading.png') {
				$('#toolbar_edit_div #tool_icon').val($(item).children('img').attr('src'));
			} else {
				$('#toolbar_edit_div #tool_icon').val('');
			}
			$('#toolbar_edit_div #tool_token').val($(item).find('input[name=token]').val());
			$('#toolbar_edit_div #tool_syntax').val($(item).find('input[name=syntax]').val());
			$('#toolbar_edit_div #tool_type').val($(item).find('input[name=type]').val());
			if ($(item).find('input[name=type]').val() === 'Wikiplugin') {
				$('#toolbar_edit_div #tool_plugin').val($(item).find('input[name=plugin]').val());
			} else {
				$('#toolbar_edit_div #tool_plugin').attr('disabled', 'disabled');
			}
		}
		$('#toolbar_edit_div').dialog('open');
	};
	// handle plugin select on edit dialogue
	$('#toolbar_edit_div #tool_type').change( function () {
		if ($('#toolbar_edit_div #tool_type').val() === 'Wikiplugin') {
			$('#toolbar_edit_div #tool_plugin').removeAttr('disabled');
		} else {
			$('#toolbar_edit_div #tool_plugin').attr('disabled', 'disabled').val("");
		}
	});
	
	$("#toolbar_edit_div").dialog({
		bgiframe: true,
		autoOpen: false,
	//	height: 300,
		modal: true,
		buttons: {
			Cancel: function () {
				$(this).dialog('close');
			},
			'Save': function() {
				var bValid = true;
				$(this).find('input[type=text]').removeClass('ui-state-error');
	
				bValid = bValid && checkLength($('#toolbar_edit_div #tool_name'),"Name",2,16);
				bValid = bValid && checkLength($('#toolbar_edit_div #tool_label'),"Label",1,80);
				
				if (bValid) {
					$("#toolbar_edit_div #save_tool").val('Save');
					$("#toolbar_edit_div form").submit();
					$(this).dialog('close');
				}
			},
			Delete: function () {
				if (confirm(toolbarsadmin_delete_text)) {
					$("#toolbar_edit_div #delete_tool").val('Delete');
					$("#toolbar_edit_div form").submit();
				}
				$(this).dialog('close');
			}
		},
		close: function () {
			$(this).find('input[type=text]').val('').removeClass('ui-state-error');
		}
	});

	var checkLength = function (o, n, min, max) {
		if (o.val().length > max || o.val().length < min) {
			o.addClass('ui-state-error');
			o.prev("label").find(".dialog_tips").text(" Length must be between " + min + " and " + max).addClass('ui-state-highlight');
			setTimeout(function () {
				o.prev("label").find(".dialog_tips").removeClass('ui-state-highlight', 1500);
			}, 500);
			return false;
		} else {
			return true;
		}
	};

	// view mode filter (still doc.ready)

	if ($("#section").val() === "sheet") {
		$("#view_mode").val("sheet");
	}

	$('#view_mode').change(setViewMode);

	setViewMode();

	$('#toolbar_add_custom').click(function () {
		showToolEditForm();
		return false;
	});

});	// end doc ready

// save toolbars
function saveRows() {
	var ser, text;
	ser = $('.toolbars-admin .row').map(function (){	/* do this on everything of class 'row' inside toolbars-admin div */
		var right_section = false;
		return $(this).children().map(function (){	/* do this on each child node */
			text = "";
			if ( !right_section && $(this).css("float") === "right") {
				text = "|";
				right_section = true;
			}
			if ($(this).hasClass('qt-plugin')) { text += 'wikiplugin_'; }
			text += $(this).text();
			return text;
		}).get().join(",").replace(",|", "|");			/* put commas inbetween */
	});
	if (typeof(ser) === 'object' && ser.length > 1) {
		ser = $.makeArray(ser).join('/');			// row separators
	} else {
		ser = ser[0];
	}
	$('#qt-form-field').val(ser.replace(',,', ','));
}


function setViewMode() {
	if ($("#view_mode").val() === 'both') {
		$('.qt-wyswik').hide();
		$('.qt-wiki').show();
		$('.qt-wys').show();
		$('.qt-sheet').hide();
	} else if ($("#view_mode").val() === 'wiki') {
		$('.qt-wyswik').hide();
		$('.qt-wys').hide();
		$('.qt-wiki').show();
		$('.qt-sheet').hide();
	} else if ($("#view_mode").val() === 'wysiwyg') {
		$('.qt-wyswik').hide();
		$('.qt-wiki').hide();
		$('.qt-wys').show();
		$('.qt-sheet').hide();
	} else if ($("#view_mode").val() === 'wysiwyg_wiki') {
		$('.qt-wiki').hide();
		$('.qt-wys').hide();
		$('.qt-sheet').hide();
		$('.qt-wyswik').show();
		$('.qt--').show();
	} else if ($("#view_mode").val() === 'sheet') {
		$('.qt-wyswik').hide();
		$('.qt-wys').hide();
		$('.qt-wiki').show();
		$('.qt-sheet').show();
	}
}


