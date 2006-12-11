<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_forums.php,v 1.11 2006-12-11 22:36:15 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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
	simple_set_toggle('feature_forum_rankings');
	simple_set_toggle('feature_forum_parse');
	simple_set_toggle('feature_forum_quickjump');
	simple_set_toggle('feature_forum_topicd');
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

include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);
$forums = $commentslib->list_forums(0, -1, 'name_desc', '');
$smarty->assign_by_ref('forums', $forums["data"]);
ask_ticket('admin-inc-forums');
?>
