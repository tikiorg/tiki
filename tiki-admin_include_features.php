<?php
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

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
		"feature_blogs",
		"feature_calendar",
		"feature_categories",
		"feature_charts",
		"feature_comm",
		"feature_contact",
		"feature_contacts",
		"feature_contribution",
		"feature_multilingual",
		"feature_custom_home",
		"feature_debug_console",
		"feature_directory",
		"feature_drawings",
		"feature_events", //2009-04-29 marclaporte: can we remove this?
		"feature_faqs",
		"feature_featuredLinks",
		"feature_file_galleries",
		"feature_forums",
		"feature_freetags",
		"feature_friends",
		"feature_fullscreen",
		"feature_galleries",
		"feature_games",
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
		"feature_newsreader",
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
		"feature_user_watches_translations",
		"feature_userfiles",
		"feature_usermenu",
		"feature_webmail",
		"feature_wiki",
		"feature_workflow",
		"feature_wysiwyg",
		"feature_xmlrpc",
		"feature_copyright",
		"feature_multimedia",
		"feature_userlevels",
		"feature_mootools",
		"feature_jquery",
		"feature_shadowbox",
		"feature_swfobj",
		"feature_tikitests",
		"feature_magic",
		"feature_minichat",
		"feature_comments_moderation",
		"feature_comments_locking",
		"feature_groupalert",
		"feature_wiki_mindmap",
		"use_minified_scripts",
		"feature_print_indexed",
		"feature_floating_help",
	);

	$pref_byref_values = array(
		"user_flip_modules"
	);

	check_ticket('admin-inc-features');
	foreach ($features_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
	foreach ($pref_byref_values as $britem) {
		byref_set_value ($britem);
	}

	$cachelib->empty_full_cache();

}

if (!empty($_REQUEST['tabs'])) {
	$_SESSION["tabbed_admin_features"] = $_REQUEST['tabs']=='on'?'n':'' ;
} else {
	if( !empty($_REQUEST["feature_tabs"]) ) {	// Verifies that form was submitted
		$_SESSION["tabbed_admin_features"] = '' ;
	}
}
$smarty->assign('tabs', empty($_SESSION["tabbed_admin_features"])?'':'n');

$smarty->assign('php_major_version', substr(PHP_VERSION, 0, strpos(PHP_VERSION, '.')));

ask_ticket('admin-inc-features');
?>
