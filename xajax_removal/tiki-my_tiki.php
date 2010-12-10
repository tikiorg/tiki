<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');
include_once ('lib/tasks/tasklib.php');
$access->check_user($user);
$userwatch = $user;
if (isset($_REQUEST["view_user"])) {
	if ($_REQUEST["view_user"] <> $user) {
		if ($tiki_p_admin == 'y') {
			$userwatch = $_REQUEST["view_user"];
		} else {
			$smarty->assign('msg', tra("You do not have permission to view other users data"));
			$smarty->display("error.tpl");
			die;
		}
	} else {
		$userwatch = $user;
	}
}
$smarty->assign('userwatch', $userwatch);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'pageName_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign('sort_mode', $sort_mode);
if ($prefs['feature_wiki'] == 'y') {
	$mytiki_pages = $tikilib->get_user_preference($user, 'mytiki_pages', 'y');
	if ($mytiki_pages == 'y') {
		$user_pages = $wikilib->get_user_all_pages($userwatch, $sort_mode);
		$smarty->assign_by_ref('user_pages', $user_pages);
		$smarty->assign('mytiki_pages', 'y');
	}
}
if ($prefs['feature_blogs'] == 'y') {
	$mytiki_blogs = $tikilib->get_user_preference($user, 'mytiki_blogs', 'y');
	if ($mytiki_blogs == 'y') {
		require_once('lib/blogs/bloglib.php');
		$user_blogs = $bloglib->list_user_blogs($userwatch, false);
		$smarty->assign_by_ref('user_blogs', $user_blogs);
		$smarty->assign('mytiki_blogs', 'y');
	}
}
if ($prefs['feature_galleries'] == 'y') {
	$mytiki_gals = $tikilib->get_user_preference($user, 'mytiki_gals', 'y');
	if ($mytiki_gals == 'y') {
		$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
		$smarty->assign_by_ref('user_galleries', $user_galleries);
		$smarty->assign('mytiki_gals', 'y');
	}
}
if ($prefs['feature_trackers'] == 'y') {
	$mytiki_user_items = $tikilib->get_user_preference($user, 'mytiki_items', 'y');
	if ($mytiki_user_items == 'y') {
		$user_items = $tikilib->get_user_items($userwatch);
		$smarty->assign_by_ref('user_items', $user_items);
		$smarty->assign('mytiki_user_items', 'y');
		global $trklib; include_once('lib/trackers/trackerlib.php');
		$nb_item_comments = $trklib->nbComments($user);
		$smarty->assign_by_ref('nb_item_comments', $nb_item_comments);
	}
}
if ($prefs['feature_forums'] == 'y') {
	$mytiki_forum_replies = $tikilib->get_user_preference($user, 'mytiki_forum_replies', 'y');
	if ($mytiki_forum_replies == 'y') {
		include_once ("lib/comments/commentslib.php");
		$commentslib = new Comments($dbTiki);
		$user_forum_replies = $commentslib->get_user_forum_comments($userwatch, -1, 'replies');
		$smarty->assign_by_ref('user_forum_replies', $user_forum_replies);
		$smarty->assign('mytiki_forum_replies', 'y');
	}
	$mytiki_forum_topics = $tikilib->get_user_preference($user, 'mytiki_forum_topics', 'y');
	if ($mytiki_forum_topics == 'y') {
		include_once ("lib/comments/commentslib.php");
		$commentslib = new Comments($dbTiki);
		$user_forum_topics = $commentslib->get_user_forum_comments($userwatch, -1, 'topics');
		$smarty->assign_by_ref('user_forum_topics', $user_forum_topics);
		$smarty->assign('mytiki_forum_topics', 'y');
	}
}
if ($prefs['feature_tasks'] == 'y') {
	$mytiki_tasks = $tikilib->get_user_preference($user, 'mytiki_tasks', 'y');
	if ($mytiki_tasks == 'y') {
		$tasks = $tasklib->list_tasks($user, 0, 20, NULL, 'priority_asc', true, false, true);
		$smarty->assign_by_ref('tasks', $tasks['data']);
		$smarty->assign('mytiki_tasks', 'y');
	}
}
if ($prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y') {
	$mytiki_msgs = $tikilib->get_user_preference($user, 'mytiki_msgs', 'y');
	if ($mytiki_msgs == 'y') {
		include_once ('lib/messu/messulib.php');
		$unread = $tikilib->user_unread_messages($userwatch);
		$smarty->assign_by_ref('unread', $unread);
		$msgs = $messulib->list_user_messages($user, 0, -1, 'date_desc', '', 'isRead', 'n', '', 'messages');
		$smarty->assign_by_ref('msgs', $msgs['data']);
		$smarty->assign('mytiki_msgs', 'y');
	}
}
if ($prefs['feature_articles'] == 'y') {
	$mytiki_articles = $tikilib->get_user_preference($user, 'mytiki_articles', 'y');
	if ($mytiki_articles == 'y') {
		include_once ('lib/articles/artlib.php');
		$user_articles = $artlib->get_user_articles($userwatch, -1);
		$smarty->assign_by_ref('user_articles', $user_articles);
		$smarty->assign('mytiki_articles', 'y');
	}
}
include_once ('tiki-section_options.php');
$smarty->assign('mid', 'tiki-my_tiki.tpl');
$smarty->display("tiki.tpl");
