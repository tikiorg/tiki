// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$(function() {	// wrapping

// drag & drop ones first
var dragZonesSelector = ".modules";
$(dragZonesSelector).droppable({ hoverClass: "ui-state-active" });
$(".module:not(.box-zone)", dragZonesSelector).each(function() {
	if ($(this).css("position") === "absolute") {
		var el = this;
		$(this).draggable({
			connectToSortable: ".modules",
			revert: "invalid",
			stop: function (event, ui) {
				$("#save_modules").show("fast").attr("dragged", $(el).attr("id"));
			}
		}).mouseover(function(event, ui) {	// sortable gets muddled when dragging so disable it
				$(dragZonesSelector).sortable("option", "disabled", true);
		}).mouseout(function(event, ui) {
				$(dragZonesSelector).sortable("option", "disabled", false);
		});
	}
});

var modAdminDirty = false;
$(document).ready(function() {
	$(window).bind("beforeunload", function() {
		if (modAdminDirty) {
			return tr("You have unsaved changes to your modules, are you sure you want to leave the page without saving?");
		}
	});
});

$(".modules").sortable( {
	connectWith: ".modules",
	items: ".module:not('.ui-draggable')",
	placeholder: "module-placeholder",
	revert: 200,
	tolerance: 'pointer',
	stop: function (event, ui) {
		if ($("#save_modules:visible").length === 0) {
			$("#save_modules").show("fast").attr("sortable", $(this).attr("id"))
					.parent().show("fast");
			modAdminDirty = true;
		}
		$(this).removeClass("ui-state-active");
	},
	start: function (event, ui) {
		$(this).addClass("ui-state-active");
	},
	receive: function(event, ui) {
		
		// check for list items arriving
		var dropped = $("> li", this);
		if (dropped.length) {
			var zone = $(this);	//dropped.parents(".modules:first");	// odd? more than one?
			if (zone && zone.attr("id") && zone.attr("id").match(/modules/)) {
				var ord = $.inArray(dropped[0], zone.children());
				var zoneStr = zone.attr("id").substring(0, zone.attr("id").indexOf("_"));
				var name = $.trim($("input:first", dropped).val());
				var options = {
					modName: name,
					modPos: zoneStr,
					modOrd: ord,
					dropped: dropped
				};
				if (zoneStr.indexOf("top") > -1 || zoneStr.indexOf("bottom") > -1 || zone.parent().parent().hasClass("box-zone")) {
					options.nobox = true;
				}
				dropped.addClass("module-placeholder");
				showModuleEditForm(false, options);
			}
			
		}
	}
});
$("span.moduleflip").each( function() {
	var $edit = $('<a title="' + tr("Edit module") + '" href="#"><img src="img/icons/page_gear.png" alt="[edit]" width="16" height="16" style="position:absolute;right:18px;opacity:0.75;"></a>')
		.click( function() {
			$(this).parents(".module:first").dblclick();
		});
	var $unassign = $('<a title="' + tr("Unassign module") + '" href="#"><img src="img/icons/cross.png" alt="[unassign]" width="16" height="16" style="position:absolute;right:36px;opacity:0.75;"></a>')
		.click( function() {
			var id = $(this).parents(".module:first").attr("id").match(/\d+$/);
			if (id) {
				moduleUnassign( id );
			}
		});
	$(this).prepend($edit, $unassign);
});

// disable all links in modules apart from app menu
$(".module:not(.cssmenubox,.box-Application_Menu, .box-quickadmin)").find("a, input").click( function (event) {
	if (!$(this).parent().hasClass("moduleflip")) {
		event.stopImmediatePropagation();
		return false;
	} else {
		return true;
	}
});

// set dbl click form action
$(".module:not(.box-zone), #assigned_modules tr").dblclick(function () {showModuleEditForm(this);});

// source list of all modules
$("#module_list li").draggable({
	connectToSortable: ".modules",
	helper: "clone",
	revert: "invalid",
	start: function (event, ui) {	// stop flashing while dragging
		$(document.body).css("user-select", "none");
		$(document.body).css("-webkit-user-select", "none");
		$(document.body).css("-moz-user-select", "none");
		$(".description", ui.helper).hide();
	},
	stop: function (event, ui) {
		$(document.body).css("user-select", "");
		$(document.body).css("-webkit-user-select", "");
		$(document.body).css("-moz-user-select", "");
	}
});

$("#save_modules").click(function(evt) {
	if ($(this).attr("sortable")) {
		// save module order
		modAdminDirty = false;
		var ser = {};
		$(".modules").each(function() { /* do this on everything of class "modules" */
			ser[$(this).attr("id")] = $(this).find("> div.module").map(function() { /* do this on each child module */
				return $(this).attr("id").match(/\d+$/)[0];	// dare to do it in one go
			}).get();
		});
		$("#module-order").val($.toJSON(ser)).parents("form")[0].submit();
	} else if ($(this).attr("dragged")) {
		$("#" + $(this).attr("dragged")).dblclick();
		$(this).attr("dragged", "");
	}
	return false;
}).hide();

