<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_calendars.php,v 1.7 2003-11-07 23:12:11 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/calendar/calendarlib.php');

if ($tiki_p_admin_calendar != 'y' and $tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!isset($_REQUEST["calendarId"])) {
	$_REQUEST["calendarId"] = 0;
}

if (isset($_REQUEST["drop"])) {
	$calendarlib->drop_calendar($_REQUEST["drop"]);

	$_REQUEST["calendarId"] = 0;
}

if (isset($_REQUEST["save"])) {
	$customflags["customlanguages"] = $_REQUEST["customlanguages"];
	$customflags["customlocations"] = $_REQUEST["customlocations"];
	$customflags["customcategories"] = $_REQUEST["customcategories"];
	$customflags["custompriorities"] = $_REQUEST["custompriorities"];
	$_REQUEST["calendarId"] = $calendarlib->set_calendar($_REQUEST["calendarId"],$user,$_REQUEST["name"],$_REQUEST["description"],$customflags);
}

if ($_REQUEST["calendarId"]) {
	$info = $calendarlib->get_calendar($_REQUEST["calendarId"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["description"] = '';
	$info["customlanguages"] = 'n';
	$info["customlocations"] = 'n';
	$info["customcategories"] = 'n';
	$info["custompriorities"] = 'n';
	$info["user"] = "$user";
}

$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('user', $info["user"]);
$smarty->assign('customlanguages', $info["customlanguages"]);
$smarty->assign('customlocations', $info["customlocations"]);
$smarty->assign('customcategories', $info["customcategories"]);
$smarty->assign('custompriorities', $info["custompriorities"]);
$smarty->assign('calendarId', $_REQUEST["calendarId"]);

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

$calendars = $calendarlib->list_calendars(0, -1, $sort_mode, $find, 0);
$smarty->assign_by_ref('calendars', $calendars);

$groups = $userlib->get_groups();

$cat_type = 'calendar';
$cat_objid = $_REQUEST["calendarId"];
include_once ("categorize_list.php");

// Display the template
$smarty->assign('mid', 'tiki-admin_calendars.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
