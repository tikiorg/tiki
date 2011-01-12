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
	placeholder: "toolbars-placeholder",
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

	if (item) {
		alert("module edit form - TODO");
	}
//	$('#module_edit_div').dialog('open');
};