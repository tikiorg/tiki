<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'forums';
require_once ('tiki-setup.php');
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once ('lib/categories/categlib.php');
	}
}
if ($prefs['feature_freetags'] == 'y') {
	include_once ('lib/freetag/freetaglib.php');
}

$access->check_feature('feature_forums');

$auto_query_args = array(
	'forumId',
	'comment_threadId',
	'comment_offset',
	'comment_threshold',
	'thread_sort_mode',
	'time_control',
	'show_archived',
	'poster',
	'filter_type',
	'reply_state'
);

include_once ("lib/comments/commentslib.php");

$commentslib = new Comments($dbTiki);

if (!isset($_REQUEST["forumId"]) || !($forum_info = $commentslib->get_forum($_REQUEST["forumId"]))) {
	$smarty->assign('errortype', 'no_redirect_login');
	$smarty->assign('msg', tra("No forum indicated"));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["comments_postCancel"])) {
	unset($_REQUEST["comments_threadId"]);
	unset($_REQUEST["comments_title"]);
	unset($_REQUEST["openpost"]);
}

if (isset($_REQUEST["openpost"])) {
	$smarty->assign('openpost', 'y');
} else {
	$smarty->assign('openpost', 'n');
}
$smarty->assign('forumId', $_REQUEST["forumId"]);
$tikilib->get_perm_object($_REQUEST['forumId'], 'forum', $forum_info, true);

// Now if the user is the moderator then give him forum admin privs -. SHOULD BE IN get_perm_object
if ($tiki_p_admin_forum != 'y' && $user) {
	if ($forum_info["moderator"] == $user) {
		$tiki_p_admin_forum = 'y';
	} elseif (in_array($forum_info['moderator_group'], $userlib->get_user_groups($user))) {
		$tiki_p_admin_forum = 'y';
	}
	
	if ($tiki_p_admin_forum == 'y') {
		$smarty->assign('tiki_p_admin_forum', 'y');
		$tiki_p_forum_post = 'y';
		$smarty->assign('tiki_p_forum_post', 'y');
		$tiki_p_forum_read = 'y';
		$smarty->assign('tiki_p_forum_read', 'y');
		$tiki_p_forum_vote = 'y';
		$smarty->assign('tiki_p_forum_vote', 'y');
		$tiki_p_forum_post_topic = 'y';
		$smarty->assign('tiki_p_forum_post_topic', 'y');
	}
}

$access->check_permission( array('tiki_p_forum_read') );

$commentslib->forum_add_hit($_REQUEST["forumId"]);

if (isset($_REQUEST['report']) && $tiki_p_forums_report == 'y') {
	check_ticket('view-forum');
	$commentslib->report_post($_REQUEST['forumId'], 0, $_REQUEST['report'], $user, '');
}

if ($tiki_p_admin_forum == 'y') {
	if (isset($_REQUEST['remove_attachment'])) {
		$access->check_authenticity();
		$commentslib->remove_thread_attachment($_REQUEST['remove_attachment']);
	}

	if (isset($_REQUEST['lock']) && isset($_REQUEST['forumId'])) {
		check_ticket('view-forum');
		if ($_REQUEST['lock'] == 'y') {
			$commentslib->lock_object_thread('forum:' . ((int)$_REQUEST['forumId']));
			$forum_info['is_locked'] = 'y';
		} elseif ($_REQUEST['lock'] == 'n') {
			$commentslib->unlock_object_thread('forum:' . ((int)$_REQUEST['forumId']));
			$forum_info['is_locked'] = 'n';
		}
	}

	if (isset($_REQUEST['locksel_x'])) {
		if (isset($_REQUEST['forumtopic'])) {
			check_ticket('view-forum');
			foreach(array_values($_REQUEST['forumtopic']) as $topic) {
				$commentslib->lock_comment($topic);
			}
		}
	}

	if (isset($_REQUEST['unlocksel_x'])) {
		if (isset($_REQUEST['forumtopic'])) {
			check_ticket('view-forum');
			foreach(array_values($_REQUEST['forumtopic']) as $topic) {
				$commentslib->unlock_comment($topic);
			}
		}
	}

	if (isset($_REQUEST['movesel'])) {
		if (isset($_REQUEST['forumtopic'])) {
			foreach(array_values($_REQUEST['forumtopic']) as $topic) {
				check_ticket('view-forum');
				// To move a topic you just have to change the object
				$obj = 'forum:' . $_REQUEST['moveto'];
				$commentslib->set_comment_object($topic, $obj);
				// update the stats for the source and destination forums
				$commentslib->forum_prune($_REQUEST['forumId']);
				$commentslib->forum_prune($_REQUEST['moveto']);
			}
		}
	}

	if (isset($_REQUEST['mergesel'])) {
		if (isset($_REQUEST['forumtopic'])) {
			foreach(array_values($_REQUEST['forumtopic']) as $topic) {
				check_ticket('view-forum');
				// To move a topic you just have to change the object
				if ($topic != $_REQUEST['mergetopic']) {
					$commentslib->set_parent($topic, $_REQUEST['mergetopic']);
				}
			}
		}
	}

	if ($prefs['feature_forum_topics_archiving'] && isset($_REQUEST['archive']) && isset($_REQUEST['comments_parentId'])) {
		check_ticket('view-forum');
		if ($_REQUEST['archive'] == 'y') {
			$commentslib->archive_thread($_REQUEST['comments_parentId']);
		} elseif ($_REQUEST['archive'] == 'n') {
			$commentslib->unarchive_thread($_REQUEST['comments_parentId']);
		}
	}

}

