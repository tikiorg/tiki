<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-calendar_import.php,v 1.5 2007-10-12 07:55:25 nyloth Exp $

// Based on tiki-galleries.php
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
require_once ('lib/calendar/calendarlib.php');

if ($prefs['feature_calendar'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_calendar");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_calendar != 'y') {
	$smarty->assign('msg', tra("Access Denied").": feature_calendar");
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["import"]) && isset($_REQUEST["calendarId"]) && isset($_FILES["fileCSV"])) {
	if ($calendarlib->importCSV($_FILES["fileCSV"]["name"], $_REQUEST["calendarId"]))
		$smarty->assign('updated', "y");
}
$calendars = $calendarlib->list_calendars(); // no check perm as p_admin only
$smarty->assign_by_ref('calendars', $calendars['data']);

$section = 'calendar';
include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-calendar_import.tpl');
$smarty->display("tiki.tpl");

?>
