<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// $start_time = microtime(true);

// This file sets up the information needed to display
// the comments preferences, post-comment box and the
// list of comments. Finally it displays blog-comments.tpl
// using this information

// Setup URLS for the Comments next and prev buttons and use variables that
// cannot be aliased by normal tiki variables.
// Traverse each _REQUEST data adn put them in an array

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
global $access, $tikilib, $headerlib;
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

/* 
 * Determine the settings used to display the thread
 */

// user requests that could be used to change thread display settings
$handled_requests = array('comments_per_page', 'thread_style', 'thread_sort_mode');

// Then determine the final value for thread display settings
if ( isset($forum_mode) && $forum_mode == 'y' ) {
	// If we are in a forum thread

	if ( $prefs['forum_thread_user_settings'] == 'y' && $prefs['forum_thread_user_settings_keep'] == 'y' ) {
		// If 'forum_thread_user_settings' is enabled (allow user to change thread display settings)
		// and if the 'forum_thread_user_settings_keep' is enabled (keep user settings for all forums during his session)
		// ... we check session vars
		//  !! Session var is not used when there is an explicit user request !!

		foreach ( $handled_requests as $request_name )
			if ( isset($_SESSION['forums_'.$request_name]) && ! isset($_REQUEST[$request_name]) )
				$$request_name = $_SESSION['forums_'.$request_name];
	}
	foreach ( $handled_requests as $request_name ) {
		if ( empty($$request_name) && empty($_REQUEST[$request_name]) ) {
			$$request_name = $prefs['forum_'.$request_name];
		} elseif ( empty($$request_name) && !empty($_REQUEST[$request_name]) ) {
			$$request_name = $_REQUEST[$request_name];
		}
	}

	if ( $forum_info['is_flat'] == 'y' ) {
		// If we have a flat forum (i.e. we reply only to the first message / thread)
		// ... we then override $thread_style and force a 'plain' style
		$thread_style = 'commentStyle_plain';
	}

} else {
	// If we are not in a forum (e.g. wiki page comments, ...), we use other fallback values
	if ( ! isset($comments_per_page) ) {
		if( isset( $prefs[$section . '_comments_per_page'] ) ) {
			$comments_per_page = (int) $prefs[$section . '_comments_per_page'];
		} else {
			$comments_per_page = 10;
		}
	}
	if ( ! isset($thread_sort_mode) ) {
		if( isset( $prefs[$section . '_comments_default_ordering'] ) ) {
			$thread_sort_mode = $prefs[$section . '_comments_default_ordering'];
		} else {
			$thread_sort_mode = 'commentDate_asc';
		}
	}
	if ( ! isset($thread_style) ) $thread_style = 'commentStyle_threaded';	
}

// Assign final values to smarty vars in order
foreach ( $handled_requests as $request_name ) $smarty->assign($request_name, $$request_name);

if ( ! isset($_REQUEST["comment_rating"]) ) $_REQUEST["comment_rating"] = '';
$comments_aux = array();

// show/hide comments zone on request and store the status in a cookie (also works with javascript off)
if (isset($_REQUEST['comzone'])) {
	$comments_show = 'n';
	$comzone_state = $_REQUEST['comzone'];
	if ($comzone_state=='show'||$comzone_state=='o') {
		$comments_show = 'y';
		if (!isset($_COOKIE['comzone'])||$_COOKIE['comzone']=='c') setcookie('comzone','o');	
	} 
	if ($comzone_state=='hide'||$comzone_state=='c') {
		if (!isset($_COOKIE['comzone'])||$_COOKIE['comzone']=='o') setcookie('comzone','c');
	}
} else {
	$comments_show = 'n';
}
$comments_t_query = '';
$comments_first = 1;

foreach ($comments_vars as $c_name) {
	$comments_avar["name"] = $c_name;
	
	if (isset($_REQUEST[$c_name])) {
		$comments_avar["value"] = $_REQUEST[$c_name];
		$comments_aux[] = $comments_avar;
	}

	if (isset($_REQUEST[$c_name])) {
		if ($comments_first) {
			$comments_first = 0;
			$comments_t_query .= "?$c_name=" . urlencode($_REQUEST["$c_name"]);
		} else {
			$comments_t_query .= "&amp;$c_name=" . urlencode($_REQUEST["$c_name"]);
		}
	}
}

$smarty->assign('comments_request_data', $comments_aux);