$smarty->assign_by_ref('forum_info', $forum_info);
$comments_per_page = $forum_info["topicsPerPage"];
$thread_sort_mode = $forum_info["topicOrdering"];
$comments_vars = array('forumId');

$comments_prefix_var = 'forum:';
$comments_object_var = 'forumId';

if (isset($forum_info['inbound_pop_server']) && !empty($forum_info['inbound_pop_server'])) {
	$commentslib->process_inbound_mail($_REQUEST['forumId']);
}

if (!isset($_REQUEST['comments_threshold'])) {
	$_REQUEST['comments_threshold'] = 0;
}

$smarty->assign('comments_threshold', $_REQUEST['comments_threshold']);
if (!isset($_REQUEST["comments_threadId"])) {
	$_REQUEST["comments_threadId"] = 0;
}

$smarty->assign("comments_threadId", $_REQUEST["comments_threadId"]);
if (!isset($comments_prefix_var)) {
	$comments_prefix_var = '';
}

if (!isset($comments_object_var) || (!$comments_object_var) || !isset($_REQUEST[$comments_object_var])) {
	die("the comments_object_var variable is not set or cannot be found as a REQUEST variable");
}

$comments_objectId = $comments_prefix_var . $_REQUEST["$comments_object_var"];

// Process a post form here
$smarty->assign('warning', 'n');

if ($tiki_p_forum_post_topic == 'n' 
			&& isset($_REQUEST['comments_postComment']) 
			&& isset($_REQUEST['comments_title']) 
			&& $_REQUEST['forumId'] == $prefs['wiki_forum_id'] 
			&& $tikilib->page_exists($_REQUEST['comments_title'])) {
	$tiki_p_forum_post_topic = 'y';
}

if (isset($_REQUEST['comments_postComment'])) {
	check_ticket('view-forum');
	$errors = array();
	$feedbacks = array();
	$threadId = $commentslib->post_in_forum($forum_info, $_REQUEST, $feedbacks, $errors);
	$smarty->assign_by_ref('errors', $errors);
	$smarty->assign_by_ref('feedbacks', $feedbacks);
}

// Here we send the user to the right thread/topic if it already exists; this
// is used for the 'discuss' tab. This is done down here because the thread
// might have to be created, which happens above.
if (isset($_REQUEST["comments_postComment"])) {
	// Check if the thread/topic already existis
	$threadId = $commentslib->check_for_topic($_REQUEST["comments_title"], $_REQUEST["comments_data"]);
	// If it does, send the user there with no delay.
	if ($threadId) {
		// If the samely titled comment already
		// exists, go straight to it.
		$url = 'tiki-view_forum_thread.php?comments_parentId=' . urlencode($threadId) . '&forumId=' . urlencode($_REQUEST["forumId"]);
		header('location: ' . $url);
		exit;
	}
}

if ($tiki_p_admin_forum == 'y' || $tiki_p_forum_vote == 'y') {
	// Process a vote here
	if (isset($_REQUEST["comments_vote"]) && isset($_REQUEST["comments_threadId"])) {
		check_ticket('view-forum');
		$comments_show = 'y';
		if ( $tikilib->register_user_vote($user, 'comment' . $_REQUEST["comments_threadId"], $_REQUEST['comments_vote'], range( 1, 5 ) ) ) {
			$commentslib->vote_comment($_REQUEST["comments_threadId"], $user, $_REQUEST["comments_vote"]);
		}
		$_REQUEST["comments_threadId"] = 0;
		$smarty->assign('comments_threadId', 0);
	}
}

if ($user) {
	$userinfo = $userlib->get_user_info($user);
	$smarty->assign_by_ref('userinfo', $userinfo);
}

