<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-calendar.php,v 1.73.2.3 2007-12-07 05:56:37 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.

// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$section = 'calendar';
require_once ('tiki-setup.php');

include_once ('lib/calendar/calendarlib.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/newsletters/nllib.php');

$headerlib->add_cssfile('css/calendar.css',20);
# perms are
# 	$tiki_p_view_calendar
# 	$tiki_p_admin_calendar
# 	$tiki_p_change_events
# 	$tiki_p_add_events
if ($prefs['feature_calendar'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_calendar");
	$smarty->display("error.tpl");
	die;
}

$myurl = 'tiki-calendar.php';
$bufid = array();
$bufdata = array();
$modifiable = array();
$cookietab = 1;
$rawcals = $calendarlib->list_calendars();
$viewOneCal = $tiki_p_view_calendar;
$modifTab = 0;

foreach ($rawcals["data"] as $cal_id=>$cal_data) {
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
		if ($userlib->user_has_perm_on_object($user, $cal_id, 'calendar', 'tiki_p_view_calendar')) {
			$cal_data["tiki_p_view_calendar"] = 'y';
		} else {
			$cal_data["tiki_p_view_calendar"] = 'n';
		}
		if ($userlib->user_has_perm_on_object($user, $cal_id, 'calendar', 'tiki_p_add_events')) {
			$cal_data["tiki_p_add_events"] = 'y';
			$tiki_p_add_events = "y";
			$smarty->assign("tiki_p_add_events", "y");
		} else {
			$cal_data["tiki_p_add_events"] = 'n';
		}
		if ($userlib->user_has_perm_on_object($user, $cal_id, 'calendar', 'tiki_p_change_events')) {
			$cal_data["tiki_p_change_events"] = 'y';
		} else {
			$cal_data["tiki_p_change_events"] = 'n';
		}
		if ($userlib->user_has_perm_on_object($user, $cal_id, 'calendar', 'tiki_p_admin_calendar')) {
			$cal_data["tiki_p_view_calendar"] = 'y';
			$cal_data["tiki_p_add_events"] = 'y';
			$cal_data["tiki_p_change_events"] = 'y';
		}
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
		$modifiable[] = $cal_id;
		$visible[] = $cal_id;
	}
}
if ($prefs['feature_categories'] == 'y' and isset($_REQUEST['calIds'])) {
	$is_categorized = FALSE;
	foreach ($_REQUEST['calIds'] as $calId) {
		$perms_array = $categlib->get_object_categories_perms($user, 'calendar', $calId);
		if ($perms_array) {
			foreach ($perms_array as $perm => $value) {
				$$perm = $value;
			}
		} else {
			$is_categorized = FALSE;
		}
		if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
			if (!isset($user)){
				$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
				$smarty->assign('errortitle',tra("Please login"));
			} else {
				$smarty->assign('msg',tra("Permission denied you cannot view this page"));
			}
			$smarty->display("error.tpl");
			die;
		}
	}
}

if ($viewOneCal != 'y') {
	$smarty->assign('msg', tra("Permission denied you cannot view the calendar"));
	$smarty->display("error.tpl");
	die;
}

$listcals = $bufid;
$infocals["data"] = $bufdata;

$smarty->assign('infocals', $infocals["data"]);
$smarty->assign('listcals', $listcals);
$smarty->assign('modifTab', $modifTab);

