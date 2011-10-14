<?php
require_once('tiki-setup.php');
global $user;
TikiLib::lib("trkqry");

$projectList = TrackerQueryLib::tracker("Project list")->byName()->query();

if (isset($_REQUEST['all'])) { //all views all sheet items
	$timeSheet = TrackerQueryLib::tracker("Time sheet")->byName()->query();
} else {//views only your items
	$timeSheet = TrackerQueryLib::tracker("Time sheet")->byName()->search(array($user))->fields(array("Done by"))->query();
}

if(isset($projectList)) {
	if (isset($_REQUEST['save'])) {
		$_REQUEST['Done_by'] = $user;
		TikiLib::lib("trk")->replaceItemFromRequestValuesByName("Time sheet", array(
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
		for (var namespace in $.DOMCached.getStorage()) {
			var row = $('<tr />').appendTo(table);
			
			row.append('<td>' + namespace + '</td>');
			row.append('<td>' + $.DOMCached.get('estimate', namespace) + '</td>');
			row.append('<td formula=\'ROUND(' + ($.DOMCached.get('timer', namespace) / 60) + ', 2)\' />');
			rowI++;
		}
		var row = $('<tr />').appendTo(table);
		row.append('<td>Totals</td>');
		row.append('<td formula=\'ROUND(SUM(B2:B' + rowI + '), 2)\'/>');
		row.append('<td formula=\'=ROUND(SUM(C2:C' + rowI + '), 2)\' />');
		
		$('#timesheetSpreadsheet').siblings().remove();
		$('#timesheetSpreadsheet').parent().width(remainingWidth);
		
		var jS = $('#timesheetSpreadsheet').getSheet();
		if (jS) {
			jS.openSheet(table);
		} else {		
			$('#timesheetSpreadsheet')
				.html(table)
				.sheet({
					buildSheet: true,
					editable: false,
					height: $('#jtrack-holder').height()
				});
		}
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
		$('#timeSheetUnsaved').modal(tr('Committing...'));
		var stack = [];
		for (var namespace in $.DOMCached.getStorage()) {
			stack.push(namespace);
			$.post('tiki-timesheet.php?save', {
				'Summary': namespace,
				'Description': '',
				'Amount of time spent': $.DOMCached.get('timer', namespace) / 60
			}, function(o) {
				$.DOMCached.deleteNamespace(namespace);
				stack.pop();
				
				if (stack.length == 0) {
					$('#timeSheetUnsaved').modal();
					document.location = document.location + '';
				}
			});
		}
	});
");
$smarty->assign('mid', 'tiki-timesheet.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki.tpl");