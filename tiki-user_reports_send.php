<?php

include_once ('tiki-setup.php');

if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1") {
	$smarty->assign('msg', tra("This script can only be called by the server!"));
	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_daily_report_watches'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_daily_report_watches");
	$smarty->display("error.tpl");
	die;
}

include_once ('lib/tikilib.php');
include_once ('lib/wiki/histlib.php');
include_once ('lib/imagegals/imagegallib.php');
include_once ('lib/imagegals/imagegallib.php');
include_once ('lib/reports.php');

foreach ($reports->getUsersForReport() as $key => $user) {
	$report_preferences = $reports->get_report_preferences_by_user($user);
	$user_data = $userlib->get_user_info($user);
	//Wenn keine Emailadresse gesetzt ist, mache nichts und leere den Cache
	if (!empty($user_data['email'])) {
		//Hole Cache
		$report_cache = $reports->get_report_cache_entries_by_user($user, "time ASC");
		//Schicke Email wenn: Einträge vorhanden oder always_email = true
		if ($report_cache OR (!$report_cache && $report_preferences['always_email']))
			$reports->sendEmail($user_data, $report_preferences, $report_cache);
	}

	//Cache leeren
	$reports->deleteUsersReportCache($user_data);
}


?>