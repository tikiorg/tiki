<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
require_once('lib/trackers/trackerlib.php');
$access->check_permission('tiki_p_export_tracker');

$headerlib->add_jq_onready('
	//manipulation objects
	var elements = $("#trackerElements").hide();
	var designer = $("#reportDesigner");
	
	var objFactory = {
		designSet: function() {
			var designSets = $(".designSet");
			var designSet = $("<div class=\'designSet\'>" + 
				"<h5>" + (designSets.length ? "|-Join " : "Base ") + "Tracker</h5>" +
			"</div>")
				.appendTo(designer);
			
			objFactory.trackerDDL(designSet);
			
			if (designSets.length) {
				this.trackerFieldDDL(designSet);
				this.trackerJoinTypeDDL(designSet);
				
				//UI stuff
				designSet
					.css("padding-left", "20px")
					.addClass("ui-state-highlight")
			} else {
				//UI stuff
				designSet
					.addClass("ui-widget-header");
			}
			
		},
		trackerDDL: function(set) {
			elements.find(".trackerList").clone()
				.addClass("trackerList_active")
				.appendTo(set)
				.change(function() {
					objFactory.includedFieldsInReport(set);
					objFactory.sortByFields(set);
					objFactory.searchFor(set);
					set.nextAll().find(".trackerFieldList_active").change();
				})
				.change();
		},
		trackerFieldDDL: function(set) {
			var trackerFieldDDL = elements.find(".trackerFieldList").clone()
				.addClass("trackerFieldList_active")
				.change(function() {
					var val = trackerFieldDDL.val();
					trackerFieldDDL.html("");
					set.prevAll().find(".trackerList_active").each(function() {
						trackerFieldDDL
							.append(elements.find(".trackerFieldList > .tracker_option_" + $(this).val()).clone());
					});
					
					trackerFieldDDL.val(val);
					
					objFactory.includedFieldsInReport(set);
					objFactory.sortByFields(set);
					objFactory.searchFor(set);
				})
				.appendTo(set);
				
				trackerFieldDDL.change();
			return trackerFieldDDL;
		},
		trackerJoinTypeDDL: function(set) {
			elements.find(".trackerJoinType").clone()
				.addClass("trackerJoinType_active")
				.appendTo(set);
		},
		trackerStatusDDL: function() {
			designer.find(".trackerStatusType").remove();
			
			elements.find(".trackerStatusType").clone()
				.addClass("trackerStatusType_active")
				.appendTo(designer);
		},
		includedFieldsInReport: function (designSet) {
			var fieldPicker = designer.find(".fieldPicker").remove();
			
			var trackerFieldCheckboxList = elements.find(".trackerFieldCheckboxList").clone();
			var fieldPicker = $("<div class=\'fieldPicker\'></div>");
			designer.find(".trackerList_active").each(function() {
				trackerFieldCheckboxList.find(".tracker_checkbox_" + $(this).val()).each(function() {
					var input = $(this);
					fieldPicker
						.append(input)
						.append("<span> " + input.attr("name") + "</span><br />");
				
				});
			});
			
			if (fieldPicker.children().length) {
				fieldPicker.prepend("<h5>Include Fields <a href=\'#\' onclick=\'checkAll(this);return false;\'>all</a> <a href=\'#\' onclick=\'uncheckAll(this);return false;\'>none</a></h5>")
				
				this.trackerStatusDDL(designSet);
			}
			
			fieldPicker.insertAfter(designer.children().last());
		},
		sortByFields: function () {
			$(".fieldSortPicker").remove();
			var trackerFieldSort = $("<div class=\'fieldSortPicker\'>" +
				"<h5>Sort By</h5>" +	
			"</div>").appendTo(designer);
			
			var trackerFieldSortDDL = elements.find(".trackerFieldList").clone()
				.addClass("trackerFieldListSort_active")
				.appendTo(trackerFieldSort)
				.html("");
				
			$(".trackerList_active").each(function() {
				trackerFieldSortDDL
					.append(elements.find(".tracker_option_" + $(this).val() + ",:first").clone());
			});
		},
		searchFor: function(set) {
			set.find(".fieldSearchPicker").remove();
			var fieldSearchPicker = $("<div class=\'fieldSearchPicker\' />").appendTo(set);
			
			var trackerFieldSearchDDL = elements.find(".trackerFieldList").clone()
				.addClass("trackerFieldListSearch_active")
				.appendTo(fieldSearchPicker)
				.html("<option value=\'\'>Search in field (optional)</option>");
			
			var trackerFieldSearch = $("<input type=\'text\' class=\'search_active\' />").appendTo(fieldSearchPicker);
			
			set.find(".trackerList_active").each(function() {
				trackerFieldSearchDDL
					.append(elements.find(".tracker_option_" + $(this).val()).clone());
			});
		}
	};
	
	$(".add_tracker_button").click(function() {
		objFactory.designSet();
		return false;
	});
	
	$(".view_button").click(function() {
		var reportUrl = "";
		
		var type = "csv";
		var csvFileName = "file.csv";
		var status = "";
		var fields = "";

		//joinable stuff
		var trackerIds = "";
		var itemIdFields = "";
		var sortFieldIds = "";
		var removeFieldIds = "";
		var showFieldIds = "";
		var dateFieldIds = "";
		var sortFieldNames = "";
		var search = "";
		var q = "";
		
		function valFromDesignerToUrl(o, param, fn, ch) {
			var result = [];
			
			fn = (fn ? fn : function(v) {return v;}); 
			
			var obj = designer.find(o)
			
			if (obj.length) {
				obj.each(function(i) {
					var v = $(this).val();
					if (v != undefined) {
						result.push(fn(v, i));
					}
				});
			
				reportUrl += (reportUrl ? "&" : "?") + param + "=" + result.join(ch !== null ? ch : ",");
			}
		}
		
		valFromDesignerToUrl(".trackerList_active", "trackerIds");
		valFromDesignerToUrl(".trackerFieldList_active", "itemIdFields", function(v, i) {
			var trackerJoinType = designer.find(".trackerJoinType").eq(i).val();
			if (trackerJoinType) {
				return v + "|outer";
			} else {
				return v;
			}
		});
		valFromDesignerToUrl(".tracker_checkbox:checked", "showFieldIds");
		valFromDesignerToUrl(".tracker_status_type:checked", "status", null, "");
		valFromDesignerToUrl(".trackerFieldListSort_active", "sortFieldIds");
		valFromDesignerToUrl(".trackerFieldListSearch_active", "fields", null, "|");
		valFromDesignerToUrl(".search_active", "q", null, "|");
		
		alert("tiki-tracker_export_join.php" + reportUrl);
		
		return false;
	});
	
	window.checkAll = function() {
		designer.find(".fieldPicker").find("input").attr("checked", "true");
	};
	
	window.uncheckAll = function() {
		designer.find(".fieldPicker").find("input").removeAttr("checked");
	};
');

$smarty->assign('trackers', $tikilib->fetchAll('select `trackerId`, `name` from `tiki_trackers`'));
$smarty->assign('trackerFields', $tikilib->fetchAll('
	select `tiki_tracker_fields`.`fieldId`, `tiki_tracker_fields`.`trackerId`, `tiki_tracker_fields`.`name` as fieldName, `tiki_trackers`.`name` as trackerName from `tiki_tracker_fields`
	left join `tiki_trackers` on `tiki_trackers`.`trackerId` = `tiki_tracker_fields`.`trackerId`
	
	order by `trackerId`, `position`
'));

// Display the template
$smarty->assign('mid', 'tiki-tracker_reports.tpl');
$smarty->display("tiki.tpl");
