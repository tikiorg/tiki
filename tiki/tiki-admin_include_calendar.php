<?php

// $Header: 

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["calprefs"])) {
	check_ticket('admin-inc-cal');
	if (isset($_REQUEST["calendar_timezone"]) && $_REQUEST["calendar_timezone"] == "on") {
        	$tikilib->set_preference('calendar_timezone', 'y');
		$calendar_timezone = "y";
	}
	else {
		$tikilib->set_preference('calendar_timezone', 'n');
		$calendar_timezone = "n";
	}
}
else
	$tikilib->get_preference('calendar_timezone', 'n');
	
ask_ticket('admin-inc-cal');
$smarty->assign("calendar_timezone", $calendar_timezone);
?>
