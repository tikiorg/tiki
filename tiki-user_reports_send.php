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
include_once ('lib/reportslib.php');

foreach ($tikilib->getUsersForSendingReport() as $key => $user) {
	$report_preferences = $tikilib->get_report_preferences_by_user($user);
	$user_data = $userlib->get_user_info($user);
	//If Emailadress isn´t set, do nothing but clear the cache
	if (!empty($user_data['email'])) {
		//Fetch cache
		$report_cache = $tikilib->get_report_cache_entries_by_user($user, "time ASC");
		//Send email if there is a cache or if always_email = true
		if ($report_cache OR (!$report_cache && $report_preferences['always_email']))
			$reportslib->sendEmail($user_data, $report_preferences, $report_cache);
	}

	//Update Database
	$tikilib->updateLastSent($user_data);

	//Empty cache
	$tikilib->deleteUsersReportCache($user_data);
}

