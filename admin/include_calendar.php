<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$calendarlib = TikiLib::lib('calendar');
$rawcals = $calendarlib->list_calendars();
if (array_key_exists('data', $rawcals)) {
	$rawcals = $rawcals['data'];
	$smarty->assign('rawcals', $rawcals);
}
// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
if (isset($_REQUEST['calprefs'])) {
	check_ticket('admin-inc-cal');
	simple_set_toggle('feature_jscalendar');
	simple_set_value('feature_default_calendars');
	simple_set_value('default_calendars', '', true);
}
ask_ticket('admin-inc-cal');
