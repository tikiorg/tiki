<?php

// Initialization
require_once('tiki-setup.php');
include_once ('lib/ical/iCal.php');
TikiInit::appendIncludePath("lib/ical/");

// list calendars //
include_once ('lib/calendar/calendarlib.php');

if ($tiki_p_admin_calendar != 'y' and $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
$now = explode("/",date('m/d/Y'));
$startTime = mktime(0,0,0,$now[0],$now[1]-1,$now[2]); // by default, export will start from yesterday's events.
$stopTime = 9999999999; // by default, this will be considered the end of time

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
		for ($i=0; $i < count($day_data); $i++) {
			$ea=array();
			$ea["Summary"]=$day_data[$i]["result"]["name"];
			$ea["dateStart"]=$day_data[$i]["result"]["start"];
			$ea["dateEnd"]=$day_data[$i]["result"]["end"];
			$ea["Description"]=preg_replace("/\n/","\\n",$day_data[$i]["result"]["description"]);
			if ($day_data[$i]["result"]["participants"]) {
				$ea["Attendees"]=$day_data[$i]["result"]["participants"];
			}
			$ea["LastModified"]=$day_data[$i]["result"]["lastModif"];
			// Second character of duration value must be a 'P' ?? 
			$ea["Duration"]=($day_data[$i]["result"]["end"] - $day_data[$i]["result"]["start"]);
			$ea["Contact"]=array($day_data[$i]["result"]["user"]);
			$ea["organizer"]=array($day_data[$i]["result"]["organizers"]);
			$ea["URL"]=$day_data[$i]["result"]["url"];
			$ea["DateStamp"]=$day_data[$i]["result"]["created"];
			//$ea["RequestStatus"]=$day_data[$i]["result"]["status"];
			$ea["UID"]="tiki-".$day_data[$i]["result"]["calendarId"]."-".$day_data[$i]["result"]["calitemId"];
			$c = $iCal->factory("Event",$ea);
			$cal->addEvent($c);
		}
	}
	$iCal->addCalendar($cal);
	$iCal->sendHeader("calendar");
	print($iCal->__toString());
	die;
}


$smarty->assign('iCal', $iCal);

// Display the template
$smarty->assign('mid','tiki-calendar_export_ical.tpl');
$smarty->display("tiki.tpl");
?>
