<?php

//print "<!--\n";
//$start_time = microtime(true);

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_forum_thread.php,v 1.96.2.7 2008-01-29 02:58:11 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'forums';
require_once ('tiki-setup.php');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($prefs['feature_forums'] != 'y') {
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
} else {
    $smarty->assign('topics_sort_mode_param', '&amp;topics_sort_mode='.$_REQUEST['topics_sort_mode']);
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

if( isset( $_REQUEST["comments_grandParentId"] ) )
{
    $smarty->assign('comments_grandParentId', $_REQUEST["comments_grandParentId"]);
}

if( isset( $_REQUEST["comments_reply_threadId"] ) )
{
    $smarty->assign('comments_reply_threadId', $_REQUEST["comments_reply_threadId"]);
} else {
	$_REQUEST["comments_reply_threadId"] = $_REQUEST["comments_parentId"];
	$smarty->assign('comments_reply_threadId', $_REQUEST["comments_reply_threadId"]);
}

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
} elseif ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
	$perms_array = $categlib->get_object_categories_perms($user, 'forum', $_REQUEST['forumId']);
   	if ($perms_array) {
   		$is_categorized = TRUE;
    	foreach ($perms_array as $perm => $value) {
    		$$perm = $value;
    	}
   	} else {
   		$is_categorized = FALSE;
   	}
	if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
		$smarty->assign('msg',tra("Permission denied you cannot view this page"));
	    $smarty->display("error.tpl");
		die;
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
    $smarty->assign('msg', tra("You do not have permission to use this feature"));

    $smarty->display("error.tpl");
    die;
}

$smarty->assign('topics_next_offset', $_REQUEST['topics_offset'] + 1);
$smarty->assign('topics_prev_offset', $_REQUEST['topics_offset'] - 1);

//$end_time = microtime(true);

//print "TIME2: ".($end_time - $start_time)."\n";

$threads = $commentslib->get_forum_topics($_REQUEST['forumId'],
	$_REQUEST['topics_offset'] - 1, 3,
	$_REQUEST["topics_sort_mode"]);

if( count( $threads ) == 3 )
{
    $next_thread = $threads[2];

    $smarty->assign('next_topic', $next_thread['threadId']);
} else {
    $smarty->assign('next_topic', false);
}

if( count( $threads ) )
{
    $prev_thread = $threads[0];
    $smarty->assign('prev_topic', $prev_thread['threadId']);
} else {
    $smarty->assign('prev_topic', false);
}

//$end_time = microtime(true);

//print "TIME3: ".($end_time - $start_time)."\n";

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
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
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

    if ( $prefs['feature_forum_topics_archiving'] == 'y' && isset($_REQUEST['archive']) && isset($_REQUEST['comments_parentId']) ) {
	check_ticket('view-forum');
	if ( $_REQUEST['archive'] == 'y' ) {
	    $commentslib->archive_thread($_REQUEST['comments_parentId']);
	} elseif ( $_REQUEST['archive'] == 'n' ) {
	    $commentslib->unarchive_thread($_REQUEST['comments_parentId']);
	}
    }
}

if ($tiki_p_forums_report == 'y' && isset($_REQUEST['report'])) {    
	check_ticket('view-forum');
	$commentslib->report_post($_REQUEST['forumId'], $_REQUEST['comments_parentId'], $_REQUEST['report'], $user, '');
	$url = "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&comments_parentId=" . $_REQUEST['comments_parentId'] . "&post_reported=y";
	header('location: ' . $url);
	die;    
}
//shows a "thanks for reporting" message
if (isset($_REQUEST["post_reported"]) && $_REQUEST["post_reported"] == 'y') {
	$smarty->assign('post_reported', 'y');
}

$smarty->assign_by_ref('forum_info', $forum_info);
$thread_info = $commentslib->get_comment($_REQUEST["comments_parentId"]);
if (empty($thread_info)) {
	$smarty->assign('msg', tra("Incorrect thread"));
	$smarty->display("error.tpl");
	die;
}
if (!empty($thread_info['parentId'])) {
	$thread_info['topic'] = $commentslib->get_comment($thread_info['parentId']);
}

if ($tiki_p_admin_forum != 'y' && $thread_info['type'] == 'l') {
	$tiki_p_forum_post = 'n';
	$smarty->assign('tiki_p_forum_post', 'n');
}
//print_r($thread_info);
$smarty->assign_by_ref('thread_info', $thread_info);

$comments_per_page = $forum_info['commentsPerPage'];
$thread_sort_mode = $forum_info['threadOrdering'];
$thread_style = $forum_info['threadStyle'];

$comments_vars = array('forumId');
$comments_prefix_var = 'forum:';
$comments_object_var = 'forumId';
$commentslib->process_inbound_mail($_REQUEST['forumId']);

//$end_time = microtime(true);

//print "TIME1: ".($end_time - $start_time)."\n";