if (!isset($_REQUEST['comments_threshold'])) {
	$_REQUEST['comments_threshold'] = 0;
} else {
	$smarty->assign('comments_threshold_param', '&amp;comments_threshold='.$_REQUEST['comments_threshold']);
}

$smarty->assign('comments_threshold', $_REQUEST['comments_threshold']);
// This sets up comments father as the father
$comments_parsed = parse_url($tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);

if (!isset($comments_parsed["query"])) {
	$comments_parsed["query"] = '';
}

TikiLib::parse_str($comments_parsed["query"], $comments_query);
$comments_father = $comments_parsed["path"];

$comments_complete_father = $comments_father . $comments_t_query;

if (strstr($comments_complete_father, "?")) {
	$comments_complete_father .= '&amp;';
} else {
	$comments_complete_father .= '?';
}

$smarty->assign('comments_father', $comments_father);
$smarty->assign('comments_complete_father', $comments_complete_father);

if (!isset($_REQUEST["comments_threadId"])) {
	$_REQUEST["comments_threadId"] = 0;
}
$smarty->assign("comments_threadId", $_REQUEST["comments_threadId"]);

// The same for replies to comments threads

if (!isset($_REQUEST["comments_reply_threadId"])) {
	$_REQUEST["comments_reply_threadId"] = 0;
}
$smarty->assign("comments_reply_threadId", $_REQUEST["comments_reply_threadId"]);

// Include the library for comments (if not included)
include_once ("lib/comments/commentslib.php");global $dbTiki;
$commentslib = new Comments($dbTiki);

if (!isset($comments_prefix_var)) {
	$comments_prefix_var = '';
}

if( ! isset( $comments_objectId ) ) {
	if (!isset($_REQUEST[$comments_object_var])) {
		die ("The comments_object_var variable cannot be found as a REQUEST variable");
	}
	$comments_objectId = $comments_prefix_var . $_REQUEST["$comments_object_var"];
}

$feedbacks = array();
$errors = array();
if ( isset($_REQUEST['comments_objectId']) && $_REQUEST['comments_objectId'] == $comments_objectId
	&& (isset($_REQUEST['comments_postComment']) || isset($_REQUEST['comments_postComment_anonymous']) )) {
	if (isset($forum_mode) && $forum_mode == 'y') {
		$forum_info = $commentslib->get_forum($_REQUEST['forumId']);
		$threadId = $commentslib->post_in_forum($forum_info, $_REQUEST, $feedbacks, $errors);
		if (!empty($threadId) && empty($errors)) {
			$url = "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&comments_parentId=" . $_REQUEST['comments_parentId'];
			if (!empty($_REQUEST['comments_threshold'])) 
				$url .= "&amp;comments_threshold=".$_REQUEST['comments_threshold'];
			if (!empty($_REQUEST['comments_offset'])) 
				$url .= "&amp;comments_offset=".$_REQUEST['comments_offset'];
			if (!empty($_REQUEST['comments_per_page'])) 
				$url .= "&amp;comments_per_page=".$_REQUEST['comments_per_page'];
			if (!empty($_REQUEST['thread_style'])) 
				$url .= "&amp;thread_style=".$_REQUEST['thread_style'];
			if (!empty($_REQUEST['thread_sort_mode'])) 
				$url .= "&amp;thread_sort_mode=".$_REQUEST['thread_sort_mode'];			
			if (!empty($feedbacks)) {
				$_SESSION['feedbacks'] = $feedbacks;
			}

			//Watches
			if ( isset($forum_mode) && $forum_mode == 'y' && $prefs['feature_user_watches'] == 'y') {
				if ( isset($_REQUEST['watch']) && $_REQUEST['watch'] == 'y') {
					$tikilib->add_user_watch($user, 'forum_post_thread', $_REQUEST['comments_parentId'], 'forum topic', $forum_info['name'] . ':' . $thread_info['title'], "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&amp;comments_parentId=" . $_REQUEST['comments_parentId']);
				} else {
					$tikilib->remove_user_watch($user, 'forum_post_thread', $_REQUEST['comments_parentId'], 'forum topic');
				}
			}

			header('location: ' . $url);
			die;
		}
	} else {
		$threadId = $commentslib->post_in_object($comments_objectId, $_REQUEST, $feedbacks, $errors);
		if (!empty($threadId) && empty($errors) && $prefs['feature_user_watches'] == 'y') {
			if ($prefs['wiki_watch_comments'] == 'y' && isset($_REQUEST["page"])) {
				global $notificationemaillib; require_once('lib/notifications/notificationemaillib.php');
				sendCommentNotification('wiki', $_REQUEST['page'], $_REQUEST['comments_title'], $_REQUEST['comments_data']);
			} else if (isset($_REQUEST["articleId"])) {
				global $notificationemaillib; require_once('lib/notifications/notificationemaillib.php');
				sendCommentNotification('article', $_REQUEST['articleId'], $_REQUEST['comments_title'], $_REQUEST['comments_data']);
			} elseif (isset($_REQUEST['itemId'])) {
				global $notificationemaillib; require_once('lib/notifications/notificationemaillib.php');
				sendCommentNotification('trackeritem', $_REQUEST['itemId'], $_REQUEST['comments_title'], $_REQUEST['comments_data']);
			}
		}
	}
	$smarty->assign_by_ref('errors', $errors);
	$smarty->assign_by_ref('feedbacks', $feedbacks);

	if( isset( $pageCache ) ) {
		$pageCache->invalidate();
	}
}

