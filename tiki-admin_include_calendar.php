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
$calendar_sticky_popup =  $tikilib->get_preference('calendar_sticky_popup', 'n'); 
$calendar_view_tab =  $tikilib->get_preference('calendar_view_tab', 'n'); 
$calendar_view_mode = $tikilib->get_preference('calendar_view_mode', 'week');


if (isset($_REQUEST["calprefs"])) { 	 
	check_ticket('admin-inc-cal'); 	 
	if (isset($_REQUEST["calendar_sticky_popup"]) && $_REQUEST["calendar_sticky_popup"] == "on") { 	 
		$tikilib->set_preference('calendar_sticky_popup', 'y'); 	 
		$calendar_sticky_popup = "y"; 	 
	} else { 	 
		$tikilib->set_preference('calendar_sticky_popup', 'n'); 	 
		$calendar_sticky_popup = "n"; 	 
	}
 	if (isset($_REQUEST["calendar_view_tab"]) && $_REQUEST["calendar_view_tab"] == "on") { 	 
		$tikilib->set_preference('calendar_view_tab', 'y'); 	 
		$calendar_view_tab = "y"; 	 
	} else { 	 
		$tikilib->set_preference('calendar_view_tab', 'n'); 	 
		$calendar_view_tab = "n"; 	 
	}
 	if (isset($_REQUEST["calendar_view_mode"])) { 	 
		$tikilib->set_preference('calendar_view_mode', $_REQUEST["calendar_view_mode"]); 	 
		$calendar_view_mode = $_REQUEST["calendar_view_mode"];
		unset($_SESSION['CalendarViewMode']);
	} else { 	 
		$tikilib->set_preference('calendar_view_mode', 'week'); 	 
		$calendar_view_mode = 'week'; 	 
	}
 }
 	if (isset($_REQUEST["feature_cal_manual_time"]) && $_REQUEST["feature_cal_manual_time"] == "on") { 	 
		$tikilib->set_preference('feature_cal_manual_time', 'y'); 	 
		$feature_cal_manual_time = "y"; 	 
	} else { 	 
		$tikilib->set_preference('feature_cal_manual_time', 'n'); 	 
		$feature_cal_manual_time = "n"; 	 
	}	
 	if (isset($_REQUEST["feature_jscalendar"]) && $_REQUEST["feature_jscalendar"] == "on") { 	 
		$tikilib->set_preference('feature_jscalendar', 'y'); 	 
		$feature_jscalendar = "y"; 	 
	} else { 	 
		$tikilib->set_preference('feature_jscalendar', 'n'); 	 
		$feature_jscalendar = "n"; 	 
	}	
}
  	 
 ask_ticket('admin-inc-cal'); 	 
$smarty->assign("calendar_sticky_popup", $calendar_sticky_popup);
$smarty->assign("calendar_view_tab", $calendar_view_tab);
$smarty->assign("calendar_view_mode", $calendar_view_mode);
$smarty->assign("feature_cal_manual_time", $feature_cal_manual_time);
$smarty->assign("feature_jscalendar", $feature_jscalendar);
?>
