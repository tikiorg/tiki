<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-calendar.php,v 1.28 2004-01-15 09:56:26 redflo Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/calendar/calendarlib.php');

# perms are 
# 	$tiki_p_view_calendar
# 	$tiki_p_admin_calendar
# 	$tiki_p_change_events
# 	$tiki_p_add_events
if ($feature_calendar != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_calendar");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_view_calendar != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view the calendar"));
	$smarty->display("error.tpl");
	die;
}
$bufid = array();
$bufdata = array();
$modifiable = array();
$rawcals = $calendarlib->list_calendars();

foreach ($rawcals["data"] as $cal_id=>$cal_data) {
	if ($tiki_p_admin == 'y') {
		$cal_data["tiki_p_view_calendar"] = 'y';
		$cal_data["tiki_p_add_events"] = 'y';
		$cal_data["tiki_p_change_events"] = 'y';
	} else {
		if ($userlib->object_has_one_permission($cal_id,'calendar')) {
			if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_view_calendar')) {
				$cal_data["tiki_p_view_calendar"] = 'y';
			} else {
				$cal_data["tiki_p_view_calendar"] = 'n';
			}
			if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_add_events')) {
				$cal_data["tiki_p_add_events"] = 'y';
			} else {
				$cal_data["tiki_p_add_events"] = 'n';
			}
			if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_change_events')) {
				$cal_data["tiki_p_change_events"] = 'y';
			} else {
				$cal_data["tiki_p_change_events"] = 'n';
			}
			if ($userlib->object_has_permission($user, $cal_id, 'calendar', 'tiki_p_admin_calendar')) {
				$cal_data["tiki_p_view_calendar"] = 'y';
				$cal_data["tiki_p_add_events"] = 'y';
				$cal_data["tiki_p_change_events"] = 'y';
			}
		} else {
			$cal_data["tiki_p_view_calendar"] = $tiki_p_view_calendar;
			$cal_data["tiki_p_add_events"] = $tiki_p_add_events;
			$cal_data["tiki_p_change_events"] = $tiki_p_change_events;
		}
	}
	if ($cal_data["tiki_p_view_calendar"] == 'y') {
		$bufid[] = $cal_id;
		$bufdata["$cal_id"] = $cal_data;
	}
	if (($cal_data["tiki_p_add_events"] == 'y') or ($cal_data["tiki_p_change_events"] == 'y')) {
		$modifiable[] = $cal_id;
	}
}
$listcals = $bufid;
$infocals["data"] = $bufdata;

$smarty->assign('infocals', $infocals["data"]);
$smarty->assign('listcals', $listcals);
$smarty->assign('modifiable', count($modifiable));

