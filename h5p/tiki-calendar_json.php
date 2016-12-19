<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'calendar';
require_once ('tiki-setup.php');

$calendarlib = TikiLib::lib('calendar');
$categlib = TikiLib::lib('categ');
include_once ('lib/newsletters/nllib.php');

$headerlib->add_cssfile('themes/base_files/feature_css/calendar.css', 20);
# perms are
# 	$tiki_p_view_calendar
# 	$tiki_p_admin_calendar
# 	$tiki_p_change_events
# 	$tiki_p_add_events
$access->check_feature('feature_calendar');

$maxSimultaneousWeekViewEvents = 3;

$myurl = 'tiki-calendar.php';
$exportUrl = 'tiki-calendar_export_ical.php';
$iCalAdvParamsUrl = 'tiki-calendar_params_ical.php';
$bufid = array();
$bufdata = array();
$editable = array();
if (!isset($cookietab)) { 
	$cookietab = '1';
}
$rawcals = $calendarlib->list_calendars();
$cals_info = $rawcals;
$rawcals['data'] = Perms::filter(array( 'type' => 'calendar' ), 'object', $rawcals['data'], array( 'object' => 'calendarId' ), 'view_calendar');
$viewOneCal = $tiki_p_view_calendar;
$modifTab = 0;

$minHourOfDay = 12;
$maxHourOfDay = 12;
$manyEvents = array();

foreach ($rawcals["data"] as $cal_data) {
	$cal_id = $cal_data['calendarId'];
	$minHourOfDay = min($minHourOfDay, intval($cal_data['startday']/3600));
	$maxHourOfDay = max($maxHourOfDay, intval(($cal_data['endday']+1)/3600));
	if ($tiki_p_admin == 'y') {
		$cal_data["tiki_p_view_calendar"] = 'y';
		$cal_data["tiki_p_view_events"] = 'y';
		$cal_data["tiki_p_add_events"] = 'y';
		$cal_data["tiki_p_change_events"] = 'y';
	} elseif ($cal_data["personal"] == "y") {
		if ($user) {
			$cal_data["tiki_p_view_calendar"] = 'y';
			$cal_data["tiki_p_view_events"] = 'y';
			$cal_data["tiki_p_add_events"] = 'y';
			$cal_data["tiki_p_change_events"] = 'y';
		} else {
			$cal_data["tiki_p_view_calendar"] = 'n';
			$cal_data["tiki_p_view_events"] = 'y';
			$cal_data["tiki_p_add_events"] = 'n';
			$cal_data["tiki_p_change_events"] = 'n';
		}
	} else {		
		$calperms = Perms::get(array( 'type' => 'calendar', 'object' => $cal_id ));
		$cal_data["tiki_p_view_calendar"] = $calperms->view_calendar ? 'y' : 'n';
		$cal_data["tiki_p_view_events"] = $calperms->view_events ? 'y' : 'n';
		$cal_data["tiki_p_add_events"] = $calperms->add_events ? 'y' : 'n';
		$cal_data["tiki_p_change_events"] = $calperms->change_events ? 'y' : 'n';
	}
	if ($cal_data["tiki_p_view_calendar"] == 'y') {
		$viewOneCal = 'y';
		$bufid[] = $cal_id;
		$bufdata["$cal_id"] = $cal_data;
	}
	if ($cal_data["tiki_p_view_events"] == 'y') {
		$visible[] = $cal_id;
	}
	if ($cal_data["tiki_p_add_events"] == 'y') {
		$modifTab = 1;
	}
	if ($cal_data["tiki_p_change_events"] == 'y') {
		$modifTab = 1;
		$editable[] = $cal_id;
		$visible[] = $cal_id;
	}
}

if ($viewOneCal != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to view the calendar"));
	$smarty->display("error.tpl");
	die;
}

$listcals = $bufid;
$infocals["data"] = $bufdata;