global $tiki_p_admin_comments;
if ((!isset($forum_mode) || $forum_mode == 'n') && $tiki_p_admin_comments == 'y' && isset($_REQUEST["comments_threadId"])) {
	// Comments Moderation
    if (!empty($_REQUEST['comments_approve'])) {
		if ( $_REQUEST['comments_approve'] == 'y' ) {
			$commentslib->approve_comment($_REQUEST["comments_threadId"]);
		} elseif ( $_REQUEST['comments_approve'] == 'n' ) {
			$commentslib->reject_comment($_REQUEST["comments_threadId"]);
		}
	}

	// Comments archive
	if ($prefs['comments_archive'] == 'y') {
		if (!empty($_REQUEST['comment_archive'])) {
			if ($_REQUEST['comment_archive'] == 'y') {
				$commentslib->archive_thread($_REQUEST['comments_threadId']);
			} else if ($_REQUEST['comment_archive'] == 'n') {
				$commentslib->unarchive_thread($_REQUEST['comments_threadId']);
			}
		}
		
		$object = explode(':', $comments_objectId);
		$smarty->assign('count_archived_comments', $commentslib->count_object_archived_comments($object[1], $object[0]));
	}
}

// Comments and Forum Locking
if ( $prefs['feature_comments_locking'] == 'y' && ! empty($_REQUEST['comments_lock']) && ! empty($comments_objectId) && ( ! isset($forum_mode) || $forum_mode == 'n' ) && $tiki_p_lock_comments == 'y' ) {
	if ( $_REQUEST['comments_lock'] == 'y' ) {
		$commentslib->lock_object_thread($comments_objectId);
	} elseif ( $_REQUEST['comments_lock'] == 'n' ) {
		$commentslib->unlock_object_thread($comments_objectId);
	}
}

if (($tiki_p_remove_comments == 'y' && (!isset($forum_mode) || $forum_mode == 'n'))
		|| (isset($forum_mode) && $forum_mode =='y' && $tiki_p_admin_forum == 'y' ) ) {
	if (isset($_REQUEST["comments_remove"]) && isset($_REQUEST["comments_threadId"])) {
		$access->check_authenticity();
		$comments_show = 'y';
		$commentslib->remove_comment($_REQUEST["comments_threadId"]);
		$_REQUEST["comments_threadId"] = 0;
		$smarty->assign('comments_threadId', 0);
	}
}