// set up list of groups 
if (isset($_REQUEST["calIds"])and is_array($_REQUEST["calIds"])and count($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = $_REQUEST["calIds"];
} elseif (!isset($_SESSION['CalendarViewGroups'])) {
	$_SESSION['CalendarViewGroups'] = $listcals;
} elseif (isset($_REQUEST["refresh"])and !isset($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array();
}

// setup list of tiki items displayed
if (isset($_REQUEST["tikicals"])and is_array($_REQUEST["tikicals"])and count($_REQUEST["tikicals"])) {
	$_SESSION['CalendarViewTikiCals'] = $_REQUEST["tikicals"];
} elseif (!isset($_SESSION['CalendarViewTikiCals'])) {
	$_SESSION['CalendarViewTikiCals'] = array();
} elseif (isset($_REQUEST["refresh"])and !isset($_REQUEST["tikicals"])) {
	$_SESSION['CalendarViewTikiCals'] = array();
}


// that should be a global array set up in tiki-setup.php
$tikiItems = array(
	"wiki" => array(
	"label" => tra("Wiki"),
	"feature" => "$feature_wiki",
	"right" => "$tiki_p_view"
),
	"gal" => array(
	"label" => tra("Image Gallery"),
	"feature" => "$feature_galleries",
	"right" => "$tiki_p_view_image_gallery"
),
	"art" => array(
	"label" => tra("Articles"),
	"feature" => "$feature_articles",
	"right" => "$tiki_p_read_article"
),
	"blog" => array(
	"label" => tra("Blogs"),
	"feature" => "$feature_blogs",
	"right" => "$tiki_p_read_blog"
),
	"forum" => array(
	"label" => tra("Forums"),
	"feature" => "$feature_forums",
	"right" => "$tiki_p_forum_read"
),
	"dir" => array(
	"label" => tra("Directory"),
	"feature" => "$feature_directory",
	"right" => "$tiki_p_view_directory"
),
	"fgal" => array(
	"label" => tra("File Gallery"),
	"feature" => "$feature_file_galleries",
	"right" => "$tiki_p_view_file_gallery"
),
	"faq" => array(
	"label" => tra("FAQs"),
	"feature" => $feature_faqs,
	"right" => $tiki_p_view_faqs
),
	"quiz" => array(
	"label" => tra("Quizzes"),
	"feature" => $feature_quizzes,
	"right" => $tiki_p_take_quiz
),
	"track" => array(
	"label" => tra("Trackers"),
	"feature" => "$feature_trackers",
	"right" => "$tiki_p_view_trackers"
),
	"surv" => array(
	"label" => tra("Survey"),
	"feature" => "$feature_surveys",
	"right" => "$tiki_p_take_survey"
),
	"nl" => array(
	"label" => tra("Newsletter"),
	"feature" => "$feature_newsletters",
	"right" => "$tiki_p_subscribe_newsletters"
),
	"eph" => array(
	"label" => tra("Ephemerides"),
	"feature" => "$feature_eph",
	"right" => "y"
),
	"chart" => array(
	"label" => tra("Charts"),
	"feature" => "$feature_charts",
	"right" => "$tiki_p_view_chart"
)
);

$smarty->assign('tikiItems', $tikiItems);

$smarty->assign('displayedcals', $_SESSION['CalendarViewGroups']);
$smarty->assign('displayedtikicals', $_SESSION['CalendarViewTikiCals']);

$thiscal = array();

foreach ($listcals as $thatid) {
	if (is_array($_SESSION['CalendarViewGroups']) && (in_array("$thatid", $_SESSION['CalendarViewGroups']))) {
		$thiscal["$thatid"] = 1;
	} else {
		$thiscal["$thatid"] = 0;
	}
}

$smarty->assign('thiscal', $thiscal);

$tikical = array();

foreach ($_SESSION['CalendarViewTikiCals'] as $calt) {
	$tikical["$calt"] = 1;
}

$trunc = "12"; // put in a pref, number of chars displayed in cal cells
$smarty->assign('tikical', $tikical);

if (isset($_REQUEST["todate"]) && $_REQUEST['todate']) {
	$_SESSION['CalendarFocusDate'] = $_REQUEST['todate'];
} elseif (isset($_SESSION['CalendarFocusDate']) && $_SESSION['CalendarFocusDate']) {
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
} else {
	$_SESSION['CalendarFocusDate'] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	$_REQUEST["todate"] = $_SESSION['CalendarFocusDate'];
}

$focusdate = $_REQUEST['todate'];
list($focus_day, $focus_month, $focus_year) = array(
	date("d", $focusdate),
	date("m", $focusdate),
	date("Y", $focusdate)
);
$focusdate = mktime(0,0,0,$focus_month,$focus_day,$focus_year);

if (isset($_REQUEST["viewmode"]) and $_REQUEST["viewmode"]) {
	$_SESSION['CalendarViewMode'] = $_REQUEST["viewmode"];
}

if (!isset($_SESSION['CalendarViewMode']) or !$_SESSION['CalendarViewMode']) {
	$_SESSION['CalendarViewMode'] = 'week';
}
$smarty->assign('viewmode', $_SESSION['CalendarViewMode']);

if (isset($_REQUEST["delete"])and ($_REQUEST["delete"]) and isset($_REQUEST["calitemId"])) {
	$calendarlib->drop_item($user, $_REQUEST["calitemId"]);
	$_REQUEST["calitemId"] = 0;
	setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1)),"tab1");	
}

