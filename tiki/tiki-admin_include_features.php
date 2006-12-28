<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_features.php,v 1.58 2006-12-28 09:55:17 mose Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.


// Process Features form(s)
if (isset($_REQUEST["features"])) {

$features_toggles = array(
	"contact_anon",
	"feature_action_calendar",
	"feature_actionlog",
	"feature_ajax",
	"feature_articles",
	"feature_autolinks",
	"feature_banners",
	"feature_banning",
	"feature_blogs",
	"feature_bot_bar",
	"feature_bot_bar_debug",
	"feature_bot_bar_icons",
	"feature_calendar",
	"feature_categories",
	"feature_categoryobjects",
	"feature_categorypath",
	"feature_charts",
	"feature_chat",
	"feature_comm",
	"feature_contact",
	"feature_contacts",
	"feature_contribution",
	"feature_custom_home",
	"feature_debug_console",
	"feature_directory",
	"feature_drawings",
	"feature_dynamic_content",
	"feature_edit_templates",
	"feature_editcss",
	"feature_eph",
	"feature_events",
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
	"feature_help",
	"feature_hotwords",
	"feature_hotwords_nw",
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
	"feature_modulecontrols",
	"feature_morcego",
	"feature_newsletters",
	"feature_newsreader",
	"feature_notepad",
	"feature_phplayers",
	"feature_polls",
	"feature_quizzes",
	"feature_redirect_on_error",
	"feature_referer_stats",
	"feature_score",
	"feature_search",
	"feature_sheet",
	"feature_shoutbox",
	"feature_siteidentity",
	"feature_smileys",
	"feature_stats",
	"feature_surveys",
	"feature_tabs",
	"feature_tasks",
	"feature_theme_control",
	"feature_top_bar",
	"feature_trackers",
	"feature_use_quoteplugin",
	"feature_userPreferences",
	"feature_user_bookmarks",
	"feature_user_watches",
	"feature_user_watches_translations",
	"feature_userfiles",
	"feature_usermenu",
	"feature_view_tpl",
	"feature_webmail",
	"feature_wiki",
	"feature_workflow",
	"feature_wysiwyg",
	"feature_xmlrpc",
	"layout_section",
	"user_assigned_modules"
);

    $pref_byref_values = array(
	"feature_left_column",
	"feature_right_column",
        "user_flip_modules"
    );


    check_ticket('admin-inc-features');
    foreach ($features_toggles as $toggle) {
        simple_set_toggle ($toggle);
    }
    foreach ($pref_byref_values as $britem) {
        byref_set_value ($britem);
    }
		$smarty->clear_compiled_tpl();
}

ask_ticket('admin-inc-features');
?>
