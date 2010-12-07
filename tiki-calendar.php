<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
$access->check_feature('feature_calendar');

$maxSimultaneousWeekViewEvents = 3;

$myurl = 'tiki-calendar.php';
$exportUrl = 'tiki-calendar_export_ical.php';
$iCalAdvParamsUrl = 'tiki-calendar_params_ical.php';
$bufid = array();
$bufdata = array();
$modifiable = array();
if (!isset($cookietab)) { $cookietab = '1'; }
$rawcals = $calendarlib->list_calendars();
$rawcals['data'] = Perms::filter( array( 'type' => 'calendar' ), 'object', $rawcals['data'], array( 'object' => 'calendarId' ), 'view_calendar' );
$viewOneCal = $tiki_p_view_calendar;
$modifTab = 0;

$minHourOfDay = 12;
$maxHourOfDay = 12;
$manyEvents = array();

foreach ($rawcals["data"] as $cal_data) {
	$cal_id = $cal_data['calendarId'];
	$minHourOfDay = min($minHourOfDay,intval($cal_data['startday']/3600));
	$maxHourOfDay = max($maxHourOfDay,intval(($cal_data['endday']+1)/3600));
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
		$calperms = Perms::get( array( 'type' => 'calendar', 'object' => $cal_id ) );
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
		$modifiable[] = $cal_id;
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
$smarty->assign('infocals', $infocals["data"]);
$smarty->assign('listcals', $listcals);
$smarty->assign('modifTab', $modifTab);
$smarty->assign('now', $tikilib->now);

// set up list of groups
$use_default_calendars = false;
if (isset($_REQUEST["calIds"])and is_array($_REQUEST["calIds"])and count($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array_intersect($_REQUEST["calIds"], $listcals);
	if ( !empty($user) ) {
		$tikilib->set_user_preference($user,'default_calendars',serialize($_SESSION['CalendarViewGroups']));
	}
} elseif (isset($_REQUEST["calIds"])and !is_array($_REQUEST["calIds"])) {
	$_SESSION['CalendarViewGroups'] = array_intersect(array($_REQUEST["calIds"]), $listcals);
	if ( !empty($user) ) {
		$tikilib->set_user_preference($user,'default_calendars',serialize($_SESSION['CalendarViewGroups']));
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
$smarty->assign_by_ref('checkedCals', $checkedCals);

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

// Calculate all the displayed days for the selected calendars
$viewdays = array();
foreach($_SESSION['CalendarViewGroups'] as $calendar) {
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

if (isset($_REQUEST['sort_mode'])) $sort_mode = $_REQUEST['sort_mode'];

if ($_SESSION['CalendarViewGroups']) { 
	if (array_key_exists('CalendarViewList',$_SESSION) && $_SESSION['CalendarViewList'] == "list") {
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

$mloop = TikiLib::date_format("%m", $viewstart);
$dloop = TikiLib::date_format("%d", $viewstart);
$yloop = TikiLib::date_format("%Y", $viewstart);

$curtikidate = new TikiDate();
$display_tz = $tikilib->get_display_timezone();
if ( $display_tz == '' ) $display_tz = 'UTC';
$curtikidate->setTZbyID($display_tz);
$curtikidate->setLocalTime($dloop,$mloop,$yloop,0,0,0,0);

$smarty->assign('display_tz', $display_tz);

$smarty->assign('day', $daystart);

$firstDay = false;

for ($i = 0; $i <= $numberofweeks; $i++) {
	$weeks[] = $weekNumbers[] = $curtikidate->getWeekOfYear();
	require_once('lib/smarty_tiki/modifier.userlink.php');

	$registeredIndexes = array();
	foreach ($weekdays as $w) {
		$leday = array();
		if ($calendarViewMode == 'day') {
			$dday = $daystart;
		} else {
			$dday = $curtikidate->getTime();
			$curtikidate->addDays(1);
		}

		// skip events that are not to be displayed
		if ( !in_array($w,$viewdays) ) {
			continue;
		}

		$cell[$i][$w]['day'] = $dday;

		if ($calendarViewMode == 'day' or ($dday>=$daystart && $dday<=$dayend)) {
			if (!$firstDay) {
				$firstDay = true;
				$smarty->assign('currMonth',$dday);
				$cell[$i][$w]['firstDay'] = true;
			} else
				$cell[$i][$w]['firstDay'] = false;
			$cell[$i][$w]['focus'] = true;
		} else {
			$cell[$i][$w]['firstDay'] = false;
			$cell[$i][$w]['focus'] = false;
		}
		if (isset($listevents["$dday"])) {
			$e = 0;

			foreach ($listevents["$dday"] as $le) {
				$nbDaysLeftThisWeek = min(ceil(($le['endTimeStamp'] - $dday)/86400),(7-$w));
				if ($calendarViewMode == 'month') {
					$endOfCurrentMonth = $tikilib->make_time(23,59,59,TikiLib::date_format('m',$dday) + 1,0,TikiLib::date_format2('Y',$dday));
					$nbDaysLeftThisWeek = min(ceil(($endOfCurrentMonth - $dday)/86400),$nbDaysLeftThisWeek);
				} elseif ($calendarViewMode == 'year') {
					$endOfCurrentYear = $tikilib->make_time(23,59,59,12,31,TikiLib::date_format2('Y',$dday));
					$nbDaysLeftThisWeek = min(ceil(($endOfCurrentYear - $dday)/86400),$nbDaysLeftThisWeek);
				}
				if (!array_key_exists('nbDaysLeftThisWeek',$le)) {
					$le['nbDaysLeftThisWeek'] = $nbDaysLeftThisWeek;
				}
				$le['modifiable'] = in_array($le['calendarId'], $modifiable)? "y": "n";
				$le['visible'] = in_array($le['calendarId'], $visible)? "y": "n";
				$lec = $infocals['data']["{$le['calendarId']}"];
				$leday["{$le['time']}$e"] = $le;
				$smarty->assign('allday', $le["result"]["allday"]);
				$smarty->assign('cellcalendarId', $le["calendarId"]);
				$smarty->assign('cellhead', $le["head"]);
				$smarty->assign('cellprio', $le["prio"]);
				$smarty->assign('cellcalname', $le["calname"]);
				$smarty->assign('celllocation', $le["location"]);
				$smarty->assign('cellcategory', $le["category"]);
				$smarty->assign('cellstatus', $le["status"]);
				$smarty->assign('cellname', $le["name"]);
				$smarty->assign('cellurl', $le["web"]);
				$smarty->assign('cellid', $le["calitemId"]);
				$smarty->assign('celldescription', $tikilib->parse_data($le["description"]));
				$smarty->assign('cellmodif', $le['modifiable']);
				$smarty->assign('cellvisible', $le['visible']);
				$smarty->assign('cellstatus', $le['status']);
				$smarty->assign('cellstart', $le["startTimeStamp"]);
				$smarty->assign('cellend', $le["endTimeStamp"]);

				$organizers = $le['result']['organizers'];
				$cellorganizers = '';
				foreach ( $organizers as $org ) {
					if ( $org == '' ) continue;
					if ( $cellorganizers != '' ) $cellorganizers .= ', ';
					$cellorganizers .= smarty_modifier_userlink(trim($org), 'link', 'not_set', '', 0, 'n');
				}
				$smarty->assign('cellorganizers', $cellorganizers);

				$cellparticipants = '';
				foreach ( $le['result']['participants'] as $parti ) {
					if ( empty($parti) || $parti['name'] == '' ) continue;
					if ( $cellparticipants != '' ) $cellparticipants .= ', ';
					$cellparticipants .= smarty_modifier_userlink(trim($parti['name']), 'link', 'not_set', '', 0, 'n');
				}
				$smarty->assign('cellparticipants', $cellparticipants);

				$smarty->assign('calendar_type', 'calendar');
				$smarty->assign('show_calname', $lec['show_calname']);
				$smarty->assign('show_description', $lec['show_description']);
				$smarty->assign('show_location', $lec['show_location']);
				$smarty->assign('show_category', $lec['show_category']);
				$smarty->assign('show_language', $lec['show_language']);
				$smarty->assign('show_participants', $lec['show_participants']);
				$smarty->assign('show_url', $lec['show_url']);
				$smarty->assign('show_status', $lec['show_status']);
				$leday["{$le['time']}$e"]["over"] = $smarty->fetch("tiki-calendar_box.tpl");
				$e++;
			}
		}
		if (is_array($leday)) {
			ksort ($leday);
			$toBeIndexed = array_values($leday);
			$tmp = array();
			foreach($toBeIndexed as $index=>$anEvent) {
				// first place the events that started before the day.
				if (array_key_exists($anEvent['calitemId'],$registeredIndexes))
					$cell[$i][$w]['items'][$registeredIndexes[$anEvent['calitemId']]] = $anEvent;
				else
					$tmp[] = $anEvent;
			}
			$cpt = 0;
			$currIndex = 0;
			if (is_array($cell[$i][$w]) && array_key_exists('items',$cell[$i][$w])) {
				ksort ($cell[$i][$w]['items']);
			}
			while ($cpt < count($tmp)) {
				if (!array_key_exists('items',$cell[$i][$w])) {
					$cell[$i][$w]['items'][$currIndex] = $tmp[$cpt];
					$cpt++;
				} else {
					if (!array_key_exists($currIndex,$cell[$i][$w]['items']) || !is_array($cell[$i][$w]['items'][$currIndex])) {
						$cell[$i][$w]['items'][$currIndex] = $tmp[$cpt];
						$cpt++;
					} else
						$currIndex++;
				}
			}

			$tmp = (is_array($cell[$i][$w]) && array_key_exists('items',$cell[$i][$w])) ? array_keys($cell[$i][$w]['items']) : array();
			arsort($tmp);
			$tmp = array_values($tmp);
			$cell[$i][$w]['max'] = count($tmp) > 0 ? $tmp[0] : -1;
			for ($tr=0 ; $tr < $cell[$i][$w]['max'] ; $tr++) {
				if (!array_key_exists($tr,$cell[$i][$w]['items']))
					$cell[$i][$w]['items'][$tr] = 0;
			}
		}
		$registeredIndexes = array();
		if (is_array($cell[$i][$w]) && array_key_exists('items',$cell[$i][$w])) {
			foreach($cell[$i][$w]['items'] as $cpt=>$anEvent) {
				if ($cell[$i][$w]['day'] + 86400 - $anEvent['result']['end'] < 0)	// event ends after the current day
					$registeredIndexes[$anEvent['calitemId']] = $cpt;
			}
		}
	}
}

$smarty->assign('calendarViewMode',$calendarViewMode);

$verticalOffset = array();
foreach($cell as $w=>$weeks) {
	$verticalOffset[$w] = array();
	foreach($weeks as $d=>$days) {
		$cpt = 0;
		if (is_array($days) && array_key_exists('items',$days) && is_array($days['items'])) {
			foreach($days['items'] as $index=>$item) {
				if (is_array($verticalOffset) && array_key_exists($w,$verticalOffset) && is_array($verticalOffset[$w]) && array_key_exists($d,$verticalOffset[$w])) {
					while (array_key_exists($cpt,$verticalOffset[$w][$d])) {
						$cpt++;
					}
					$alreadyExists = 0;
					foreach ($verticalOffset[$w][$d] as $elt)
						if ($elt == $item['calitemId'])
							$alreadyExists++;
					if ($alreadyExists == 0)
						$verticalOffset[$w][$d][$cpt] = $item['calitemId'];
					if ($item['nbDaysLeftThisWeek'] > 1) {
						if (!array_key_exists($d+1,$verticalOffset[$w]) || !is_array($verticalOffset[$w][$d+1]))
							$verticalOffset[$w][$d+1] = array();
						$tmp = array_flip($verticalOffset[$w][$d]);
						$verticalOffset[$w][$d+1][$tmp[$item['calitemId']]] = $item['calitemId'];
					}
				}
			}
		}
	}
}
foreach($cell as $w=>$weeks) {
	foreach($weeks as $d=>$days) {
		$dayOffset = 0;
		if (is_array($days) && array_key_exists('items',$days) && is_array($days['items'])) {
			foreach($days['items'] as $index=>$item) {
				if (is_array($verticalOffset) && array_key_exists($w,$verticalOffset) && is_array($verticalOffset[$w]) && array_key_exists($d,$verticalOffset[$w])) {
					$tmp = array_flip($verticalOffset[$w][$d]);
					$cell[$w][$d]['items'][$index]['top'] = 14 * $tmp[$item['calitemId']];
				}
			}
		}
	}
}

$hrows = array();
$hours = array();
$concurrencies = array();
$arows = array();
if ($calendarViewMode == 'day') {
	$hours = range($minHourOfDay,$maxHourOfDay);
	$eventHoraires = array();
	if (!empty($cell[0]["{$weekdays[0]}"]['items'])) {
		foreach ($cell[0]["{$weekdays[0]}"]['items'] as $dayitems) {
			$dayitems['time'] = ($dayitems['startTimeStamp'] > $cell[0]["{$weekdays[0]}"]['day'])
				? $dayitems['time']
				: str_pad($minHourOfDay,2,'0',STR_PAD_LEFT) . "00";
			$dayitems['end'] = ($dayitems['endTimeStamp'] < ($cell[0]["{$weekdays[0]}"]['day'] + 86399))
				? $dayitems['end']
				: str_pad($maxHourOfDay,2,'0',STR_PAD_LEFT) . "59";
			$rawhour =intval(substr($dayitems['time'],0,2));
			$dayitems['mins'] = substr($dayitems['time'],2);
			$dayitems['top'] = (($rawhour - $minHourOfDay) + $dayitems['mins']/60)*24 + 35;
			$hrows["$rawhour"][] = $dayitems;
	
			$currIndex = count($eventHoraires);
			$eventHoraires[$currIndex]['id'] = $dayitems['calitemId'];
			$eventHoraires[$currIndex]['start'] = $dayitems['time'];
			$eventHoraires[$currIndex]['end'] =	$dayitems['end'];
			$eventHoraires[$currIndex]['duree'] = max(1,number_format(($tikilib->make_time(substr($dayitems['end'],0,2),substr($dayitems['end'],2) + 1,0,1,1,2000) - $tikilib->make_time(substr($dayitems['time'],0,2),substr($dayitems['time'],2),0,1,1,2000)) / 3600,2));
		}
	}
	$orderedEventHoraires = array();
	$eventIndexes = array();
	while (count($eventHoraires) > 0) {
		$indexEarlierEvent = 0;
		$currEarlierEventStart = 2400;
		foreach($eventHoraires as $index=>$event) {
			if ($event['start'] < $currEarlierEventStart) {
				$currEarlierEventStart = $event['start'];
				$indexEarlierEvent = $index;
			}
		}
		$orderedEventHoraires[] = $eventHoraires[$indexEarlierEvent];
		$eventIndexes[$eventHoraires[$indexEarlierEvent]['id']] = count($eventIndexes);
		unset($eventHoraires[$indexEarlierEvent]);
	}
	$eventHoraires = $orderedEventHoraires;
	unset($orderedEventHoraires);

	$tmpRes = array();
	for ($h=0 ; $h<24 ; $h++) {
		for ($m=0 ; $m<60 ; $m+=5) {
			$tmp = array();
			foreach($eventHoraires as $evtId=>$event) {
				$currTime = 100*$h + $m;
				if ($currTime >= $event['start'] && $currTime <= $event['end']) {
					$tmp[] = $event['id'];
				}
			}
			if(!in_array($tmp,$tmpRes) && count($tmp) > 0)
				$tmpRes[] = $tmp;
		}
	}
	$tmp2 = array();
	foreach($tmpRes as $val) {
		foreach($val as $index=>$evtId) {
			if (array_key_exists($evtId,$tmp2)) {
				if (count($val) > count($tmp2[$evtId])) {
					$tmp2[$evtId] = $val;
				}
			} else {
				$tmp2[$evtId] = $val;
			}
		}
	}
	$tmpVals = array();
	foreach($tmp2 as $elt=>$maxSim) {
		foreach($maxSim as $index=>$evtId) {
			if (!array_key_exists($evtId,$tmpVals)) {
				$offset = 0;
				$width = 0;
				if ($index == 0) {
					$offset = 0;
					$width = 100 / count($tmp2[$elt]);
				} else {
					for($i=0 ; $i < $index ; $i++) {
						$offset = max($offset,$tmpVals[$tmp2[$elt][$i]]['offset'] + $tmpVals[$tmp2[$elt][$i]]['width']);
						$width  += $tmpVals[$tmp2[$elt][$i]]['width'];
					}
					$width = (100 - $width) / (count($tmp2[$elt]) - $index);
				}
				$tmpVals[$evtId]['offset'] = $offset;
				$tmpVals[$evtId]['width'] = round($width,1);
			}
		}
	}
	$max = 0;
	foreach($tmpVals as $evtId=>$values)
		$max = max($max,$values['offset'] + $values['width']);

	if ($max > 100) {
		foreach(array_keys($tmpVals) as $evtId) {
			$tmpVals[$evtId]['offset'] = round(100 * $tmpVals[$evtId]['offset'] / $max + .5);
			$tmpVals[$evtId]['width'] = round(100 * $tmpVals[$evtId]['width'] / $max - 2,5);
		}
	}
	foreach($concurrencies as $key=>$value)
		$concurrencies[$key]['offset'] = $value['offset'] * 100 / $value['value'];
	foreach(array_keys($hrows) as $anHour) {
		for($i=0, $tmp_count = count($hrows[$anHour]) ; $i < $tmp_count ; $i++) {
			// setting number of simulaneous events foreach event, so that we can figure out its width without overwriting
			$hrows[$anHour][$i]['concurrences'] = $concurrencies[$hrows[$anHour][$i]['calitemId']]['value'];
			// setting duration of the event within the day
			$hrows[$anHour][$i]['duree'] = $eventHoraires[$eventIndexes[$hrows[$anHour][$i]['calitemId']]]['duree'] * 24;
			$hrows[$anHour][$i]['left'] = .9 * $tmpVals[$hrows[$anHour][$i]['calitemId']]['offset'] + 10;
			$hrows[$anHour][$i]['width'] = .9 * $tmpVals[$hrows[$anHour][$i]['calitemId']]['width'] - .4;
		}
	}

} else if($calendarViewMode == 'week') {
	$viewWeekDays = array();
	for ($i=0 ; $i < 7 ; $i++)
		$viewWeekDays[$i] = $viewstart + 86400*$i;
	$hours = range($minHourOfDay,$maxHourOfDay);
	$eventHoraires = array();
	$concurrencies = array();
	$tmpRes = array();
	// The zoom factor is calculated to compensate missing days
	$zoom = 100 / ( 9 + 13 * count($viewdays2) );
	foreach($weekdays as $wd) {
		// If the day is not shown skip it
		if ( !in_array($wd,$viewdays) ) {
			continue;
		}
		if ( isset($cell[0][$wd]['items']) && is_array($cell[0][$wd]['items']) ) {
			foreach ($cell[0][$wd]['items'] as $dayitems) {
				$dayitems['time'] = ($dayitems['startTimeStamp'] >= $cell[0][$wd]['day'])
					? $dayitems['time']
					: str_pad($minHourOfDay,2,'0',STR_PAD_LEFT) . "01";
				$dayitems['end'] = ($dayitems['endTimeStamp'] < ($cell[0][$wd]['day'] + 86400))
					? $dayitems['end']
					: str_pad($maxHourOfDay,2,'0',STR_PAD_LEFT) . "59";
				$rawhour =intval(substr($dayitems['time'],0,2));
				if ($rawhour < $minHourOfDay)
					$rawhour = $minHourOfDay;
				$dayitems['mins'] = substr($dayitems['time'],2);
				$dayitems['top'] = 24 * (($rawhour - $minHourOfDay) + $dayitems['mins']/60) + 35;
				$dayitems['left'] = $zoom * ( 9 + 13 * array_search($wd,$viewdays2) );
				$dayitems['width'] = 12 * $zoom;
				$hrows[$wd]["$rawhour"][] = $dayitems;
				$eventHoraires[$wd][$dayitems['calitemId']]['start'] = ($dayitems['time'] < $minHourOfDay."00") ? str_pad($minHourOfDay."00",4,'0',STR_PAD_LEFT) : $dayitems['time'];
				$eventHoraires[$wd][$dayitems['calitemId']]['end'] = ($dayitems['end'] > ($maxHourOfDay + 1)."00") ? str_pad(($maxHourOfDay + 1)."00",4,'0',STR_PAD_LEFT) : $dayitems['end'];
				$eventHoraires[$wd][$dayitems['calitemId']]['duree'] = max(1,($tikilib->make_time(substr($eventHoraires[$wd][$dayitems['calitemId']]['end'],0,2),substr($eventHoraires[$wd][$dayitems['calitemId']]['end'],2),0,1,1,2000) - $tikilib->make_time(substr($eventHoraires[$wd][$dayitems['calitemId']]['start'],0,2),substr($eventHoraires[$wd][$dayitems['calitemId']]['start'],2),0,1,1,2000)) / 3600);

				$tmpRes = array();
				for ($h=0 ; $h<24 ; $h++) {
					for ($m=0 ; $m<60 ; $m+=5) {
						$tmp = array();
						foreach(array_keys($eventHoraires[$wd]) as $evtId) {
							$currTime = 100*$h + $m;
							if ($currTime >= $eventHoraires[$wd][$evtId]['start'] && $currTime <= $eventHoraires[$wd][$evtId]['end'])
								if ($eventHoraires[$wd][$evtId]['end'] - $eventHoraires[$wd][$evtId]['start'] >= 0)
									$tmp[] = $evtId;
						}
						if( !in_array($tmp,$tmpRes))
							$tmpRes[] = $tmp;
					}
				}
			}
		}
		$slots = array();
		$maxConcurrency = 0;
		foreach($tmpRes as $val) {
			$maxConcurrency = max($maxConcurrency,count($val));
		}
		$slots = array_fill(0,max(1,min($maxSimultaneousWeekViewEvents,$maxConcurrency)), -1);
		foreach($tmpRes as $val) {
			foreach($val as $index=>$evtId) {
				$concurrencies[$wd][$evtId]['value'] = $maxConcurrency;
				$startNew = $eventHoraires[$wd][$evtId]['start'];
				foreach($slots as $index=>$oldEvtId) {
					if ($oldEvtId != $evtId) {
						if ($oldEvtId > 0) {
							if ($startNew > $eventHoraires[$wd][$oldEvtId]['end'])
								$slots[$index] = -1;
						}
					}
				}
				foreach($slots as $index=>$oldEvtId) {
					if (in_array($evtId,$slots))
						break;
					if ($oldEvtId == -1) {
						$slots[$index] = $evtId;
						$concurrencies[$wd][$evtId]['offset'] = $index;
						break;
					}
				}
			}
		}
	}
	foreach(array_keys($concurrencies) as $wd) {
		foreach(array_keys($concurrencies[$wd]) as $key)
			$concurrencies[$wd][$key]['offset'] = $zoom * 13 * ($concurrencies[$wd][$key]['offset'] / $concurrencies[$wd][$key]['value']);
	}
	foreach(array_keys($hrows) as $aDay) {
		foreach(array_keys($hrows[$aDay]) as $anHour) {
			for($i=0, $tmp_count = count($hrows[$aDay][$anHour]) ; $i < $tmp_count ; $i++) {
				if (!$manyEvents[$aDay]['tooMany'] && $concurrencies[$aDay][$hrows[$aDay][$anHour][$i]['calitemId']]['value'] <= $maxSimultaneousWeekViewEvents) {
					$hrows[$aDay][$anHour][$i]['concurrences'] = $concurrencies[$aDay][$hrows[$aDay][$anHour][$i]['calitemId']]['value'];
					$hrows[$aDay][$anHour][$i]['duree'] = $eventHoraires[$aDay][$hrows[$aDay][$anHour][$i]['calitemId']]['duree'] * 24;
					$hrows[$aDay][$anHour][$i]['left'] = $hrows[$aDay][$anHour][$i]['left'] + $concurrencies[$aDay][$hrows[$aDay][$anHour][$i]['calitemId']]['offset'];

					if ( $concurrencies[$aDay][$hrows[$aDay][$anHour][$i]['calitemId']]['value'] != 1
							&& $hrows[$aDay][$anHour][$i]['width'] > 0
							&& $concurrencies[$aDay][$hrows[$aDay][$anHour][$i]['calitemId']]['value'] > 0
						 ) {
						$hrows[$aDay][$anHour][$i]['width'] = $hrows[$aDay][$anHour][$i]['width'] / $concurrencies[$aDay][$hrows[$aDay][$anHour][$i]['calitemId']]['value'];
					} else {
						$hrows[$aDay][$anHour][$i]['width'] = $zoom * 12.8;
					}

					$manyEvents[$aDay]['tooMany'] = false;
				} else {
					$manyEvents[$aDay]['tooMany'] = true;
					$tmpTop = 99999999;
					$tmpBottom = 0;
					foreach ($hrows[$aDay] as $hour=>$events) {
						foreach ($hrows[$aDay][$hour] as $event) {
							$tmpTop = min($tmpTop,$event['time']);
							$tmpBottom = max($tmpBottom,$event['end']);
						}
					}
					if ($tmpTop < 100*$minHourOfDay)
						$tmpTop = str_pad(100*$minHourOfDay,4,'0',STR_PAD_LEFT);
					if ($tmpBottom > 100*(1 + $maxHourOfDay))
						$tmpBottom = str_pad(100*(1 + $maxHourOfDay),4,'0',STR_PAD_LEFT);
					$top = 36 + 24*((intval(substr($tmpTop,0,2)) + intval(substr($tmpTop,2))/60) - $minHourOfDay);
					$duree = max(23.9,23.9 * (($tikilib->make_time(substr($tmpBottom,0,2),substr($tmpBottom,2),0,1,1,2000) - $tikilib->make_time(substr($tmpTop,0,2),substr($tmpTop,2),0,1,1,2000)) / 3600));
					$manyEvents[$aDay]['top'] = $top;
					$manyEvents[$aDay]['left'] = $zoom * ( 9 + ($aDay * 13) );
					$manyEvents[$aDay]['width'] = $zoom * 12.8;
					$manyEvents[$aDay]['duree'] = $duree;
				}
			}
		}
	}
	foreach($hrows as $aDay=>$dayEvents) {
		if ($manyEvents[$aDay]['tooMany']) {
			// sorting events by start date ASC
			$tmp = array();
			foreach($hrows[$aDay] as $hourEvents)
				foreach($hourEvents as $event)
				$tmp[] = $event;

			$theEvents = array();
			while (count($tmp) > 0) {
				$indexEarlierEvent = 0;
				$currEarlierEventStart = 999999999999999;
				foreach($tmp as $index=>$event) {
					if ($event['startTimeStamp'] < $currEarlierEventStart) {
						$currEarlierEventStart = $event['startTimeStamp'];
						$indexEarlierEvent = $index;
					}
				}
				$theEvents[] = $tmp[$indexEarlierEvent];
				unset($tmp[$indexEarlierEvent]);
			}
			$smarty->assign('currDay',$manyEvents[$aDay]);
			$smarty->assign('currHrows',$theEvents);
			$manyEvents[$aDay]['overMany'] = $smarty->fetch("tiki-calendar_box_multiple.tpl");
		}
	}
	$smarty->assign('viewWeekDays', $viewWeekDays);
}

$smarty->assign('hrows', $hrows);
$smarty->assign('manyEvents', $manyEvents);
$smarty->assign('hours', $hours);
$smarty->assign('arows', $arows);
$smarty->assign('mrows', array(0=>"00", 5=>"05", 10=>"10", 15=>"15", 20=>"20", 25=>"25", 30=>"30", 35=>"35", 40=>"40", 45=>"45", 50=>"50", 55=>"55"));

$smarty->assign('trunc', $trunc);
$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M");
$smarty->assign('daformat2', $tikilib->get_long_date_format());
$smarty->assign('currentweek', $currentweek);
$smarty->assign('firstweek', $firstweek);
$smarty->assign('lastweek', $lastweek);
$smarty->assign('weekdays', $weekdays);
$smarty->assign('viewdays', $viewdays);
$smarty->assign('weeks', $weeks);
$smarty->assign_by_ref('weekNumbers', $weekNumbers);
$smarty->assign('daysnames', $daysnames);
$smarty->assign('daysnames_abr', $daysnames_abr);
foreach($cell as $a=>$x) {
	foreach($x as $b=>$y) {
		if (!array_key_exists('items',$y) || !is_array($y['items']))
			$cell[$a][$b]['items'] = array();
	}
}
$smarty->assign('cell', $cell);
$smarty->assign('var', '');
$smarty->assign('myurl', $myurl);
$smarty->assign('exportUrl', $exportUrl);
$smarty->assign('iCalAdvParamsUrl', $iCalAdvParamsUrl);

if($prefs['feature_user_watches'] == 'y' && $user && count($_SESSION['CalendarViewGroups']) == 1) {
	$calId = $_SESSION['CalendarViewGroups'][0];
	if (isset($_REQUEST['watch_event']) && isset($_REQUEST['watch_action'])) {
		check_ticket('calendar');
		if ($_REQUEST['watch_action'] == 'add') {
			$tikilib->add_user_watch($user, $_REQUEST['watch_event'], $calId, 'calendar', $infocals['data'][$calId]['name'],"tiki-calendar.php?calIds[]=$calId");
		} else {
			$tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $calId, 'calendar');
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

if ($prefs['feature_theme_control'] == 'y'	and isset($_REQUEST['calIds'])) {
	$cat_type = "calendar";
	$cat_objid = $_REQUEST['calIds'][0]; 
}
include_once ('tiki-section_options.php');

setcookie('tab',$cookietab);
$smarty->assign('cookietab',$cookietab);

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
$smarty->assign('headtitle',tra('Calendar'));
$smarty->display("tiki.tpl");