if (isset($_REQUEST["comments_remove"]) && isset($_REQUEST["comments_threadId"])) {
	if ($tiki_p_admin_forum == 'y' || ($commentslib->user_can_edit_post($user, $_REQUEST["comments_threadId"]) && $tiki_p_forum_post_topic == 'y')) {
		$access->check_authenticity();
		$comments_show = 'y';
		$commentslib->remove_comment($_REQUEST["comments_threadId"]);
		$commentslib->register_remove_post($_REQUEST["forumId"], 0);
	} else { // user can't edit this post
		$smarty->assign('msg', tra('You are not permitted to remove someone else\'s post!'));
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->display("error.tpl");
		die;
	}
	unset($_REQUEST["comments_threadId"]);
}

if ($_REQUEST["comments_threadId"] > 0) {
	$comment_info = $commentslib->get_comment($_REQUEST["comments_threadId"]);
	$smarty->assign('comment_title', isset($_REQUEST['comments_title']) ? $_REQUEST['comments_title'] : $comment_info['title']);
	$smarty->assign('comment_data', isset($_REQUEST['comments_data']) ? $_REQUEST['comments_data'] : $comment_info['data']);
	$smarty->assign('comment_topictype', isset($_REQUEST['comment_topictype']) ? $_REQUEST['comment_topictype'] : $comment_info["type"]);
	$smarty->assign('comment_topicsummary', isset($_REQUEST["comment_topicsummary"]) ? $_REQUEST["comment_topicsummary"] : $comment_info["summary"]);
	$smarty->assign('comment_topicsmiley', $comment_info["smiley"]);
} else {
	$smarty->assign('comment_title', isset($_REQUEST["comments_title"]) ? $_REQUEST["comments_title"] : '');
	$smarty->assign('comment_data', isset($_REQUEST["comments_data"]) ? $_REQUEST["comments_data"] : '');
	$smarty->assign('comment_topictype', isset($_REQUEST["comment_topictype"]) ? $_REQUEST["comment_topictype"] : '');
	$smarty->assign('comment_topictype', 'n');
	$smarty->assign('comment_topicsummary', '');
	$smarty->assign('comment_topicsmiley', '');
}

$smarty->assign('comment_preview', 'n');
if (isset($_REQUEST["comments_previewComment"])) {
	check_ticket('view-forum');
	$smarty->assign('comments_preview_title', $_REQUEST["comments_title"]);
	$smarty->assign('comments_preview_data', ($commentslib->parse_comment_data($_REQUEST["comments_data"])));
	$smarty->assign('comment_title', $_REQUEST["comments_title"]);
	$smarty->assign('comment_data', $_REQUEST["comments_data"]);
	$smarty->assign('comment_topictype', $_REQUEST["comment_topictype"]);
	if ($forum_info['topic_summary'] == 'y') $smarty->assign('comment_topicsummary', $_REQUEST["comment_topicsummary"]);
	if ($forum_info['topic_smileys'] == 'y') $smarty->assign('comment_topicsmiley', $_REQUEST["comment_topicsmiley"]);
	$smarty->assign('openpost', 'y');
	$smarty->assign('comment_preview', 'y');
}

// Check for settings
if (!isset($_REQUEST["comments_per_page"])) {
	$_REQUEST["comments_per_page"] = $comments_per_page;
}

if (!isset($_REQUEST["thread_sort_mode"])) {
	$_REQUEST["thread_sort_mode"] = $thread_sort_mode;
} else {
	$comments_show = 'y';
}

if (!isset($_REQUEST["comments_commentFind"])) {
	$_REQUEST["comments_commentFind"] = '';
} else {
	$comments_show = 'y';
}

$smarty->assign('comments_per_page', $_REQUEST["comments_per_page"]);
$smarty->assign('thread_sort_mode', $_REQUEST["thread_sort_mode"]);
$smarty->assign('comments_commentFind', $_REQUEST["comments_commentFind"]);

// Offset setting for the list of comments
if (!isset($_REQUEST["comments_offset"])) {
	$comments_offset = 0;
} else {
	$comments_offset = $_REQUEST["comments_offset"];
}

$smarty->assign('comments_offset', $comments_offset);
// Now check if we are displaying top-level comments or a specific comment
if (!isset($_REQUEST["comments_parentId"])) {
	$_REQUEST["comments_parentId"] = 0;
}

$smarty->assign('comments_parentId', $_REQUEST["comments_parentId"]);
if (!isset($_REQUEST['time_control'])) {
	$_REQUEST['time_control'] = 0;
}

$commentslib->set_time_control($_REQUEST['time_control']);
$show_archived = isset($_REQUEST['show_archived']);
$smarty->assign('show_archived', $show_archived);
$view_archived_topics = ($show_archived == 'y' && ($tiki_p_admin_forum == 'y' || $prefs['feature_forum_topics_archiving'] == 'n'));

if (!isset($_REQUEST['poster']) || $_REQUEST['poster'] == '')
	$user_param = '';
elseif ($_REQUEST['poster'] == '_me')
	$user_param = $user;
else
	$user_param = $_REQUEST['poster'];

