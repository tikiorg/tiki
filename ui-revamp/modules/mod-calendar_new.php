<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

global $prefs, $tiki_p_view_calendar;
if ( $prefs['feature_calendar'] == 'y' && $tiki_p_view_calendar == 'y' ) {

	global $calendarlib; include_once('lib/calendar/calendarlib.php');
	global $headerlib; $headerlib->add_cssfile('css/calendar.css',20);
	global $calendarViewMode;

	$calendarViewMode = 'month';
	$group_by = 'day';

	include('tiki-calendar_setup.php');

	$tc_infos = $calendarlib->getCalendar(array(1), $viewstart, $viewend, $group_by);

	foreach ( $tc_infos as $tc_key => $tc_val ) {
        	$smarty->assign($tc_key, $tc_val);
	}

	$module_params['name'] = 'calendar';
	if ( ! isset($module_params['title']) ) $module_params['title'] = tra('Calendar');

	$smarty->assign('daformat2', $tikilib->get_long_date_format());
	$smarty->assign('var', '');
	$smarty->assign('myurl', 'tiki-calendar.php');
}
?>
