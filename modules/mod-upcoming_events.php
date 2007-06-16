<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $calendarlib, $userlib, $tiki_p_admin, $tiki_p_view_calendar;
include_once ('lib/calendar/calendarlib.php');

$rawcals = $calendarlib->list_calendars();
$calIds = array();
$viewable = array();

foreach ($rawcals["data"] as $cal_id=>$cal_data) {
	$calIds[] = $cal_id;
	if ($tiki_p_admin == 'y') {
		$canView = 'y';
	} elseif ($cal_data["personal"] == "y") {
		if ($user) {
			$canView = 'y';
		} else {
			$canView = 'n';
		}
	} else {
			if ($userlib->user_has_perm_on_object($user, $cal_id, 'calendar', 'tiki_p_view_calendar')) {
				$canView = 'y';
			} else {
				$canView = 'n';
			}		
			if ($userlib->user_has_perm_on_object($user, $cal_id, 'calendar', 'tiki_p_admin_calendar')) {
				$canView = 'y';
			}				
	}
	if ($canView == 'y') {
		$viewable[] = $cal_id;
	}
}

$events = array();
if (!empty($viewable))  $events = $calendarlib->upcoming_events($module_rows,
    array_intersect(isset($module_params["calendarId"]) ? array($module_params["calendarId"]) : $calIds, $viewable),
    isset($module_params["maxDays"]) ? $module_params["maxDays"] : 365);
$smarty->assign('modUpcomingEvents', $events);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$module_rows = count($events);
$smarty->assign('module_rows', $module_rows);
?>
