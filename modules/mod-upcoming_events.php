<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ('lib/calendar/calendarlib.php');
global $calendarlib;

//$events = $calendarlib->last_modif_events($module_rows, isset($module_params["calendarId"]) ? $module_params["calendarId"] : 0);
if (!isset($calendarlib)) global $calendarlib;
$events = $calendarlib->upcoming_events($module_rows, 
					isset($module_params["calendarId"]) ? $module_params["calendarId"] : 0, 
					isset($module_params["maxDays"]) ? $module_params["maxDays"] : 365);

$smarty->assign('modUpcomingEvents', $events);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
