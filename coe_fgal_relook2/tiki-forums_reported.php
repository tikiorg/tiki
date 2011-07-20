<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'forums';
require_once ('tiki-setup.php');

$access->check_feature('feature_forums');

// forumId must be received
if (!isset($_REQUEST["forumId"])) {
	$smarty->assign('msg', tra("No forum indicated"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('forumId', $_REQUEST["forumId"]);
include_once ("lib/comments/commentslib.php");
$commentslib = new Comments($dbTiki);
$forum_info = $commentslib->get_forum($_REQUEST["forumId"]);

//Check individual permissions for this forum
$smarty->assign('individual', 'n');

$tikilib->get_perm_object($_REQUEST["forumId"], 'forum');

// Now if the user is the moderator then give hime forum admin privs
if ($user) {
	if ($forum_info["moderator"] == $user) {
		$tiki_p_admin_forum = 'y';
		$smarty->assign('tiki_p_admin_forum', 'y');
	} elseif (in_array($forum_info['moderator_group'], $userlib->get_user_groups($user))) {
		$tiki_p_admin_forum = 'y';
		$smarty->assign('tiki_p_admin_forum', 'y');
	}
}

$access->check_permission('tiki_p_admin_forum');

$smarty->assign_by_ref('forum_info', $forum_info);
include_once ('tiki-section_options.php');

if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'forum';
	$cat_objid = $_REQUEST["forumId"];
	include ('tiki-tc.php');
}

if (isset($_REQUEST['qId'])) {
	$msg_info = $commentslib->queue_get($_REQUEST['qId']);

	$smarty->assign_by_ref('msg_info', $msg_info);
}

$smarty->assign('form', 'y');

if (isset($_REQUEST['del']) && isset($_REQUEST['msg'])) {
	check_ticket('forum-reported');
	foreach (array_keys($_REQUEST['msg'])as $msg) {
		$commentslib->remove_reported($msg);
	}
}

// Quickjumpt to other forums
if ($tiki_p_admin_forum == 'y' || $prefs['feature_forum_quickjump'] == 'y') {
	$all_forums = $commentslib->list_forums(0, -1, 'name_asc', '');
	Perms::bulk( array( 'type' => 'forum' ), 'object', $all_forums['data'], 'forumId' );

	$temp_max = count($all_forums["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		$forumperms = Perms::get( array( 'type' => 'forum', 'object' => $channels['data'][$i]['forumId'] ) );
		$all_forums["data"][$i]["can_read"] = $forumperms->forum_read ? 'y' : 'n';
	}

	$smarty->assign('all_forums', $all_forums['data']);
}

// Number of queued messages
if ($tiki_p_admin_forum == 'y') {
	$smarty->assign('reported', $commentslib->get_num_reported($_REQUEST['forumId']));
}

// Items will contain messages
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'timestamp_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$items = $commentslib->list_reported($_REQUEST['forumId'], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign('cant', $items['cant']);
$smarty->assign_by_ref('cant_pages', $items["cant"]);

$smarty->assign_by_ref('items', $items["data"]);

ask_ticket('forum-reported');

// Display the template
$smarty->assign('mid', 'tiki-forums_reported.tpl');
$smarty->display("tiki.tpl");
