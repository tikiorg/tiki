<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_forum.php,v 1.57 2004-01-30 22:22:29 rlpowell Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($feature_forums != 'y') {
    $smarty->assign('msg', tra("This feature is disabled").": feature_forums");

    $smarty->display("error.tpl");
    die;
}

if (!isset($_REQUEST["forumId"])) {
    $smarty->assign('msg', tra("No forum indicated"));

    $smarty->display("error.tpl");
    die;
}

if (isset($_REQUEST["openpost"])) {
    $smarty->assign('openpost', 'y');
} else {
    $smarty->assign('openpost', 'n');
}

$smarty->assign('forumId', $_REQUEST["forumId"]);
include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);

$commentslib->forum_add_hit($_REQUEST["forumId"]);

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

if ($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
    $smarty->assign('msg', tra("You dont have permission to use this feature"));

    $smarty->display("error.tpl");
    die;
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

if ($tiki_p_forums_report == 'y') {
    if (isset($_REQUEST['report'])) {
	check_ticket('view-forum');
	$commentslib->report_post($_REQUEST['forumId'], 0, $_REQUEST['report'], $user, '');
    }
}

if ($tiki_p_admin_forum == 'y') {
    if (isset($_REQUEST['remove_attachment'])) {
	check_ticket('view-forum');
	$commentslib->remove_thread_attachment($_REQUEST['remove_attachment']);
    }

    if (isset($_REQUEST['delsel_x'])) {
	if (isset($_REQUEST['forumtopic'])) {
		check_ticket('view-forum');
	    foreach (array_values($_REQUEST['forumtopic'])as $topic) {
		$commentslib->remove_comment($topic);

		$commentslib->register_remove_post($_REQUEST['forumId'], 0);
	    }
	}
    }

    if (isset($_REQUEST['locksel_x'])) {
	if (isset($_REQUEST['forumtopic'])) {
		check_ticket('view-forum');
	    foreach (array_values($_REQUEST['forumtopic'])as $topic) {
		$commentslib->lock_comment($topic);
	    }
	}
    }

    if (isset($_REQUEST['unlocksel_x'])) {
	if (isset($_REQUEST['forumtopic'])) {
		check_ticket('view-forum');
	    foreach (array_values($_REQUEST['forumtopic'])as $topic) {
		$commentslib->unlock_comment($topic);
	    }
	}
    }

    if (isset($_REQUEST['movesel'])) {
	if (isset($_REQUEST['forumtopic'])) {
	    foreach (array_values($_REQUEST['forumtopic'])as $topic) {
		check_ticket('view-forum');
		// To move a topic you just have to change the object
		$obj = 'forum:' . $_REQUEST['moveto'];

		$commentslib->set_comment_object($topic, $obj);
	    }
	}
    }

    if (isset($_REQUEST['mergesel'])) {
	if (isset($_REQUEST['forumtopic'])) {
	    foreach (array_values($_REQUEST['forumtopic'])as $topic) {
		check_ticket('view-forum');
		// To move a topic you just have to change the object
		if ($topic != $_REQUEST['mergetopic']) {
		    $commentslib->set_parent($topic, $_REQUEST['mergetopic']);
		}
	    }
	}
    }
}

$smarty->assign_by_ref('forum_info', $forum_info);

$comments_per_page = $forum_info["topicsPerPage"];
$comments_default_ordering = $forum_info["topicOrdering"];
$comments_vars = array('forumId');
$comments_prefix_var = 'forum:';
$comments_object_var = 'forumId';

$commentslib->process_inbound_mail($_REQUEST['forumId']);

/******************************/
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
    die ("the comments_object_var variable is not set or cannot be found as a REQUEST variable");
}

$comments_objectId = $comments_prefix_var . $_REQUEST["$comments_object_var"];
// Process a post form here 
$smarty->assign('warning', 'n');