if (!isset($_REQUEST["calitemId"]))
	$_REQUEST["calitemId"] = 0;

if (!isset($_REQUEST["locationId"]))
	$_REQUEST["locationId"] = 0;

if (!isset($_REQUEST["categoryId"]))
	$_REQUEST["categoryId"] = 0;

if (!isset($_REQUEST["organizers"]))
	$_REQUEST["organizers"] = "";

if (!isset($_REQUEST["participants"]))
	$_REQUEST["participants"] = "";

if (!isset($_REQUEST["newloc"]))
	$_REQUEST["newloc"] = "";

if (!isset($_REQUEST["newcat"]))
	$_REQUEST["newcat"] = "";

if (!isset($_REQUEST["priority"]))
	$_REQUEST["priority"] = "5";

if (!isset($_REQUEST["lang"]))
	$_REQUEST["lang"] = $language;

if (!isset($_REQUEST["status"]))
	$_REQUEST["status"] = 0;

if (isset($_REQUEST["copy"])and ($_REQUEST["copy"])) {
	check_ticket('calendar');
	$_REQUEST["calitemId"] = 0;
	$_REQUEST["save"] = true;
}

if (isset($_REQUEST["save"])and ($_REQUEST["save"])) {
	check_ticket('calendar');
	if (!isset($_REQUEST["name"])or !(trim($_REQUEST["name"]))) {
		$_REQUEST["name"] = tra("event without name");
	}
	if (isset($_REQUEST["start_date_input"]) and $_REQUEST["start_date_input"]) {
		$event_start = $_REQUEST["start_date_input"];
	} else {
		if (isset($_REQUEST["start_freeform"])and $_REQUEST["start_freeform"]) {
			$event_start = strtotime($_REQUEST["start_freeform"]);
		}
		if (!isset($event_start)) {
			$event_start = mktime($_REQUEST["starth_Hour"], $_REQUEST["starth_Minute"],
					0, $_REQUEST["start_Month"], $_REQUEST["start_Day"], $_REQUEST["start_Year"]);
		}
	}
	if (isset($_REQUEST["end_date_input"]) and $_REQUEST["end_date_input"]) {
		$event_end = $_REQUEST["end_date_input"];
	} else {
		if (isset($_REQUEST["end_freeform"])and $_REQUEST["end_freeform"]) {
			$event_end = strtotime($_REQUEST["end_freeform"]);
		}
		if (!isset($event_end)) {
			$event_end = mktime($_REQUEST["endh_Hour"], $_REQUEST["endh_Minute"],
					0, $_REQUEST["end_Month"], $_REQUEST["end_Day"], $_REQUEST["end_Year"]);
		}
	}
	$_REQUEST["calitemId"] = $calendarlib->set_item($user, $_REQUEST["calitemId"], array(
		"user" => $user,
		"organizers" => $_REQUEST["organizers"],
		"participants" => $_REQUEST["participants"],
		"calendarId" => $_REQUEST["calendarId"],
		"start" => $event_start,
		"end" => $event_end,
		"locationId" => $_REQUEST["locationId"],
		"newloc" => $_REQUEST["newloc"],
		"categoryId" => $_REQUEST["categoryId"],
		"newcat" => $_REQUEST["newcat"],
		"priority" => $_REQUEST["priority"],
		"status" => $_REQUEST["status"],
		"url" => $_REQUEST["url"],
		"lang" => $_REQUEST["lang"],
		"name" => $_REQUEST["name"],
		"description" => $_REQUEST["description"]
	));
	//setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1)),"");	
	//$_REQUEST["calitemId"] = 0;
}

