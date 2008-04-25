<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_calendars.php,v 1.34.2.2 2008-02-12 19:20:11 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/calendar/calendarlib.php');

if ($tiki_p_admin_calendar != 'y' and $tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["calendarId"])) {
	$_REQUEST["calendarId"] = 0;
} else {
	 $smarty->assign('individual', $userlib->object_has_one_permission($_REQUEST["calendarId"], 'calendar'));
}

if (isset($_REQUEST["drop"])) {
	$area = "delcalendar";
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		$calendarlib->drop_calendar($_REQUEST["drop"]);
		$_REQUEST["calendarId"] = 0;
	} else {
		key_get($area); 
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-calendars');
	$customflags["customlanguages"] = $_REQUEST["customlanguages"];
	$customflags["customlocations"] = $_REQUEST["customlocations"];
	$customflags["customparticipants"] = $_REQUEST["customparticipants"];
	$customflags["customcategories"] = $_REQUEST["customcategories"];
	$customflags["custompriorities"] = $_REQUEST["custompriorities"];
	$customflags["customsubscription"] = isset($_REQUEST["customsubscription"]) ? $_REQUEST["customsubscription"] : 'n';
	$customflags["personal"] = $_REQUEST["personal"];
	$options = $_REQUEST['options'];
	if (!preg_match('/^[0-9a-fA-F]{3,6}$/',$options['customfgcolor'])) $options['customfgcolor'] = '000000';
	if (!preg_match('/^[0-9a-fA-F]{3,6}$/',$options['custombgcolor'])) $options['custombgcolor'] = 'ffffff';
	$options['startday'] = $_REQUEST['startday_Hour']*60*60;
	$options['endday'] = $_REQUEST['endday_Hour']*60*60 - 1;
	$extra = array('calname','description','location','description','language','category','participants','url');
	foreach ($extra as $ex) {
		if (isset($_REQUEST['show'][$ex]) and $_REQUEST['show'][$ex] == 'on') {
			$options["show_$ex"] = 'y';
		} else {
			$options["show_$ex"] = 'n';
		}
	}
	$_REQUEST["calendarId"] = $calendarlib->set_calendar($_REQUEST["calendarId"],$user,$_REQUEST["name"],$_REQUEST["description"],$customflags,$options);
	if ($_REQUEST['personal'] == 'y') {
		$userlib->assign_object_permission("Registered", $_REQUEST["calendarId"], "calendar", "tiki_p_view_calendar");
		$userlib->assign_object_permission("Registered", $_REQUEST["calendarId"], "calendar", "tiki_p_view_events");
		$userlib->assign_object_permission("Registered", $_REQUEST["calendarId"], "calendar", "tiki_p_add_events");
		$userlib->assign_object_permission("Registered", $_REQUEST["calendarId"], "calendar", "tiki_p_change_events");
	}
	if ($prefs['feature_categories'] == 'y') {
		$cat_type = 'calendar';
		$cat_objid = $_REQUEST["calendarId"];
		$cat_desc = $_REQUEST["description"];
		$cat_name = $_REQUEST["name"];
		$cat_href = "tiki-calendar.php?calIds[]=".$_REQUEST["calendarId"];
		include_once("categorize.php");
	}
}
if (isset($_REQUEST['clean']) && isset($_REQUEST['days'])) {
	check_ticket('admin-calendars');
	$calendarlib->cleanEvents($_REQUEST['calendarId'], $_REQUEST['days']);
}
if ($prefs['feature_categories'] == 'y') {
	$cat_type = 'calendar';
	$cat_objid = $_REQUEST["calendarId"];
	include_once ("categorize_list.php");
	$cs = $categlib->get_object_categories('calendar', $cat_objid);
	if (!empty($cs)) {
		for ($i = count($categories) - 1; $i >= 0; --$i) {
			if (in_array($categories[$i]['categId'], $cs)) {
				$categories[$i]['incat'] = 'y';
			}
		}
	}
}

if ($_REQUEST["calendarId"]) {
	$info = $calendarlib->get_calendar($_REQUEST["calendarId"]);
	setcookie("activeTabs".urlencode(substr($_SERVER["REQUEST_URI"],1)),"tab2");
} else {
	$info = array();
	$info["name"] = '';
	$info["description"] = '';
	$info["customlanguages"] = 'n';
	$info["customlocations"] = 'n';
	$info["customparticipants"] = 'n';
	$info["customcategories"] = 'n';
	$info["custompriorities"] = 'n';
	$info["customsubscription"] = 'n';
	$info["customurl"] = 'n';
	$info["customfgcolor"] = '000000';
	$info["custombgcolor"] = 'ffffff';
	$info["show_calname"] = 'y';
	$info["show_description"] = 'y';
	$info["show_category"] = 'n';
	$info["show_location"] = 'n';
	$info["show_language"] = 'n';
	$info["show_participants"] = 'n';
	$info["show_url"] = 'n';
	$info["user"] = "$user";
	$info["personal"] = 'n';
	$info["startday"] = '25200';
	$info["endday"] = '72000';
}

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('user', $info["user"]);
$smarty->assign('customlanguages', $info["customlanguages"]);
$smarty->assign('customlocations', $info["customlocations"]);
$smarty->assign('customparticipants', $info["customparticipants"]);
$smarty->assign('customcategories', $info["customcategories"]);
$smarty->assign('custompriorities', $info["custompriorities"]);
$smarty->assign('customsubscription', $info["customsubscription"]);
$smarty->assign('customurl', $info["customurl"]);
$smarty->assign('customfgcolor', $info["customfgcolor"]);
$smarty->assign('custombgcolor', $info["custombgcolor"]);
$smarty->assign('show_calname', $info["show_calname"]);
$smarty->assign('show_description', $info["show_description"]);
$smarty->assign('show_category', $info["show_category"]);
$smarty->assign('show_location', $info["show_location"]);
$smarty->assign('show_language', $info["show_language"]);
$smarty->assign('show_participants', $info["show_participants"]);
$smarty->assign('show_url', $info["show_url"]);
$smarty->assign('calendarId', $_REQUEST["calendarId"]);
$smarty->assign('personal', $info["personal"]);
$smarty->assign('startday', $info["startday"] < 0 ?0: round($info['startday']/(60*60)));
$smarty->assign('endday', $info["endday"] < 0 ?0: round($info['endday']/(60*60)));
$smarty->assign('hours', array('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24'));

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$calendars = $calendarlib->list_calendars(0, -1, $sort_mode, $find);

foreach (array_keys($calendars["data"]) as $i) {
	$calendars["data"][$i]["individual"] = $userlib->object_has_one_permission($i, 'calendar');
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);

$cant_pages = ceil($calendars["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($calendars["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('calendars', $calendars["data"]);

// $cat_type = 'calendar';
// $cat_objid = $_REQUEST["calendarId"];
// include_once ("categorize_list.php");
$section = 'calendar';
include_once ('tiki-section_options.php');

ask_ticket('admin-calendars');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('uses_tabs', 'y');
$smarty->assign('mid', 'tiki-admin_calendars.tpl');
$smarty->display("tiki.tpl");

?>