// Here we send the user to the right thread/topic if it already exists;
// this is used for the 'discuss' tab.
if (isset($_REQUEST["comments_postComment"])) 
{
    // Check if the thread/topic already existis
    $threadId = $commentslib->check_for_topic(
	    $_REQUEST["comments_title"],
	    $_REQUEST["comments_data"]
	    );

    // If it does, send the user there with no delay.
    if ( $threadId )
    {
	// If the samely titled comment already
	// exists, go straight to it.
	$url = 'tiki-view_forum_thread.php?comments_parentId=' 
	    . urlencode( $threadId )
	    . '&topics_threshold=0&topics_offset=1&topics_sort_mode=commentDate_desc&topics_find=&forumId='
	    . urlencode( $_REQUEST["forumId"]);
	header('location: ' . $url);
	exit;
    }
}

if ($tiki_p_admin_forum == 'y' || $tiki_p_forum_post_topic == 'y') {
    if (isset($_REQUEST["comments_postComment"])) {
	if ((!empty($_REQUEST["comments_title"])) && (!empty($_REQUEST["comments_data"]))) {
	    if ($tiki_p_admin_forum == 'y' || $commentslib->user_can_post_to_forum($user, $_REQUEST["forumId"])) {
		//Replace things between square brackets by links
		$_REQUEST["comments_data"] = strip_tags($_REQUEST["comments_data"]);

		if ($tiki_p_admin_forum != 'y') {
		    $_REQUEST["comment_topictype"] = 'n';
		}

		if ($forum_info['topic_summary'] != 'y')
		    $_REQUEST['comment_topicsummary'] = '';

		if ($forum_info['topic_smileys'] != 'y')
		    $_REQUEST['comment_topicsmiley'] = '';

		if ($forum_info['forum_use_password'] != 'n' && $_REQUEST['password'] != $forum_info['forum_password']) {
		    $smarty->assign('msg', tra("Wrong password. Cannot post comment"));

		    $smarty->display("error.tpl");
		    die;
		}

		if (($tiki_p_forum_autoapp != 'y')
			&& ($forum_info['approval_type'] == 'queue_all' || (!$user && $forum_info['approval_type'] == 'queue_anon'))) {
		    print("<p>queued.\n");
		    $smarty->assign('was_queued', 'y');

		    $qId = $commentslib->replace_queue(0, $_REQUEST['forumId'], $comments_objectId, 0,
			    $user, $_REQUEST["comments_title"], $_REQUEST["comments_data"], $_REQUEST["comment_topictype"],
			    $_REQUEST['comment_topicsmiley'], $_REQUEST["comment_topicsummary"], $_REQUEST["comments_title"]);

		    // PROCESS ATTACHMENT HERE        
		    if ($qId && ($forum_info['att'] == 'att_all')
			    || ($forum_info['att'] == 'att_admin' && $tiki_p_admin_forum == 'y')
			    || ($forum_info['att'] == 'att_perm' && $tiki_p_forum_attach == 'y')) {
			if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
			    check_ticket('view-forum');
			    $fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

			    $data = '';
			    $fhash = '';

			    if ($forum_info['att_store'] == 'dir') {
				$name = $_FILES['userfile1']['name'];

				$fhash = md5(uniqid('.'));

				// Just in case the directory doesn't have the trailing slash
				if (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) == '\\') {
				    $forum_info['att_store_dir']
					= substr($forum_info['att_store_dir'], 0, strlen($forum_info['att_store_dir']) - 1). '/';
				} elseif (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) != '/')
				{
				    $forum_info['att_store_dir'] .= '/';
				}

				@$fw = fopen($forum_info['att_store_dir'] . $fhash, "wb");

				if (!$fw) {
				    $smarty->assign('msg', tra('Cannot write to this file:'). $fhash);

				    $smarty->display("error.tpl");
				    die;
				}
			    }

			    while (!feof($fp)) {
				if ($forum_info['att_store'] == 'db') {
				    $data .= fread($fp, 8192 * 16);
				} else {
				    $data = fread($fp, 8192 * 16);

				    fwrite($fw, $data);
				}
			    }

			    fclose ($fp);

			    if ($forum_info['att_store'] == 'dir') {
				fclose ($fw);

				$data = '';
			    }

			    $size = $_FILES['userfile1']['size'];
			    $name = $_FILES['userfile1']['name'];
			    $type = $_FILES['userfile1']['type'];

			    if ($size > $forum_info['att_max_size']) {
				$smarty->assign('msg', tra('Cannot upload this file maximum upload size exceeded'));

				$smarty->display("error.tpl");
				die;
			    }

			    $commentslib->attach_file(
				    0, $qId, $name, $type, $size, $data, $fhash, $forum_info['att_store_dir'], $_REQUEST['forumId']);
			}
		    }
		    //END ATTACHMENT PROCESSING
		    // Now process attchement here (queued attachment)
		} else {
		    $smarty->assign('was_queued', 'n');

		    if ($_REQUEST["comments_threadId"] == 0) {
			if (!isset($_REQUEST['comment_topicsummary']))
			    $_REQUEST['comment_topicsummary'] = '';

			if (!isset($_REQUEST['comment_topicsmiley']))
			    $_REQUEST['comment_topicsmiley'] = '';

			$message_id = '';
			$threadId =
			    $commentslib->post_new_comment(
				    $comments_objectId,
				    0, $user, $_REQUEST["comments_title"],
				    ($_REQUEST["comments_data"]),
				    $message_id,
				    '', // in_reply_to
				    $_REQUEST["comment_topictype"],
				    $_REQUEST["comment_topicsummary"],
				    $_REQUEST['comment_topicsmiley']
				    );

			if( $threadId ) 
			{
			    // The thread *WAS* successfully
			    // created.

			    // Deal with mail notifications.

			    // outbound_from is redundant with sender_email: can be merged to simplify
			    $from = $forum_info["outbound_from"]? $forum_info["outbound_from"]: $sender_email;

			    if ($forum_info["outbound_address"]) {
				$cdata_data = $_REQUEST["comments_data"];

				if (isset($_REQUEST["comments_topicssummary"]) &&
					$_REQUEST["comments_topicssummary"] )
				{
				    $cadata_data = $_REQUEST["comments_topicssummary"] . $cdata_data;
				}

				if ($forum_info["outbound_address"]) {
				    @mail(
					    $forum_info["outbound_address"],
					    $_REQUEST["comments_title"],
					    $_REQUEST["comments_title"] . "\n" . $_REQUEST["comments_data"],
					    "From: " . $from. "\r\nContent-type: text/plain;charset=utf-8\r\n"
					 );
				}
			    }

			    if ($forum_info["useMail"] == 'y') {
				$smarty->assign('mail_forum', $forum_info["name"]);

				$smarty->assign('mail_title', $_REQUEST["comments_title"]);
				$smarty->assign('mail_date', date("U"));
				$smarty->assign('mail_message', $_REQUEST["comments_data"]);
				$smarty->assign('mail_author', $user);
				$smarty->assign('mail_topic', tra(' new topic:'). $_REQUEST["comments_title"]);
				$mail_data = $smarty->fetch('mail/forum_post_notification.tpl');
				@mail($forum_info["mail"], tra('Tiki email notification'), $mail_data,
					"From: " . $from . "\r\nContent-type: text/plain;charset=utf-8\r\n");
			    }

			    // Check if the user is monitoring this post
			    if ($feature_user_watches == 'y') {
				$nots = $commentslib->get_event_watches('forum_post_topic', $_REQUEST['forumId']);

				foreach ($nots as $not) {
				    $smarty->assign('mail_forum', $forum_info["name"]);

				    $smarty->assign('mail_title', $_REQUEST["comments_title"]);
				    $smarty->assign('mail_date', date("U"));
				    $smarty->assign('mail_message', $_REQUEST["comments_data"]);
				    $smarty->assign('mail_author', $user);
				    $smarty->assign('mail_topic', tra(' new topic:'). $_REQUEST["comments_title"]);
				    $mail_data = $smarty->fetch('mail/forum_post_notification.tpl');
				    @mail($not['email'], tra('Tiki email notification'), $mail_data,
					    "From: ". $from . "\r\nContent-type: text/plain;charset=utf-8\r\n");
				}
			    }

			}


			// PROCESS ATTACHMENT HERE        
			if ($threadId && ($forum_info['att'] == 'att_all') || ($forum_info['att'] == 'att_admin' && $tiki_p_admin_forum == 'y') || ($forum_info['att'] == 'att_perm' && $tiki_p_forum_attach == 'y')) {
			    if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
				check_ticket('view-forum');
				$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

				$data = '';
				$fhash = '';

				if ($forum_info['att_store'] == 'dir') {
				    $name = $_FILES['userfile1']['name'];

				    $fhash = md5(uniqid('.'));

				    // Just in case the directory doesn't have the trailing slash
				    if (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) == '\\') {
					$forum_info['att_store_dir'] = substr($forum_info['att_store_dir'], 0, strlen($forum_info['att_store_dir']) - 1). '/';
				    } elseif (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) != '/') {
					$forum_info['att_store_dir'] .= '/';
				    }

				    @$fw = fopen($forum_info['att_store_dir'] . $fhash, "wb");

				    if (!$fw) {
					$smarty->assign('msg', tra('Cannot write to this file:'). $fhash);

					$smarty->display("error.tpl");
					die;
				    }
				}

				while (!feof($fp)) {
				    if ($forum_info['att_store'] == 'db') {
					$data .= fread($fp, 8192 * 16);
				    } else {
					$data = fread($fp, 8192 * 16);

					fwrite($fw, $data);
				    }
				}

				fclose ($fp);

				if ($forum_info['att_store'] == 'dir') {
				    fclose ($fw);

				    $data = '';
				}

				$size = $_FILES['userfile1']['size'];
				$name = $_FILES['userfile1']['name'];
				$type = $_FILES['userfile1']['type'];

				if ($size > $forum_info['att_max_size']) {
				    $smarty->assign('msg', tra('Cannot upload this file maximum upload size exceeded'));

				    $smarty->display("error.tpl");
				    die;
				}

				$commentslib->attach_file($threadId, 0, $name, $type, $size, $data, $fhash, $forum_info['att_store_dir'], $_REQUEST['forumId']);
			    }
			}
			//END ATTACHMENT PROCESSING
			$commentslib->register_forum_post($_REQUEST["forumId"], 0);
		    } else {
			if ($tiki_p_admin_forum == 'y') {
			    $commentslib->update_comment($_REQUEST["comments_threadId"], $_REQUEST["comments_title"], '', ($_REQUEST["comments_data"]), $_REQUEST["comment_topictype"], $_REQUEST['comment_topicsummary'], $_REQUEST['comment_topicsmiley']);

			    // PROCESS ATTACHMENT HERE        
			    if (($forum_info['att'] == 'att_all') || ($forum_info['att'] == 'att_admin' && $tiki_p_admin_forum == 'y') || ($forum_info['att'] == 'att_perm' && $tiki_p_forum_attach == 'y')) {
				if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	check_ticket('view-forum');
				    $fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

				    $data = '';
				    $fhash = '';

				    if ($forum_info['att_store'] == 'dir') {
					$name = $_FILES['userfile1']['name'];

					$fhash = md5(uniqid('.'));

					// Just in case the directory doesn't have the trailing slash
					if (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) == '\\') {
					    $forum_info['att_store_dir'] = substr($forum_info['att_store_dir'], 0, strlen($forum_info['att_store_dir']) - 1). '/';
					} elseif (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) != '/') {
					    $forum_info['att_store_dir'] .= '/';
					}

					@$fw = fopen($forum_info['att_store_dir'] . $fhash, "wb");

					if (!$fw) {
					    $smarty->assign('msg', tra('Cannot write to this file:'). $fhash);

					    $smarty->display("error.tpl");
					    die;
					}
				    }

				    while (!feof($fp)) {
					if ($forum_info['att_store'] == 'db') {
					    $data .= fread($fp, 8192 * 16);
					} else {
					    $data = fread($fp, 8192 * 16);

					    fwrite($fw, $data);
					}
				    }

				    fclose ($fp);

				    if ($forum_info['att_store'] == 'dir') {
					fclose ($fw);

					$data = '';
				    }

				    $size = $_FILES['userfile1']['size'];
				    $name = $_FILES['userfile1']['name'];
				    $type = $_FILES['userfile1']['type'];

				    if ($size > $forum_info['att_max_size']) {
					$smarty->assign('msg', tra('Cannot upload this file maximum upload size exceeded'));

					$smarty->display("error.tpl");
					die;
				    }

				    $commentslib->attach_file($_REQUEST["comments_threadId"], 0, $name, $type, $size, $data, $fhash, $forum_info['att_store_dir'], $_REQUEST['forumId']);
				}
			    }
			    //END ATTACHMENT PROCESSING
			}
		    }
		}
	    } else {
		$smarty->assign('msg', tra("Please wait 2 minutes between posts"));

		$smarty->display("error.tpl");
		die;
	    }
	} else {
	    //User didn't give title or data.
	    // give him a chance to correct this
	    $smarty->assign('openpost', 'y');

	    $smarty->assign('warning', 'y');
	}
    }
}

