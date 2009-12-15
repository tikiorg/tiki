<?php

// $Id: 

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ('lib/calendar/calendarlib.php');
$rawcals = $calendarlib->list_calendars();

if (array_key_exists('data',$rawcals)) {
	$rawcals = $rawcals['data'];
	$smarty->assign('rawcals',$rawcals);
}

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["calprefs"])) { 	 
	check_ticket('admin-inc-cal'); 	 

	simple_set_toggle('calendar_sticky_popup');
	simple_set_toggle('calendar_view_tab');
	simple_set_toggle('feature_jscalendar');
	simple_set_toggle('feature_cal_manual_time');
	simple_set_value('feature_default_calendars');
	simple_set_value('default_calendars','',true);
	simple_set_value('calendar_view_mode');
	simple_set_value('calendar_firstDayofWeek');
	simple_set_value('calendar_timespan');
	simple_set_value('calendar_start_year');
	simple_set_value('calendar_end_year');
	simple_set_value('calendar_list_begins_focus');
}
  	 
ask_ticket('admin-inc-cal'); 	 
?>
