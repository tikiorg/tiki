<?php

// $Id: 

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

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

	simple_set_value('calendar_view_mode');
	simple_set_value('calendar_firstDayofWeek');
	simple_set_value('calendar_timespan');
	simple_set_value('calendar_start_year');
	simple_set_value('calendar_end_year');
}
  	 
ask_ticket('admin-inc-cal'); 	 
?>