// set up list of groups
if (isset($_REQUEST["calIds"])and is_array($_REQUEST["calIds"])and count($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array_intersect($_REQUEST["calIds"], $listcals);
} elseif (isset($_REQUEST["calIds"])and !is_array($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array_intersect(array($_REQUEST["calIds"]), $listcals);
} elseif (!isset($_SESSION['CalendarViewGroups']) || !empty($_REQUEST['allCals'])) {
	$_SESSION['CalendarViewGroups'] = $listcals;
} elseif (isset($_REQUEST["refresh"])and !isset($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array();
} else {
	$_SESSION['CalendarViewGroups'] = array_intersect($_SESSION['CalendarViewGroups'], $listcals);
}

$smarty->assign('displayedcals', $_SESSION['CalendarViewGroups']);

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
$smarty->assign('thiscal', $thiscal);


if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);

if (isset($_REQUEST['mon']) && !empty($_REQUEST['mon'])) {
	$request_month = $_REQUEST['mon'];
}
if (isset($_REQUEST['day']) && !empty($_REQUEST['day'])) {
	$request_day = $_REQUEST['day'];
}
if (isset($_REQUEST['year']) && !empty($_REQUEST['year'])) {
	$request_year = $_REQUEST['year'];
}

include_once("tiki-calendar_setup.php");

if (isset($_REQUEST['sort_mode'])) $sort_mode = $_REQUEST['sort_mode'];

if ($_SESSION['CalendarViewGroups']) { 
  if ($_SESSION['CalendarViewList'] == "list") {
    if (isset($sort_mode)) {
      $smarty->assign_by_ref('sort_mode', $sort_mode);
    } else {
      $sort_mode = "start_asc";
		}
    $listevents = $calendarlib->list_raw_items($_SESSION['CalendarViewGroups'], $user, $viewstart, $viewend, 0, -1, $sort_mode);
    for ($i = count($listevents) - 1; $i >= 0; --$i) {
      $listevents[$i]['modifiable'] = in_array($listevents[$i]['calendarId'], $modifiable)? "y": "n";
		}
  } else {
    $listevents = $calendarlib->list_items($_SESSION['CalendarViewGroups'], $user, $viewstart, $viewend, 0, -1);
  }
  $smarty->assign_by_ref('listevents', $listevents);
} else {
  $listevents = array();
}

define("weekInSeconds", 604800);
$mloop = TikiLib::date_format("%m", $viewstart);
$dloop = TikiLib::date_format("%d", $viewstart);
$yloop = TikiLib::date_format("%Y", $viewstart);

for ($i = 0; $i <= $numberofweeks; $i++) {
  $wee = TikiLib::date_format("%U",$viewstart + ($i * weekInSeconds) + $d);
  $weeks[] = $wee;

   // $startOfWeek is a unix timestamp
   $startOfWeek = $viewstart + $i * weekInSeconds;

  foreach ($weekdays as $w) {
    $leday = array();
    If ($calendarViewMode == 'day') {
      $dday = $daystart;
    } else {
      //$dday = $startOfWeek + $d * $w;
      $dday = TikiLib::make_time(0,0,0, $mloop, $dloop++, $yloop);
    }
    $cell[$i][$w]['day'] = $dday;

    if ($calendarViewMode == 'day' or ($dday>=$daystart && $dday<=$dayend)) {
      $cell[$i][$w]['focus'] = true;
    } else {
      $cell[$i][$w]['focus'] = false;
    }
    if (isset($listevents["$dday"])) {
      $e = 0;

      foreach ($listevents["$dday"] as $le) {
        $le['modifiable'] = in_array($le['calendarId'], $modifiable)? "y": "n";
        $le['visible'] = in_array($le['calendarId'], $visible)? "y": "n";
				$lec = $infocals['data']["{$le['calendarId']}"];
        $leday["{$le['time']}$e"] = $le;

        $smarty->assign('cellcalendarId', $le["calendarId"]);
        $smarty->assign('cellhead', $le["head"]);
        $smarty->assign('cellprio', $le["prio"]);
        $smarty->assign('cellcalname', $le["calname"]);
        $smarty->assign('celllocation', $le["location"]);
        $smarty->assign('cellcategory', $le["category"]);
        $smarty->assign('cellname', $le["name"]);
        $smarty->assign('cellurl', $le["web"]);
        $smarty->assign('cellid', $le["calitemId"]);
        $smarty->assign('celldescription', $tikilib->parse_data($le["description"]));
        $smarty->assign('cellmodif', $le['modifiable']);
        $smarty->assign('cellvisible', $le['visible']);
        $smarty->assign('show_calname', $lec['show_calname']);
        $smarty->assign('show_description', $lec['show_description']);
        $smarty->assign('show_location', $lec['show_location']);
        $smarty->assign('show_category', $lec['show_category']);
        $smarty->assign('show_language', $lec['show_language']);
        $smarty->assign('show_participants', $lec['show_participants']);
        $smarty->assign('show_url', $lec['show_url']);
        $leday["{$le['time']}$e"]["over"] = $smarty->fetch("tiki-calendar_box.tpl");
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
$hours = array();
if ($calendarViewMode == 'day') {
  $hours = range(0,23);
  foreach ($cell[0]["{$weekdays[0]}"]['items'] as $dayitems) {
    $rawhour = intval(substr($dayitems['time'],0,2));
    $dayitems['mins'] = substr($dayitems['time'],2);
    $hrows["$rawhour"][] = $dayitems;
  }
}
$smarty->assign('hrows', $hrows);
$smarty->assign('hours', $hours);
$smarty->assign('mrows', array(0=>"00", 5=>"05", 10=>"10", 15=>"15", 20=>"20", 25=>"25", 30=>"30", 35=>"35", 40=>"40", 45=>"45", 50=>"50", 55=>"55"));

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
$smarty->assign('myurl', $myurl);

if($prefs['feature_user_watches'] == 'y' && $user && count($_SESSION['CalendarViewGroups']) == 1) {
	$calId = $_SESSION['CalendarViewGroups'][0];
	if (isset($_REQUEST['watch_event']) && isset($_REQUEST['watch_action'])) {
		check_ticket('calendar');
		if ($_REQUEST['watch_action'] == 'add') {
	    	$tikilib->add_user_watch($user, $_REQUEST['watch_event'], $calId, 'calendar', $infocals['data'][$calId]['name'],"tiki-calendar.php?calIds[]=$calId");
		} else {
	    	$tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $calId);
		}
	}
	if ($tikilib->user_watches($user,'calendar_changed', $calId, 'calendar')) {
		$smarty->assign('user_watching', 'y');	
	} else {
		$smarty->assign('user_watching', 'n');
	}

    // Check, if a user is watching this calendar.
	if ($prefs['feature_categories'] == 'y') {    			
	    $watching_categories_temp=$categlib->get_watching_categories($calId,'calendar',$user);	    
	    $smarty->assign('category_watched','n');
	 	if (count($watching_categories_temp) > 0) {
	 		$smarty->assign('category_watched','y');
	 		$watching_categories=array();	 			 	
	 		foreach ($watching_categories_temp as $wct ) {
	 			$watching_categories[]=array("categId"=>$wct,"name"=>$categlib->get_category_name($wct));
	 		}		 		 	
	 		$smarty->assign('watching_categories', $watching_categories);
	 	}    
	}
		
}

$section = 'calendar';
if ($prefs['feature_theme_control'] == 'y'	and isset($_REQUEST['calIds'])) {
	$cat_type = "calendar";
	$cat_objid = $_REQUEST['calIds'][0]; 
}
include_once ('tiki-section_options.php');

setcookie('tab',$cookietab);
$smarty->assign('cookietab',$cookietab);

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','calendar');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
include_once("textareasize.php");

ask_ticket('calendar');

include_once('tiki-jscalendar.php');
$smarty->assign('uses_tabs', 'y');
if(isset($_REQUEST['editmode']) && ($_REQUEST['editmode'] == 'add' || $_REQUEST['editmode'] == 'edit')) {
  $smarty->assign('mid', 'tiki-calendar_add_event.tpl');
}
else {
  $smarty->assign('mid', 'tiki-calendar.tpl');
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->display("tiki.tpl");
?>