if ($_REQUEST["comments_threadId"] > 0) {
	$comment_info = $commentslib->get_comment($_REQUEST["comments_threadId"]);

	$smarty->assign('comment_title', $comment_info["title"]);
	$smarty->assign('comment_rating', $comment_info["comment_rating"]);	
	$smarty->assign('comment_data', $comment_info["data"]);
} elseif ($_REQUEST["comments_reply_threadId"] > 0) {
	// Replies to comments.

	$comment_info = $commentslib->get_comment($_REQUEST["comments_reply_threadId"]);

	if ( $comment_info['parentId'] > 0 && $forum_info['is_flat'] == 'y' ) {
		$smarty->assign('msg', tra("This forum is flat and doesn't allow replies to other replies"));
		$smarty->display("error.tpl");
		die;
	}

	// Add the replied-to text, with >.
	// Disabled by damian
	//	$smarty->assign('comment_data', '');
	// Re-enabled by rlpowell; my users rely on this.  If you want to disable it, put an option in the forums or something.
	// However, I re-enabled it *working*, instead of broken.  -rlpowell
	// check to see if QUOTE plugin or > should be used -Terence
	global $prefs;
	if ( $comment_info["data"] != ''  ) {
		if ( ($prefs['feature_forum_parse'] == 'y' || $prefs['section_comments_parse'] == 'y') && $prefs['feature_use_quoteplugin'] == 'y' ) {
			$comment_info["data"] = "\n{QUOTE(replyto=>" . $comment_info["userName"] . ")}" . $comment_info["data"] . '{QUOTE}';
		} else {
			$comment_info["data"] = preg_replace( '/\n/', "\n> ", $comment_info["data"] ) ;
			$comment_info["data"] = "\n> " . $comment_info["data"];
		}
	}
	$smarty->assign( 'comment_data', $comment_info["data"] );

	if( ! array_key_exists( "title", $comment_info ) )	{
		if( array_key_exists( "comments_title", $_REQUEST ) ) {
			$comment_info["title"] = $_REQUEST["comments_title"];
		} else {
			$comment_info["title"] = "";
			$_REQUEST["comments_title"] = "";
		}
	}

	$smarty->assign('comment_title', ( $prefs['forum_comments_no_title_prefix'] != 'y' ? tra('Re:').' ' : '' ).$comment_info["title"]);
	$smarty->assign('comments_reply_threadId', $_REQUEST["comments_reply_threadId"]);
} else {
	$smarty->assign('comment_title', '');
	$smarty->assign('comment_rating', '');	
	$smarty->assign('comment_data', '');
}

$smarty->assign('comment_preview', 'n');

if (isset($_REQUEST["comments_previewComment"]) || isset($_REQUEST["comments_postComment"])) {
	$comment_preview = array();

	$comment_preview['title'] = $_REQUEST["comments_title"];

	$comment_preview['parsed'] = $commentslib->parse_comment_data(strip_tags($_REQUEST["comments_data"]));
	$comment_preview['rating'] = $_REQUEST["comment_rating"];
	$comment_preview['commentDate'] = $tikilib->now;

	if (isset($_REQUEST["anonymous_name"])) {
		$comment_preview['anonymous_name'] = $_REQUEST["anonymous_name"];
	}
	if (isset($_REQUEST["anonymous_email"])) {
		$comment_preview['email'] = $_REQUEST["anonymous_email"];
	}
	if (isset($_REQUEST["anonymous_website"])) {
		$comment_preview['website'] = $_REQUEST["anonymous_website"];
	}

	$smarty->assign('comment_preview_data', $comment_preview);

	if (isset($_REQUEST["comments_previewComment"])) {
		$smarty->assign('comment_preview', 'y');
	}
	$smarty->assign('comment_data', $_REQUEST["comments_data"]);
	$smarty->assign('comment_title', $comment_preview["title"]);
	$comments_show = 'y';
}

// Always show comments when a display setting has been explicitely specified
if ( isset($_REQUEST['comments_per_page']) || isset($_REQUEST['thread_style']) || isset($_REQUEST['thread_sort_mode']) ) {
	$comments_show = 'y';
}

if (!isset($_REQUEST["comments_commentFind"])) $_REQUEST["comments_commentFind"] = ''; else $comments_show = 'y';

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

$forum_check = explode(':', $comments_objectId);
if ($forum_check[0] == 'forum' ) {
	$smarty->assign('forumId', $forum_check[1]);
}

if( isset( $_REQUEST["comments_grandParentId"] ) ) {
	$smarty->assign('comments_grandParentId', $_REQUEST["comments_grandParentId"]);
}

if (isset($_REQUEST["post_reply"]) && isset($_REQUEST["comments_reply_threadId"]))
	$threadId_if_reply = $_REQUEST["comments_reply_threadId"];
else
	$threadId_if_reply = 0;

if (empty($thread_sort_mode)) {
	if( !empty($_REQUEST['thread_sort_mode'])) {
		$thread_sort_mode = $_REQUEST['thread_sort_mode'];
	} else {
		$thread_sort_mode = 'commentDate_asc';
	}
}

$comments_coms = $commentslib->get_comments($comments_objectId,
		(isset($forum_mode) && $forum_mode == 'y') ? $_REQUEST["comments_parentId"] : null,
		$comments_offset, $comments_per_page, $thread_sort_mode, $_REQUEST["comments_commentFind"],
		$_REQUEST['comments_threshold'], $thread_style, $threadId_if_reply);