// module select action when in main page
$("#assign_name", "#tiki-center").change( function () {
	needToConfirm=false;
	//this.form.submit();
	$("input[name=preview]", this.form).click();
});

// show edit form dialogue
var showModuleEditForm = function(item, options) {
	var modId = 0, modName, modPos = "", modOrd = 0, modStyle = "", dropped = null;
	if (item) {
		if ($(item).is("tr")) {		// assigned_modules row dblclicked
			modName = $("td:first", item).text();
			modId = $("a:last", item).data("content").match(/modup=(\d+)/);
			if (modId) {
				modId = modId[1];
				modOrd = $("td:eq(1)", item).text();
				modPos = $(item).parents("table:first").attr("id").match(/_([^_]*)$/);
				if (modPos) {
					modPos = modPos[1];
				}
			}
		} else {					// .module div dblclicked
			modName = $(item).attr("class").match(/box-[\S_-]+/);
			if (modName) {
				modName = modName[0].substring(4);
			}
			modId = $(item).attr("id").match(/\d+$/);
			if (modId) {
				modId = modId[0];
				var id = $("div:first", item).attr("id");
				if (id) {
					modPos = id.match(/(top|topbar|pagetop|left|right|pagebottom|bottom)(\d+)$/);
					if (modPos) {
						modOrd = modPos[2];
						modPos = modPos[1];
					}
				}
				modStyle = $(item).attr("style");
				if (modStyle && !modStyle.match(/absolute/)) {
					modStyle = "";	// use style from object if draggable
				}
			}
		}
	} else { // new module assignment
		modName = options.modName;
		modPos = options.modPos;
		modOrd = options.modOrd;
		dropped = options.dropped;
		if (typeof options.modId !== "undefined") {
			modId = options.modId;
		}
	}
	
	if ($("#module_edit_div").length === 0) {
		$("body").append($("<div id='module_edit_div'><form action='#' method='post'></form></div>"));
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
		if (typeof options.formVals !== "undefined") {
			var v = options.formVals;
			$.extend(v, postData);
			postData = v;
		}
		postData.preview = true;
	}
	
	$.post("tiki-admin_modules.php", postData, function(data) {
			$('#module_edit_div form').html(data);
			$("#module_edit_div form").append($("<input type='hidden' name='assign' value='popup' />" +
											"<input type='hidden' name='assign_name' value='"+modName+"' />" +
											"<input type='hidden' name='moduleId' value='"+modId+"' />"));
			$('#module_edit_div').dialog("option", "width", 580)
					.dialog("option", "height", 500)
					.dialog( "option", "position", 'center' )
					.find("input[type='submit']").hide();
			if (options && options.nobox) {
				$('input[name*=nobox]').val("y");
			}
			$(this).applyChosen();
			$("#module_params").tabs();
			$('.pagename').tiki("autocomplete", "pagename", {multiple: true, multipleSeparator: ";"});
			if (modStyle) {
				// preload style field with style if position:absolute (unnecessary spaces removed)
				$('input[name*=style]').val(modStyle.replace(/:\s*/g, ":").replace(/;\s*/g, ";"));
			}
			$("#assign_name", "#module_edit_div").change( function () {
				var formVals = {};
				$(this).parents("form").find("input[name!=assign], select, textarea").each( function () {
					formVals[$(this).attr("name")] = $(this).val();
				});
				showModuleEditForm (null, {
					modName: $(this).val(),
					modPos: modPos,
					modOrd: modOrd,
					dropped: dropped,
					modId: modId,
					formVals: formVals
				});
			});
			ajaxLoadingHide();
		},
	"html");

	var CancelLabel = tr("Cancel");
	var SaveLabel = tr("Save");
	var DeleteLabel = tr("Remove");
	var navbuttons = {};

	navbuttons[ CancelLabel ] = function () {
        $(this).dialog('close');
        if (dropped) {
          dropped.remove();
        }
      };
	navbuttons[ DeleteLabel ] = function () {
        moduleUnassign( modId );
        $(this).dialog('close');
      };
	navbuttons[ SaveLabel ] = function() {
        var bValid = true;
        $(this).find('input[type=text]').removeClass('ui-state-error');

        if (bValid) {
          modAdminDirty = false;
          $("#module_edit_div form").submit();
          $(this).dialog('close');
        }
      };

	
	$('#module_edit_div').dialog({
		bgiframe: true,
		width: 580,
		height: 500,
		modal: true,
		title: tr("Edit module:") + " " + tiki_decodeURIComponent(modName).replace("+"," "),
		buttons: navbuttons,
		close: function () {
			$(this).find('input[type=text]').val('').removeClass('ui-state-error');
		}
	});
	 ajaxLoadingShow('module_edit_div');
};

var moduleUnassign = function(modId) {
	var modName = $("#module_" + modId).attr("class").match(/box-[\S_-]+/);	// TODO REFACTOR
	if (modName) {
		modName = modName[0].substring(4);
	}
	if (confirm(tr("Are you sure you want to unassign this module?") + " (" + modName + ")")) {
		window.location.replace("tiki-admin_modules.php?unassign=" + modId);
	}
};

});	// close closure