if ($_REQUEST["calitemId"]) {
	$info = $calendarlib->get_item($_REQUEST["calitemId"]);
} else {
	$info = array();

	$info["calitemId"] = "";
	$info["calendarId"] = "";
	$info["user"] = "";
	$info["calname"] = "";
	$info["organizers"] = $user . ",";
	$info["participants"] = $user . ":0,";
	$info["start"] = $focusdate + date("H") * 60 * 60;
	$info["end"] = $focusdate + (date("H") + 2) * 60 * 60;
	$info["locationId"] = 0;
	$info["locationName"] = '';
	$info["categoryId"] = 0;
	$info["categoryName"] = '';
	$info["priority"] = 5;
	$info["url"] = '';
	$info["lang"] = $tikilib->get_user_preference($user, "language");
	$info["name"] = '';
	$info["description"] = '';
	$info["created"] = time();
	$info["lastModif"] = time();
	$info["status"] = '0';
	$info["customlocations"] = 'n';
	$info["customcategories"] = 'n';
	$info["customlanguages"] = 'n';
	$info["custompriorities"] = 'n';
	$info["customparticipants"] = 'n';
}

if (!isset($_REQUEST["calendarId"])or !$_REQUEST["calendarId"]) {
	$_REQUEST["calendarId"] = $info["calendarId"];
}

$smarty->assign('calitemId', $info["calitemId"]);
$smarty->assign('calendarId', $_REQUEST["calendarId"]);
$smarty->assign('organizers', $info["organizers"]);
$smarty->assign('participants', $info["participants"]);
$smarty->assign('calname', $info["calname"]);
$smarty->assign('start', $info["start"]);
$smarty->assign('end', $info["end"]);
$smarty->assign('locationId', $info["locationId"]);
$smarty->assign('locationName', $info["locationName"]);
$smarty->assign('categoryId', $info["categoryId"]);
$smarty->assign('categoryName', $info["categoryName"]);
$smarty->assign('priority', $info["priority"]);
$smarty->assign('url', $info["url"]);
$smarty->assign('lang', $info["lang"]);
$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('created', $info["created"]);
$smarty->assign('lastModif', $info["lastModif"]);
$smarty->assign('lastUser', $info["user"]);
$smarty->assign('status', $info["status"]);

if (!isset($_REQUEST["editmode"])) $_REQUEST["editmode"] = 0;

if ($_REQUEST["editmode"]) {
	$thatcal = $calendarlib->get_calendar($_REQUEST["calendarId"]);

	$info["customlocations"] = $thatcal["customlocations"];
	$info["customcategories"] = $thatcal["customcategories"];
	$info["customlanguages"] = $thatcal["customlanguages"];
	$info["custompriorities"] = $thatcal["custompriorities"];
	$info["customparticipants"] = $thatcal["customparticipants"];
	$listcat = array();
	$listloc = array();
	$listpeople = array();
	$languages = array();

	if ($thatcal["customcategories"] == 'y') {
		$listcat = $calendarlib->list_categories($_REQUEST["calendarId"]);
	}

	if ($thatcal["customlocations"] == 'y') {
		$listloc = $calendarlib->list_locations($_REQUEST["calendarId"]);
	}

	if ($thatcal["customlanguages"] == 'y') {
		$languages = $tikilib->list_languages();
	}

	$smarty->assign('listcat', $listcat);
	$smarty->assign('listloc', $listloc);
	$smarty->assign_by_ref('languages', $languages);
	setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1)),"tab2");	
}

$smarty->assign('customlocations', $info["customlocations"]);
$smarty->assign('customcategories', $info["customcategories"]);
$smarty->assign('customlanguages', $info["customlanguages"]);
$smarty->assign('custompriorities', $info["custompriorities"]);
$smarty->assign('customparticipants', $info["customparticipants"]);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if (isset($_REQUEST['drop'])) {
	check_ticket('calendar');
	if (is_array($_REQUEST['drop'])) {
		foreach ($_REQUEST['drop'] as $dropme) {
			$calendarlib->drop_item($user, $dropme);
		}
	} else {
		$calendarlib->drop_item($user, $_REQUEST['drop']);
	}
}

$z = date("z");

