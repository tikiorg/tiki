<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ('tiki-setup.php');
if ( '127.0.0.1' != $tikilib->get_ip_address() ) {
	die("This script can only be called by the server!");
}
if ($prefs['feature_daily_report_watches'] != 'y') {
	die("This feature is disabled");
}
include_once ('lib/reportslib.php');

//Complete URL to your Tikiwiki installation without ending slash!
$tikiUrl = "http://localhost/trunktest";

foreach($reportslib->getUsersForSendingReport() as $key => $user) {
	$report_preferences = $reportslib->get_report_preferences_by_user($user);
	$user_data = $userlib->get_user_info($user);

	//If Emailadress isn´t set, do nothing but clear the cache
	if (!empty($user_data['email'])) {
		//Fetch cache
		$report_cache = $reportslib->get_report_cache_entries_by_user($user, "time ASC");
		//Send email if there is a cache or if always_email = true
		if ($report_cache OR (!$report_cache && $report_preferences['always_email']))
			$reportslib->sendEmail($user_data, $report_preferences, $report_cache, $tikiUrl);
	}
	//Update Database
	$reportslib->updateLastSent($user_data['login']);
	//Empty cache
	$reportslib->deleteUsersReportCache($user_data['login']);
}