if ($tiki_p_admin_forum == 'y' || $tiki_p_forum_vote == 'y') {
    // Process a vote here
    if (isset($_REQUEST["comments_vote"]) && isset($_REQUEST["comments_threadId"])) {
	check_ticket('view-forum');
	$comments_show = 'y';

	if (!$tikilib->user_has_voted($user, 'comment' . $_REQUEST["comments_threadId"])) {
	    $commentslib->vote_comment($_REQUEST["comments_threadId"], $user, $_REQUEST["comments_vote"]);

	    $tikilib->register_user_vote($user, 'comment' . $_REQUEST["comments_threadId"]);
	}

	$_REQUEST["comments_threadId"] = 0;
	$smarty->assign('comments_threadId', 0);
    }
}

$smarty->assign('last_forum_visit', $_SESSION["last_forum_visit"]);

if ($_REQUEST["comments_threadId"] > 0) {
    $comment_info = $commentslib->get_comment($_REQUEST["comments_threadId"]);

    $smarty->assign('comment_title', $comment_info["title"]);
    $smarty->assign('comment_data', $comment_info["data"]);
    $smarty->assign('comment_topictype', $comment_info["type"]);
    $smarty->assign('comment_topicsummary', $comment_info["summary"]);
    $smarty->assign('comment_topicsmiley', $comment_info["smiley"]);
} else {
    $smarty->assign('comment_title', isset($_REQUEST["comments_title"]) ? $_REQUEST["comments_title"] : '');

    $smarty->assign('comment_data', isset($_REQUEST["comments_data"]) ? $_REQUEST["comments_data"] : '');
    $smarty->assign('comment_topictype', isset($_REQUEST["comment_topictype"]) ? $_REQUEST["comment_topictype"] : '');

    if (isset($REQUEST["comments_title"])) {
	$smarty->assign('comment_title', $_REQUEST["comments_title"]);
    } else {
	$smarty->assign('comment_title', '');
    }

    if (isset($REQUEST["comments_data"])) {
	$smarty->assign('comment_data', $_REQUEST["comments_data"]);
    } else {
	$smarty->assign('comment_data', '');
    }

    $smarty->assign('comment_topictype', 'n');
    $smarty->assign('comment_topicsummary', '');
    $smarty->assign('comment_topicsmiley', '');
}

