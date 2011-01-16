// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $

// drag & drop ones first
var dragZonesSelector = "#top_modules, #bottom_modules";
$(dragZonesSelector).droppable({});
$(".module", dragZonesSelector).each(function() {
	if ($(this).css("position") === "absolute") {
		var el = this;
		$(this).draggable({
			connectToSortable: ".modules",
			revert: "invalid",
			stop: function (event, ui) {
				$("#save_modules a").show("fast").attr("dragged", $(el).attr("id"));
			}
		}).mouseover(function(event, ui) {	// sortable gets muddled when dragging so disable it
				$(dragZonesSelector).sortable("option", "disabled", true);
		}).mouseout(function(event, ui) {
				$(dragZonesSelector).sortable("option", "disabled", false);
		});
	}
});

$(".modules").sortable( {
	connectWith: ".modules",
	items: ".module:not('.ui-draggable')",
//	forcePlaceholderSize: true,
//	forceHelperSize: true,
//	helper: "original",
	placeholder: "module-placeholder",
	revert: 200,
	stop: function (event, ui) {
		$("#save_modules a").show("fast").attr("sortable", $(this).attr("id"));
	},
	start: function (event, ui) {
		
	},
	receive: function(event, ui) {
		
		// check for list items arriving
		var dropped = $("> li", this);
		if (dropped.length) {
			var zone = $(this);	//dropped.parents(".modules:first");	// odd? more than one?
			if (zone && zone.attr("id") && zone.attr("id").match(/modules/)) {
				var ord = $.inArray(dropped[0], zone.children());
				var zoneStr = zone.attr("id").substring(0, 1);
				var name = dropped.text().match(/\((.*?)\)$/);
				if (name) {
					name = name[1];
				}
				var options = {
					modName: name,
					modPos: zoneStr,
					modOrd: ord,
					dropped: dropped
				};
				if (zoneStr === "t" || zoneStr === "b") {
					options.nobox = true;
				}
				dropped.addClass("module-placeholder");
				showModuleEditForm(false, options);
			}
			
		}
	}
});
$("span.moduleflip").each( function() {
	var $edit = $('<a title="Edit module" href="#"><img src="pics/icons/page_gear.png" alt="[edit]" width="16" height="16" style="position:absolute;right:18px;opacity:0.75;"></a>')
		.click( function() {
			$(this).parents(".module:first").dblclick();
		});
	var $unassign = $('<a title="Unassign module" href="#"><img src="pics/icons/cross.png" alt="[unassign]" width="16" height="16" style="position:absolute;right:36px;opacity:0.75;"></a>')
		.click( function() {
			var id = $(this).parents(".module:first").attr("id").match(/\d+$/);
			if (id) {
				moduleUnassign( id );
			}
		});
	$(this).prepend($edit, $unassign);
});

// disable all links in modules apart from app menu
$(".module:not(.box-ApplicationMenu)").find("a:not(.moduleflip a), input").click( function (event) {
	event.stopImmediatePropagation();
	return false;
});

// set dbl click form action
$(".module").dblclick(function () { showModuleEditForm(this); });

// source list of all modules
$("#module_list li").draggable({
	connectToSortable: ".modules",
	helper: "clone",
	revert: "invalid",
	start: function (event, ui) {	// stop flashing while dragging
		$(document.body).css("user-select", "none");
		$(document.body).css("-webkit-user-select", "none");
		$(document.body).css("-moz-user-select", "none");
	},
	stop: function (event, ui) {
		$(document.body).css("user-select", "");
		$(document.body).css("-webkit-user-select", "");
		$(document.body).css("-moz-user-select", "");
	}
});

