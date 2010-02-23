<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

$access->check_feature('feature_calendar');
$access->check_permission('tiki_p_admin_calendar');

// Initialization
TikiInit::appendIncludePath("lib/ical/");
include_once ('lib/ical/iCal.php');

// list calendars //
include_once ('lib/calendar/calendarlib.php');

if (isset($_SESSION['CalendarFocusDate']) && $_SESSION['CalendarFocusDate']) {
	$now = explode("/",date('m/d/Y',$_SESSION['CalendarFocusDate']));
} else {
	$now = explode("/",date('m/d/Y'));
}

// by default, export will start from yesterday's events.
$startTime = mktime(0,0,0,$now[0],$now[1]-1,$now[2]);
// by default, this will be considered the end of time
$stopTime = 9999999999;

if (isset($_REQUEST['start_date_Month'])) {
	$startTime = TikiLib::make_time(0, 0, 0, $_REQUEST['start_date_Month'],	$_REQUEST['start_date_Day'], $_REQUEST['start_date_Year']);
} elseif (isset($_REQUEST["tstart"])) {
	$startTime = $_REQUEST["tstart"];
}
if (isset($_REQUEST['stop_date_Month'])) {
	$stopTime = TikiLib::make_time(0, 0, 0, $_REQUEST['stop_date_Month'],	$_REQUEST['stop_date_Day'], $_REQUEST['stop_date_Year']);
} elseif (isset($_REQUEST["tstop"])) {
	$stopTime = $_REQUEST["tstop"];
}

$calendarIds = array();
if (isset($_REQUEST['calendarIds'])) {
	$calendarIds = $_REQUEST['calendarIds'];
	foreach($calendarIds as $anId)
		$smarty->assign('individual_'.$anId, $userlib->object_has_one_permission($anId, 'calendar'));
} else {
	if (!isset($_REQUEST["calendarId"])) {
		$_REQUEST["calendarId"] = 0;
	} else {
		 $smarty->assign('individual_'.$_REQUEST["calendarId"], $userlib->object_has_one_permission($_REQUEST["calendarId"], 'calendar'));
	}
}
$sort_mode="name";

$find="";
$calendars = $calendarlib->list_calendars(0, -1, $sort_mode, $find);

foreach (array_keys($calendars["data"]) as $i) {
	$calendars["data"][$i]["individual"] = $userlib->object_has_one_permission($i, 'calendar');
}
$smarty->assign('calendars',$calendars["data"]);

// export calendar //
if ( ((is_array($calendarIds) && (count($calendarIds) > 0)) or isset($_REQUEST["calendarItem"]) ) && $_REQUEST["export"]=='y') {
	// get calendar events 
	if ( !isset($_REQUEST["calendarItem"]) ) {
		$events=$calendarlib->list_raw_items($calendarIds, $user, $startTime, $stopTime, -1, $maxRecords, $sort_mode='start_asc', $find='');
	} else {
		$events[] = $calendarlib->get_item($_REQUEST["calendarItem"]);
	}

	// create ical array//
	$iCal = new File_iCal();
	
	$cal = $iCal->getSkeletonCalendar();
	foreach ($events as $event) {
		$ea=array();
		$ea['Summary']=$event['name'];
		$ea['dateStart']=$event['start'];
		$ea['dateEnd']=$event['end'];
		$ea['Description']=preg_replace('/\n/',"\\n",$event['description']);
		if ($event['participants']) {
			$ea['Attendees']=$event['participants'];
		}
		$ea['LastModified']=$event['lastModif'];
		// Second character of duration value must be a 'P' ?? 
		$ea['Duration']=($event['end'] - $event['start']);
		$ea['Contact']=array($event['user']);
		$ea['organizer']=array($event['organizers']);
		$ea['URL']=$event['url'];
		$ea['DateStamp']=$event['created'];
		//$ea['RequestStatus']=$event['status'];
		$ea['UID']='tiki-'.$event['calendarId'].'-'.$event['calitemId'];
		$c = $iCal->factory('Event',$ea);
		$cal->addEvent($c);
	}
	$iCal->addCalendar($cal);
	$iCal->sendHeader("calendar");
	$calendar_str = $iCal->__toString();
	header("Content-Length: ".strlen($calendar_str));
	header("Expires: 0");
	// These two lines fix pb with IE and HTTPS
	header("Cache-Control: private");
	header("Pragma: dummy=bogus");
	// Outlook needs iso8859 encoding 
	header("Content-Type:text/calendar; method=REQUEST; charset=iso-8859-15");
	header("Content-Transfer-Encoding:quoted-printable");
	$re_encode = stripos($_SERVER['HTTP_USER_AGENT'], 'windows');	// only re-encode to ISO-8859-15 if client on Windows
	if (function_exists('recode') && $re_encode !== false) {
		print(recode('utf-8..iso8859-15',$calendar_str));
	} elseif (function_exists('iconv') && $re_encode !== false) {
		print(iconv("UTF-8", "ISO-8859-15", $calendar_str));
	} else {
		print($calendar_str);	// UTF-8 is good for other platforms
	}
	die;
}


$smarty->assign('iCal', $iCal);

// Display the template
$smarty->assign('mid','tiki-calendar_export_ical.tpl');
$smarty->display("tiki.tpl");