if ( isset($_REQUEST['display']) && $_REQUEST['display'] == 'print_all' ) {
	$_REQUEST['comments_per_page'] = 0; // unlimited
}

$forum_mode = 'y';
include_once ("comments.php");

//$end_time = microtime(true);

//print "TIME4: ".($end_time - $start_time)."\n";

$cat_type = 'forum';
$cat_objid = $_REQUEST["forumId"];
include_once ('tiki-section_options.php');

//$end_time = microtime(true);

//print "TIME5: ".($end_time - $start_time)."\n";

if ($user && $tiki_p_notepad == 'y' && $prefs['feature_notepad'] == 'y' && isset($_REQUEST['savenotepad'])) {
    check_ticket('view-forum');
    $info = $commentslib->get_comment($_REQUEST['savenotepad']);
    $tikilib->replace_note($user, 0, $info['title'], $info['data']);
}

if ($prefs['feature_user_watches'] == 'y') {
    if ($user && isset($_REQUEST['watch_event'])) {
	check_ticket('view-forum');
	if ($_REQUEST['watch_action'] == 'add') {
	    $tikilib->add_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], 'forum topic', $forum_info['name'] . ':' . $thread_info['title'], "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&amp;comments_parentId=" . $_REQUEST['comments_parentId']);
	} else {
	    $tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object']);
	}
    }

    $smarty->assign('user_watching_topic', 'n');

    if ($user && $tikilib->user_watches($user, 'forum_post_thread', $_REQUEST['comments_parentId'], 'forum topic')) {
	$smarty->assign('user_watching_topic', 'y');
    }
    
    // Check, if the user is watching this forum's topic and thread by a category.
	if ($prefs['feature_categories'] == 'y') {    			
	    $watching_categories_temp=$categlib->get_watching_categories($_REQUEST["forumId"],'forum',$user);	    
	    $smarty->assign('category_watched','n');
	 	if (count($watching_categories_temp) > 0) {
	 		$smarty->assign('category_watched','y');
	 		$watching_categories=array();	 			 	
	 		foreach ($watching_categories_temp as $wct ) {
	 			$watching_categories[]=array("categId"=>$wct,"name"=>$categlib->get_category_name($wct));
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

// Generate the list of topics, used in comments.tpl for the moderator actions (e.g. move a comment to another topic)
if ( $tiki_p_admin_forum == 'y' ) {
	$topics = $commentslib->get_forum_topics($_REQUEST['forumId'], 0, 200, 'commentDate_desc');
	$smarty->assign_by_ref('topics', $topics);
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
    $cat_objid = $comments_parentId;
    $tags = $freetaglib->get_tags_on_object($cat_objid, $cat_type);
    $smarty->assign('freetags',$tags);
}

$defaultRows = $prefs['default_rows_textarea_forumthread'];
include_once("textareasize.php");

if ($prefs['feature_forum_parse'] == "y") {
	include_once ('lib/quicktags/quicktagslib.php');
	$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','forums');
	$smarty->assign_by_ref('quicktags', $quicktags["data"]);
}
$smarty->assign('forum_mode', 'y');

if ($prefs['feature_mobile'] =='y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");

	HAWTIKI_view_forum_thread($forum_info['name'], $thread_info, $tiki_p_forum_read);
}

if ($prefs['feature_actionlog'] == 'y') {
	include_once('lib/logs/logslib.php');
	$logslib->add_action('Viewed', $_REQUEST['forumId'], 'forum', 'comments_parentId='.$comments_parentId);
}

ask_ticket('view-forum');

// Display the template
if ( isset($_REQUEST['display']) ) {

	// Remove icons and actions that should not be printed
	$prefs['forum_thread_user_settings'] = 'n';
	$smarty->assign('display', $_REQUEST['display']);
	$smarty->assign('thread_show_comment_footers', 'n');
	$smarty->assign('thread_show_pagination', 'n');
	$smarty->assign('tiki_p_forum_post', 'n');
	$smarty->assign('tiki_p_admin_forum', 'n');
	$smarty->assign('tiki_p_forum_edit_own_posts', 'n');
	$smarty->assign('tiki_p_notepad', 'n');

	// Display the forum messages
	$smarty->assign('mid', 'tiki-print_forum_thread.tpl');

	// Allow PDF export by installing a Mod that define an appropriate function
	if ( $_REQUEST['display'] == 'pdf' ) {
		// Method using 'mozilla2ps' mod
		if ( file_exists('lib/mozilla2ps/mod_urltopdf.php') ) {
			include_once('lib/mozilla2ps/mod_urltopdf.php');
			mod_urltopdf();
		}
	} else {
		$smarty->display('tiki-print.tpl');
	}

} else {
	// Detect if we have a PDF export mod installed
	$smarty->assign('pdf_export', file_exists('lib/mozilla2ps/mod_urltopdf.php') ? 'y' : 'n');

	$smarty->assign('display', '');
	$smarty->assign('mid', 'tiki-view_forum_thread.tpl');
	$smarty->display('tiki.tpl');
}

?>
