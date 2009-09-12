<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
// Process Features form(s)
if (isset($_REQUEST["features"])) {
	$features_toggles = array(
		"feature_action_calendar",
		"feature_banning",
		"feature_comm",
		"feature_contacts",
		"feature_custom_home",
		"feature_debug_console",
		"feature_events", //2009-04-29 marclaporte: can we remove this?
		"feature_integrator",
		"feature_messages",
		"feature_minical",
		"feature_notepad",
		"feature_redirect_on_error",
		"feature_referer_stats",
		"feature_stats",
		"feature_tasks",
		"feature_mytiki",
		"feature_userPreferences",
		"feature_user_bookmarks",
		"feature_user_watches",
		"feature_group_watches",
		"feature_daily_report_watches",
		"feature_user_watches_translations",
		"feature_userfiles",
		"feature_usermenu",
		"feature_workflow",
		"feature_xmlrpc",
		"feature_userlevels",
		"feature_tikitests",
		"feature_groupalert",
		"use_minified_scripts",
		'debug_ignore_xdebug',
		'feature_purifier',
	);
	$pref_byref_values = array(
		"user_flip_modules"
	);
	check_ticket('admin-inc-features');
	foreach($features_toggles as $toggle) {
		simple_set_toggle($toggle);
	}
	foreach($pref_byref_values as $britem) {
		byref_set_value($britem);
	}
	$cachelib->empty_full_cache();
}
$smarty->assign('php_major_version', substr(PHP_VERSION, 0, strpos(PHP_VERSION, '.')));
ask_ticket('admin-inc-features');
