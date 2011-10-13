<?php
require_once('tiki-setup.php');
TikiLib::lib("trkqry");

$projectList = TrackerQueryLib::tracker("Project list")->byName()->query();
$timeSheet = TrackerQueryLib::tracker("Time sheet")->byName()->query();

function processItem($trackerName, $fieldNames, $fieldValues, $itemId = 0, $i = 0) {
	$trklib = TikiLib::lib("trk");
	$trackerId = $trklib->get_tracker_by_name($trackerName);
	$trackerDefinition = Tracker_Definition::get($trackerId);
	$fields = $trackerDefinition->getFieldsIdKeys();

	foreach ($fields as $key => $field) {
		$fieldName = $field['name'];
		$fieldValue = ($i > 0 ? $fieldValues[str_replace(" ", "_", $fieldName)][$i] : $fieldValues[str_replace(" ", "_", $fieldName)]);
		$fields[$key]['value'] = (empty($fieldValue) ? '' : $fieldValue);
	}

	return $trklib->replace_item($trackerId, $itemId, array("data"=>$fields), 'o');
}
if(isset($projectList)) {
	if (isset($_REQUEST['save'])) {	
		processItem("Time sheet", array(
			"Summary",
            "Associated project",
            "Description",
            "Amount of time spent",
            "Done by"
		), $_REQUEST); 
		die;
	}

	$timeSheetProfileLoaded = true;
	$smarty->assign("timeSheetProfileLoaded", $timeSheetProfileLoaded);
	$smarty->assign("projectList", $projectList);
	$smarty->assign("timeSheet", $timeSheet);
}

TikiLib::lib("sheet")->setup_jquery_sheet();

$headerlib = TikiLib::lib("header");

$headerlib->add_cssfile("lib/jquery/jtrack/css/jtrack.css");
$headerlib->add_jsfile("lib/jquery/jtrack/js/domcached-0.1-jquery.js");
$headerlib->add_jsfile("lib/jquery/jtrack/js/jtrack.js");


$headerlib->add_jq_onready("
	jTask.init();
	var remainingWidth = $('#timeSheetUnsaved').width() - $('#jtrack-holder').width();
	
	$.timesheetSpreadsheet = function() {
		var table = $('<table title=/>').attr('title', tr('Local Cache (Not Committed)'));
		table.append('<tr><td>Summary</td><td>Estimate</td><td>Time Spent</td></tr>');
		
		var rowI = 1;
		for (var item in $.DOMCached.storage) {
			var row = $('<tr />').appendTo(table);
			
			row.append('<td>' + item + '</td>');
			row.append('<td>' + $.DOMCached.storage[item].estimate.value + '</td>');
			row.append('<td formula=\'ROUND(' + ($.DOMCached.storage[item].timer.value ? $.DOMCached.storage[item].timer.value / 60 : 0) + ')\' />');
			rowI++;
		}
		var row = $('<tr />').appendTo(table);
		row.append('<td>Totals</td>');
		row.append('<td formula=\'ROUND(SUM(B2:B' + rowI + '))\'/>');
		row.append('<td formula=\'=ROUND(SUM(C2:C' + rowI + '))\' />');
		
		$('#timesheetSpreadsheet').siblings().remove();
		$('#timesheetSpreadsheet').parent().width(remainingWidth);
		
		$('#timesheetSpreadsheet')
			.html(table)
			.sheet({
				buildSheet: true,
				editable: false,
				height: $('#jtrack-holder').height()
			});
	};
	
	$('.jtrack-create,.jtrack-update,.jtrack-remove,.jtrack-remove-all,.jtrack-cancel,.jtrack-power,#jtrack-button-remove,#jtrack-button-remove-all,#jtrack-button-create,#jtrack-button-update').live('click', function() {
		$.timesheetSpreadsheet();
	});
	
	$.timesheetSpreadsheet();
	
	$('#timeSheetSaved').sheet({
		buildSheet: true,
		editable: false
	});
	
	$('#timeSheetCommit').click(function() {
		for (var item in $.DOMCached.storage) {
			$.post('tiki-timesheet.php?save', {
				'Summary': item,
				'Description': '',
				'Amount of time spent': $.DOMCached.storage[item].timer.value / 60
			}, function(o) {
				//delete $.DOMCached.storage[item];
			});
		}
	});
");
$smarty->assign('mid', 'tiki-timesheet.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki.tpl");