<?php

function wikiplugin_calendar_help() {
	return tra("Calendar").":<br />~np~{CALENDAR(calendarId=>1)}{CALENDAR}<br />You can choose with calendar to display by setting the calendarId.~/np~";
}

function wikiplugin_calendar($data, $params) {
    global $smarty, $tikilib, $prefs, $tiki_p_admin, $tiki_p_view_calendar;
    global $dbTiki, $dc, $user, $calendarlib;

    require_once("lib/calendar/calendarlib.php");

    extract ($params,EXTR_SKIP);
    include("tiki-calendar_setup.php");
    include("tiki-show_calendar.php");
    
    
    if(!isset($calendarId))
		$calendarId = 1;
    $_SESSION['CalendarViewGroups'] = array($calendarId);

    return $smarty->fetch('tiki-show_calendar.tpl');

}

?>