if (isset($_REQUEST["comments_remove"]) && isset($_REQUEST["comments_threadId"]))
{
    if ($tiki_p_admin_forum == 'y'
            || ($commentslib->user_can_edit_post(
                    $user,
                    $_REQUEST["comments_threadId"]
                    )
                && $tiki_p_forum_post == 'y')
       )
    {
	check_ticket('view-forum');
        $comments_show = 'y';

        $commentslib->remove_comment($_REQUEST["comments_threadId"]);
        $commentslib->register_remove_post($_REQUEST["forumId"], 0);
    } else { // user can't edit this post
        $smarty->assign('msg', tra('You are not permitted to remove someone else\'s post!'));

        $smarty->display("error.tpl");
        die;
    }
}

$smarty->assign('comment_preview', 'n');

if (isset($_REQUEST["comments_previewComment"])) {
	check_ticket('view-forum');
    $smarty->assign('comments_preview_title', $_REQUEST["comments_title"]);

    $smarty->assign('comments_preview_data', ($commentslib->parse_comment_data($_REQUEST["comments_data"])));
    $smarty->assign('comment_title', $_REQUEST["comments_title"]);
    $smarty->assign('comment_data', $_REQUEST["comments_data"]);
    $smarty->assign('comment_topictype', $_REQUEST["comment_topictype"]);

    if ($forum_info['topic_summary'] == 'y')
	$smarty->assign('comment_topicsummary', $_REQUEST["comment_topicsummary"]);

    if ($forum_info['topic_smileys'] == 'y')
	$smarty->assign('comment_topicsmiley', $_REQUEST["comment_topicsmiley"]);

    $smarty->assign('openpost', 'y');
    $smarty->assign('comment_preview', 'y');
}

