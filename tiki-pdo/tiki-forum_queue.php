<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-forum_queue.php,v 1.18 2007-10-12 07:55:27 nyloth Exp $

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
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

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

if (isset($_REQUEST['remove_attachment'])) {
	check_ticket('forum-queue');
	$commentslib->remove_thread_attachment($_REQUEST['remove_attachment']);
}

if (isset($_REQUEST['qId'])) {
	if (isset($_REQUEST['save'])) {
	check_ticket('forum-queue');
		$smarty->assign('form', 'n');

		if (!isset($_REQUEST['summary']))
			$_REQUEST['summary'] = '';

		if (!isset($_REQUEST['topic_smiley']))
			$_REQUEST['topic_smiley'] = '';

		if (!isset($_REQUEST['type']))
			$_REQUEST['type'] = '';

		if (!isset($_REQUEST['topic_title']))
			$_REQUEST['topic_title'] = '';

		if (!isset($_REQUEST['in_reply_to']))
			$_REQUEST['in_reply_to'] = '';

		if (!isset($_REQUEST['parentId']))
			$_REQUEST['parentId'] = $msg_info['parentId'];

		if ($_REQUEST['parentId'] > 0) {
			$p_info = $commentslib->get_comment($_REQUEST['parentId']);

			$_REQUEST['topic_title'] = $p_info['title'];
		}

		$commentslib->replace_queue($_REQUEST['qId'], $_REQUEST['forumId'], 'forum' . $_REQUEST['forumId'], $_REQUEST['parentId'],
			$user, $_REQUEST['title'], $_REQUEST['data'], $_REQUEST['type'], $_REQUEST['topic_smiley'], $_REQUEST['summary'],
			$_REQUEST['topic_title'], $_REQUEST['in_reply_to']);
		unset ($_REQUEST['qId']);
	}

	if (isset($_REQUEST['remove'])) {
		$area = 'delcomment';
	  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
  	  key_check($area);
			$smarty->assign('form', 'n');
			$commentslib->remove_queued($_REQUEST['qId']);
		  } else {
		    key_get($area);
  		}
	}

	if (isset($_REQUEST['saveapp'])) {
	check_ticket('forum-queue');
		$smarty->assign('form', 'n');

		if (!isset($_REQUEST['summary']))
			$_REQUEST['summary'] = '';

		if (!isset($_REQUEST['topic_smiley']))
			$_REQUEST['topic_smiley'] = '';

		if (!isset($_REQUEST['type']))
			$_REQUEST['type'] = '';

		if (!isset($_REQUEST['topic_title']))
			$_REQUEST['topic_title'] = '';

		if (!isset($_REQUEST['in_reply_to']))
			$_REQUEST['in_reply_to'] = '';

		if (!isset($_REQUEST['parentId']))
			$_REQUEST['parentId'] = $msg_info['parentId'];

		if ($_REQUEST['parentId'] > 0) {
			$p_info = $commentslib->get_comment($_REQUEST['parentId']);

			$_REQUEST['topic_title'] = $p_info['title'];
		}

		$commentslib->replace_queue($_REQUEST['qId'], $_REQUEST['forumId'], 'forum' . $_REQUEST['forumId'], $_REQUEST['parentId'],
			$user, $_REQUEST['title'], $_REQUEST['data'], $_REQUEST['type'], $_REQUEST['topic_smiley'], $_REQUEST['summary'],
			$_REQUEST['topic_title'], $_REQUEST['in_reply_to']);
		$commentslib->approve_queued($_REQUEST['qId']);
		unset ($_REQUEST['qId']);
	}

	if (isset($_REQUEST['topicize'])) {
	check_ticket('forum-queue');
		$smarty->assign('form', 'n');

		// Convert to a topic
		if (!isset($_REQUEST['summary']))
			$_REQUEST['summary'] = '';

		if (!isset($_REQUEST['type']))
			$_REQUEST['type'] = '';

		if (!isset($_REQUEST['topic_smiley']))
			$_REQUEST['topic_smiley'] = '';

		if (!isset($_REQUEST['topic_title']))
			$_REQUEST['topic_title'] = '';

		if (!isset($_REQUEST['in_reply_to']))
			$_REQUEST['in_reply_to'] = '';

		$_REQUEST['parentId'] = 0;
		$_REQUEST['type'] = 'n';
		$commentslib->replace_queue($_REQUEST['qId'], $_REQUEST['forumId'], 'forum' . $_REQUEST['forumId'], $_REQUEST['parentId'],
			$user, $_REQUEST['title'], $_REQUEST['data'], $_REQUEST['type'], $_REQUEST['topic_smiley'], $_REQUEST['summary'],
			$_REQUEST['topic_title'], $_REQUEST['in_reply_to']);
		unset ($_REQUEST['qId']);
	}
}

if (isset($_REQUEST['rej']) && isset($_REQUEST['msg'])) {
	check_ticket('forum-queue');
	foreach (array_keys($_REQUEST['msg'])as $msg) {
		$commentslib->remove_queued($msg);
	}
}

if (isset($_REQUEST['app']) && isset($_REQUEST['msg'])) {
	check_ticket('forum-queue');
	foreach (array_keys($_REQUEST['msg'])as $msg) {
		$commentslib->approve_queued($msg);
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
	$smarty->assign('queued', $commentslib->get_num_queued('forum' . $_REQUEST['forumId']));
}

// Items will contain messages
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'timestamp_asc';
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
$items = $commentslib->list_forum_queue('forum:' . $_REQUEST['forumId'], $offset, $maxRecords, $sort_mode, $find);
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
ask_ticket('forum-queue');

include_once ('tiki-section_options.php');

// Display the template
$smarty->assign('mid', 'tiki-forum_queue.tpl');
$smarty->display("tiki.tpl");

?>
