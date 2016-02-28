<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
$calendarlib = TikiLib::lib('calendar');

// ###trebly:B10111:[FIX-ADD-ENH]->  there are several meaning for the same var $calendarViewMode
if ( ! isset($calendarViewMode) ) {
// ###trebly:B10111:[FIX-ADD-ENH]-> $calendarViewMode become an array, several bugs comes from confusion of global values and parameters by ref
// for calendars : (main-)calendar, action_calendar, mod_calendar, mod_action_calendar the changes of values by url request is terrible
// for the moment 01/11/2011:11:55 just one value is used with index 'default', but initialisation is done.
// The init is actually into two places, tiki-calendar_setup.php and tiki-calendar_export.php will be grouped for clean
// $prefs would be added when need, $_SESSION, $PARAMS too this now generates not any change in the behavior.
$calendarViewMode=array(casedefault=>'month',calgen=>'month',calaction=>'month',modcalgen=>'month',modcalaction=>'month',trackercal=>'month');

  if (!empty($_REQUEST['viewmode'])) {
    $calendarViewMode['casedefault'] = $_REQUEST['viewmode'];
  } elseif (!empty($_SESSION['CalendarViewMode'])) {
    $calendarViewMode['casedefault'] = $_SESSION['CalendarViewMode'];
  } else {
    $calendarViewMode['casedefault'] = $prefs['calendar_view_mode'];
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
	$startTime = TikiLib::make_time(0, 0, 0, $_REQUEST['start_date_Month'], $_REQUEST['start_date_Day'], $_REQUEST['start_date_Year']);
} elseif (isset($_REQUEST["tstart"])) {
	$startTime = $_REQUEST["tstart"];
}

$endDate = new TikiDate();
$endDate->setDate($startTime);
if ($calendarViewMode['casedefault'] == 'month') {
     $stopTime = $endDate->addMonths(1);
} elseif ($calendarViewMode['casedefault'] == 'quarter') {
  $stopTime = $endDate->addMonths(3);
} elseif ($calendarViewMode['casedefault'] == 'semester') {
  $stopTime = $endDate->addMonths(6);
} elseif ($calendarViewMode['casedefault'] == 'year') {
  $stopTime = $endDate->addMonths(12);
} else {
  $stopTime = $endDate->addMonths(1);
}
$stopTime = $endDate->getTime();

if (isset($_REQUEST['stop_date_Month'])) {
	$stopTime = TikiLib::make_time(0, 0, 0, $_REQUEST['stop_date_Month'], $_REQUEST['stop_date_Day'], $_REQUEST['stop_date_Year']);
} elseif (isset($_REQUEST["tstop"])) {
	$stopTime = $_REQUEST["tstop"];
}

$calendarIds = array();
if (isset($_REQUEST['calendarIds'])) {
	$calendarIds = $_REQUEST['calendarIds'];
	foreach ($calendarIds as $anId)
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
$smarty->assign('calendars', $calendars["data"]);

// export calendar //
if ( ((is_array($calendarIds) && (count($calendarIds) > 0)) or isset($_REQUEST["calendarItem"]) ) && $_REQUEST["export"]=='y') {
	// get calendar events
	if ( !isset($_REQUEST["calendarItem"]) ) {
		$events = $calendarlib->list_raw_items($calendarIds, $user, $startTime, $stopTime, -1, $maxRecords, $sort_mode = 'start_asc', $find = '');
	} else {
		$events[] = $calendarlib->get_item($_REQUEST["calendarItem"]);
	}

	if ( isset($_REQUEST['csv']) ) {
		header('Content-type: text/csv');
		header("Content-Disposition: inline; filename=tiki-calendar.csv");
		$first = true;
		$description = '';
		foreach ($events as $event) {
			$line = '';
			foreach ($event as $name => $field) {
				if ( $first === true ) {
					$description .= '"'.$name.'";';
				}
				if ( is_array($field) ) {
					$line .= '"'.str_replace(array("\n","\r",'"'), array('\\n','','""'), join(',', $field)).'";';
				} else {
					$line .= '"'.str_replace(array("\n","\r",'"'), array('\\n','','""'), $field).'";';
				}
			}
			if ( $first === true ) {
				echo (trim($description, ';'))."\n";
				$first = false;
			}
			echo trim($line, ';')."\n";
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
			$ea['Description']= preg_replace(
				'/\n/',
				"\\n",
				strip_tags(
					TikiLib::lib('parser')->parse_data(
						$event['description'],
						array('is_html' => $prefs['calendar_description_is_html'] === 'y')
					)
				)
			);
			if ($event['participants']) {
				$ea['Attendees']=$event['participants'];
			}
			$ea['LastModified']=$event['lastModif'];

			// re: Second character of duration value must be a 'P' ??
			// jb for tiki 11 - feb 2013
			// spec is at: https://tools.ietf.org/html/rfc5545#section-3.3.6, so i tried:
			//	$durationSeconds = $event['end'] - $event['start'];
			//	$duration = $durationSeconds > 0 ? '+' : '-';
			//	$duration .= 'P' . $durationSeconds . 'S';
			// however, when formatted seemingly correctly you then get an error saying it's not in integer! :(
			// so just removing duration for now as it's implied by the start and end anyway - TODO better
			// $ea['Duration']=($duration);

			$ea['Contact']=array($event['user']);
			if (!empty($event['organizers'])) {
				$ea['organizer']=array($event['organizers']);
			}
			if (!empty($event['url'])) {
				$ea['URL']=$event['url'];
			}
			$ea['DateStamp']=$event['created'];
			//$ea['RequestStatus']=$event['status'];
			$ea['UID']='tiki-'.$event['calendarId'].'-'.$event['calitemId'];
			$c = $iCal->factory('Event', $ea);
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
			print(recode('utf-8..iso8859-15', $calendar_str));
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
$smarty->assign('mid', 'tiki-calendar_export_ical.tpl');
$smarty->display("tiki.tpl");