// Check for settings
if (!isset($_REQUEST["comments_maxComments"])) {
    $_REQUEST["comments_maxComments"] = $comments_per_page;
}

if (!isset($_REQUEST["comments_sort_mode"])) {
    $_REQUEST["comments_sort_mode"] = $comments_default_ordering;
} else {
    $comments_show = 'y';
}

if (!isset($_REQUEST["comments_commentFind"])) {
    $_REQUEST["comments_commentFind"] = '';
} else {
    $comments_show = 'y';
}

$smarty->assign('comments_maxComments', $_REQUEST["comments_maxComments"]);
$smarty->assign('comments_sort_mode', $_REQUEST["comments_sort_mode"]);
$smarty->assign('comments_commentFind', $_REQUEST["comments_commentFind"]);

//print("Show: $comments_show<br/>");
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

if (!isset($_REQUEST['time_control']))
$_REQUEST['time_control'] = 0;

$commentslib->set_time_control($_REQUEST['time_control']);
$comments_coms = $commentslib->get_forum_topics($_REQUEST['forumId'],
		$comments_offset, $_REQUEST['comments_maxComments'],
		$_REQUEST['comments_sort_mode']);

// Get the last "n" comments to this forum
$last_comments = $commentslib->get_last_forum_posts($_REQUEST['forumId'],
		$forum_info['forum_last_n']);
