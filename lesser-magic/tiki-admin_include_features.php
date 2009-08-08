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
		"feature_actionlog",
		"feature_ajax",
		"feature_ajax_autosave",
		"feature_articles",
		"feature_banners",
		"feature_banning",
		"feature_calendar",
		"feature_categories",
		"feature_comm",
		"feature_contact",
		"feature_contacts",
		"feature_contribution",
		"feature_multilingual",
		"feature_custom_home",
		"feature_debug_console",
		"feature_directory",
		"feature_events", //2009-04-29 marclaporte: can we remove this?
		"feature_faqs",
		"feature_featuredLinks",
		"feature_file_galleries",
		"feature_forums",
		"feature_freetags",
		"feature_friends",
		"feature_fullscreen",
		"feature_gmap",
		"feature_purifier",
		"feature_html_pages",
		"feature_integrator",
		"feature_intertiki",
		"feature_jscalendar",
		"feature_live_support",
		"feature_mailin",
		"feature_maps",
		"feature_messages",
		"feature_minical",
		"feature_mobile",
		"feature_morcego",
		"feature_newsletters",
		"feature_notepad",
		"feature_phplayers",
		"feature_cssmenus",
		"feature_polls",
		"feature_tell_a_friend",
		"feature_quizzes",
		"feature_redirect_on_error",
		"feature_referer_stats",
		"feature_score",
		"feature_search",
		"feature_sheet",
		"feature_sefurl",
		"feature_shoutbox",
		"feature_stats",
		"feature_surveys",
		"feature_tasks",
		"feature_trackers",
		"feature_mytiki",
		"feature_userPreferences",
		"feature_user_bookmarks",
		"feature_user_watches",
		"feature_group_watches",
		"feature_daily_report_watches",
		"feature_quick_object_perms",
		"feature_user_watches_translations",
		"feature_userfiles",
		"feature_usermenu",
		"feature_webmail",
		"feature_workflow",
		"feature_wysiwyg",
		"feature_xmlrpc",
		"feature_copyright",
		"feature_multimedia",
		"feature_userlevels",
		"feature_shadowbox",
		"feature_tikitests",
		"feature_magic",
		"feature_minichat",
		"feature_comments_moderation",
		"feature_comments_locking",
		"feature_groupalert",
		"feature_wiki_mindmap",
		"use_minified_scripts",
		"feature_print_indexed",
		'debug_ignore_xdebug',
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
