<?php
// $Header: /cvsroot/tikiwiki/tiki/Attic/tiki-tracker_reports.php,v 1.1.2.1 2008/07/11 17:44:00 kerrnel22 Exp $
//
// Copyright (c)2002-2008
// Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of 
// authors. Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. 
// See license.txt for details.

/****************************************************************************
 ** TikiWiki Tracker Reporting Tool for Tiki 2.0+               07/15/2007 **
 ****************************************************************************
 ** Written by Mike Kerr (kerrnel22 at kerris.com)
 ** 
 ** This script is for building reports for the Trackers, either site-wide,
 ** as a subset of trackers, or for individual trackers.
 **   
 ** TO CVS COMMIT: (remove this before actually committing)
 ** ==============
 ** tiki-tracker_reports.php
 ** lib/trackers/reportlib.php
 ** templates/tiki-tracker_reports.tpl
 ** templates/tiki-admin_tracker.tpl
 ** templates/tiki-view_tracker.tpl
 ** 
 ****************************************************************************/

$tr_info = array();

// Initialization
require_once ('tiki-setup.php');

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

include_once ('lib/trackers/trackerlib.php');
include_once ('lib/trackers/reportlib.php');
$rptlib = new ReportLib($dbTiki);


if (isset($_REQUEST["submit"])) {
	if ($_REQUEST["submit"] == "cancel") {
		header('location: tiki-list_trackers.php');
	} else if ($_REQUEST["submit"] == "new") {
		header('location: tiki-tracker_reports.php');
	} else if ($_REQUEST["submit"] == "next" || $_REQUEST["submit"] == "submit") {
		// Store page 1 data
		$tr_info['trackers'] = $_REQUEST['trackers'];
		$tr_info['range'] = $_REQUEST['range'];
		$tr_info['granularity'] = $_REQUEST['granularity'];
		$tr_info['format'] = $_REQUEST['format'];
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
			//$tr_info['fields'] = $rptlib->getTrackerFields($_REQUEST['trackerId']);

			if ($tr_info['format'] == 'c') {	// CSV Output
				if ($tr_info['range'] == 'custom') {
					$fromRange = mktime(0,0,0,$tr_info['custom_fromMonth'],$tr_info['custom_fromDay'],$tr_info['custom_fromYear']);
					$toRange = mktime(23,59,59,$tr_info['custom_toMonth'],$tr_info['custom_toDay'],$tr_info['custom_toYear']);
					$csv_data = $rptlib->GetCSVReport($tr_info['trackers'], $tr_info['range'], $fromRange, $toRange);
				} else {
					$csv_data = $rptlib->GetCSVReport($tr_info['trackers'], $tr_info['range']);
				}
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

}

$smarty->assign('tr_info', $tr_info);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the page
$smarty->assign('mid', 'tiki-tracker_reports.tpl');
$smarty->display("tiki.tpl");

exit;

?>

