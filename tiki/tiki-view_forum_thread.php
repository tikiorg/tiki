<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_forum_thread.php,v 1.48 2003-12-04 09:05:45 mose Exp $

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
	    foreach(array_values($_REQUEST['forumthread']) as $thread) {
		$commentslib->remove_comment($thread);
		$commentslib->register_remove_post($_REQUEST['forumId'], $_REQUEST['comments_parentId']);
	    }
	}
    }

    if (isset($_REQUEST['remove_attachment'])) {
	$commentslib->remove_thread_attachment($_REQUEST['remove_attachment']);
    }

    if(isset($_REQUEST['movesel'])) {
	if (isset($_REQUEST['forumthread'])) {
	    foreach(array_values($_REQUEST['forumthread']) as $thread) {
		$commentslib->set_parent($thread,$_REQUEST['moveto']);
	    }
	}
    }
}

if ($tiki_p_forums_report == 'y') {
    if (isset($_REQUEST['report'])) {
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
if ($tiki_p_admin_forum == 'y' || $tiki_p_forum_post == 'y') {
    if ($thread_info["type"] <> 'l' || $tiki_p_admin_forum == 'y') {
	if (isset($_REQUEST["comments_postComment"])) {
	    if (!empty($_REQUEST["comments_data"])) {
		if (empty($_REQUEST["comments_title"])) {
		    $_REQUEST["comments_title"] = tra('Re:').' '.$thread_info["title"];
		}

		if ($commentslib->user_can_post_to_forum($user, $_REQUEST["forumId"])) {
		    //Replace things between square brackets by links
		    $_REQUEST["comments_data"] = strip_tags($_REQUEST["comments_data"]);

		    if ($forum_info['forum_use_password'] == 'a' && $_REQUEST['password'] != $forum_info['forum_password']) {
			$smarty->assign('msg', tra("Wrong password. Cannot post comment"));

			$smarty->display("error.tpl");
			die;
		    }

		    if (($tiki_p_forum_autoapp != 'y') && ($forum_info['approval_type']
				== 'queue_all' || (!$user && $forum_info['approval_type'] == 'queue_anon'))) {
			$smarty->assign('was_queued', 'y');

			$qId = $commentslib->replace_queue(
				0, $_REQUEST['forumId'], $comments_objectId, $_REQUEST["comments_parentId"],
				$user, $_REQUEST["comments_title"], $_REQUEST["comments_data"], '', '', '', $thread_info['title']);

			// PROCESS ATTACHMENT HERE        
			if ($qId && ($forum_info['att'] == 'att_all')
				|| ($forum_info['att'] == 'att_admin' && $tiki_p_admin_forum == 'y')
				|| ($forum_info['att'] == 'att_perm' && $tiki_p_forum_attach == 'y')) {
			    if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
				$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

				$data = '';
				$fhash = '';

				if ($forum_info['att_store'] == 'dir') {
				    $name = $_FILES['userfile1']['name'];

				    $fhash = md5(uniqid('.'));

				    // Just in case the directory doesn't have the trailing slash
				    if (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) == '\\') {
					$forum_info['att_store_dir'] = substr($forum_info['att_store_dir'],
						0, strlen($forum_info['att_store_dir']) - 1). '/';
				    } elseif (
					    substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) != '/') {
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

				$commentslib->attach_file(0, $qId, $name, $type, $size, $data,
					$fhash, $forum_info['att_store_dir'], $_REQUEST['forumId']);
			    }
			}
			//END ATTACHMENT PROCESSING
		    } else {
			$smarty->assign('was_queued', 'n');

			if ($_REQUEST["comments_threadId"] == 0) {
			    if (isset($_REQUEST["quote"]) &&
				    $_REQUEST["quote"] )
			    {
				$quote_info = $commentslib->get_comment($_REQUEST["quote"]);
				$in_reply_to = $quote_info["message_id"];

				// Don't carry the quote value through
				// after this.
				$smarty->clear_assign('quote');
				$quote = 0;
				$_REQUEST["quote"] = 0;
			    } else {
				$in_reply_to = '';
			    }

			    $message_id = '';

			    $threadId = $commentslib->post_new_comment($comments_objectId, $_REQUEST["comments_parentId"],
				    $user, $_REQUEST["comments_title"],'',
				    $_REQUEST["comments_data"],
				    $message_id, $in_reply_to
				    );

			    // PROCESS ATTACHMENT HERE        
			    if ($threadId && ($forum_info['att'] == 'att_all')
				    || ($forum_info['att'] == 'att_admin' && $tiki_p_admin_forum == 'y')
				    || ($forum_info['att'] == 'att_perm' && $tiki_p_forum_attach == 'y')) {
				if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
				    $fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

				    $data = '';
				    $fhash = '';

				    if ($forum_info['att_store'] == 'dir') {
					$name = $_FILES['userfile1']['name'];

					$fhash = md5(uniqid('.'));

					// Just in case the directory doesn't have the trailing slash
					if (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1)
						== '\\') {
					    $forum_info['att_store_dir'] = substr($forum_info['att_store_dir'],
						    0, strlen($forum_info['att_store_dir']) - 1). '/';
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

			    // outbound_from is redundant with sender_email: can be merged to simplify
			    $from = $forum_info["outbound_from"]? $forum_info["outbound_from"]: $sender_email;
			    if ($forum_info["outbound_address"]) {
				if( $in_reply_to )
				{
				    $in_reply_line = "In-Reply-To: <" . $in_reply_to . ">\r\n";
				} else {
				    $in_reply_line = '';
				}
				@mail(
					$forum_info["outbound_address"],
					$thread_info['title'],
					$_REQUEST["comments_title"] .
					"\n\n" . $_REQUEST["comments_data"],
					"From: " . $from . "\r\n" .
					"Message-Id: <" . $message_id . ">\r\n" .
					$in_reply_line .
					"Content-type: text/plain;charset=utf-8\r\n"
				     );
			    }

			    if ($feature_user_watches == 'y') {
				$nots = $commentslib->get_event_watches('forum_post_thread', $_REQUEST['comments_parentId']);

				foreach ($nots as $not) {
				    $smarty->assign('mail_forum', $forum_info["name"]);

				    $smarty->assign('mail_title', $_REQUEST["comments_title"]);
				    $smarty->assign('mail_date', date("u"));
				    $smarty->assign('mail_message', $_REQUEST["comments_data"]);
				    $smarty->assign('mail_author', $user);
				    $smarty->assign('mail_topic', tra('topic:'). $thread_info['title']);
				    $mail_data = $smarty->fetch('mail/forum_post_notification.tpl');
				    @mail($not['email'], tra('Tiki email notification'), $mail_data,
					    "From: " . $from . "\r\nContent-type: text/plain;charset=utf-8\r\n");
				}
			    }

			    if ($forum_info["useMail"] == 'y') {
				$smarty->assign('mail_forum', $forum_info["name"]);

				$smarty->assign('mail_title', $_REQUEST["comments_title"]);
				$smarty->assign('mail_date', date("u"));
				$smarty->assign('mail_message', $_REQUEST["comments_data"]);
				$smarty->assign('mail_author', $user);

				$mail_data = $smarty->fetch('mail/forum_post_notification.tpl');
				@mail($forum_info["mail"], tra('Tiki email notification'), $mail_data,
					"From: " . $from . "\r\nContent-type: text/plain;charset=utf-8\r\n");
			    }

			    $commentslib->register_forum_post($_REQUEST["forumId"], $_REQUEST["comments_parentId"]);
			} else {

			    if( $commentslib->user_can_edit_post(
					$user,
					$_REQUEST["comments_threadId"]
					)
				    || $tiki_p_admin_forum == 'y'
			      )
			    {

				// if($tiki_p_edit_comments == 'y') {
				$commentslib->update_comment($_REQUEST["comments_threadId"], $_REQUEST["comments_title"], $_REQUEST["comments_data"]);

				// PROCESS ATTACHMENT HERE        
				if (($forum_info['att'] == 'att_all') || ($forum_info['att'] == 'att_admin' && $tiki_p_admin_forum == 'y') || ($forum_info['att'] == 'att_perm' && $tiki_p_forum_attach == 'y')) {
				    if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
					$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");

					$data = '';
					$fhash = '';

					if ($forum_info['att_store'] == 'dir')
					{
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

				//}

			    } else { // user can't edit this post
				$smarty->assign('msg', tra('You are not permitted to edit someone else\'s post!'));

				$smarty->display("error.tpl");
				die;
			    }
			}
		    }
		} else {
		    $smarty->assign('msg', tra("Please wait 2 minutes between posts"));

		    $smarty->display("error.tpl");
		    die;
		}
	    }
	}
    }
}

if ($tiki_p_admin_forum == 'y' || $tiki_p_forum_vote == 'y') {
    // Process a vote here
    if (isset($_REQUEST["comments_vote"]) && isset($_REQUEST["comments_threadId"])) {
	$comments_show = 'y';

	if (!$tikilib->user_has_voted($user, 'comment' . $_REQUEST["comments_threadId"])) {
	    $commentslib->vote_comment($_REQUEST["comments_threadId"], $user, $_REQUEST["comments_vote"]);

	    $tikilib->register_user_vote($user, 'comment' . $_REQUEST["comments_threadId"]);
	}

	$_REQUEST["comments_threadId"] = 0;
	$smarty->assign('comments_threadId', 0);
    }
}

if ($_REQUEST["comments_threadId"] > 0) {
    $comment_info = $commentslib->get_comment($_REQUEST["comments_threadId"]);

    $smarty->assign('comment_title', $comment_info["title"]);
    $smarty->assign('comment_data', $comment_info["data"]);
} else {
    $smarty->assign('comment_title', tra('Re:').' '.$thread_info["title"]);

    $smarty->assign('comment_data', '');
}

if (isset($_REQUEST["quote"]) &&
	$_REQUEST["quote"] )
{
    $quote_info = $commentslib->get_comment($_REQUEST["quote"]);

    $quoted_lines = split("\n", $quote_info["data"]);
    $qdata = '';

    for ($i = 0; $i < count($quoted_lines); $i++) {
	$quoted_lines[$i] = '> ' . $quoted_lines[$i];
    }

    $qdata = implode("\n", $quoted_lines);
    $qdata = '> ' . $quote_info["userName"] . ":\n" . $qdata;
    $smarty->assign('comment_data', $qdata);
    $smarty->assign('comment_title', tra('Re:').' '.$quote_info["title"]);
    $smarty->assign('openpost', 'y');
}

$smarty->assign('comment_preview', 'n');

if (isset($_REQUEST["comments_previewComment"])) {
    $smarty->assign('comments_preview_title', $_REQUEST["comments_title"]);

    if ($feature_forum_parse == 'y') {
	$smarty->assign('comments_preview_data', ($tikilib->parse_data($_REQUEST["comments_data"])));
    } else {
	$smarty->assign('comments_preview_data', ($commentslib->parse_comment_data($_REQUEST["comments_data"])));
    }

    $smarty->assign('comment_title', $_REQUEST["comments_title"]);
    $smarty->assign('comment_data', $_REQUEST["comments_data"]);
    $smarty->assign('openpost', 'y');
    $smarty->assign('comment_preview', 'y');
}

if (isset($_REQUEST["comments_remove"]) && isset($_REQUEST["comments_threadId"]))
{
    if ($tiki_p_admin_forum == 'y')
    {
	$comments_show = 'y';

	$commentslib->remove_comment($_REQUEST["comments_threadId"]);
	$commentslib->register_remove_post($_REQUEST["forumId"], $_REQUEST["comments_parentId"]);
    } else { // user can't edit this post
	$smarty->assign('msg', tra('Only an admin can remove a thread.'));

	$smarty->display("error.tpl");
	die;
    }
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
$comments_coms = $commentslib->get_comments($comments_objectId, $_REQUEST["comments_parentId"], $comments_offset, $_REQUEST["comments_maxComments"], $_REQUEST["comments_sort_mode"], $_REQUEST["comments_commentFind"], $_REQUEST['comments_threshold']);
$replies_cant = $comments_coms['cant'];

// Was getting an error about comments_cant variable not found.  Not
// certain this is the right solution, so apologies in advance if this
// breaks something. -rlpowell
$comments_cant = $commentslib->count_comments($comments_objectId);
$smarty->assign('comments_below',$comments_coms["below"]);
$smarty->assign('comments_cant',$comments_cant);
$smarty->assign('replies_cant',$replies_cant);

$comments_maxRecords = $_REQUEST["comments_maxComments"];
$comments_cant_pages = ceil($comments_coms["cant"] / $comments_maxRecords);
$smarty->assign('comments_cant_pages', $comments_cant_pages);
$smarty->assign('comments_actual_page', 1 + ($comments_offset / $comments_maxRecords));

if ($comments_coms["cant"] > ($comments_offset + $comments_maxRecords)) {
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

$smarty->assign('comments_coms', $comments_coms["data"]);
/******************************/
$section = 'forums';
include_once ('tiki-section_options.php');

if ($feature_theme_control == 'y') {
    $cat_type = 'forum';

    $cat_objid = $_REQUEST["forumId"];
    include ('tiki-tc.php');
}

if ($user && $tiki_p_notepad == 'y' && $feature_notepad == 'y' && isset($_REQUEST['savenotepad'])) {
    $info = $commentslib->get_comment($_REQUEST['savenotepad']);

    $tikilib->replace_note($user, 0, $info['title'], $info['data']);
}

if ($feature_user_watches == 'y') {
    if ($user && isset($_REQUEST['watch_event'])) {
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

// Display the template
$smarty->assign('mid', 'tiki-view_forum_thread.tpl');
$smarty->display("tiki.tpl");

?>
