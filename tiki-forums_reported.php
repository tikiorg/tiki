<?php

// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'forums';
require_once ('tiki-setup.php');

// Forums must be active
if ($prefs['feature_forums'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_forums");

	$smarty->display("error.tpl");
	die;
}

// forumId must be received
if (!isset($_REQUEST["forumId"])) {
	$smarty->assign('msg', tra("No forum indicated"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('forumId', $_REQUEST["forumId"]);
include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);
$forum_info = $commentslib->get_forum($_REQUEST["forumId"]);

//Check individual permissions for this forum
$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["forumId"], 'forum')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'forums');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["forumId"], 'forum', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}

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

// Must be admin to manipulate the queue
if ($tiki_p_admin_forum != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign_by_ref('forum_info', $forum_info);
$section = 'forums';
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

	$temp_max = count($all_forums["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		if ($userlib->object_has_one_permission($all_forums["data"][$i]["forumId"], 'forum')) {
			if ($tiki_p_admin == 'y'
				|| $userlib->object_has_permission($user, $all_forums["data"][$i]["forumId"], 'forum', 'tiki_p_admin_forum')
				|| $userlib->object_has_permission($user, $all_forums["data"][$i]["forumId"], 'forum', 'tiki_p_forum_read')) {
				$all_forums["data"][$i]["can_read"] = 'y';
			} else {
				$all_forums["data"][$i]["can_read"] = 'n';
			}
		} else {
			$all_forums["data"][$i]["can_read"] = 'y';
		}
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
$cant_pages = ceil($items["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($items["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('items', $items["data"]);

$topics = $commentslib->get_forum_topics($_REQUEST['forumId']);
$smarty->assign_by_ref('topics', $topics);
ask_ticket('forum-reported');

include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-forums_reported.tpl');
$smarty->display("tiki.tpl");

?>
