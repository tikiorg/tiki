<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

$access->check_feature('feature_calendar');
$access->check_permission('tiki_p_view_events');

// Initialization
TikiInit::appendIncludePath("lib/ical/");
include_once ('lib/ical/iCal.php');

// list calendars //
include_once ('lib/calendar/calendarlib.php');

if ( ! isset($calendarViewMode) ) {
  if (!empty($_REQUEST['viewmode'])) {
    $calendarViewMode = $_REQUEST['viewmode'];
  } elseif (!empty($_SESSION['CalendarViewMode'])) {
    $calendarViewMode = $_SESSION['CalendarViewMode'];
  } else {
    $calendarViewMode = $prefs['calendar_view_mode'];
  }
}

# If specified, limit the export to the maximum number of records (events)
# indicated in the request; otherwise, the limit is from the global preferences.
if (isset($_REQUEST['maxRecords'])) {
        $maxRecords = $_REQUEST['maxRecords'];
}

if ( isset($_SESSION['CalendarFocusDate']) ) {
	$startTime = $_SESSION['CalendarFocusDate'];
} else {
// by default, export will start from yesterday's events.
	$startDate = new TikiDate();
	$startDate->addDays(-1);
	$startTime = $startDate->getTime();
}

if (isset($_REQUEST['start_date_Month'])) {
	$startTime = TikiLib::make_time(0, 0, 0, $_REQUEST['start_date_Month'],	$_REQUEST['start_date_Day'], $_REQUEST['start_date_Year']);
} elseif (isset($_REQUEST["tstart"])) {
	$startTime = $_REQUEST["tstart"];
}

$endDate = new TikiDate();
$endDate->setDate($startTime);
if ($calendarViewMode == 'month') {
     $stopTime = $endDate->addMonths(1);
   } elseif ($calendarViewMode == 'quarter') {
     $stopTime = $endDate->addMonths(3);
   } elseif ($calendarViewMode == 'semester') {
     $stopTime = $endDate->addMonths(6);
   } elseif ($calendarViewMode == 'year') {
     $stopTime = $endDate->addMonths(12);
   } else {
     $stopTime = $endDate->addMonths(1);
   }
$stopTime = $endDate->getTime();

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

	if ( isset($_REQUEST['csv']) ) {
		header('Content-type: text/csv');
		header("Content-Disposition: inline; filename=tiki-calendar.csv");
		$first = true;
		$description = '';
		foreach($events as $event) {
			$line = '';
			foreach($event as $name => $field) {
				if ( $first === true ) {
					$description .= '"'.$name.'";';
				}
				if ( is_array($field) ) {
					$line .= '"'.str_replace(array("\n","\r",'"'),array('\\n','','""'),join(',',$field)).'";';
				} else {
					$line .= '"'.str_replace(array("\n","\r",'"'),array('\\n','','""'),$field).'";';
				}
			}
			if ( $first === true ) {
				echo (trim($description,';'))."\n";
				$first = false;
			}
			echo trim($line,';')."\n";
		}
	} else {
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
	}
	die;
}


$smarty->assign('iCal', $iCal);

// Display the template
$smarty->assign('mid','tiki-calendar_export_ical.tpl');
$smarty->display("tiki.tpl");
