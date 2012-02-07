<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once ('tiki-setup.php');

if (php_sapi_name() != 'cli') {
	$access->check_permission('tiki_p_admin');
}

if ($prefs['feature_daily_report_watches'] != 'y') {
	die(tr('This feature is disabled'));
}

include_once ('lib/reportslib.php');

$reportsUsers = Reports_Factory::build('Reports_Users');
$reportsCache = Reports_Factory::build('Reports_Cache');

foreach ($reportsUsers->getUsersForReport() as $key => $user) {
	$userReportPreferences = $reportsUsers->get($user);
	$userData = $userlib->get_user_info($user);

	// if email address isn't set, do nothing but clear the cache
	if (!empty($userData['email'])) {
		//Fetch cache
		$cache = $reportsCache->get($user);
		//Send email if there is a cache or if always_email = true
		if (!empty($cache) || $userReportPreferences['always_email'])
			$reportslib->sendEmail($userData, $userReportPreferences, $cache);
	}
	//Update Database
	$reportsUsers->updateLastReport($userData['login']);
	//Empty cache
	$reportsCache->delete($userData['login']);
}
