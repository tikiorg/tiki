<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

global $prefs, $tiki_p_view_tiki_calendar;
if ( $prefs['feature_action_calendar'] == 'y' && $tiki_p_view_tiki_calendar == 'y' ) {

	global $tikicalendarlib; include_once('lib/calendar/tikicalendarlib.php');
	global $headerlib; $headerlib->add_cssfile('css/calendar.css',20);
	global $calendarViewMode;

	$calendarViewMode = 'month';
	$group_by = 'day';

	include('tiki-calendar_setup.php');

	$viewTikiCals = $tikicalendarlib->getTikiItems(false);
	if ( isset($module_params['items']) ) {
		$viewTikiCals = array_intersect(explode(',', strtolower(str_replace(' ', '', $module_params['items']))), $viewTikiCals);
	}

	$tc_infos = $tikicalendarlib->getCalendar($viewTikiCals, $viewstart, $viewend, $group_by);
	foreach ( $tc_infos as $tc_key => $tc_val ) {
        	$smarty->assign($tc_key, $tc_val);
	}

	$module_params['name'] = 'tiki_calendar';
	if ( ! isset($module_params['title']) ) $module_params['title'] = tra('Tiki Calendar');

	$smarty->assign('daformat2', $tikilib->get_long_date_format());
	$smarty->assign('var', '');
	$smarty->assign('myurl', 'tiki-action_calendar.php');
}
?>
