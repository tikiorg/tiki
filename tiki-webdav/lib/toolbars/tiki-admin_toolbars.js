/**
 * $Id $
 * 
 * Include for tiki-admin_toolbars.php
 *  
 */

$jq(document).ready( function () {
	
	$jq(toolbarsadmin_rowStr).sortable({
		connectWith: toolbarsadmin_fullStr + ', .row',
		forcePlaceholderSize: true,
		forceHelperSize: true,
		placeholder: 'toolbars-placeholder',
		stop: function(event, ui) {
			var elx = 0, lastLeftX, firstRightX;	// find the gap between left and right aligned tools
			$jq(ui.item).parent().children().each( function () {
				if ($jq(this).css("float") == "right") {
					if (!lastLeftX) { lastLeftX = elx; }
					firstRightX = $jq(this).position().left;
				}
				elx = $jq(this).position().left;
			});
			var midPoint = lastLeftX + (firstRightX - lastLeftX) / 2;
			if (ui.offset.left > midPoint) {
				$jq(ui.item).css("float", "right");
			} else {
				$jq(ui.item).css("float", "left");
			}
		},
		start: function (event, ui) {
			if ($jq(ui.item).css("float") == "right") {
				$jq(ui.placeholder).css('float', "right");
			} else {
				$jq(ui.placeholder).css('float', "left");
			}
		}
	});
	$jq(toolbarsadmin_fullStr).sortable({
		connectWith: '.row, #full-list-c',
		forcePlaceholderSize: true,
		forceHelperSize: true,
		placeholder: 'toolbars-placeholder',
		remove: function(event, ui) {	// special handling for separator to allow duplicates
			if ($jq(ui.item).text() == '-') {
				$jq(this).prepend($jq(ui.item).clone());	// leave a copy at the top of the full list
			}
		},
		receive: function(event, ui) {
			$jq(ui.item).css('float', '');
			if ($jq(ui.item).text() == '-') {
				$jq(this).children().remove('.qt--');				// remove all seps
				$jq(this).prepend($jq(ui.item).clone());			// put one back at the top
	
			} else if ($jq(this).attr('id') == 'full-list-c') {	// dropped in custom list
				$jq(ui.item).dblclick(function() { showToolEditForm(ui.item); });
				$jq(ui.item).trigger('dblclick');
			}
			sortList(this);
		},
		stop: function (event, ui) {
			sortList(this);
		}
	});
	sortList = function (list) {
		var arr = $jq(list).children().get(), item, labelA, labelB;
		arr.sort( function(a, b) {
			labelA = $jq(a).text().toUpperCase();
			labelB = $jq(b).text().toUpperCase();
			if (labelA < labelB) { return -1; }
			if (labelA > labelB) { return 1; }
			return 0;
		});
		$jq(list).empty();
		for (item in arr) {
			$jq(list).append(arr[item]);
		}
	};
	$jq('#full-list-c').sortable({	// custom tools list
		connectWith: '.lists'
	}).children().each(function() {	// add double click action
		$jq(this).dblclick(function() { showToolEditForm(this); });
	});
	$jq('.qt-custom').dblclick(function() { showToolEditForm(this); });
	
	// show edit form dialogue
	showToolEditForm = function(item) {
	
		if (item) {
			$jq('#toolbar_edit_div #tool_name').val($jq(item).text()); //.attr('disabled','disabled');
			$jq('#toolbar_edit_div #tool_label').val($jq(item).children('img').attr('title'));
			$jq('#toolbar_edit_div #tool_icon').val($jq(item).children('img').attr('src'));
			$jq('#toolbar_edit_div #tool_token').val($jq(item).find('input[name=token]').val());
			$jq('#toolbar_edit_div #tool_syntax').val($jq(item).find('input[name=syntax]').val());
			$jq('#toolbar_edit_div #tool_type').val($jq(item).find('input[name=type]').val());
			if ($jq(item).find('input[name=type]').val() == 'Wikiplugin') {
				$jq('#toolbar_edit_div #tool_plugin').val($jq(item).find('input[name=plugin]').val());
			} else {
				$jq('#toolbar_edit_div #tool_plugin').attr('disabled', 'disabled');
			}
		}
		$jq('#toolbar_edit_div').dialog('open');
	};
	// handle plugin select on edit dialogue
	$jq('#toolbar_edit_div #tool_type').change( function () {
		if ($jq('#toolbar_edit_div #tool_type').val() == 'Wikiplugin') {
			$jq('#toolbar_edit_div #tool_plugin').removeAttr('disabled');
		} else {
			$jq('#toolbar_edit_div #tool_plugin').attr('disabled', 'disabled').val("");
		}
	});
	
	$jq("#toolbar_edit_div").dialog({
		bgiframe: true,
		autoOpen: false,
	//	height: 300,
		modal: true,
		buttons: {
			Cancel: function() {
				$jq(this).dialog('close');
			},
			'Save': function() {
				var bValid = true;
	//			allFields.removeClass('ui-state-error');
	//
	//			bValid = bValid && checkLength($jq('#toolbar_edit_div #tool_name'),"name",2,16);
	//			bValid = bValid && checkLength($jq('#toolbar_edit_div #tool_label'),"label",1,80);
				
				if (bValid) {
					$jq("#toolbar_edit_div #save_tool").val('Save');
					$jq("#toolbar_edit_div form").submit();
				}
				$jq(this).dialog('close');
			},
			Delete: function() {
				if (confirm(toolbarsadmin_delete_text)) {
					$jq("#toolbar_edit_div #delete_tool").val('Delete');
					$jq("#toolbar_edit_div form").submit();
				}
				$jq(this).dialog('close');
			}
		},
		close: function() {
			//allFields.val('').removeClass('ui-state-error');
		}
	});
	
	// save toolbars
	saveRows = function() {
		var ser, text;
		ser = $jq('.row').map(function(){				/* do this on everything of class 'row' */
			var right_section = false;
			return $jq(this).children().map(function(){	/* do this on each child node */
				text = "";
//				if ($jq(this).text() == "help") {
//					var a = 1;
//				}
				if ( !right_section && $jq(this).css("float") == "right") {
					text = "|";
					right_section = true;
				}
				if ($jq(this).hasClass('qt-plugin')) { text += 'wikiplugin_'; }
				text += $jq(this).text();
				return text;
			}).get().join(",").replace(",|", "|");			/* put commas inbetween */
		});
		if (typeof(ser) == 'object' && ser.length > 1) {
			ser = $jq.makeArray(ser).join('/');			// row separators
		} else {
			ser = ser[0];
		}
		$jq('#qt-form-field').val(ser.replace(',,', ','));
	};
	
	// view mode filter
	
	$jq('#view_mode').change( function () {
		if ($jq(this).val() == 'both') {
			$jq('.qt-wiki').show();
			$jq('.qt-wys').show();
		} else if ($jq(this).val() == 'wiki') {
			$jq('.qt-wys').hide();
			$jq('.qt-wiki').show();
		} else if ($jq(this).val() == 'wysiwyg') {
			$jq('.qt-wiki').hide();
			$jq('.qt-wys').show();
		}
	});
	
	$jq('#toolbar_add_custom').click(function () {
		showToolEditForm();
	});

});	// end doc ready
