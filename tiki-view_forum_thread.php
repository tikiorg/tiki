<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_forum_thread.php,v 1.64 2004-06-16 19:40:52 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($feature_forums != 'y') {
    $smarty->assign('msg', tra("This feature is disabled").": feature_forums");

    $smarty->display("error.tpl");
    die;
}

if (!isset($_REQUEST['forumId'])) {
    $smarty->assign('msg', tra("No forum indicated"));

    $smarty->display("error.tpl");
    die;
}

if (!isset($_REQUEST['comments_parentId'])) {
    $smarty->assign('msg', tra("No thread indicated"));

    $smarty->display("error.tpl");
    die;
}

if (!isset($_REQUEST['topics_offset'])) {
    $_REQUEST['topics_offset'] = 1;
}

if(!isset($_REQUEST['topics_sort_mode']) || empty($_REQUEST['topics_sort_mode'])) {
    $_REQUEST['topics_sort_mode'] = 'commentDate_desc';
}

if (!isset($_REQUEST['topics_find'])) {
    $_REQUEST['topics_find'] = '';
}

if(!isset($_REQUEST['topics_threshold']) || empty($_REQUEST['topics_threshold'])) {
    $_REQUEST['topics_threshold'] = 0;
}

if (isset($_REQUEST["quote"]) &&
	$_REQUEST["quote"] )
{
    $quote = $_REQUEST["quote"];
} else {
    $quote = 0;
}

$smarty->assign('quote', $quote);

$comments_parentId = $_REQUEST["comments_parentId"];

if (isset($_REQUEST["openpost"])) {
    $smarty->assign('openpost', 'y');
} else {
    $smarty->assign('openpost', 'n');
}

$smarty->assign('comments_parentId', $_REQUEST["comments_parentId"]);

$smarty->assign('forumId', $_REQUEST["forumId"]);
include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);

$commentslib->comment_add_hit($_REQUEST["comments_parentId"]);
$commentslib->mark_comment($user, $_REQUEST['forumId'], $_REQUEST["comments_parentId"]);

$forum_info = $commentslib->get_forum($_REQUEST["forumId"]);

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

if ($user) {
    if ($forum_info["moderator"] == $user) {
	$tiki_p_admin_forum = 'y';

	$smarty->assign('tiki_p_admin_forum', 'y');
    } elseif (in_array($forum_info['moderator_group'], $userlib->get_user_groups($user))) {
	$tiki_p_admin_forum = 'y';

	$smarty->assign('tiki_p_admin_forum', 'y');
    }
}

if ($tiki_p_admin_forum == 'y') {
    $tiki_p_forum_post = 'y';

    $smarty->assign('tiki_p_forum_post', 'y');
    $tiki_p_forum_read = 'y';
    $smarty->assign('tiki_p_forum_read', 'y');
    $tiki_p_forum_vote = 'y';
    $smarty->assign('tiki_p_forum_vote', 'y');
    $tiki_p_forum_post_topic = 'y';
    $smarty->assign('tiki_p_forum_post_topic', 'y');
}

if ($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
    $smarty->assign('msg', tra("You dont have permission to use this feature"));

    $smarty->display("error.tpl");
    die;
}

$next_thread = $commentslib->get_forum_topics($_REQUEST['forumId'],
	$_REQUEST['topics_offset'] + 1, 1,
	$_REQUEST["topics_sort_mode"]);

if (count($next_thread)) {
    $smarty->assign('next_topic', $next_thread[0]['threadId']);
} else {
    $smarty->assign('next_topic', false);
}

$smarty->assign('topics_next_offset', $_REQUEST['topics_offset'] + 1);
$smarty->assign('topics_prev_offset', $_REQUEST['topics_offset'] - 1);

// Use topics_offset, topics_find, topics_sort_mode to get the next and previous topics!
$prev_thread = $commentslib->get_forum_topics($_REQUEST['forumId'],
	$_REQUEST['topics_offset'] - 1, 1,
	$_REQUEST["topics_sort_mode"]);

if (count($prev_thread)) {
    $smarty->assign('prev_topic', $prev_thread[0]['threadId']);
} else {
    $smarty->assign('prev_topic', false);
}

