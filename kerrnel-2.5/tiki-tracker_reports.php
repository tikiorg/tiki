<?php
// $Header$
//
// Copyright (c)2002-2008
// Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of 
// authors. Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. 
// See license.txt for details.

/****************************************************************************
 ** TikiWiki Tracker Reporting Tool 2.0                         08/07/2008 **
 ****************************************************************************
 ** TO SVN COMMIT: (remove this before actually committing)
 ** ==============
 ** tiki-tracker_reports.php
 ** lib/trackers/reportlib.php
 ** templates/tiki-tracker_reports.tpl
 ** templates/tiki-admin_tracker.tpl	- add link
 ** templates/tiki-view_tracker.tpl		- add link
 ** 
 ****************************************************************************/

$tr_info = array();

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
include_once ('lib/trackers/reportlib.php');
$rptlib = new ReportLib($dbTiki);

if ($prefs['feature_trackers'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled: feature_trackers"));
	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if (isset($_REQUEST["submit"])) {
	if ($_REQUEST["submit"] == "cancel") {
		header('location: tiki-list_trackers.php');
	} else if ($_REQUEST["submit"] == "new") {
		header('location: tiki-tracker_reports.php');
	} else if ($_REQUEST["submit"] == "next" || $_REQUEST["submit"] == "submit") {
		// Store page 1 data
		$tr_info['format'] = $_REQUEST['format'];
		$tr_info['trackers'] = $_REQUEST['trackers'];
		$tr_info['delimiter'] = $_REQUEST['delimiter'];
		$tr_info['otherdel'] = $_REQUEST['otherdel'];
		$tr_info['textqual'] = $_REQUEST['textqual'];
		$tr_info['fieldcr'] = $_REQUEST['fieldcr'];
		$tr_info['parse'] = $_REQUEST['parse'];
		$tr_info['range'] = $_REQUEST['range'];
		$tr_info['datekey'] = $_REQUEST['datekey'];
		$tr_info['canfields'] = $_REQUEST['canfields'];
		$tr_info['fieldsao'] = $_REQUEST['fieldsao'];
		$tr_info['fields'] = $_POST['fields'];
		$tr_info['submit'] = $_REQUEST['submit'];
		if (isset($_REQUEST['custom_fromMonth']))
			$tr_info['custom_fromMonth'] = $_REQUEST['custom_fromMonth'];
		if (isset($_REQUEST['custom_fromDay']))
			$tr_info['custom_fromDay'] = $_REQUEST['custom_fromDay'];
		if (isset($_REQUEST['custom_fromYear']))
			$tr_info['custom_fromYear'] = $_REQUEST['custom_fromYear'];
		if (isset($_REQUEST['custom_toMonth']))
			$tr_info['custom_toMonth'] = $_REQUEST['custom_toMonth'];
		if (isset($_REQUEST['custom_toDay']))
			$tr_info['custom_toDay'] = $_REQUEST['custom_toDay'];
		if (isset($_REQUEST['custom_toYear']))
			$tr_info['custom_toYear'] = $_REQUEST['custom_toYear'];

		// Store page 2 data
		if ($_REQUEST["submit"] == "next") {
			//$tr_info['fields'] = $rptlib->getTrackerFields($_REQUEST['trackerId']);

		} else if ($_REQUEST["submit"] == "submit") {
			if (!isset($_REQUEST['trackerId'])) {
				$smarty->assign('msg', tra('No tracker indicated'));
				$smarty->display('error.tpl');
				die;
			} else {
				$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker');
				if ($tiki_p_view_trackers != 'y') {
						$smarty->assign('errortype', 401);
						$smarty->assign('msg', tra('You do not have permission to use this feature'));
						$smarty->display("error.tpl");
						die;
				}
			}

			if ($tr_info['format'] == 'c') {	// CSV Output
				if ($tr_info['range'] == 'custom') {
					$tr_info['fromRange'] = mktime(0,0,0,$tr_info['custom_fromMonth'],$tr_info['custom_fromDay'],$tr_info['custom_fromYear']);
					$tr_info['toRange'] = mktime(23,59,59,$tr_info['custom_toMonth'],$tr_info['custom_toDay'],$tr_info['custom_toYear']);
				}

				$csv_data = $rptlib->GetCSVReport($tr_info);

				if ($csv_data[0] != 'ok') {
					echo "ERROR!\n";
				} else {
					// Send data as CSV format to inline browser for display 
					// or to save
					header( 'Content-type: application/ms-excel' );
					header( 'Content-Disposition: inline, filename=tracker_report.csv' );
					echo $csv_data[1];
					exit;
				}
			}
		}
	}

// New form
} else {

	if (isset($_GET["trackerId"])) {
		$trackerId = $_GET["trackerId"];
	} else {
		$trackerId = null;
	}
	$smarty->assign('trackerId', $trackerId);

	$tr_info = $rptlib->getTrackers();
	foreach ($tr_info as $key => $value) {
		$tr_info[$key]["dkfields"] = $rptlib->getTrackerFields($value["trackerId"], 'fj', true, true);
		$tr_info[$key]["fields"] = $rptlib->getTrackerFields($value["trackerId"], '', true);
		$tr_info[$key] = array_merge($tr_info[$key], $rptlib->get_tracker_options($value["trackerId"]));
	}
}

$smarty->assign('tr_info', $tr_info);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the page
$smarty->assign('headtitle', 'Tracker reporting');
$smarty->assign('mid', 'tiki-tracker_reports.tpl');
$smarty->display("tiki.tpl");

exit;

?>