$("#save_modules a").click(function(evt) {
	if ($(this).attr("sortable")) {
		// save module order
		var ser = {};
		$(".modules").each(function() { /* do this on everything of class "modules" */
			ser[$(this).attr("id")] = $(this).find(".module").map(function() { /* do this on each child module */
				return $(this).attr("id").match(/\d+$/)[0];	// dare to do it in one go
			}).get();
		});
		$("#module-order").val($.toJSON(ser)).parents("form")[0].submit();
	} else if ($(this).attr("dragged")) {
		$("#" + $(this).attr("dragged")).dblclick();
		$(this).attr("dragged", "");
	}
	return false;
});

// show edit form dialogue
showModuleEditForm = function(item, options) {
	var modId = 0, modName, modPos = "", modOrd = 0, modStyle = "", dropped = null;
	if (item) {
		modName = $(item).attr("class").match(/box-[\S_-]+/);
		if (modName) {
			modName = modName[0].substring(4);
		}
		modId = $(item).attr("id").match(/\d+$/);
		if (modId) {
			modId = modId[0];
			var id = $("div:first", item).attr("id");
			if (id) {
				modPos = id.match(/.\d+$/);
				if (modPos) {
					modOrd = modPos[0].substring(1, modPos[0].length);
					modPos = modPos[0].substring(0, 1);
				}
			}
			modStyle = $(item).attr("style");
			if (modStyle && !modStyle.match("absolute")) {
				modStyle = "";	// use style from object if draggable
			}
		}
	} else { // new module assignment
		modName = options.modName;
		modPos = options.modPos;
		modOrd = options.modOrd;
		dropped = options.dropped;
	}
	
	if ($("#module_edit_div").length === 0) {
		$("body").append($("<div id='module_edit_div'><form action='#' method='post'><table></table></form></div>"));
		$("#module_edit_div form").append($("<input type='hidden' name='assign' value='popup' />" +
											"<input type='hidden' name='assign_name' value='' />" +
											"<input type='hidden' name='moduleId' value='' />"));
	}
	$("#module_edit_div input[name=assign_name]").val(modName);
	$("#module_edit_div input[name=moduleId]").val(modId);
	
	var postData = {
		edit_module: true,
		assign_name: modName,
		moduleId: modId,
		assign_position: modPos,
		assign_order: modOrd
		//preview: true
	};
	if (item) {
		postData.edit_assign = modId;
		//postData.assign_params = { style: modStyle };
	} else {
		postData.preview = true;
	}
	
	$.post("tiki-admin_modules.php", postData, function(data) {
			$('#module_edit_div table').html(data);
			$('#module_edit_div').dialog("option", "width", 580)
					.dialog("option", "height", 500)
					.dialog( "option", "position", 'center' )
					.find("table input[type='submit']").hide();
			if (options && options.nobox) {
				$('input[name*=nobox]').val("y");
			}
			if (modStyle) {
				// preload style field with style if position:absolute (unnecessary spaces removed)
				$('input[name*=style]').val(modStyle.replace(/\:\s*/g, ":").replace(/\;\s*/g, ";"));
			}
			ajaxLoadingHide();
		},
	"html");
	
	$('#module_edit_div').dialog({
		bgiframe: true,
		width: 580,
		height: 500,
		modal: true,
		title: tr("Edit module:") + " " + unescape(modName).replace("+"," "),
		buttons: {
			Cancel: function () {
				$(this).dialog('close');
				if (dropped) {
					dropped.remove();
				}
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
				moduleUnassign( modId );
				$(this).dialog('close');
			}
		},
		close: function () {
			$(this).find('input[type=text]').val('').removeClass('ui-state-error');
		}
	});
	 ajaxLoadingShow('module_edit_div');
};

moduleUnassign = function(modId) {
	var modName = $("#module_" + modId).attr("class").match(/box-[\S_-]+/);	// TODO REFACTOR
	if (modName) {
		modName = modName[0].substring(4);
	}
	if (confirm(tr("Are you sure you want to unassign this module?") + " (" + modName + ")")) {
		window.location.replace("tiki-admin_modules.php?unassign=" + modId);
	}
};

