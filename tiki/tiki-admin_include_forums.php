<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_forums.php,v 1.4 2003-12-28 20:12:51 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
if (isset($_REQUEST["homeforumprefs"]) && isset($_REQUEST["homeForum"])) {
	check_ticket('admin-inc-forums');
	$tikilib->set_preference("home_forum", $_REQUEST["homeForum"]);

	$smarty->assign('home_forum', $_REQUEST["homeForum"]);
}

if (isset($_REQUEST["forumprefs"])) {
	check_ticket('admin-inc-forums');
	if (isset($_REQUEST["feature_forum_rankings"]) && $_REQUEST["feature_forum_rankings"] == "on") {
		$tikilib->set_preference("feature_forum_rankings", 'y');

		$smarty->assign("feature_forum_rankings", 'y');
	} else {
		$tikilib->set_preference("feature_forum_rankings", 'n');

		$smarty->assign("feature_forum_rankings", 'n');
	}

	if (isset($_REQUEST["feature_forum_parse"]) && $_REQUEST["feature_forum_parse"] == "on") {
		$tikilib->set_preference("feature_forum_parse", 'y');

		$smarty->assign("feature_forum_parse", 'y');
	} else {
		$tikilib->set_preference("feature_forum_parse", 'n');

		$smarty->assign("feature_forum_parse", 'n');
	}

	if (isset($_REQUEST["feature_forum_quickjump"]) && $_REQUEST["feature_forum_quickjump"] == "on") {
		$tikilib->set_preference("feature_forum_quickjump", 'y');

		$smarty->assign("feature_forum_quickjump", 'y');
	} else {
		$tikilib->set_preference("feature_forum_quickjump", 'n');

		$smarty->assign("feature_forum_quickjump", 'n');
	}

	if (isset($_REQUEST["feature_forum_topicd"]) && $_REQUEST["feature_forum_topicd"] == "on") {
		$tikilib->set_preference("feature_forum_topicd", 'y');

		$smarty->assign("feature_forum_topicd", 'y');
	} else {
		$tikilib->set_preference("feature_forum_topicd", 'n');

		$smarty->assign("feature_forum_topicd", 'n');
	}

	if (isset($_REQUEST["forums_ordering"])) {
		$tikilib->set_preference("forums_ordering", $_REQUEST["forums_ordering"]);

		$smarty->assign('forums_ordering', $_REQUEST["forums_ordering"]);
	}
}

if (isset($_REQUEST["forumlistprefs"])) {
	check_ticket('admin-inc-forums');
	if (isset($_REQUEST["forum_list_topics"]) && $_REQUEST["forum_list_topics"] == "on") {
		$tikilib->set_preference("forum_list_topics", 'y');

		$smarty->assign("forum_list_topics", 'y');
	} else {
		$tikilib->set_preference("forum_list_topics", 'n');

		$smarty->assign("forum_list_topics", 'n');
	}

	if (isset($_REQUEST["forum_list_posts"]) && $_REQUEST["forum_list_posts"] == "on") {
		$tikilib->set_preference("forum_list_posts", 'y');

		$smarty->assign("forum_list_posts", 'y');
	} else {
		$tikilib->set_preference("forum_list_posts", 'n');

		$smarty->assign("forum_list_posts", 'n');
	}

	if (isset($_REQUEST["forum_list_ppd"]) && $_REQUEST["forum_list_ppd"] == "on") {
		$tikilib->set_preference("forum_list_ppd", 'y');

		$smarty->assign("forum_list_ppd", 'y');
	} else {
		$tikilib->set_preference("forum_list_ppd", 'n');

		$smarty->assign("forum_list_ppd", 'n');
	}

	if (isset($_REQUEST["forum_list_lastpost"]) && $_REQUEST["forum_list_lastpost"] == "on") {
		$tikilib->set_preference("forum_list_lastpost", 'y');

		$smarty->assign("forum_list_lastpost", 'y');
	} else {
		$tikilib->set_preference("forum_list_lastpost", 'n');

		$smarty->assign("forum_list_lastpost", 'n');
	}

	if (isset($_REQUEST["forum_list_visits"]) && $_REQUEST["forum_list_visits"] == "on") {
		$tikilib->set_preference("forum_list_visits", 'y');

		$smarty->assign("forum_list_visits", 'y');
	} else {
		$tikilib->set_preference("forum_list_visits", 'n');

		$smarty->assign("forum_list_visits", 'n');
	}

	if (isset($_REQUEST["forum_list_desc"]) && $_REQUEST["forum_list_desc"] == "on") {
		$tikilib->set_preference("forum_list_desc", 'y');

		$smarty->assign("forum_list_desc", 'y');
	} else {
		$tikilib->set_preference("forum_list_desc", 'n');

		$smarty->assign("forum_list_desc", 'n');
	}
}

include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);
$forums = $commentslib->list_forums(0, -1, 'name_desc', '');
$smarty->assign_by_ref('forums', $forums["data"]);
ask_ticket('admin-inc-forums');
?>
