<?php

// Initialization
require_once('tiki-setup.php');
include_once ('lib/ical/iCal.php');
TikiInit::appendIncludePath("lib/ical/");
//$iCal->sendHeader();

// Import Calendar //
// read ics file // 
//$myFile="temp/Personnel2.ics";
//$cal=$iCal->ReadFile("temp/Personnel.ics");
//$fh = fopen($myFile, 'r');
//$data = fread($fh,  filesize($myFile));
//fclose($fh);
//require_once 'File/iCal/iCalendar.php';
//$iCal = new File_iCal_iCalendar($data);

// list calendars //
include_once ('lib/calendar/calendarlib.php');
if ($tiki_p_admin_calendar != 'y' and $tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["calendarId"])) {
	$_REQUEST["calendarId"] = 0;
} else {
	 $smarty->assign('individual', $userlib->object_has_one_permission($_REQUEST["calendarId"], 'calendar'));
}
$sort_mode="name_ASC";

$find="";
$calendars = $calendarlib->list_calendars(0, -1, $sort_mode, $find);

foreach (array_keys($calendars["data"]) as $i) {
	$calendars["data"][$i]["individual"] = $userlib->object_has_one_permission($i, 'calendar');
}
$smarty->assign('calendars',$calendars["data"]);
$smarty->assign('uses_jscalendar','y');
// export calendar //
if (isset($_REQUEST["calendarId"]) && $_REQUEST["calendarId"] && $_REQUEST["export"]=='y') {
	// get calendar events 
	$events=$calendarlib->list_items(array($_REQUEST["calendarId"]), $user, $_REQUEST["tstart"], $_REQUEST["tstop"], -1, $maxRecords, $sort_mode='start_asc', $find='');
	
	// create ical array//
	$iCal = new File_iCal();
	
	$cal = $iCal->getSkeletonCalendar();
	foreach ($events as $day=>$day_data) {
		for ($i=0; $i < count($day_data); $i++) {
			//var_dump($day_data[$i]["result"]);
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
			//$ea["Duration"]=($day_data[$i]["result"]["end"] - $day_data[$i]["result"]["start"]);
			//$ea["Contact"]=array($day_data[$i]["result"]["user"]);
			//$ea["organizer"]=array($day_data[$i]["result"]["organizers"]);
			//$ea["URL"]=$day_data[$i]["result"]["url"];
			$ea["DateStamp"]=$day_data[$i]["result"]["created"];
			//$ea["RequestStatus"]=$day_data[$i]["result"]["status"];
			$ea["UID"]="tiki-".$day_data[$i]["result"]["calendarId"]."-".$day_data[$i]["result"]["calitemId"];
			$c = $iCal->factory("Event",$ea);

			$cal->addEvent($c);
		}
	}
	$iCal->addCalendar($cal);
	$iCal->sendHeader();
	print($iCal->__toString());
	die;
}


$smarty->assign('iCal', $iCal);
//$smarty->assign('filedata', $data);

// Display the template
$smarty->assign('mid','tiki-calendar_export_ical.tpl');
$smarty->display("tiki.tpl");
?>
