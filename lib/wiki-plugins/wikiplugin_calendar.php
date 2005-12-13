<?php

function wikiplugin_calendar_help() {
	return tra("Calendar").":<br />~np~{CALENDAR(calendarId=>1)}{CALENDAR}<br />You can choose with calendar to display by setting the calendarId.~/np~";
}

function wikiplugin_calendar($data, $params) {
	global $smarty;
	global $tikilib;
	global $feature_calendar;
	global $tiki_p_admin;
	global $tiki_p_view_calendar;
	global $dbTiki;
	global $dc;

	extract ($params,EXTR_SKIP);
	include_once("tiki-show_calendar.php");
	

	if(!isset($calendarId))
	$calendarId = 1;

	return $smarty->fetch('tiki-show_calendar.tpl');

}

?>