if (!isset($_REQUEST['filter_type']))
	$type_param = '';
else
	$type_param = $_REQUEST['filter_type'];

if (!isset($_REQUEST['reply_state']))
	$reply_state = '';
else
	$reply_state = $_REQUEST['reply_state'];

$comments_coms = $commentslib->get_forum_topics($_REQUEST['forumId']
																							, $comments_offset
																							, $_REQUEST['comments_per_page']
																							, $_REQUEST['thread_sort_mode']
																							, $view_archived_topics
																							, $user_param
																							, $type_param
																							, $reply_state
																							, $forum_info);

$comments_cant = $commentslib->count_forum_topics($_REQUEST['forumId']
																								, $comments_offset
																								, $_REQUEST['comments_per_page']
																								, $_REQUEST['thread_sort_mode']
																								, $view_archived_topics
																								, $user_param
																								, $type_param
																								, $reply_state);

$last_comments = $commentslib->get_last_forum_posts($_REQUEST['forumId'], $forum_info['forum_last_n']);

$smarty->assign_by_ref('last_comments', $last_comments);
$smarty->assign('comments_cant', $comments_cant);
$comments_maxRecords = $_REQUEST["comments_per_page"];
$smarty->assign_by_ref('comments_coms', $comments_coms);
$cat_type = 'forum';
$cat_objid = $_REQUEST["forumId"];

include_once ('tiki-section_options.php');

if ($prefs['feature_user_watches'] == 'y') {
	if ($user && isset($_REQUEST['watch_event'])) {
		check_ticket('view-forum');
		if ($_REQUEST['watch_action'] == 'add') {
			$tikilib->add_user_watch($user
															, $_REQUEST['watch_event']
															, $_REQUEST['watch_object']
															, 'forum'
															, $forum_info['name']
															, "tiki-view_forum.php?forumId=" . $_REQUEST['forumId']
															);
		} else {
			$tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], 'forum');
		}
	}

	if ($user && $watch = $tikilib->user_watches($user, 'forum_post_topic', $_REQUEST['forumId'], 'forum')) {
		$smarty->assign('user_watching_forum', 'y');
	} else {
		$smarty->assign('user_watching_forum', 'n');
	}

	if ($user && $watch = $tikilib->user_watches($user, 'forum_post_topic_and_thread', $_REQUEST['forumId'], 'forum')) {
		$smarty->assign('user_watching_forum_topic_and_thread', 'y');
	} else {
		$smarty->assign('user_watching_forum_topic_and_thread', 'n');
	}

	// Check, if the user is watching this forum by a category.
	if ($prefs['feature_categories'] == 'y') {
		$watching_categories_temp = $categlib->get_watching_categories($_REQUEST["forumId"], 'forum', $user);
		$smarty->assign('category_watched', 'n');
		if (count($watching_categories_temp) > 0) {
			$smarty->assign('category_watched', 'y');
			$watching_categories = array();
			foreach($watching_categories_temp as $wct) {
				$watching_categories[] = array(
					"categId" => $wct,
					"name" => $categlib->get_category_name($wct)
				);
			}
			$smarty->assign('watching_categories', $watching_categories);
		}
	}
}

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

$smarty->assign('unread', 0);
if ($user && $prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y') {
	$unread = $tikilib->user_unread_messages($user);
	$smarty->assign('unread', $unread);
}

if ($tiki_p_admin_forum == 'y') {
	$smarty->assign('queued', $commentslib->get_num_queued($comments_objectId));
	$smarty->assign('reported', $commentslib->get_num_reported($_REQUEST['forumId']));
}

if ($prefs['feature_freetags'] == 'y') {
	$cat_type = 'forum post';
	$cat_objid = $_REQUEST["comments_threadId"];
	include_once ("freetag_list.php");
	//If in preview mode get the tags from the form and not from database
	if (isset($_REQUEST["comments_previewComment"])) {
		$smarty->assign('taglist', $_REQUEST["freetag_string"]);
	}
}

$defaultRows = $prefs['default_rows_textarea_forum'];
if ($prefs['feature_mobile'] == 'y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");
	HAWTIKI_view_forum($forum_info['name'], $comments_coms, $tiki_p_forum_read, $comments_offset, $comments_maxRecords, $comments_cant);
}

if ($prefs['feature_contribution'] == 'y') {
	$contributionItemId = $_REQUEST['comments_threadId'];
	include_once ('contribution.php');
}

if ($prefs['feature_forum_parse'] == 'y') {
	global $wikilib;
	include_once ('lib/wiki/wikilib.php');
	$plugins = $wikilib->list_plugins(true, 'editpost');
	$smarty->assign_by_ref('plugins', $plugins);
}

ask_ticket('view-forum');
$smarty->assign('mid', 'tiki-view_forum.tpl');
$smarty->display("tiki.tpl");
