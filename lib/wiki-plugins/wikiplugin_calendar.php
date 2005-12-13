<?php

function wikiplugin_calendar_help() {
	return tra("Calendar").":<br />~np~{CALENDAR(calendarId=>3,viewmode=>topicName)}{CALENDAR}<br />Id is optional. if not given, last article is used. default field is
	heading.~/np~";
}

function wikiplugin_calendar($data, $params) {
	global $smarty;
	global $tikilib;
	global $feature_calendar;
	global $tiki_p_admin;
	global $tiki_p_view_calendar;
	global $dbTiki;

	extract ($params,EXTR_SKIP);
	include_once("tiki-show_calendar.php");
	
	if(!isset($dc))
	$dc = $tikilib->get_date_converter($user);

	if(!isset($calendarId))
	$calendarId = 1;

	if(!isset($viewmode))
	$viewmode = "semester";

	return $smarty->fetch('tiki-show_calendar.tpl');
	//return $viewmode;

}

?>
