<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-minical.php,v 1.26.2.1 2007-11-04 21:49:20 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$section = 'calendar';
require_once('tiki-setup.php');
if ($prefs['feature_ajax'] == "y") {
require_once ('lib/ajax/ajaxlib.php');
}
include_once('lib/minical/minicallib.php');

if ($prefs['feature_minical'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_minical");

	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("Must be logged to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_minical != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
}


if (!isset($_REQUEST["eventId"]))
	$_REQUEST["eventId"] = 0;

if (isset($_REQUEST['remove'])) {
  $area = 'delminicalevent';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$minicallib->minical_remove_event($user, $_REQUEST['remove']);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST['remove2'])) {
  $area = 'delminicalevent2';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
	$minicallib->minical_remove_event($user, $_REQUEST['eventId']);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST['delete'])) {
  $area = 'delminical';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
	foreach (array_keys($_REQUEST["event"])as $ev) {
		$minicallib->minical_remove_event($user, $ev);
	}
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST['day']) && isset($_REQUEST['mon']) && isset($_REQUEST['year'])) {
	$pdate = mktime(0, 0, 0, $_REQUEST['mon'], $_REQUEST['day'], $_REQUEST['year']);
} else {
	if (isset($_SESSION['thedate'])) {
		$pdate = mktime(0, 0, 0, date("m", $_SESSION['thedate']), date("d", $_SESSION['thedate']), date("Y", $_SESSION['thedate']));
	} else {
		$pdate = date("U");
	}
}

$yesterday = $pdate - 60 * 60 * 24;
$tomorrow = $pdate + 60 * 60 * 24;
$smarty->assign('yesterday', $yesterday);
$smarty->assign('tomorrow', $tomorrow);
$smarty->assign('day', date("d", $pdate));
$smarty->assign('mon', date("m", $pdate));
$smarty->assign('year', date("Y", $pdate));
$pdate_h = mktime(date("G"), date("i"), date("s"), date("m", $pdate), date("d", $pdate), date("Y", $pdate));
$smarty->assign('pdate', $pdate);
$smarty->assign('pdate_h', $pdate_h);

if (isset($_REQUEST['removeold'])) {
  $area = 'delminicaloldevents';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$minicallib->minical_remove_old($user, $pdate_h);
  } else {
    key_get($area);
  }
}

if ($_REQUEST["eventId"]) {
	$info = $minicallib->minical_get_event($user, $_REQUEST["eventId"]);
	$ev_pdate = $info['start'];
	$ev_pdate_h = $info['start'];
} else {
	$info = array();

	$info['title'] = '';
	$info['topicId'] = 0;
	$info['description'] = '';
	$info['start'] = mktime(date("H"), date("i"), date("s"), date("m", $pdate), date("d", $pdate), date("Y", $pdate));
	$info['duration'] = 60 * 60;
}

$smarty->assign('ev_pdate', $ev_pdate);
$smarty->assign('ev_pdate_h', $ev_pdate_h);

if (isset($_REQUEST['save'])) {
	check_ticket('minical');
	$start = mktime($_REQUEST['Time_Hour'], $_REQUEST['Time_Minute'],
		0, $_REQUEST['Date_Month'], $_REQUEST['Date_Day'], $_REQUEST['Date_Year']);

	$minicallib->minical_replace_event($user, $_REQUEST["eventId"], $_REQUEST["title"], $_REQUEST["description"],
		$start, ($_REQUEST['duration_hours'] * 60 * 60) + ($_REQUEST['duration_minutes'] * 60), $_REQUEST['topicId']);
	$info = array();
	$info['title'] = '';
	$info['topicId'] = 0;
	$info['description'] = '';
	$info['start'] = mktime(date("h"), date("i"), date("s"), date("m", $pdate), date("d", $pdate), date("Y", $pdate));
	$info['duration'] = 60 * 60;
	$_REQUEST["eventId"] = 0;
}

$smarty->assign('eventId', $_REQUEST["eventId"]);
$smarty->assign('info', $info);

//Check here the interval for the calendar
if (!isset($_REQUEST['view'])) {
	$_REQUEST['view'] = 'daily';
}

$smarty->assign('view', $_REQUEST['view']);

$minical_interval = $tikilib->get_user_preference($user, 'minical_interval', 60 * 60);
$minical_start_hour = $tikilib->get_user_preference($user, 'minical_start_hour', 9);
$minical_end_hour = $tikilib->get_user_preference($user, 'minical_end_hour', 20);
$minical_public = $tikilib->get_user_preference($user, 'minical_public', 'n');
$minical_upcoming = $tikilib->get_user_preference($user, 'minical_upcoming', 7);

