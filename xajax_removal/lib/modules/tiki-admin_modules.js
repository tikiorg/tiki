// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $


$(".modules").sortable({
	connectWith: ".modules",
	items: "div.module",
	forcePlaceholderSize: true,
	forceHelperSize: true,
	placeholder: "module-placeholder",
	stop: function (event, ui) {
		$("#save_modules *").show("fast");
	},
	start: function (event, ui) {
		
	},
	receive: function(event, ui) {
		$("#save_modules").show();
	}
});
$(".module").dblclick(function () { showModuleEditForm(this); });

$("#module_list").sortable({
	connectWith: ".modules",
	items: "tr",
	forcePlaceholderSize: true,
	forceHelperSize: true,
	placeholder: "module-placeholder",
	stop: function (event, ui) {
		$("#save_modules *").show("fast");
	},
	start: function (event, ui) {
		
	},
	receive: function(event, ui) {
		$("#save_modules").show();
	}
});

$("#save_modules a").click(function(evt) {
	// save module order
	var ser = {};
	$(".modules").each(function (){				/* do this on everything of class "modules" */
		ser[$(this).attr("id")] = $(this).find(".module").map(function (){	/* do this on each child module */
			return $(this).attr("id");
		}).get();
	});
	$("#module-order").val($.toJSON(ser)).parents("form")[0].submit();
	return false;
});

// show edit form dialogue
showModuleEditForm = function(item) {
	var modId, modName, modPos, modOrd;
	if (item) {
		//alert("module edit form - TODO");
		modName = $(item).attr("class").match(/box-\S+/);
		if (modName) {
			modName = modName[0].substring(4);
		}
		modId = $(item).attr("id").match(/\d+$/);
		if (modId) {
			modId = modId[0];
			var id = $("div:first", item).attr("id");
//			modPos = id.match(/.\d+$/);
//			if (modPos) {
//				modOrd = modPos[0].substring( 1, modPos[0].length);
//				modPos = modPos[0].substring( 0, 1);
//			}
		}
	}
	if ($("#module_edit_div").length === 0) {
		$("body").append($("<div id='module_edit_div'><form action='#' method='post'><table></table></form></div>"));
		$("#module_edit_div form").append($("<input type='hidden' name='assign' value='popup' />" +
											"<input type='hidden' name='assign_name' value='" + modName + "' />" +
											"<input type='hidden' name='moduleId' value='" + modId + "' />"));
	}
	$.post("tiki-admin_modules.php", {
			edit_module: true,
			edit_assign: modId,
			assign_name: modName
//			moduleId: modId,
//			assign_position: modPos,
//			assign_order: modOrd
		}, function(data) {
			$('#module_edit_div table').html(data);
			$('#module_edit_div').dialog("option", "width", 500)
					.dialog("option", "height", 500)
					.dialog( "option", "position", 'center' )
					.find("table input[type='submit']").hide();
			ajaxLoadingHide();
		},
	"html");
	
	$('#module_edit_div').dialog({
		bgiframe: true,
		width: 500,
		height: 500,
		modal: true,
		title: tr("Edit module:") + " " + modName,
		buttons: {
			Cancel: function () {
				$(this).dialog('close');
			},
			'Save': function() {
				var bValid = true;
				$(this).find('input[type=text]').removeClass('ui-state-error');
					
				if (bValid) {
					//$("#module_edit_div #save_tool").val('Save');
					$("#module_edit_div form").submit();
					$(this).dialog('close');
				}
			},
			Delete: function () {
				if (confirm(tr("Are you sure you want to unassign this module?") + " (" + modName + ")")) {
					window.location.replace("tiki-admin_modules.php?unassign=" + modId);
				}
				$(this).dialog('close');
			}
		},
		close: function () {
			$(this).find('input[type=text]').val('').removeClass('ui-state-error');
		}
	});
	 ajaxLoadingShow('module_edit_div');
};


