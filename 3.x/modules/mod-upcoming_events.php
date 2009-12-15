<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $calendarlib, $userlib, $tiki_p_admin, $tiki_p_view_calendar, $smarty;
if ($prefs['feature_calendar'] != 'y')
	return;
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
$smarty->assign_by_ref('infocals', $rawcals['data']);

$events = array();
if (!empty($module_params['calendarId']) && !is_array($module_params['calendarId']) && !is_numeric($module_params['calendarId'])) {
	$module_params['calendarId'] = preg_split('/[\|:\&,]/', $module_params['calendarId']);
}
if (!empty($viewable))
	$events = $calendarlib->upcoming_events($module_rows,
		array_intersect(isset($module_params['calendarId']) ? (is_array($module_params['calendarId'])?$module_params['calendarId']: array($module_params['calendarId'])) : $calIds, $viewable),
		isset($module_params["maxDays"]) ? (int) $module_params["maxDays"] : 365,
		'start_asc', 
		isset($module_params["priorDays"]) ? (int) $module_params["priorDays"] : 0
	);
$smarty->assign('modUpcomingEvents', $events);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$module_rows = count($events);
$smarty->assign('module_rows', $module_rows);
if (isset($module_params['title'])) {
	$smarty->assign('tpl_module_title', $module_params['title']);
}