if($tiki_p_admin_forum == 'y') {
    if(isset($_REQUEST['delsel'])) {
	if (isset($_REQUEST['forumthread'])) {
	    check_ticket('view-forum');
	    foreach(array_values($_REQUEST['forumthread']) as $thread) {
		$commentslib->remove_comment($thread);
		$commentslib->register_remove_post($_REQUEST['forumId'], $_REQUEST['comments_parentId']);
	    }
	}
    }

    if (isset($_REQUEST['remove_attachment'])) {
	$area = 'delforumattach';
	if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
	    key_check($area);
	    $commentslib->remove_thread_attachment($_REQUEST['remove_attachment']);
	} else {
	    key_get($area);
	}
    }

    if(isset($_REQUEST['movesel'])) {
	if (isset($_REQUEST['forumthread'])) {
	    check_ticket('view-forum');
	    foreach(array_values($_REQUEST['forumthread']) as $thread) {
		$commentslib->set_parent($thread,$_REQUEST['moveto']);
	    }
	}
    }
}

if ($tiki_p_forums_report == 'y') {
    if (isset($_REQUEST['report'])) {
	check_ticket('view-forum');
	$commentslib->report_post($_REQUEST['forumId'], $_REQUEST['comments_parentId'], $_REQUEST['report'], $user, '');
    }
}

$smarty->assign_by_ref('forum_info', $forum_info);
$thread_info = $commentslib->get_comment($_REQUEST["comments_parentId"]);
//print_r($thread_info);
$smarty->assign_by_ref('thread_info', $thread_info);

$comments_per_page = $forum_info["topicsPerPage"];
$comments_default_ordering = $forum_info["threadOrdering"];
$comments_vars = array('forumId');
$comments_prefix_var = 'forum:';
$comments_object_var = 'forumId';
$commentslib->process_inbound_mail($_REQUEST['forumId']);

include_once ("comments.php");

$section = 'forums';
include_once ('tiki-section_options.php');

if ($feature_theme_control == 'y') {
    $cat_type = 'forum';

    $cat_objid = $_REQUEST["forumId"];
    include ('tiki-tc.php');
}

if ($user && $tiki_p_notepad == 'y' && $feature_notepad == 'y' && isset($_REQUEST['savenotepad'])) {
    check_ticket('view-forum');
    $info = $commentslib->get_comment($_REQUEST['savenotepad']);

    $tikilib->replace_note($user, 0, $info['title'], $info['data']);
}

if ($feature_user_watches == 'y') {
    if ($user && isset($_REQUEST['watch_event'])) {
	check_ticket('view-forum');
	if ($_REQUEST['watch_action'] == 'add') {
	    $tikilib->add_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], tra('forum topic'), $forum_info['name'] . ':' . $thread_info['title'], "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&amp;comments_parentId=" . $_REQUEST['comments_parentId']);
	} else {
	    $tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object']);
	}
    }

    $smarty->assign('user_watching_topic', 'n');

    if ($user && $watch = $tikilib->get_user_event_watches($user, 'forum_post_thread', $_REQUEST['comments_parentId'])) {
	$smarty->assign('user_watching_topic', 'y');
    }
}

if ($tiki_p_admin_forum == 'y' || $feature_forum_quickjump == 'y') {
    $all_forums = $commentslib->list_forums(0, -1, 'name_asc', '');

    $temp_max = count($all_forums["data"]);
    for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($all_forums["data"][$i]["forumId"], 'forum')) {
	    if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $all_forums["data"][$i]["forumId"], 'forum', 'tiki_p_admin_forum') || $userlib->object_has_permission($user, $all_forums["data"][$i]["forumId"], 'forum', 'tiki_p_forum_read')) {
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

if ($tiki_p_admin_forum == 'y') {
    $topics = $commentslib->get_forum_topics($_REQUEST['forumId'], 0, 200,
	    "commentDate_desc");

    $smarty->assign_by_ref('topics', $topics);
}

$smarty->assign('unread', 0);

if ($user && $feature_messages == 'y' && $tiki_p_messages == 'y') {
    $unread = $tikilib->user_unread_messages($user);

    $smarty->assign('unread', $unread);
}

if ($tiki_p_admin_forum == 'y') {
    $smarty->assign('queued', $commentslib->get_num_queued($comments_objectId));

    $smarty->assign('reported', $commentslib->get_num_reported($_REQUEST['forumId']));
}

include_once("textareasize.php");

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,20,'taglabel_desc','');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);

ask_ticket('view-forum');

// Display the template
$smarty->assign('mid', 'tiki-view_forum_thread.tpl');
$smarty->display("tiki.tpl");

?>