$focus_prevday = mktime(0, 0, 0, $focus_month, $focus_day - 1, $focus_year);
$focus_nextday = mktime(0, 0, 0, $focus_month, $focus_day + 1, $focus_year);
$focus_prevweek = mktime(0, 0, 0, $focus_month, $focus_day - 7, $focus_year);
$focus_nextweek = mktime(0, 0, 0, $focus_month, $focus_day + 7, $focus_year);
$focus_prevmonth = mktime(0, 0, 0, $focus_month - 1, $focus_day, $focus_year);
$focus_nextmonth = mktime(0, 0, 0, $focus_month + 1, $focus_day, $focus_year);

$smarty->assign('daybefore', $focus_prevday);
$smarty->assign('weekbefore', $focus_prevweek);
$smarty->assign('monthbefore', $focus_prevmonth);
$smarty->assign('dayafter', $focus_nextday);
$smarty->assign('weekafter', $focus_nextweek);
$smarty->assign('monthafter', $focus_nextmonth);
$smarty->assign('focusmonth', $focus_month);
$smarty->assign('focusdate', $focusdate);
$smarty->assign('now', mktime(0, 0, 0, date('m'), date('d'), date('Y')));

$weekdays = range(0, 6);

$d = 60 * 60 * 24;
$currentweek = date("W", $focusdate);
$wd = date('w', $focusdate);

#if ($wd == 0) $w = 7;
#$wd--;

// calculate timespan for sql query
if ($_SESSION['CalendarViewMode'] == 'month') {
   $viewstart = mktime(0,0,0,$focus_month, 1, $focus_year);
   $TmpWeekday = date("w",$viewstart);
   // move viewstart back to Sunday....
   $viewstart -= $TmpWeekday * $d;
   // this is the last day of $focus_month
   $viewend = mktime(0,0,0,$focus_month + 1, 0, $focus_year);
   $TmpWeekday = date("w", $viewend);
   $viewend += (6 - $TmpWeekday) * $d;
   $viewend -= 1;
   // ISO weeks --- kinda mangled because ours begin on Sunday...
   $firstweek = date("W", $viewstart + $d);
   $lastweek = date("W", $viewend);
   $numberofweeks = $lastweek - $firstweek;
} elseif ($_SESSION['CalendarViewMode'] == 'week') {
   $firstweek = $currentweek;
   $lastweek = $currentweek;
   // start by putting $viewstart at midnight starting focusdate
   $viewstart = mktime(0,0,0,$focus_month, $focus_day, $focus_year);
   // then back up to the preceding Sunday;
   $viewstart -= $wd * $d;
   // then go to the end of the week for $viewend
   $viewend = $viewstart + ((7 * $d) - 1);
   $numberofweeks = 0;
} else {
   $firstweek = $currentweek;
   $lastweek = $currentweek;
   $viewstart = mktime(0,0,0,$focus_month, $focus_day, $focus_year);
   $viewend = $viewstart + ($d - 1);
   $weekdays = array(date('w',$focusdate));
   $numberofweeks = 0;
}
// untested (by me, anyway!) function grabbed from the php.net site:
// [2004/01/05:rpg]
function m_weeks($y, $m){
  // monthday array
  $monthdays = array(1=>31, 3=>31, 4=>30, 5=>31, 6=>30,7=>31,
               8=>31, 9=>30, 10=>31, 11=>30, 12=>31);
  // weekdays remaining in a week starting on 7 - Sunday...(could be changed)
  $weekdays = array(7=>7, 1=>6, 2=>5, 3=>4, 4=>3, 5=>2, 6=>1);
  $date = mktime( 0, 0, 0, $m, 1, $y);
  $leap = date("L", $date);
  // if it is a leap year set February to 29 days, otherwise 28
  $monthdays[2] = ($leap ? 29 : 28);
  // get the weekday of the first day of the month
  $wn = strftime("%u",$date);
  $days = $monthdays[$m] - $weekdays[$wn];
  return (ceil($days/7) + 1);
}


$smarty->assign('viewstart', $viewstart);
$smarty->assign('viewend', $viewend);
$smarty->assign('numberofweeks', $numberofweeks);

$daysnames = array(
	tra("Sunday"),
	tra("Monday"),
	tra("Tuesday"),
	tra("Wednesday"),
	tra("Thursday"),
	tra("Friday"),
	tra("Saturday")
);

