<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once('tiki-setup.php');

$access->check_user($user);
$access->check_feature('feature_daily_report_watches');

$reportsManager = Reports_Factory::build('Reports_Manager');

//Enable User Reports
if (isset($_POST['report_preferences']) && $_POST['use_daily_reports'] == "true") {
	$interval = filter_input(INPUT_POST, 'interval', FILTER_SANITIZE_STRING);
	$view = filter_input(INPUT_POST, 'view', FILTER_SANITIZE_STRING);
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	$always_email = filter_input(INPUT_POST, 'always_email', FILTER_SANITIZE_NUMBER_INT);
	if ($always_email != 1)
		$always_email = 0;
	
	$reportsManager->save($user, $interval, $view, $type, $always_email);
	header('Location: tiki-user_watches.php');
	die;
}
//Disable User Reports
if (isset($_POST['report_preferences']) && $_POST['use_daily_reports'] != "true") {
	$reportsManager->delete($user);
	header('Location: tiki-user_watches.php');
	die;
}
