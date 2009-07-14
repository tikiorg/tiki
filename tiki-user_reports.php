<?php
// Copyright (c) 2009, Clemens John
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ('tiki-setup.php');
include_once('lib/reportslib.php');

if (!$user) {
	$smarty->assign('msg', tra("You must log in to use this feature"));
	$smarty->assign('errortype', '402');
	$smarty->display("error.tpl");
	die;
}
if ($prefs['feature_daily_report_watches'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_daily_report_watches");
	$smarty->display("error.tpl");
	die;
}
include_once ('lib/reportslib.php');
//Enable User Reports
if (isset($_POST['report_preferences']) && $_POST['use_daily_reports'] == "true") {
	$reportslib->add_user_report($user, $_POST['interval'], $_POST['view'], $_POST['type'], $_POST['always_email']);
	header('Location: tiki-user_watches.php');
	die;
}
//Disable User Reports
if (isset($_POST['report_preferences']) && $_POST['use_daily_reports'] != "true") {
	$reportslib->delete_user_report($user);
	header('Location: tiki-user_watches.php');
	die;
}
