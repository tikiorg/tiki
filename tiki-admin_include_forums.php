<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_forums.php,v 1.18.2.2 2008-01-08 16:01:38 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


if (isset($_REQUEST["homeforumprefs"]) && isset($_REQUEST["home_forum"])) {
	check_ticket('admin-inc-forums');
	simple_set_value('home_forum');
}

if (isset($_REQUEST["forumprefs"])) {
	check_ticket('admin-inc-forums');
	$pref_toggles = array('feature_forum_rankings',
	'feature_forum_parse',
	'feature_forum_topics_archiving',
	'feature_forum_replyempty',
	'feature_forum_quickjump',
	'feature_forum_topicd',
	'feature_forum_local_search',
	'feature_forum_local_tiki_search',
	'feature_forums_search',
        'feature_forum_content_search',
        'feature_forums_name_search',
	'feature_forums_allow_thread_titles',
	'forum_comments_no_title_prefix'
	);
	foreach ( $pref_toggles as $toggle) simple_set_toggle($toggle);

	simple_set_value('forums_ordering');
}

if (isset($_REQUEST["forumlistprefs"])) {
	check_ticket('admin-inc-forums');
	$pref_toggles = array(
	'forum_list_topics',
	'forum_list_posts',
	'forum_list_ppd',
	'forum_list_lastpost',
	'forum_list_visits',
	'forum_list_desc'
	);
	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
}

if (isset($_REQUEST["forumthreadprefs"])) {
	check_ticket('admin-inc-forums');
	$pref_toggles = array(
	'forum_thread_defaults_by_forum',
	'forum_thread_user_settings',
	'forum_thread_user_settings_keep'
	);
	$pref_values = array(
	'forum_comments_per_page',
	'forum_thread_style',
	'forum_thread_sort_mode'
	);
	foreach ( $pref_toggles as $toggle) simple_set_toggle($toggle);
	foreach ( $pref_values as $value ) simple_set_value($value);
}

include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);
$forums = $commentslib->list_forums(0, -1, 'name_desc', '');
$smarty->assign_by_ref('forums', $forums["data"]);
ask_ticket('admin-inc-forums');
?>