// Interval is in hours
if ($_REQUEST['view'] == 'daily') {
	$tempdate = $pdate - $pdate % (24 * 60 * 60); /// added by Wells Wang to solve Mini Cal Event List Wrong Time BUG

	$slot_start = $tempdate + 60 * 60 * $minical_start_hour;
	$slot_end = $tempdate + 60 * 60 * $minical_end_hour;
	$interval = $minical_interval;
}

// end of modification
if ($_REQUEST['view'] == 'weekly') {
	$interval = 24 * 60 * 60;

	// Determine weekday
	$wd = date('w', $pdate);

	if ($wd == 0)
		$wd = 7;

	$wd = $wd - 1;
	// Now get the number of days to substract
	$week_start = $pdate - ($wd * 60 * 60 * 24);
	$week_end = $week_start + 60 * 60 * 24 * 7 - 1;
	$smarty->assign('week_start', $week_start);
	$smarty->assign('week_end', $week_end);
	$next_week_start = $week_end + 1;
	$smarty->assign('next_week_start', $next_week_start);
	$prev_week_start = $week_start - (60 * 60 * 24 * 7);
	$smarty->assign('prev_week_start', $prev_week_start);
	$slot_start = $pdate - ($wd * 60 * 60 * 24);
	$slot_end = $slot_start + 60 * 60 * 24 * 7 - 1;
}

if ($_REQUEST['view'] == 'daily' || $_REQUEST['view'] == 'weekly') {
	$smarty->assign('slot_start', $slot_start);

	$smarty->assign('slot_end', $slot_end);
	$events = $minicallib->minical_events_by_slot($user, $slot_start, $slot_end, $interval);
	$smarty->assign_by_ref('slots', $events);
}

// List view
if ($_REQUEST['view'] == 'list') {
	if (!isset($_REQUEST["sort_mode"])) {
		$sort_mode = 'start_asc';
	} else {
		$sort_mode = $_REQUEST["sort_mode"];
	}

	if (!isset($_REQUEST["offset"])) {
		$offset = 0;
	} else {
		$offset = $_REQUEST["offset"];
	}

	$smarty->assign_by_ref('offset', $offset);

	if (isset($_REQUEST["find"])) {
		$find = $_REQUEST["find"];
	} else {
		$find = '';
	}

	$smarty->assign('find', $find);

	$smarty->assign_by_ref('sort_mode', $sort_mode);

	if (isset($_SESSION['thedate'])) {
		$pdate = $_SESSION['thedate'];
	} else {
		$pdate = date("U");
	}

	$channels = $minicallib->minical_list_events($user, $offset, $maxRecords, $sort_mode, $find);
	$cant_pages = ceil($channels["cant"] / $maxRecords);
	$smarty->assign_by_ref('cant_pages', $cant_pages);
	$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

	if ($channels["cant"] > ($offset + $maxRecords)) {
		$smarty->assign('next_offset', $offset + $maxRecords);
	} else {
		$smarty->assign('next_offset', -1);
	}

	// If offset is > 0 then prev_offset
	if ($offset > 0) {
		$smarty->assign('prev_offset', $offset - $maxRecords);
	} else {
		$smarty->assign('prev_offset', -1);
	}

	$smarty->assign('channels', $channels["data"]);
}

$upcoming = $minicallib->minical_list_events_from_date($user, 0, $minical_upcoming, 'start_asc', '', $pdate_h);
$smarty->assign('upcoming', $upcoming['data']);

$hours = range(0, 23);
$smarty->assign('hours', $hours);
$minutes = range(0, 59);
$smarty->assign('minutes', $minutes);
$duration_hours = $info['duration'] / (60 * 60);
$duration_minutes = $info['duration'] % (60 * 60);
$smarty->assign('duration_hours', $duration_hours);
$smarty->assign('duration_minutes', $duration_minutes);

$topics = $minicallib->minical_list_topics($user, 0, -1, 'name_asc', '');
$smarty->assign('topics', $topics['data']);
include_once ('tiki-section_options.php');

include_once('tiki-mytiki_shared.php');
ask_ticket('minical');
if ($prefs['feature_ajax'] == "y") {
function user_minical_ajax() {
    global $ajaxlib, $xajax;
    $ajaxlib->registerTemplate("tiki-minical.tpl");
    $ajaxlib->registerFunction("loadComponent");
    $ajaxlib->processRequests();
}
user_minical_ajax();
$smarty->assign("mootab",'y');
}
$smarty->assign('mid', 'tiki-minical.tpl');
$smarty->display("tiki.tpl");

?>
