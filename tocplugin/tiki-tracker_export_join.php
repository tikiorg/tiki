<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (isset($_REQUEST['time']) == true) {
	$starttime = microtime();
	$startarray = explode(" ", $starttime);
	$starttime = $startarray[1] + $startarray[0];
}

require_once('tiki-setup.php');

if ($tiki_p_admin_trackers != 'y') {
	$access->display_error('', tra('Permission denied').": ". 'tiki_p_admin_trackers', '403');
}

$trklib = TikiLib::lib("trk");

//TODO: This needs rewritten to match tiki
/**
 * @param $fieldIds
 * @param $tracker
 * @return mixed
 */
function dateFormat($fieldIds, $tracker)
{
	foreach ($tracker as $key => $item) {
		foreach ($fieldIds as $fieldId) {
			if (isset($item[$fieldId]) == true && is_numeric($item[$fieldId])) {
				$tracker[$key][$fieldId] = $item[$fieldId] = date("F j, Y, g:i a", $item[$fieldId]);
			}
		}
	}
	return $tracker;
}

//TODO: Find alternative for obtaining querystring/form data
/**
 * @param $name
 * @param null $default
 */
function defVal($name, $default = null)
{
	if (isset($_GET[$name]) == true) {
		$_REQUEST[$name] = $_GET[$name];
	}

	if (isset($default) == true) {
		if (isset($_REQUEST[$name]) == false) {
			$_REQUEST[$name] = $default;
		}
	}
}

//none join stuff
defVal('type', 'csv');
defVal('csvFileName', "file.csv");
defVal('status');
defVal('fields');

//joinable stuff
defVal('trackerIds'); //Order Items
defVal('itemIdFields');
defVal('sortFieldIds');
defVal('removeFieldIds');
defVal('showFieldIds');
defVal('dateFieldIds', "45,16,158,159,103");
defVal('sortFieldNames');
defVal('search');
defVal('q');
defVal('start');
defVal('end');

//TODO: integrate into tracker query lib
/**
 * @param $param
 */
function splitToTracker($param)
{
	if (isset($_REQUEST[$param])) {
		$_REQUEST[$param] = explode("|", $_REQUEST[$param]);
		foreach ($_REQUEST[$param] as $key => $field) {
			$_REQUEST[$param][$key] = explode(',', $_REQUEST[$param][$key]);
		}
	}
}

splitToTracker('fields');
splitToTracker('search');
splitToTracker('q');

if (isset($_REQUEST['status']))				$_REQUEST['status'] = explode(",", $_REQUEST['status']);
if (isset($_REQUEST['removeFieldIds']))		$_REQUEST['removeFieldIds'] = explode(",", $_REQUEST['removeFieldIds']);
if (isset($_REQUEST['showFieldIds']))		$_REQUEST['showFieldIds'] = explode(",", $_REQUEST['showFieldIds']);
if (isset($_REQUEST['sortFieldIds']))		$_REQUEST['sortFieldIds'] = explode(",", $_REQUEST['sortFieldIds']);
if (isset($_REQUEST['dateFieldIds']))		$_REQUEST['dateFieldIds'] = explode(",", $_REQUEST['dateFieldIds']);
if (isset($_REQUEST['sortFieldNames']))		$_REQUEST['sortFieldNames'] = explode(",", $_REQUEST['sortFieldNames']);
if (isset($_REQUEST['start']))				$_REQUEST['start'] = explode(",", $_REQUEST['start']);
if (isset($_REQUEST['end']))				$_REQUEST['end'] = explode(",", $_REQUEST['end']);

$_REQUEST['trackerIds'] = explode(",", $_REQUEST['trackerIds']);
$_REQUEST['itemIdFields'] = explode(",", $_REQUEST['itemIdFields']);

$trackerPrimary = array();
if (isset($_REQUEST['trackerIds']) == true) {
	$i = 0;
	foreach ($_REQUEST['trackerIds'] as $key => $trackerId) {
		if ($key == 0) {
			$trackerPrimary = Tracker_Query::tracker($trackerId)
				->start($_REQUEST['start'][$key])
				->end($_REQUEST['end'][$key])
				->equals($_REQUEST['q'][$key])
				->search($_REQUEST['search'][$key])
				->fields($_REQUEST['fields'][$key])
				->status($_REQUEST['status'][$key])
				->query();
		} else {
			$joinVars = $_REQUEST['itemIdFields'][$key - 1];
			$joinVars = explode('|', $joinVars);

			$trackerPrimary = Tracker_Query::join_trackers(
				$trackerPrimary,
				Tracker_Query::tracker($trackerId)
				->start($_REQUEST['start'][$key])
				->end($_REQUEST['end'][$key])
				->equals($_REQUEST['q'][$key])
				->search($_REQUEST['search'][$key])
				->fields($_REQUEST['fields'][$key])
				->status($_REQUEST['status'][$key])
				->query(),
				$joinVars[0],
				$joinVars[1]
			);
		}
		$i++;
	}
}

if (isset($_REQUEST['sortFieldIds']) == true) {
	Tracker_Query::arfsort($trackerPrimary, $_REQUEST['sortFieldIds']);
}

if (
		isset($_REQUEST['removeFieldIds']) == true ||
		isset($_REQUEST['showFieldIds']) == true
	) {
	$trackerPrimary = Tracker_Query::filter_fields_from_tracker_query($trackerPrimary, $_REQUEST['removeFieldIds'], $_REQUEST['showFieldIds']);
}

if (isset($_REQUEST['dateFieldIds'])) {
	$trackerPrimary = dateFormat($_REQUEST['dateFieldIds'], $trackerPrimary);
}

$trackerPrimary = Tracker_Query::prepend_field_header($trackerPrimary, $_REQUEST['sortFieldNames']);

if (isset($_REQUEST['time']) == true) {
	$endtime = microtime();
	$endarray = explode(" ", $endtime);
	$endtime = $endarray[1] + $endarray[0];
	$totaltime = $endtime - $starttime;
	$totaltime = round($totaltime, 5);
	echo "This page loaded in $totaltime seconds.\n\n\n";
}

if ($_REQUEST['type'] == 'csv' && count($trackerPrimary) > 0) {
	print_r(Tracker_Query::to_csv($trackerPrimary));
}
