<?php
require_once('tiki-setup.php');

if ($prefs['feature_calendar'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_calendar");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_calendar != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

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

if (isset($_REQUEST["tstart"]))
	$startTime = $_REQUEST["tstart"];
if (isset($_REQUEST["tstop"]))
	$stopTime = $_REQUEST["tstop"];

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
if (is_array($calendarIds) && (count($calendarIds) > 0) && $_REQUEST["export"]=='y') {
	// get calendar events 
	$events=$calendarlib->list_items($calendarIds, $user, $startTime, $stopTime, -1, $maxRecords, $sort_mode='start_asc', $find='');

	// create ical array//
	$iCal = new File_iCal();
	
	$cal = $iCal->getSkeletonCalendar();
	foreach ($events as $day=>$day_data) {
		foreach( $day_data as $dd) {
			$ea=array();
			$ea["Summary"]=$dd["result"]["name"];
			$ea["dateStart"]=$dd["result"]["start"];
			$ea["dateEnd"]=$dd["result"]["end"];
			$ea["Description"]=preg_replace("/\n/","\\n",$dd["result"]["description"]);
			if ($dd["result"]["participants"]) {
				$ea["Attendees"]=$dd["result"]["participants"];
			}
			$ea["LastModified"]=$dd["result"]["lastModif"];
			// Second character of duration value must be a 'P' ?? 
			$ea["Duration"]=($dd["result"]["end"] - $dd["result"]["start"]);
			$ea["Contact"]=array($dd["result"]["user"]);
			$ea["organizer"]=array($dd["result"]["organizers"]);
			$ea["URL"]=$dd["result"]["url"];
			$ea["DateStamp"]=$dd["result"]["created"];
			//$ea["RequestStatus"]=$dd["result"]["status"];
			$ea["UID"]="tiki-".$dd["result"]["calendarId"]."-".$dd["result"]["calitemId"];
			$c = $iCal->factory("Event",$ea);
			$cal->addEvent($c);
		}
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
	} else if (function_exists('iconv') && $re_encode !== false) {
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
