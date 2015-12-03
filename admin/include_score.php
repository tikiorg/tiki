<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
$scorelib = TikiLib::lib('score');
if (isset($_REQUEST["scoreevents"])) {
	check_ticket('admin-inc-score');
	if (isset($_REQUEST['events']) && is_array($_REQUEST['events'])) {
		$scorelib->update_events($_REQUEST['events']);
	}
}

$smarty->assign('eventTypes', $scorelib->getEventTypes());
$smarty->assign('events', $scorelib->get_all_events());
ask_ticket('admin-inc-score');
