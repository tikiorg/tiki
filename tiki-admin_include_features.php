<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_features.php,v 1.17 2004-02-26 06:30:24 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$features_toggles = array(
	"feature_articles",
	"feature_autolinks",
	"feature_babelfish",
	"feature_babelfish_logo",
	"feature_banners",
	"feature_banning",
	"feature_blogs",
	"feature_bot_bar",
	"feature_calendar",
	"feature_categories",
	"feature_categoryobjects",
	"feature_categorypath",
	"feature_charts",
	"feature_chat",
	"feature_comm",
	"feature_contact",
	"contact_anon",
	"feature_custom_home",
	"feature_debug_console",
	"feature_directory",
	"feature_drawings",
	"feature_dynamic_content",
	"feature_edit_templates",
	"feature_editcss",
	"feature_eph",
	"feature_faqs",
	"feature_featuredLinks",
	"feature_file_galleries",
	"feature_forums",
	"feature_galleries",
	"feature_games",
	"feature_homework",
	"feature_hotwords",
	"feature_hotwords_nw",
	"feature_help",
	"feature_html_pages",
	"feature_integrator",
	"feature_jscalendar",
	"feature_jukebox",
	"feature_left_column",
	"feature_live_support",
	"feature_maps",
	"feature_messages",
	"feature_minical",
  "feature_modulecontrols",
	"feature_newsletters",
	"feature_newsreader",
	"feature_notepad",
  "feature_phplayers",
	"feature_phpopentracker",
	"feature_polls",
	"feature_quizzes",
	"feature_referer_stats",
	"feature_right_column",
	"feature_search",
	"feature_search_fulltext",
	"feature_search_stats",
	"feature_shoutbox",
	"feature_smileys",
	"feature_stats",
	"feature_submissions",
	"feature_surveys",
	"feature_tasks",
	"feature_theme_control",
	"feature_top_bar",
	"feature_trackers",
	"feature_userPreferences",
	"feature_user_bookmarks",
	"feature_user_watches",
	"feature_userfiles",
	"feature_usermenu",
	"feature_view_tpl",
	"feature_webmail",
	"feature_wiki",
	"feature_workflow",
	"feature_xmlrpc",
	"layout_section",
	"user_assigned_modules"
);

// Process Features form(s)
if (isset($_REQUEST["features"])) {
    check_ticket('admin-inc-features');
	foreach ($features_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
}
ask_ticket('admin-inc-features');
?>
