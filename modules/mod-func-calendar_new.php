<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

function module_calendar_new_info() {
	return array(
		'name' => tra('Calendar'),
		'description' => tra('Includes a calendar or a list of calendar events.'),
		'prefs' => array( 'feature_calendar' ),
		'params' => array(
			'calendarId' => array(
				'name' => tra('Calendar filter'),
				'description' => tra('If set to a calendar identifier, restricts the events displayed to those in the specified calendar.'),
				'filter' => 'digits',
			),
			'month_delta' => array(
				'name' => tra('Displayed month (relative)'),
				'description' => tra('Distance in month to the month to display. A distance of -1 would display the previous month. Setting this option implies a calendar view type with a month time span.') . ' ' . tra('Example values:') . ' 2, 0, -2, -12.',
				'filter' => 'int'
			),
			'viewlist' => array(
				'name' => tra('View type'),
				'description' => tra('Determines how to show events.') . ' ' . tra('Possible values:') . ' ' . 'calendar, list. ' . tra('Default value:') . ' calendar.',
				'filter' => 'word',
			),
			'viewmode' => array(
				'name' => tra('Calendar view type time span'),
				'description' => tra('If in calendar (or "table") view type, determines the time span displayed by the calendar.') . ' ' . tra('Possible values:') . ' year, semester, quarter, month, week, day. A user changing this time span in the calendar can change the time span the module displays for him.',
				'filter' => 'word'
			)
		)
	);
}

function module_calendar_new( $mod_reference, $module_params ) {
	global $prefs, $user, $tiki_p_admin_calendars, $tikilib, $smarty;
	global $calendarlib; include_once('lib/calendar/calendarlib.php');
	global $userlib; include_once('lib/userslib.php');
	global $headerlib; $headerlib->add_cssfile('css/calendar.css',20);
	global $calendarViewMode, $focusdate;

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
		$_REQUEST['todate'] = $tikilib->make_time(0,0,0,$focus_month+$module_params['month_delta'],1,$focus_year);
	}

	if (isset($module_params['calendarId'])) {
		$calIds = array($module_params['calendarId']);
	} // Should ideally support several ids at some point

	if (empty($calIds)) {
		if (!empty($_SESSION['CalendarViewGroups'])) {
			$calIds = $_SESSION['CalendarViewGroups'];
		} elseif ( $prefs['feature_default_calendars'] == 'n' ) {
			$calIds = $calendarlib->list_calendars();
			$calIds = array_keys($module_params['calIds']['data']);
		} elseif ( ! empty($prefs['default_calendars']) ) {
			$calIds = $_SESSION['CalendarViewGroups'] = is_array($prefs['default_calendars']) ? $prefs['default_calendars'] : unserialize($prefs['default_calendars']);
		} else {
			$calIds = array();
		}
	}
	foreach ($calIds as $i=>$cal_id) {
		if ($tiki_p_admin_calendars != 'y' && !$userlib->user_has_perm_on_object($user, $cal_id, 'calendar', 'tiki_p_view_calendar')) {
			unset($calIds[$i]);
		}
	}
	$_REQUEST['gbi'] = 'y';
	if ( !empty($module_params['viewlist']) ) {
		$_REQUEST['viewlist'] = $module_params['viewlist'];
	} else {
		$_REQUEST['viewlist'] = 'table';
	}

	include('tiki-calendar_setup.php');

	$tc_infos = $calendarlib->getCalendar($calIds, $viewstart, $viewend, 'day');
	if ($viewlist == 'list') {
		foreach ($tc_infos['listevents'] as $i=>$e) {
			$tc_infos['listevents'][$i]['head'] = '';
			$tc_infos['listevents'][$i]['group_description'] ='';
		}
		$tc_infos['listevents'] = array_unique($tc_infos['listevents']);	
	}

	foreach ( $tc_infos as $tc_key => $tc_val ) {
		$smarty->assign($tc_key, $tc_val);
	}

	$smarty->assign('name', 'calendar');

	$smarty->assign('daformat2', $tikilib->get_long_date_format());
	$smarty->assign('var', '');
	$smarty->assign('myurl', 'tiki-calendar.php');
	$smarty->assign('show_calendar_module', 'y');

	if ( isset($save_todate) ) {
		$_REQUEST['todate'] = $save_todate;
	} else {
		unset($_REQUEST['todate']);
	}
}
