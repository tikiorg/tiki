<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

global $prefs, $tiki_p_view_calendar, $tiki_p_admin_calendars;
if ( $prefs['feature_calendar'] == 'y' ) {

	global $calendarlib; include_once('lib/calendar/calendarlib.php');
	global $userlib; include_once('lib/userslib.php');
	global $headerlib; $headerlib->add_cssfile('css/calendar.css',20);
	global $calendarViewMode;

	$calendarViewMode = 'month';
	$group_by = 'day';
	if (empty($module_params['calIds'])) {
		if (!empty($_SESSION['CalendarViewGroups'])) {
			$module_params['calIds'] = $_SESSION['CalendarViewGroups'];
		} elseif (!empty($prefs['default_calendars'])) {
			$module_params['calIds'] = $_SESSION['CalendarViewGroups'] = is_array($prefs['default_calendars']) ? $prefs['default_calendars'] : unserialize($prefs['default_calendars']);
		} else {
			$module_params['calIds'] = array();
		}
	}
	foreach ($module_params['calIds'] as $i=>$cal_id) {
		if ($tiki_p_admin_calendars != 'y' && !$userlib->user_has_perm_on_object($user, $cal_id, 'calendar', 'tiki_p_view_calendar')) {
			unset($module_params['calIds'][$i]);
		}
	}
	include('tiki-calendar_setup.php');

	$tc_infos = $calendarlib->getCalendar($module_params['calIds'], $viewstart, $viewend, $group_by);

	foreach ( $tc_infos as $tc_key => $tc_val ) {
        	$smarty->assign($tc_key, $tc_val);
	}

	$module_params['name'] = 'calendar';
	if ( ! isset($module_params['title']) ) $module_params['title'] = tra('Calendar');

	$smarty->assign('daformat2', $tikilib->get_long_date_format());
	$smarty->assign('var', '');
	$smarty->assign('myurl', 'tiki-calendar.php');
}

