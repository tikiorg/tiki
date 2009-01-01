<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-my_tiki.php,v 1.28.2.1 2007-12-13 23:24:45 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'mytiki';
require_once ('tiki-setup.php');
if ($prefs['feature_ajax'] == "y") {
require_once ('lib/ajax/ajaxlib.php');
}
include_once ('lib/wiki/wikilib.php');
include_once ('lib/tasks/tasklib.php');

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));
	$smarty->assign('errortype', '402');
	$smarty->display("error.tpl");
	die;
}
$smarty->assign("mootab",'y');
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
	$user_pages = $wikilib->get_user_all_pages($userwatch, $sort_mode);
	$smarty->assign('user_pages', $user_pages);
	$smarty->assign('mytiki_pages', $tikilib->get_user_preference($user, 'mytiki_pages'), 'y');
}

if ($prefs['feature_blogs'] == 'y') {
	$user_blogs = $tikilib->list_user_blogs($userwatch,false);
	$smarty->assign('user_blogs', $user_blogs);
	$smarty->assign('mytiki_blogs', $tikilib->get_user_preference($user, 'mytiki_blogs'), 'y');
}

if ($prefs['feature_galleries'] == 'y') {
	$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
	$smarty->assign('user_galleries', $user_galleries);
	$smarty->assign('mytiki_gals', $tikilib->get_user_preference($user, 'mytiki_gals'), 'y');
}

if ($prefs['feature_trackers'] == 'y') {
	$user_items = $tikilib->get_user_items($userwatch);
	$smarty->assign('user_items', $user_items);
	$smarty->assign('mytiki_items', $tikilib->get_user_preference($user, 'mytiki_items'), 'y');
}

if ($prefs['feature_forums'] == 'y') {
	include_once ("lib/commentslib.php");
	$commentslib = new Comments($dbTiki);
	
	$user_forum_replies = $commentslib->get_user_forum_comments($userwatch, -1, 'replies');
	$smarty->assign('user_forum_replies', $user_forum_replies);	
	
	$user_forum_topics = $commentslib->get_user_forum_comments($userwatch, -1, 'topics');
	$smarty->assign('user_forum_topics', $user_forum_topics);
		
	$smarty->assign('mytiki_forum_replies', $tikilib->get_user_preference($user, 'mytiki_forum_replies'), 'y');
	$smarty->assign('mytiki_forum_topics', $tikilib->get_user_preference($user, 'mytiki_forum_topics'), 'y');
}

if ($prefs['feature_tasks'] == 'y') {
	$tasks = $tasklib->list_tasks($user, 0, 20,NULL,'priority_asc',true,false,true);
	$smarty->assign('tasks', $tasks['data']);
	$smarty->assign('mytiki_tasks', $tikilib->get_user_preference($user, 'mytiki_tasks'), 'y');
}

if ($prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y') {
	include_once ('lib/messu/messulib.php');
	$unread = $tikilib->user_unread_messages($userwatch);
	$smarty->assign('unread', $unread);
	$msgs = $messulib->list_user_messages($user, 0, -1, 'date_desc', '', 'isRead', 'n', '', 'messages');
	$smarty->assign('msgs', $msgs['data']);
	$smarty->assign('mytiki_msgs', $tikilib->get_user_preference($user, 'mytiki_msgs'), 'y');
}

//Get Workflow processes + activity instances
if ($prefs['feature_workflow'] == 'y' && $tiki_p_use_workflow == 'y' && $tikilib->get_user_preference($user, 'mytiki_workflow') == 'y') {
  include_once('tiki-g-my_activities.php');
  include_once('tiki-g-my_instances.php');
	$smarty->assign('mytiki_workflow', $tikilib->get_user_preference($user, 'mytiki_workflow'), 'y');
}

include_once ('tiki-section_options.php');

if ($prefs['feature_ajax'] == "y") {
function mytiki_ajax() {
    global $ajaxlib, $xajax;
    $ajaxlib->registerTemplate("tiki-my_tiki.tpl");
    $ajaxlib->registerTemplate("user_profile_s.tpl");
    $ajaxlib->registerFunction("loadComponent");
    $ajaxlib->processRequests();
}
mytiki_ajax();
}
$smarty->assign('mid', 'tiki-my_tiki.tpl');
$smarty->display("tiki.tpl");

?>
