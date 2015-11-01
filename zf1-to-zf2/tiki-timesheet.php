<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$access->check_feature(array('feature_time_sheet','feature_trackers'));
$access->check_permission_either(array('tiki_p_view_trackers', 'tiki_p_create_tracker_items'));

global $user, $prefs, $tiki_p_admin;
$auto_query_args = array(
	'all',
	'profile',
	'page',
	'list'
);

$trackerId = (int)TikiLib::lib('trk')->get_tracker_by_name('Time sheet');
$smarty->assign("tiki_p_admin", $tiki_p_admin);
$smarty->assign("timeSheetProfileLoaded", $trackerId > 0 ? true : false);

$projectList = Tracker_Query::tracker("Project list")->byName()->query();

if (isset($_REQUEST['all'])) { //all views all sheet items
	$smarty->assign("all", true);

	$timeSheet = Tracker_Query::tracker("Time sheet")
		->byName()
		->query();
} else {//views only your items
	$smarty->assign("all", false);

	$timeSheet = Tracker_Query::tracker("Time sheet")
		->byName()
		->filter(array("field" => "Done by", "value" => $user))
		->query();
}

if (isset($projectList)) {
	if (isset($_REQUEST['save'])) {
		echo json_encode(
			$timeSheetNewInputs = Tracker_Query::tracker("Time sheet")
				->byName()
				->queryInput()
		);
		die;
	}

	$smarty->assign("projectList", $projectList);
	$smarty->assign("timeSheet", $timeSheet);
}

TikiLib::lib("sheet")->setup_jquery_sheet();

$headerlib = TikiLib::lib("header")
	->add_cssfile("vendor/jquery/jtrack/css/jtrack.css")
	->add_jsfile("vendor/jquery/jtrack/js/domcached-0.1-jquery.js")
	->add_jsfile("vendor/jquery/jtrack/js/jtrack.js")
	->add_jq_onready(
		"jTask.init();

	$.timesheetSpreadsheet = function() {
		var table = $('<table title=/>').attr('title', tr('Local Cache (Not Committed)'));
		table.append('<tr><td>Summary</td><td>Estimate</td><td>Time Spent</td></tr>');

		var rowI = 1;
		for (var namespace in $.DOMCached.getStorage()) {
			var row = $('<tr />').appendTo(table);

			row.append('<td>' + namespace + '</td>');
			row.append('<td>' + $.DOMCached.get('estimate', namespace) + '</td>');
			row.append('<td formula=\'ROUND(' + ($.DOMCached.get('timer', namespace) / 60) + ', 2)\'></td>');
			rowI++;
		}
		var row = $('<tr />').appendTo(table);
		row.append('<td>Totals</td>');
		row.append('<td formula=\'ROUND(SUM(B2:B' + rowI + '), 2)\'/>');
		row.append('<td formula=\'=ROUND(SUM(C2:C' + rowI + '), 2)\' />');

		$('#timesheetSpreadsheet').siblings().remove();

		var jS = $('#timesheetSpreadsheet').getSheet();
		if (jS) {
			$('#timesheetSpreadsheet')
				.unbind('visible')
				.visible(function() {
					jS.openSheet(table);
				});
		} else {
			$('#timesheetSpreadsheet')
				.visible(function() {
					$(this).sheet({
						buildSheet: table,
						editable: false,
						height: $('#jtrack-holder').height()
					});
				});
		}
	};

	$('.jtrack-create,.jtrack-update,.jtrack-remove,.jtrack-remove-all,.jtrack-cancel,.jtrack-power,#jtrack-button-remove,#jtrack-button-remove-all,#jtrack-button-create,#jtrack-button-update').on('click', function() {
		$.timesheetSpreadsheet();
	});

	$.timesheetSpreadsheet();

	$('#timeSheetSaved').visible(function() {
		$(this).sheet({
			buildSheet: true,
			editable: false,
			height: $('#jtrack-holder').height()
		});
	});

	$('#timeSheetCommit').click(function() {
		$('#timeSheetTabs').tikiModal(tr('Committing...'));
		var stack = [];
		$.getJSON('tiki-timesheet.php?save', function(inputs) {
			for (var namespace in $.DOMCached.getStorage()) {
				var summary = namespace + '',
				time =  $.DOMCached.get('timer', summary) / 60;
				stack.push(summary);

				var form = $('<form />').submit(function() {
					var fields = '';

					$.each(form.serializeArray(), function() {
						fields += '&fields[' + this.name + ']=' + this.value;
					});

					$.post('tiki-ajax_services.php?controller=tracker&trackerId=$trackerId&action=insert_item' + fields,function() {
						$.DOMCached.deleteNamespace(namespace);

						stack.pop();

						if (stack.length == 0) {
							document.location = document.location + '';
						}
					})
					.error(function() {
						$('#timeSheetTabs').tikiModal();
						alert(tr('Could not save'));
					});
					return false;
				});

				var input = {
					'Summary': $(inputs['Summary']),
					'Description': $(inputs['Description']),
					'Amount of time spent': $(inputs['Amount of time spent']),
					'Done by': $(inputs['Done by'])
				};

				input['Summary'].val(summary);
				input['Amount of time spent'].val(time);
				input['Done by'].val('" . addslashes($user) . "');
				form.append(input['Summary']);
				form.append(input['Description']);
				form.append(input['Amount of time spent']);
				form.append(input['Done by']);
				form.submit();
			}
		});

		return false;
	});

	$('#timeSheetTabs')
		.width($('#timeSheetTabs').parent().width())
		.tabs();"
	);

$smarty->assign('mid', 'tiki-timesheet.tpl');
// use tiki_full to include include CSS and JavaScript
$smarty->display("tiki.tpl");