// set up list of groups
$use_default_calendars = false;
if (isset($_REQUEST["calIds"])and is_array($_REQUEST["calIds"])and count($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array_intersect($_REQUEST["calIds"], $listcals);
	if ( !empty($user) ) {
		$tikilib->set_user_preference($user, 'default_calendars', serialize($_SESSION['CalendarViewGroups']));
	}
} elseif (isset($_REQUEST["calIds"])and !is_array($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array_intersect(array($_REQUEST["calIds"]), $listcals);
	if ( !empty($user) ) {
		$tikilib->set_user_preference($user, 'default_calendars', serialize($_SESSION['CalendarViewGroups']));
	}
} elseif (!empty($_REQUEST['allCals'])) {
	$_SESSION['CalendarViewGroups'] = $listcals;
} elseif (!isset($_SESSION['CalendarViewGroups']) || !empty($_REQUEST['allCals'])) {
	$use_default_calendars = true;
} elseif (isset($_REQUEST["refresh"])and !isset($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array();
} elseif ( ! empty($user) || ! isset($_SESSION['CalendarViewGroups']) ) {
	$use_default_calendars = true;
}

if ( $use_default_calendars ) {
	if ( $prefs['feature_default_calendars'] == 'y' ) {
		$_SESSION['CalendarViewGroups'] = array_intersect(is_array($prefs['default_calendars']) ? $prefs['default_calendars'] : unserialize($prefs['default_calendars']), $listcals);
	} elseif ( ! empty($user) ) {
		$user_default_calendars = $tikilib->get_user_preference($user, 'default_calendars', $listcals);
		if ( is_string($user_default_calendars) ) $user_default_calendars = unserialize($user_default_calendars);
		$_SESSION['CalendarViewGroups'] = $user_default_calendars;
	} else {
		$_SESSION['CalendarViewGroups'] = $listcals;
	}
}

$thiscal = array();
$checkedCals = array();

foreach ($listcals as $thatid) {
	if (is_array($_SESSION['CalendarViewGroups']) && (in_array("$thatid", $_SESSION['CalendarViewGroups']))) {
		$thiscal["$thatid"] = 1;
		$checkedCals[] = $thatid;
	} else {
		$thiscal["$thatid"] = 0;
	}
}

//include_once("tiki-calendar_setup.php");

// Calculate all the displayed days for the selected calendars
/*
$viewdays = array();
foreach ($_SESSION['CalendarViewGroups'] as $calendar) {
	$info = $calendarlib->get_calendar($calendar);
	if (is_array($info['viewdays']))
		$viewdays = array_merge($info['viewdays'],$viewdays);
}
if (empty($viewdays)) {
		$viewdays = array(0,1,2,3,4,5,6);
}
sort($viewdays, SORT_NUMERIC);
$viewdays = array_map("correct_start_day", array_unique($viewdays));
$viewdays2 = array_values($viewdays);
*/

if (isset($_REQUEST['sort_mode'])) $sort_mode = $_REQUEST['sort_mode'];

$viewstart = $_REQUEST['start'];
$viewend = $_REQUEST['end'];

if ($_SESSION['CalendarViewGroups']) {
	$listevents = $calendarlib->list_raw_items($_SESSION['CalendarViewGroups'], $user, $viewstart, $viewend, 0, -1);
	for ($i = count($listevents) - 1; $i >= 0; --$i) {
		$listevents[$i]['editable'] = in_array($listevents[$i]['calendarId'], $editable)? "y": "n";
		$listevents[$i]['visible'] = in_array($listevents[$i]['calendarId'], $visible)? "y": "n";
	}
} else {
	$listevents = array();
}


if ($prefs['feature_theme_control'] == 'y'	and isset($_REQUEST['calIds'])) {
	$cat_type = "calendar";
	$cat_objid = $_REQUEST['calIds'][0]; 
}

$events = array();
foreach ($listevents as $event) {
	if ($event['editable'] === 'y' and $cal_data["tiki_p_change_events"] == 'y') {
		$url = 'tiki-calendar_edit_item.php?fullcalendar=y&calitemId='.$event['calitemId']; 
	} else {
		$url = 'tiki-calendar_edit_item.php?viewcalitemId='.$event['calitemId']; // removed fullcalendar=y param to prevent display without tpl for anons in some setups
	}
	$events[] = array ( 'id' => $event['calitemId'],
											'title' => $event['name'],
											'description' => !empty($event["description"]) ? $tikilib->parse_data($event["description"], array('is_html' => $prefs['calendar_description_is_html'] === 'y')) : "",
											'url' => $url,
											'allDay' => $event['allday'] != 0 ,
											'start' => $event['date_start'],
											'end' => $event['date_end'],
											'editable' => $event['editable'] === 'y',
											'color' => '#'.$cals_info['data'][$event['calendarId']]['custombgcolor'],
											'textColor' => '#'.$cals_info['data'][$event['calendarId']]['customfgcolor']);
}

echo json_encode($events);