$weeks = array();
$cell = array();

if ($_SESSION['CalendarViewGroups']) {
	$listevents = $calendarlib->list_items($_SESSION['CalendarViewGroups'], $user, $viewstart, $viewend, 0, 50, 'name_desc', '');
} else {
	$listevents = array();
}

if ($_SESSION['CalendarViewTikiCals']) {
	$listtikievents = $calendarlib->list_tiki_items($_SESSION['CalendarViewTikiCals'], $user, $viewstart, $viewend, 0, 50, 'name_desc', '');
} else {
	$listtikievents = array();
}

define("weekInSeconds", 604800);

// note that number of weeks starts at ZERO (i.e., zero = 1 week to display).
for ($i = 0; $i <= $numberofweeks; $i++) {
	$wee = $firstweek + $i;

	$weeks[] = $wee;

   // $startOfWeek is a unix timestamp
   $startOfWeek = $viewstart + $i * weekInSeconds;

	foreach ($weekdays as $w) {
		$leday = array();
	        $dday = $startOfWeek + $d * $w;
		$cell[$i][$w]['day'] = $dday;
		if (isset($listevents["$dday"])) {
			$e = 0;

			foreach ($listevents["$dday"] as $le) {
				$leday["{$le['time']}$e"] = $le;

				$smarty->assign_by_ref('cellextra', $le["extra"]);
				$smarty->assign_by_ref('cellhead', $le["head"]);
				$smarty->assign_by_ref('cellprio', $le["prio"]);
				$smarty->assign_by_ref('cellcalname', $le["calname"]);
				$smarty->assign_by_ref('cellname', $le["name"]);
				$smarty->assign_by_ref('celldescription', $le["description"]);
				$leday["{$le['time']}$e"]["over"] = $smarty->fetch("tiki-calendar_box.tpl");
				$e++;
			}
		}

		if (isset($listtikievents["$dday"])) {
			$e = 0;

			foreach ($listtikievents["$dday"] as $lte) {
				$leday["{$lte['time']}$e"] = $lte;

				$smarty->assign('cellextra', "");
				$smarty->assign_by_ref('cellhead', $lte["head"]);
				$smarty->assign_by_ref('cellprio', $lte["prio"]);
				$smarty->assign_by_ref('cellcalname', $lte["calname"]);
				$smarty->assign_by_ref('cellname', $lte["name"]);
				$smarty->assign_by_ref('celldescription', $lte["description"]);
				$leday["{$lte['time']}$e"]["over"] = $smarty->fetch("tiki-calendar_box.tpl");
				$e++;
			}
		}

		if (is_array($leday)) {
			ksort ($leday);
			$cell[$i][$w]['items'] = array_values($leday);
		}
	}
}

$hrows = array();
if ($_SESSION['CalendarViewMode'] == 'day') {
	foreach ($cell[0]["{$weekdays[0]}"]['items'] as $dayitems) {
		$rawhour = substr($dayitems['time'],0,2);
		$dayitems['mins'] = substr($dayitems['time'],2);
		$hrows["$rawhour"][] = $dayitems;
	}
}
$smarty->assign('hrows', $hrows); 

$smarty->assign('trunc', $trunc); 
$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M"); 
$smarty->assign('daformat2', $tikilib->get_long_date_format()); 
$smarty->assign('currentweek', $currentweek);
$smarty->assign('firstweek', $firstweek);
$smarty->assign('lastweek', $lastweek);
$smarty->assign('weekdays', $weekdays);
$smarty->assign('weeks', $weeks);
$smarty->assign('daysnames', $daysnames);
$smarty->assign('cell', $cell);
$smarty->assign('var', '');

$section = 'calendar';
include_once ('tiki-section_options.php');
ask_ticket('calendar');

if ($feature_jscalendar == 'y') {
	$smarty->assign('uses_jscalendar', 'y');
}

$smarty->assign('uses_tabs', 'y');
$smarty->assign('mid', 'tiki-calendar.tpl');
$smarty->display("tiki.tpl");

//echo "<pre>";print_r($cell);echo "</pre>";
?>