$smarty->assign_by_ref('last_comments',$last_comments);
$comments_cant = $commentslib->count_comments_threads($comments_objectId);
$smarty->assign('comments_cant', $comments_cant);

$comments_maxRecords = $_REQUEST["comments_maxComments"];
$comments_cant_pages = ceil($comments_cant / $comments_maxRecords);
$smarty->assign('comments_cant_pages', $comments_cant_pages);
$smarty->assign('comments_actual_page', 1 + ($comments_offset / $comments_maxRecords));

if ($comments_cant > ($comments_offset + $comments_maxRecords)) {
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

$smarty->assign_by_ref('comments_coms', $comments_coms);

$section = 'forums';
include_once ('tiki-section_options.php');

if ($feature_theme_control == 'y') {
    $cat_type = 'forum';

    $cat_objid = $_REQUEST["forumId"];
    include ('tiki-tc.php');
}

if ($feature_user_watches == 'y') {
    if ($user && isset($_REQUEST['watch_event'])) {
	check_ticket('view-forum');
	if ($_REQUEST['watch_action'] == 'add') {
	    $tikilib->add_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object'], tra('forum'), $forum_info['name'], "tiki-view_forum.php?forumId=" . $_REQUEST['forumId']);
	} else {
	    $tikilib->remove_user_watch($user, $_REQUEST['watch_event'], $_REQUEST['watch_object']);
	}
    }

    $smarty->assign('user_watching_forum', 'n');

    if ($user && $watch = $tikilib->get_user_event_watches($user, 'forum_post_topic', $_REQUEST['forumId'])) {
	$smarty->assign('user_watching_forum', 'y');
    }
}

if ($tiki_p_admin_forum == 'y' || $feature_forum_quickjump == 'y') {
    $all_forums = $commentslib->list_forums(0, -1, 'name_asc', '');

    for ($i = 0; $i < count($all_forums["data"]); $i++) {
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
$smarty->assign('mid', 'tiki-view_forum.tpl');
$smarty->display("tiki.tpl");

?>
