<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'calendar';
require_once ('tiki-setup.php');
include_once ('lib/minical/minicallib.php');
$access->check_feature('feature_minical');
$access->check_user($user);
$access->check_permission('tiki_p_minical');
if (!isset($_REQUEST["eventId"])) $_REQUEST["eventId"] = 0;
if (isset($_REQUEST['remove'])) {
	$access->check_authenticity();
	$minicallib->minical_remove_event($user, $_REQUEST['remove']);
}
if (isset($_REQUEST['remove2'])) {
	$access->check_authenticity();
	$minicallib->minical_remove_event($user, $_REQUEST['eventId']);
}
if (isset($_REQUEST['delete'])) {
	$access->check_authenticity();
	foreach(array_keys($_REQUEST["event"]) as $ev) {
		$minicallib->minical_remove_event($user, $ev);
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
	$access->check_authenticity();
	$minicallib->minical_remove_old($user, $pdate_h);
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
	//Convert 12-hour clock hours to 24-hour scale to compute time
	if (!empty($_REQUEST['Time_Meridian'])) {
		$_REQUEST['Time_Hour'] = date('H', strtotime($_REQUEST['Time_Hour'] . ':00 ' . $_REQUEST['Time_Meridian']));
	}
	$start = mktime($_REQUEST['Time_Hour'], $_REQUEST['Time_Minute'], 0, $_REQUEST['Date_Month'], $_REQUEST['Date_Day'], $_REQUEST['Date_Year']);
	$minicallib->minical_replace_event($user, $_REQUEST["eventId"], $_REQUEST["title"], $_REQUEST["description"], $start, ($_REQUEST['duration_hours'] * 60 * 60) + ($_REQUEST['duration_minutes'] * 60), $_REQUEST['topicId']);
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
	if ($wd == 0) $wd = 7;
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
	$smarty->assign_by_ref('cant_pages', $channels["cant"]);
	$smarty->assign('channels', $channels["data"]);
}
$upcoming = $minicallib->minical_list_events_from_date($user, 0, $minical_upcoming, 'start_asc', '', $pdate_h);
$smarty->assign('upcoming', $upcoming['data']);
//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
include_once ('lib/userprefs/userprefslib.php');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

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
include_once ('tiki-mytiki_shared.php');
ask_ticket('minical');
$smarty->assign('mid', 'tiki-minical.tpl');
$smarty->display("tiki.tpl");
