<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

global $prefs, $tiki_p_view_calendar, $tiki_p_admin_calendars, $tikilib, $smarty;
if ( $prefs['feature_calendar'] == 'y' ) {

	global $calendarlib; include_once('lib/calendar/calendarlib.php');
	global $userlib; include_once('lib/userslib.php');
	global $headerlib; $headerlib->add_cssfile('css/calendar.css',20);
	global $calendarViewMode;

	if (isset($_REQUEST['viewmode'])) $save_viewmode = $_REQUEST['viewmode'];
	if (!empty($module_params['viewmode']))
		$calendarViewMode = $module_params['viewmode'];

	if (isset($_REQUEST['todate'])) $save_todate = $_REQUEST['todate'];

	if (isset($module_params['month_delta'])) {
		$calendarViewMode = 'month';
		include('tiki-calendar_setup.php');
		list($focus_day, $focus_month, $focus_year) = array(
			TikiLib::date_format("%d", $focusdate),
			TikiLib::date_format("%m", $focusdate),
			TikiLib::date_format("%Y", $focusdate)
		);
		$_REQUEST['todate'] = TikiLib::make_time(0,0,0,$focus_month+$module_params['month_delta'],1,$focus_year);
	}

	$group_by = 'day';
	if (empty($module_params['calIds'])) {
		if (!empty($_SESSION['CalendarViewGroups'])) {
			$module_params['calIds'] = $_SESSION['CalendarViewGroups'];
		} elseif ( $prefs['feature_default_calendars'] == 'n' ) {
			$module_params['calIds'] = $calendarlib->list_calendars();
			$module_params['calIds'] = array_keys($module_params['calIds']['data']);
		} elseif ( ! empty($prefs['default_calendars']) ) {
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
	$_REQUEST['gbi'] = 'y';
	$_REQUEST['viewlist'] = 'table';
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

	if ( isset($save_todate) ) {
		$_REQUEST['todate'] = $save_todate;
	} else {
		unset($_REQUEST['todate']);
	}
}