if ($comments_prefix_var == 'forum:') {
	$comments_cant = $commentslib->count_comments('topic:'. $_REQUEST['comments_parentId']); // comments in the topic not in the forum
} else {
	$comments_cant = $commentslib->count_comments($comments_objectId);
}
$comments_cant_page = $comments_coms['cant'];

$smarty->assign('comments_below', $comments_coms["below"]);
$smarty->assign('comments_cant', $comments_cant);

// Offset management
$comments_maxRecords = $comments_per_page;

if( $comments_maxRecords != 0 ) {
	$comments_cant_pages = ceil($comments_cant_page / $comments_maxRecords);
	$smarty->assign('comments_actual_page', 1 + ($comments_offset / $comments_maxRecords));
} else {
	$comments_cant_pages = 1;
	$smarty->assign('comments_actual_page', 1 );
}
$smarty->assign('comments_cant_pages', $comments_cant_pages);

if ($comments_cant_page > ($comments_offset + $comments_maxRecords)) {
	$smarty->assign('comments_next_offset', $comments_offset + $comments_maxRecords);
} else {
	$smarty->assign('comments_next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($comments_offset > 0) {
	$smarty->assign('comments_prev_offset', $comments_offset - $comments_maxRecords);
} else {
	$smarty->assign('comments_prev_offset', -1);
}

if (!isset($forum_mode) || $forum_mode == 'n') {
	$defaultRows = $prefs['default_rows_textarea_comment'];
}

if ( ! isset($forum_mode) || $forum_mode == 'n' ) {
	$queued = 0;
	foreach ( $comments_coms['data'] as $k => $v ) {
		if ( $v['approved'] == 'n' ) $queued++;
	}
	$smarty->assign('queued', $queued);
}
$smarty->assign('comments_coms', $comments_coms["data"] );

// Grab the parent comment to show.  -rlpowell
if (isset($_REQUEST["comments_parentId"]) &&
		$_REQUEST["comments_parentId"] > 0 && 
		(($tiki_p_post_comments == 'y' && (!isset($forum_mode) || $forum_mode == 'n')) ||($tiki_p_forum_post == 'y' && isset($forum_mode) && $forum_mode == 'y')) &&
		(isset($_REQUEST['comments_previewComment']) || isset($_REQUEST['post_reply']))) {
	$parent_com = $commentslib->get_comment($_REQUEST["comments_parentId"]);
	$smarty->assign_by_ref('parent_com', $parent_com);
}

// Get comments / forum lock status
if ( isset($forum_mode) && $forum_mode == 'y' ) {
	$thread_is_locked = ( ! empty($comments_objectId) && $commentslib->is_object_locked($comments_objectId) ) ? 'y' : 'n';
	$forum_is_locked = $thread_is_locked;
	$thread_is_locked = $comment_info['locked'];
	$smarty->assign('forum_is_locked', $forum_is_locked);
	$smarty->assign('thread_is_locked', $thread_is_locked);
} elseif ( $prefs['feature_comments_locking'] == 'y' ) {
	$thread_is_locked = ( ! empty($comments_objectId) && $commentslib->is_object_locked($comments_objectId) ) ? 'y' : 'n';
	$smarty->assign('thread_is_locked', $thread_is_locked);
}

if (!empty($_REQUEST['post_reply'])) {
	$smarty->assign('post_reply', $_REQUEST['post_reply']);
} elseif (!empty($_REQUEST['edit_reply'])) {
	$smarty->assign('edit_reply', $_REQUEST['edit_reply']);
}

if ($prefs['feature_contribution'] == 'y') {
	$contributionItemId = $_REQUEST["comments_threadId"];
	include_once('contribution.php');
}
// see if comments are allowed on this specific wiki page
global $section;
if ($section == 'wiki page') {
	if ($prefs['wiki_comments_allow_per_page'] != 'n') {
		global $info;
		if (!empty($info['comments_enabled'])) {
			$smarty->assign('comments_allowed_on_page', $info['comments_enabled']);
		} else {
			if ($prefs['wiki_comments_allow_per_page'] == 'y') {
				$smarty->assign('comments_allowed_on_page', 'y');
			} else {
				$smarty->assign('comments_allowed_on_page', 'n');
			}
		}
	} else {
		$smarty->assign('comments_allowed_on_page', 'y');
	}
}

$headerlib->add_jsfile('lib/comments/commentslib.js');
$smarty->assign('comments_objectId', $comments_objectId);
$smarty->assign('comments_show', $comments_show);
