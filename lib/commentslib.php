<?php

// A library to handle comments on object (notes, articles, etc)
// This is just a test
class Comments extends TikiLib {
#  var $db;  // The PEAR db object used to access the database
    var $time_control = 0;

    function Comments($db) {
	if (!$db) {
	    die ("Invalid db object passed to CommentsLib constructor");
	}

	$this->db = $db;
    }

    /* Functions for the forums */
    function report_post($forumId, $parentId, $threadId, $user, $reason = '') {

	$now = date("U");

	$query = "delete from `tiki_forums_reported` where `forumId`=?";
	$bindvars=array($forumId);
	$this->query($query,$bindvars,-1,-1,false);

	$query = "insert into `tiki_forums_reported`(`forumId`,
	`parentId`, `threadId`, `user`, `reason`, `timestamp`)
	    values(?,?,?,?,?,?)";
	$bindvars=array($forumId,$parentId,$threadId,$user,$reason,$now);
	$this->query($query,$bindvars);
    }

    function list_reported($forumId, $offset, $maxRecords, $sort_mode, $find) {

	if ($find) {
	    $findesc = '%' . $find . '%';

	    $mid = " and `reason` like ? or `user` like ?";
	    $bindvars=array($forumId, $findesc, $findesc );
	} else {
	    $mid = "";
	    $bindvars=array($forumId);
	}

	$query = "select `forumId`, tfr.`threadId`, tfr.`parentId`,
	tfr.`reason`, tfr.`user`, `title` from `tiki_forums_reported`
	    tfr,  `tiki_comments` tc where tfr.`threadId` = tc.`threadId`
	    and `forumId`=? $mid order by ".
	    $this->convert_sortmode($sort_mode);
	$query_cant = "select count(*) from `tiki_forums_reported` tfr,
	`tiki_comments` tc where tfr.`threadId` = tc.`threadId` and
	    `forumId`=? $mid";
	$result = $this->query($query, $bindvars, $maxRecords, $offset);
	$cant = $this->getOne($query_cant, $bindvars);
	$now = date("U");
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $ret[] = $res;
	}

	$retval = array();
	$retval["data"] = $ret;
	$retval["cant"] = $cant;
	return $retval;
    }

    function is_reported($threadId) {
	return $this->getOne("select count(*) from `tiki_forums_reported` where `threadId`=?",array($threadId));
    }

    function remove_reported($threadId) {
	$query = "delete from `tiki_forums_reported` where `threadId`=?";

	$this->query($query,array((int) $threadId));
    }

    function get_num_reported($forumId) {
	return $this->getOne("select count(*) from `tiki_forums_reported` where `forumId`=?",array((int) $forumId));
    }

    function mark_comment($user, $forumId, $threadId) {
	if (!$user)
	    return false;

	$now = date("U");

	$query = "delete from `tiki_forum_reads` where `user`=? and `threadId`=?";
	$bindvars=array($user,(int) $threadId);
	$this->query($query,$bindvars,-1,-1,false);

	$query = "insert into `tiki_forum_reads`(`user`,`threadId`,`forumId`,`timestamp`)
	    values(?,?,?,?)";
	$bindvars=array($user,(int) $threadId,(int) $forumId,$now);
	$this->query($query,$bindvars);
    }

    function unmark_comment($user, $forumId, $threadId) {
	$query = "delete from `tiki_forum_reads` where `user`=? and `threadId`=?";

	$this->query($query,array($user,(int) $threadId));
    }

    function is_marked($threadId) {
	global $user;

	if (!$user)
	    return false;

	return $this->getOne("select count(*) from `tiki_forum_reads` where `user`=? and `threadId`=?",array($user,$threadId));
    }

    function attach_file($threadId, $qId, $name, $type, $size, $data, $fhash, $dir, $forumId) {
	$now = date("U");

	if ($fhash) {
	    // Do not store data if we have a file
	    $data = '';
	}

	$query = "insert into
	    `tiki_forum_attachments`(`threadId`, `qId`, `filename`,
		    `filetype`, `filesize`, `data`, `path`, `created`, `dir`,
		    `forumId`)
	    values(?,?,?,?,?,?,?,?,?,?)";
	$this->query($query,array($threadId,$qId,$name,$type,$size,$data,$fhash,$now,$dir,$forumId));
	// Now the file is attached and we can proceed.
    }

    function get_thread_attachments($threadId, $qId) {
	if ($threadId) {
	    $cond = " where `threadId`=?";
	    $bindvars=array($threadId);
	} else {
	    $cond = " where `qId`=?";
	    $bindvars=array($qId);
	}

	$query = "select `filename`,`filesize`,`attId` from `tiki_forum_attachments` $cond";
	$result = $this->query($query,$bindvars);
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $ret[] = $res;
	}

	return $ret;
    }

    function get_thread_attachment($attId) {
	$query = "select * from `tiki_forum_attachments` where `attId`=?";

	$result = $this->query($query,array($attId));
	$res = $result->fetchRow();
	$forum_info = $this->get_forum($res['forumId']);

	$res['forum_info'] = $forum_info;
	return $res;
    }

    function remove_thread_attachment($attId) {
	$query = "delete from `tiki_forum_attachments` where `attId`=?";

	$this->query($query,array($attId));
    }

    function parse_output(&$obj, &$parts, $i) {
	if (!empty($obj->parts)) {
	    for ($i = 0; $i < count($obj->parts); $i++)
		$this->parse_output($obj->parts[$i], $parts, $i);
	} else {
	    $ctype = $obj->ctype_primary . '/' . $obj->ctype_secondary;

	    switch ($ctype) {
		case 'text/plain':
		case 'TEXT/PLAIN':
		    if (!empty($obj->disposition)AND $obj->disposition == 'attachment') {
			$names = split(';', $obj->headers["content-disposition"]);

			$names = split('=', $names[1]);
			$aux['name'] = $names[1];
			$aux['content-type'] = $obj->headers["content-type"];
			$aux['part'] = $i;
			$parts['attachments'][] = $aux;
		    } else {
			$parts['text'][] = $obj->body;
		    }

		    break;

		case 'text/html':
		case 'TEXT/HTML':
		    if (!empty($obj->disposition)AND $obj->disposition == 'attachment') {
			$names = split(';', $obj->headers["content-disposition"]);

			$names = split('=', $names[1]);
			$aux['name'] = $names[1];
			$aux['content-type'] = $obj->headers["content-type"];
			$aux['part'] = $i;
			$parts['attachments'][] = $aux;
		    } else {
			$parts['html'][] = $obj->body;
		    }

		    break;

		default:
		    $names = split(';', $obj->headers["content-disposition"]);

		    $names = split('=', $names[1]);
		    $aux['name'] = $names[1];
		    $aux['content-type'] = $obj->headers["content-type"];
		    $aux['part'] = $i;
		    $parts['attachments'][] = $aux;
	    }
	}
    }

    function process_inbound_mail($forumId) {
	require_once ("lib/webmail/pop3.php");

	require_once ("lib/webmail/mimeDecode.php");
	include_once ("lib/webmail/class.rc4crypt.php");
	include_once ("lib/webmail/htmlMimeMail.php");
	$info = $this->get_forum($forumId);

	if (!$info["inbound_pop_server"])
	    return;

	$pop3 = new POP3($info["inbound_pop_server"], $info["inbound_pop_user"], $info["inbound_pop_password"]);

	if (!$pop3)
	    return;

	$pop3->Open();
	$s = $pop3->Stats();
	$mailsum = $s["message"];

	for ($i = 1; $i <= $mailsum; $i++) {
	    $aux = $pop3->ListMessage($i);

	    if (empty($aux["sender"]["name"]))
		$aux["sender"]["name"] = $aux["sender"]["email"];

	    // Remove 're:' and [forum]. -rlpowell
	    $title = trim(
		    preg_replace( "/[rR][eE]:/", "", 
			preg_replace( "/\[[-A-Za-z _:]*\]/", "", 
			    $aux['subject'] 
			    )
			)
		    );

	    $email = $aux["sender"]["email"];
	    $message = $pop3->GetMessage($i);
	    $full = $message["full"];
	    $params = array(
		    'input' => $full,
		    'crlf' => "\r\n",
		    'include_bodies' => TRUE,
		    'decode_headers' => TRUE,
		    'decode_bodies' => TRUE
		    );

	    $decoder = new Mail_mimeDecode($full);
	    $output = $decoder->decode($params);
	    unset ($parts);
	    $this->parse_output($output, $parts, 0);

	    if (isset($parts["text"][0]))
		$body = $parts["text"][0];

	    //Todo: check permissions
	    $message_id = substr($output->headers["message-id"], 1,
		    strlen($output->headers["message-id"])-2);

	    if( isset( $output->headers["in-reply-to"] ) )
	    {
		$in_reply_to = substr($output->headers["in-reply-to"], 1,
			strlen($output->headers["in-reply-to"])-2);
	    } else {
		$in_reply_to = '';
	    }

	    // Determine user from email
	    $userName = $this->getOne("select `login` from `users_users` where `email`=?",array($email));

	    if (!$userName)
		$user = '';

	    // Determine if the thread already exists.
	    $parentId = $this->getOne(
		    "select `threadId` from `tiki_comments` where
		    `object`=? and `objectType` = 'forum' and
		    `parentId`=0 and `title`=?",
		    array($forumId,$title) 
		    );

	    if (!$parentId)
	    {
		// No thread already; create it.

		$temp_msid = '';

		$parentId = $this->post_new_comment(
			'forum:' . $forumId, 0,
			$userName, $title, 
			"Use this thread to discuss the [tiki-index.php?page=$title|$title] page.",
			$temp_msid
			);
	    }

	    // post
	    $this->post_new_comment( 'forum:' . $forumId,
		    $parentId, $userName, $title, $body,
		    $message_id, $in_reply_to);

	    $pop3->DeleteMessage($i);
	}

	$pop3->close();
    }

    /* queue management */
    function replace_queue($qId, $forumId, $object, $parentId, $user, $title, $data, $type = 'n', $topic_smiley = '', $summary = '',
	    $topic_title = '') {
	// timestamp

	$hash2 = md5($title . $data);

	if ($qId == 0 && $this->getOne("select count(*) from
		    `tiki_forums_queue` where `title`=? and `data`=?",array($title,$hash2)))
	    return false;

	$now = date("U");

	if ($qId) {
	    $query = "update `tiki_forums_queue` set
		`object` = ?,
	    `parentId`=?,
	    `user`=?,
	    `title`=?,
	    `data`=?,
	    `forumId`=?,
	    `type`=?,
	    `hash`=?,
	    `topic_title`=?,
	    `topic_smiley`=?,
	    `summary` = ?,
	    `timestamp` = ?
		where `qId`=?
		";

	    $this->query($query,array($object,$parentId,$user,$title,$data,$forumId,$type,$hash2,$topic_title,$topic_smiley,$summary,$now,$qId));
	    return $qId;
	} else {
	    $query = "insert into
		`tiki_forums_queue`(`object`, `parentId`, `user`,
			`title`, `data`, `type`, `topic_smiley`, `summary`,
			`timestamp`, `topic_title`, `hash`, `forumId`)
		values(?,?,?,?,?,?,?,?,?,?,?,?)";

	    $this->query($query, array($object, $parentId, $user,
			$title, $data, $type, $topic_smiley, $summary, $now,
			$topic_title, $hash2, $forumId));
	    $qId = $this->getOne("select max(`qId`) from
		    `tiki_forums_queue` where `hash`=? and
		    `timestamp`=?",array($hash2,$now));
	}

	return $qId;
    }

    function get_num_queued($object) {
	return $this->getOne("select count(*) from
		`tiki_forums_queue` where `object`=?",array($object));
    }

    function list_forum_queue($object, $offset, $maxRecords, $sort_mode, $find) {
	$sort_mode = str_replace("_", " ", $sort_mode);

	if ($find) {
	    $findesc = $this->qstr('%' . $find . '%');

	    $mid = " and title like $findesc or data like $findesc";
	} else {
	    $mid = "";
	}

	$query = "select * from `tiki_forums_queue` where `object`=? $mid order by $sort_mode limit $offset,$maxRecords";
	$query_cant = "select count(*) from `tiki_forums_queue` where `object`=? $mid";

	$result = $this->query($query, array($object) );
	$cant = $this->getOne($query_cant, array($object) );
	$now = date("U");
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $res['parsed'] = $this->parse_comment_data($res['data']);

	    $res['attachments'] = $this->get_thread_attachments(0, $res['qId']);
	    $ret[] = $res;
	}

	$retval = array();
	$retval["data"] = $ret;
	$retval["cant"] = $cant;
	return $retval;
    }

    function queue_get($qId) {
	$query = "select * from `tiki_forums_queue` where `qId`=$qId";

	$result = $this->query($query);
	$res = $result->fetchRow();
	$res['attchments'] = $this->get_thread_attachments(0, $res['qId']);
	return $res;
    }

    function remove_queued($qId) {
	$query = "delete from `tiki_forums_queue` where `qId`=$qId";

	$this->query($query);
	$query = "delete from `tiki_forum_attachments` where `qId`=$qId";
	$this->query($query);
    }

    //Approve queued message -> post as new comment
    function approve_queued($qId) {
	$info = $this->queue_get($qId);

	$message_id = '';
	$threadId = $this->post_new_comment(
		'forum:' . $info['forumId'], $info['parentId'],
		$info['user'], $info['title'], $info['data'], 
		$message_id, '', //in_reply_to
		$info['type'],
		$info['summary'], $info['topic_smiley']
		);
	$this->remove_queued($qId);

/* This all looks redundant to me -dheltzel
	if ($threadId) {
	    $query = "update `tiki_forum_attachments` set `threadId`=$threadId where `qId`=$qId";

	    $this->query($query);
	    $query = "delete from `tiki_forum_attachments` where `qId`=$qId";
	    $this->query($query);
	}
*/
    }

    function get_forum_topics($forumId, $offset = 0, $max = -1,
		    $sort_mode = 'commentDate_asc') {
	if ($sort_mode == 'points_asc') {
		$sort_mode = 'average_asc';
	}
	if ($this->time_control) {
		$limit = date("U") - $this->time_control;
		$time_cond = " and `commentDate` > ? ";
		$bind_time = array($limit);
	} else {
		$time_cond = '';
		$bind_time = array();
	}

	$old_sort_mode = '';
	if (in_array($sort_mode, array(
			'replies_desc',
			'replies_asc',
			'lastPost_desc',
			'lastPost_asc'
			))) {
	    $old_offset = $offset;

	    $old_maxRecords = $max;
	    $old_sort_mode = $sort_mode;
	    $sort_mode = 'title_desc';
	    $offset = 0;
	    $maxRecords = -1;
	}


	$ret = array();
	foreach (array('=', '<>') as $stickytest) {
		$query = "select * from `tiki_comments` where `object`=?
			and `type` $stickytest ?  and `objectType` = 'forum'
			and `parentId` = ? $time_cond
			order by ".$this->convert_sortmode($sort_mode).
			", `threadId`";
		$result = $this->query($query, array($forumId, 's', 0) +
				$bind_time, $max, $offset);

		while ($res = $result->fetchRow()) {
		    $tid = $res['threadId'];
		    // When was this topic last posted to?
		    $query = "select max(`commentDate`) from `tiki_comments`
			    where `parentId` = ?";
		    $res['lastPost'] = $this->getOne($query, array($tid));
		    if (!$res['lastPost']) {
			    $res['lastPost'] = $res['commentDate'];
		    } else {
			    $query = "select * from `tiki_comments`
				    where `parentId` = ? and `commentDate` = ?
				    order by `threadId` desc";
			    $r2 = $this->query($query, array($tid,
						    $res['lastPost']));
			    $res['lastPostData'] = $r2->fetchRow();
		    }

		    // How many replies to this topic?
		    $query = "select count(*) from `tiki_comments`
			    where `parentId` = ?";
		    $res['replies']['numReplies'] = $this->getOne($query,
				    array($tid));

		    // Has the user read it?
		    $res['is_marked'] = $this->is_marked($tid);
		    $ret[] = $res;
		}
	}

	if ($old_sort_mode) {
		if ($old_sort_mode == 'replies_asc') {
		    usort($ret, 'compare_replies');
		} elseif ($old_sort_mode == 'replies_desc') {
		    usort($ret, 'r_compare_replies');
		} elseif ($old_sort_mode == 'lastPost_asc') {
		    usort($ret, 'compare_lastPost');
		} elseif ($old_sort_mode == 'lastPost_desc') {
		    usort($ret, 'r_compare_lastPost');
		}

		$ret = array_slice($ret, $old_offset, $old_maxRecords);
	}

//	print("<pre>");
//	print_r($ret);
//	print("</pre>");

	return $ret;
    }

    function get_last_forum_posts($forumId, $maxRecords = -1)
    {
	$mid = " where `objectType` = ? and `object`=? ";
	$bind_mid = array('forum', $forumId);
	$sort_mode = 'commentDate_desc';

	$query = "select * from `tiki_comments` $mid order by ".$this->convert_sortmode($sort_mode);
	$result = $this->query($query, $bind_mid, $maxRecords, 0);

	while ($res = $result->fetchRow()) {
	    $ret[] = $res;
	}

	return $ret;
    }

    function replace_forum($forumId, $name, $description, $controlFlood,
	    $floodInterval, $moderator, $mail, $useMail,
	    $usePruneUnreplied, $pruneUnrepliedAge, $usePruneOld,
	    $pruneMaxAge, $topicsPerPage, $topicOrdering,
	    $threadOrdering, $section, $topics_list_reads,
	    $topics_list_replies, $topics_list_pts,
	    $topics_list_lastpost, $topics_list_author, $vote_threads,
	    $show_description, $inbound_pop_server, $inbound_pop_port,
	    $inbound_pop_user, $inbound_pop_password, $outbound_address,
	    $outbound_from, $topic_smileys, $topic_summary, $ui_avatar,
	    $ui_flag, $ui_posts, $ui_level, $ui_email, $ui_online,
	    $approval_type, $moderator_group, $forum_password,
	    $forum_use_password, $att, $att_store, $att_store_dir,
	    $att_max_size,$forum_last_n)
    {

	if ($forumId)
	{
	    $query = "update tiki_forums set
		name = ?,  	
	    description = ?,
	    controlFlood = ?,
	    floodInterval = ?,
	    moderator = ?,
	    mail = ?,
	    useMail = ?,
	    section = ?,
	    usePruneUnreplied = ?,
	    pruneUnrepliedAge = ?,
	    usePruneOld = ?,
	    vote_threads = ?,
	    topics_list_reads = ?,
	    topics_list_replies = ?,
	    show_description = ?,
	    inbound_pop_server = ?,
	    inbound_pop_port = ?,
	    inbound_pop_user = ?,
	    inbound_pop_password = ?,
	    outbound_address = ?,
	    outbound_from = ?,
	    topic_smileys = ?,
	    topic_summary = ?,
	    ui_avatar = ?,
	    ui_flag = ?,
	    ui_posts = ?,
	    ui_level = ?,
	    ui_email = ?,
	    ui_online = ?,
	    approval_type = ?,
	    moderator_group = ?,
	    forum_password = ?,
	    forum_use_password = ?,
	    att = ?,
	    att_store = ?,
	    att_store_dir = ?,
	    att_max_size = ?, 
	    topics_list_pts = ?,
	    topics_list_lastpost = ?,
	    topics_list_author = ?,
	    topicsPerPage = ?,
	    topicOrdering = ?,
	    threadOrdering = ?,
	    pruneMaxAge = ?,
	    forum_last_n = ?
		where `forumId` = ?";
	    $result = $this->query(
		    $query,
		    array(
			$name,  	
			$description,
			$controlFlood,
			$floodInterval,
			$moderator,
			$mail,
			$useMail,
			$section,
			$usePruneUnreplied,
			$pruneUnrepliedAge,
			$usePruneOld,
			$vote_threads,
			$topics_list_reads,
			$topics_list_replies,
			$show_description,
			$inbound_pop_server,
			$inbound_pop_port,
			$inbound_pop_user,
			$inbound_pop_password,
			$outbound_address,
			$outbound_from,
			$topic_smileys,
			$topic_summary,
			$ui_avatar,
			$ui_flag,
			$ui_posts,
			$ui_level,
			$ui_email,
			$ui_online,
			$approval_type,
			$moderator_group,
			$forum_password,
			$forum_use_password,
			$att,
			$att_store,
			$att_store_dir,
			$att_max_size,
			$topics_list_pts,
			$topics_list_lastpost,
			$topics_list_author,
			$topicsPerPage,
			$topicOrdering,
			$threadOrdering,
			$pruneMaxAge,
			$forum_last_n,
			$forumId
			    )
			    );
	} else {
	    $now = date("U");

	    $query = "insert into `tiki_forums`(`name`, `description`,
	    `created`, `lastPost`, `threads`, `comments`,
	    `controlFlood`,`floodInterval`, `moderator`, `hits`, `mail`,
	    `useMail`, `usePruneUnreplied`, `pruneUnrepliedAge`,
	    `usePruneOld`,`pruneMaxAge`, `topicsPerPage`,
	    `topicOrdering`, `threadOrdering`,`section`,
	    `topics_list_reads`, `topics_list_replies`,
	    `topics_list_pts`, `topics_list_lastpost`,
	    `topics_list_author`, `vote_threads`, `show_description`,
	    `inbound_pop_server`,`inbound_pop_port`,`inbound_pop_user`,`inbound_pop_password`,
	    `outbound_address`, `outbound_from`,
	    `topic_smileys`,`topic_summary`,
	    `ui_avatar`, `ui_flag`, `ui_posts`, `ui_level`, `ui_email`,
	    `ui_online`, `approval_type`, `moderator_group`,
	    `forum_password`, `forum_use_password`, `att`, `att_store`,
	    `att_store_dir`, `att_max_size`,`forum_last_n`) 
		values (?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,?,?,?,?,?,?)";
	    $bindvars=array($name, $description, (int) $now, (int) $now, 0, 0,
		    $controlFlood, (int) $floodInterval, $moderator, 0, $mail,
		    $useMail, $usePruneUnreplied, (int) $pruneUnrepliedAge,
		    $usePruneOld, (int) $pruneMaxAge, (int) $topicsPerPage,  $topicOrdering,
		    $threadOrdering, $section, $topics_list_reads,
		    $topics_list_replies, $topics_list_pts,
		    $topics_list_lastpost, $topics_list_author, $vote_threads,
		    $show_description, $inbound_pop_server, $inbound_pop_port,
		    $inbound_pop_user, $inbound_pop_password, $outbound_address,
		    $outbound_from,  $topic_smileys, $topic_summary, $ui_avatar,
		    $ui_flag, $ui_posts, $ui_level, $ui_email, $ui_online,
		    $approval_type, $moderator_group, $forum_password,
		    $forum_use_password, $att, $att_store, $att_store_dir,
		    (int) $att_max_size,(int) $forum_last_n);

	    $result = $this->query($query,$bindvars);
	    $forumId = $this->getOne("select max(`forumId`)
		    from `tiki_forums` where `name`=? and `created`=?",
		    array($name,(int) $now));
	}

	return $forumId;
    }

    function get_forum($forumId) {
	$query = "select * from `tiki_forums` where `forumId`=?";

	$result = $this->query($query,array((int) $forumId));
	$res = $result->fetchRow();
	return $res;
    }

    function remove_forum($forumId) {
	$query = "delete from `tiki_forums` where `forumId`=?";

	$result = $this->query($query, array( $forumId ) );
	// Now remove all the messages for the forum

	$query = "delete from `tiki_comments` where `object`=? and
	`objectType` = 'forum'";
	$result = $this->query($query, array( $forumId ) );
	$query = "delete from `tiki_forum_attachments` where `forumId`=?";
	$this->query($query, array( $forumId ) );
	return true;
    }

    function list_forums($offset, $maxRecords, $sort_mode, $find) {

	if ($find) {
	    $findesc = '%' . $find . '%';

	    $mid = " where `name` like ? or `description` like ? ";
	    $bindvars=array($findesc,$findesc);
	} else {
	    $mid = "";
	    $bindvars=array();
	}

	$query = "select * from `tiki_forums` $mid order by `section` asc,".$this->convert_sortmode($sort_mode);
	$query_cant = "select count(*) from `tiki_forums` $mid";
	$result = $this->query($query,$bindvars,$maxRecords,$offset);
	$cant = $this->getOne($query_cant,$bindvars);
	$now = date("U");
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $forum_age = ceil(($now - $res["created"]) / (24 * 3600));

	    $res["age"] = $forum_age;

	    if ($forum_age) {
		$res["posts_per_day"] = $res["comments"] / $forum_age;
	    } else {
		$res["posts_per_day"] = 0;
	    }

	    // Now select users
	    $query = "select distinct `userName` from
	    `tiki_comments` where `object`=? and `objectType` =
	    'forum'";
	    $result2 = $this->query($query,array((string) $res["forumId"]));
	    $res["users"] = $result2->numRows();

	    if ($forum_age) {
		$res["users_per_day"] = $res["users"] / $forum_age;
	    } else {
		$res["users_per_day"] = 0;
	    }


	    $query2 = "select * from `tiki_comments`,`tiki_forums`
		where `object`=".$this->sql_cast('`forumId`','string')." and `objectType` = ?
		and `commentDate`=?";
	    $result2 = $this->query($query2,array('forum',(int) $res["lastPost"]));
	    $res2 = $result2->fetchRow();
	    $res["lastPostData"] = $res2;
	    $ret[] = $res;
	}

	$retval = array();
	$retval["data"] = $ret;
	$retval["cant"] = $cant;
	return $retval;
    }

    function list_forums_by_section($section, $offset, $maxRecords, $sort_mode, $find) {

	if ($find) {
	    $findesc = '%' . $find . '%';

	    $mid = " where `section`=? and `name` like ? or `description` like ?";
	    $bindvars=array($section,$findesc,$findesc);
	} else {
	    $mid = " where `section`=? ";
	    $bindvars=array($section);
	}

	$query = "select * from `tiki_forums` $mid order by ".$this->convert_sortmode($sort_mode);
	$query_cant = "select count(*) from `tiki_forums`";
	$result = $this->query($query,$bindvars,$maxRecords,$offset);
	$cant = $this->getOne($query_cant,array());
	$now = date("U");
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $forum_age = ceil(($now - $res["created"]) / (24 * 3600));

	    $res["age"] = $forum_age;

	    if ($forum_age) {
		$res["posts_per_day"] = $res["comments"] / $forum_age;
	    } else {
		$res["posts_per_day"] = 0;
	    }

	    // Now select users
	    $query = "select distinct(`username`) from `tiki_comments`
	    where `object`=? and `objectType` = 'forum'";
	    $result2 = $this->query($query, array( $res["forumId"] ));
	    $res["users"] = $result2->numRows();

	    if ($forum_age) {
		$res["users_per_day"] = $res["users"] / $forum_age;
	    } else {
		$res["users_per_day"] = 0;
	    }

	    $query2 = "select * from `tiki_comments`,`tiki_forums` where
		`object`=`forumId` and `objectType` = 'forum' and
		`commentDate` = ?";
	    $result2 = $this->query($query2, array($res["lastPost"]));
	    $res2 = $result2->fetchRow();
	    $res["lastPostData"] = $res2;
	    $ret[] = $res;
	}

	$retval = array();
	$retval["data"] = $ret;
	$retval["cant"] = $cant;
	return $retval;
    }

    function user_can_edit_post( $user, $threadId )
    {
	$result = $this->getOne( "select `userName` from `tiki_comments`
		where `threadId` = ?", array( $threadId ) );

	if( $result == $user )
	{
	    return true;
	} else {
	    return false;
	}
    }

    function user_can_post_to_forum($user, $forumId) {
	// Check flood interval for the forum
	$forum = $this->get_forum($forumId);

	if ($forum["controlFlood"] != 'y')
	    return true;

	if ($user) {
	    $query = "select max(`commentDate`) from `tiki_comments`
	    where `object` = ? and `objectType` = 'forum' and
	    `userName` = ?";
	    $maxDate = $this->getOne($query, array( $forumId, $user) );

	    if (!$maxDate) {
		return true;
	    }

	    $now = date("U");

	    if ($maxDate + $forum["floodInterval"] > $now) {
		return false;
	    } else {
		return true;
	    }
	} else {
	    // Anonymous users
	    if (!isset($_SESSION["lastPost"])) {
		return true;
	    } else {
		$now = date("U");

		if ($_SESSION["lastPost"] + $forum["floodInterval"] > $now) {
		    return false;
		} else {
		    return true;
		}
	    }
	}
    }

    function register_forum_post($forumId, $parentId) {
	$now = date("U");

	if (!$parentId) {
	    $query = "update `tiki_forums` set `threads`=`threads`+1, `comments`=`comments`+1 where `forumId`=?";
	} else {
	    $query = "update `tiki_forums` set `comments`=`comments`+1 where `forumId`=?";
	}

	$result = $this->query($query,array((int) $forumId));

	$lastPost = $this->getOne("select max(`commentDate`) from
		`tiki_comments`,`tiki_forums` where `object` = `forumId`
		and `objectType` = 'forum' and
		`forumId` = ?", array( (int) $forumId ) );
	$query = "update `tiki_forums` set `lastPost`=? where
	`forumId`=? ";
	$result = $this->query($query, array( (int) $lastPost, (int) $forumId ));

	$this->forum_prune($forumId);
	return true;
    }

    function register_remove_post($forumId, $parentId) {
	$this->forum_prune($forumId);
    }

    function forum_add_hit($forumId) {
	global $count_admin_pvs;

	global $user;

	if ($count_admin_pvs == 'y' || $user != 'admin') {
	    $query = "update `tiki_forums` set `hits`=`hits`+1 where
	    `forumId`=?";

	    $result = $this->query($query, array( (int) $forumId ) );
	    $this->forum_prune($forumId);
	}

	return true;
    }

    function comment_add_hit($threadId) {
	global $count_admin_pvs;

	global $user;

	if ($count_admin_pvs == 'y' || $user != 'admin') {
	    $query = "update `tiki_comments` set `hits`=`hits`+1 where
	    `threadId`=?";

	    $result = $this->query($query, array( (int) $threadId ) );
	    //$this->forum_prune($forumId);
	}

	return true;
    }

    function forum_prune($forumId) {
	$forum = $this->get_forum($forumId);

	if ($forum["usePruneUnreplied"] == 'y') {
	    $age = $forum["pruneUnrepliedAge"];

	    // Get all unreplied threads
	    // Get all the top_level threads
	    $now = date("U");
	    $oldage = $now - $age;
	    $query = "select `threadId` from `tiki_comments` where
	    `parentId`=0  and commentDate<?";
	    $result = $this->query($query, array( (int) $oldage ));

	    while ($res = $result->fetchRow()) {
		// Check if this old top level thread has replies
		$id = $res["threadId"];

		$query2 = "select count(*) from `tiki_comments`
		where `parentId`=?";
		$cant = $this->getOne($query2, array( (int) $id ));

		if ($cant == 0) {
		    // Remove this old thread without replies
		    $query3 = "delete from `tiki_comments` where
		    `threadId` = ?";

		    $result3 = $this->query($query3, array( (int) $id ));
		    // This is just to be sure
		    $query3 = "delete from `tiki_comments` where
		    `parentId` = ?";
		    $result3 = $this->query($query3, array( (int) $id ));
		}
	    }
	}

	if ($forum["usePruneOld"] == 'y') {
	    $maxAge = $forum["pruneMaxAge"];

	    $old = date("U") - $maxAge;
	    $query = "delete from `tiki_comments` where `object`=?
	    and `objectType` = 'forum' and `commentDate`<?";
	    $result = $this->query($query, array($forumId, (int) $old));
	}

	// Recalculate comments and threads
	$query = "select count(*) from `tiki_comments` where
	`objectType` = 'forum' and `object`=?";
	$comments = $this->getOne($query, array( $forumId ) );
	$query = "select count(*) from `tiki_comments` where
	`objectType` = 'forum' and `object`=? and `parentId`=0";
	$threads = $this->getOne($query, array( $forumId ));
	$query = "update `tiki_forums` set `comments`=?, `threads`=?
	where `forumId`=?";
	$result = $this->query($query, array( (int) $comments, (int) $threads,
	(int) $forumId) );
	return true;
    }

    // FORUMS END
    function get_comment($id) {
	$query = "select * from `tiki_comments` where `threadId`=?";

	$result = $this->query($query, array( (int) $id ) );
	$res = $result->fetchRow();
	$res["parsed"] = $this->parse_comment_data($res["data"]);

	$res['user_posts'] = $this->getOne("select `posts` from
		`tiki_user_postings` where `user`=?",
		array( $res['userName'] ) );
	$res['user_level'] = $this->getOne("select `level` from
		`tiki_user_postings` where `user`=?",
		array( $res['userName'] ) );

	if ($this->get_user_preference($res['userName'], 'email is public', 'n') == 'y') {
	    $res['user_email'] = $this->getOne("select `email` from
		    `users_users` where `login`=?",
		    array( $res['userName'] ) );
	} else {
	    $res['user_email'] = '';
	}

	$res['attachments'] = $this->get_thread_attachments($res['threadId'], 0);
	$res['user_online'] = 'n';
	$res['is_reported'] = $this->is_reported($res['threadId']);

	if ($res['userName']) {
	    $res['user_online'] = $this->getOne("select count(*) from
		    `tiki_sessions` where `user`=?", array( $res['userName'] ) )
		? 'y' : 'n';
	}

	return $res;
    }

    function get_comment_father($id) {
	$query = "select `parentId` from `tiki_comments` where
	`threadId`=?";

	$ret = $this->getOne($query, array( $id ));
	return $ret;
    }

    function count_comments($objectId) {
	$object = explode( ":", $objectId, 2);
	$query = "select count(*) from `tiki_comments` where `objectType`=? and `object`=?";
	$cant = $this->getOne($query, $object );
	return $cant;
    }

    function get_comment_replies($id, $sort_mode, $offset, $max, $threshold = 0) {
	$query = "select
	`threadId`,`title`,`userName`,`points`,`commentDate`,`parentId`
	from `tiki_comments` where `parentId`=? and `average`>=?
	order by " .
	$this->convert_sortmode($sort_mode).",`commentDate` desc";

	$result = $this->query($query,array((int) $id,(int) $threshold),$max,$offset);
	$retval = array();
	$retval["numReplies"] = $result->numRows();
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $ret[] = $res;
	}

	for ($i = 0; $i < $retval["numReplies"]; $i++) {
		$ret[$i]['replies'] =
			$this->get_comment_replies($ret[$i]['threadId'],
					$sort_mode, 0, $max, $threshold);
	}

	$retval["replies"] = $ret;
	return $retval;
    }

    function flatten_comment_replies(&$replies, &$rep_flat, $level = 0)
    {
	$reps = $replies['numReplies'];
	for ($i = 0; $i < $reps; $i++) {
		$replies['replies'][$i]['level'] = $level;
		$rep_flat[] = &$replies['replies'][$i];
		if (isset($replies['replies'][$i]['replies'])) {
			$this->flatten_comment_replies(
					$replies['replies'][$i]['replies'],
					$rep_flat, $level + 1);
		}
	}
    }

    function parse_smileys($data) {
	global $feature_smileys;

	if ($feature_smileys == 'y') {
	    $data = preg_replace("/\(:([^:]+):\)/", "<img alt=\"$1\" src=\"img/smiles/icon_$1.gif\" />", $data);
	}

	return $data;
    }

    function pick_cookie() {
	$cant = $this->getOne("select count(*) from `tiki_cookies`",array());

	if (!$cant)
	    return '';

	$bid = rand(0, $cant - 1);
	$cookie = $this->query("select `cookie` from `tiki_cookies`",array(),1,$bid);
	$cookie = str_replace("\n", "", $cookie);
	return 'Cookie: ' . $cookie . '';
    }

    function parse_comment_data($data) {
	global $feature_forum_parse;

	global $tikilib;

	if ($feature_forum_parse == 'y') {
	    return $this->parse_data($data);
	}

	// Cookies
	if (preg_match_all("/\{cookie\}/", $data, $rsss)) {
	    for ($i = 0; $i < count($rsss[0]); $i++) {
		$cookie = $this->pick_cookie();

		$data = str_replace($rsss[0][$i], $cookie, $data);
	    }
	}

	// Fix up special characters, so it can link to pages with ' in them.  -rlpowell
	$data = htmlspecialchars( $data, ENT_QUOTES );
	$data = preg_replace("/\[([^\|\]]+)\|([^\]]+)\]/", '<a class="commentslink" href="$1">$2</a>', $data);
	// Segundo intento reemplazar los [link] comunes
	$data = preg_replace("/\[([^\]\|]+)\]/", '<a class="commentslink" href="$1">$1</a>', $data);

	// Llamar aqui a parse smileys
	$data = $this->parse_smileys($data);
	$data = preg_replace("/---/", "<hr/>", $data);
	// Reemplazar --- por <hr/>
	return nl2br($data);
    }

    /*****************/
    function set_time_control($time) {
	$this->time_control = $time;
    }

    function get_comments($objectId, $parentId, $offset = 0, $maxRecords = -1,
		    $sort_mode = 'commentDate_asc', $find = '', $threshold = 0)
    {
	if ($sort_mode == 'points_asc') {
	    $sort_mode = 'average_asc';
	}

	if ($this->time_control) {
	    $limit = date("U") - $this->time_control;

	    $time_cond = " and `commentDate` > ? ";
	    $bind_time = array($limit);
	} else {
	    $time_cond = '';
	    $bind_time = array();
	}

	$old_sort_mode = '';

	if (in_array($sort_mode, array(
			'replies_desc',
			'replies_asc'
			))) {
	    $old_offset = $offset;

	    $old_maxRecords = $maxRecords;
	    $old_sort_mode = $sort_mode;
	    $sort_mode = 'title_desc';
	    $offset = 0;
	    $maxRecords = -1;
	}

	// Break out the type and object parameters.
	$object = explode( ":", $objectId, 2);

	$query = "select count(*) from `tiki_comments` where
	`objectType`=? and `object`=? and `average` < ? $time_cond";
	$below = $this->getOne($query, array_merge(
	array($object[0], $object[1], (float) $threshold), $bind_time) );

	if ($find) {
	    $findesc = '%' . $find . '%';

	    $mid = " where `objectType` = ? and `object`=? and
	    `parentId`=? and `average`>=? and (`title`
	    like ? or `data` like ?) ";
	    $bind_mid=array($object[0],  $object[1],  (int) $parentId,
			    (int) $threshold, $findesc, $findesc);
	} else {
	    $mid = " where `objectType` = ? and `object`=? and `parentId`=? and `average`>=? ";
	    $bind_mid=array($object[0], $object[1], (int) $parentId, (int) $threshold);
	}

	$query = "select * from `tiki_comments` $mid $time_cond order by ".$this->convert_sortmode($sort_mode).",`threadId`";
	//print("$query<br/>");
	$query_cant = "select count(*) from `tiki_comments` $mid $time_cond";
	$result = $this->query($query,array_merge($bind_mid,$bind_time),$maxRecords,$offset);
	$cant = $this->getOne($query_cant,array_merge($bind_mid,$bind_time));
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $tid = $res["threadId"];

	    $res['user_posts'] = $this->getOne("select `posts` from `tiki_user_postings` where `user`=?",array($res['userName']));
	    $res['user_level'] = $this->getOne("select `level` from `tiki_user_postings` where `user`=?",array($res['userName']));

	    if ($this->get_user_preference($res['userName'], 'email is public', 'n') == 'y') {
		$res['user_email'] = $this->getOne("select `email` from `users_users` where `login`=?",array($res['userName']));
	    } else {
		$res['user_email'] = '';
	    }

	    $res['user_online'] = 'n';
	    $res['is_reported'] = $this->is_reported($tid);

	    if ($res['userName']) {
		$res['user_online'] = $this->getOne("select count(*) from `tiki_sessions` where `user`=?",array($res['userName'])) ? 'y' : 'n';
	    }

	    $res['attachments'] = $this->get_thread_attachments($res['threadId'], 0);

	    // Get the grandfather
	    if ($res["parentId"] > 0) {
		$res["grandFather"] = $this->get_comment_father($res["parentId"]);
	    } else {
		$res["grandFather"] = 0;
	    }

	    $res["parsed"] = $this->parse_comment_data($res["data"]);
	    // Get the replies
	    $res['replies'] = $this->get_comment_replies($res["threadId"], $sort_mode, 0, -1, $threshold);

	    if (empty($res["data"])) {
		$res["isEmpty"] = 'y';
	    } else {
		$res["isEmpty"] = 'n';
	    }

	    $ret[] = $res;
	}

	if ($old_sort_mode == 'replies_asc') {
	    usort($ret, 'compare_replies');
	}

	if ($old_sort_mode == 'replies_desc') {
	    usort($ret, 'r_compare_replies');
	}

	if (in_array($old_sort_mode, array(
			'replies_desc',
			'replies_asc',
			))) {
	    $ret = array_slice($ret, $old_offset, $old_maxRecords);
	}

	$retval = array();
	$retval["data"] = $ret;
	$retval["below"] = $below;
	$retval["cant"] = $cant;

	$msgs = count($retval['data']);
	for ($i = 0; $i < $msgs; $i++) {
		$r = &$retval['data'][$i]['replies'];
		$retval['data'][$i]['replies_flat'] = array();
		$rf = &$retval['data'][$i]['replies_flat'];
		$this->flatten_comment_replies($r, $rf);
	}

	return $retval;
    }

    function lock_comment($threadId) {
	$query = "update `tiki_comments`
	    set `type`='l' where `threadId`=?";

	$this->query($query, array( (int) $threadId ) );
    }

    function set_comment_object($threadId, $objectId) {
	// Break out the type and object parameters.
	$object = explode( ":", $objectId, 2);

	$query = "update `tiki_comments`
	    set `objectType` = ?, object`=? where `threadId`=? or
	    `parentId`=?";
	$this->query($query, array( $object[0], $object[1],
	(int) $threadId, (int) $threadId ) );
    }

    function set_parent($threadId, $parentId) {
	$query = "update `tiki_comments`
	    set `parentId`=? where `threadId`=?";

	$this->query($query, array( (int) $parentId, (int) $threadId ) );
    }

    function unlock_comment($threadId) {
	$query = "update `tiki_comments`
	    set `type`='n' where `threadId`=?";

	$this->query($query, array( (int) $threadId ) );
    }

    function update_comment($threadId, $title, $data, $type = 'n', $summary = '', $smiley = '') {
	$query = "update `tiki_comments` set `title`=?,
	data=?, type=?, summary=?, smiley=?
	    where `threadId`=?";
	$result = $this->query($query, array( $title, $data, $type,
		    $summary, $smiley, (int) $threadId ) );
    }

    // Added an aption, $getold, to have it return the threadId of
    // the old comment instead, if it finds one.  The threadId is
    // returned in the $getold variable iteslf. -Robin
    function post_new_comment($objectId, $parentId, $userName,
	    $title, $data, &$message_id, $in_reply_to = '', $type = 'n',
	    $summary = '', $smiley = '', $getold = false
	    )
    {
	//print "msid: $message_id, $in_reply_to.\n";
	if (!$userName) {
	    $_SESSION["lastPost"] = date("U");
	}

	if (!isset($_SERVER['REMOTE_ADDR']))
	    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

	// Check for banned userName or banned IP or IP in banned range

	// Check for duplicates.
	$title = strip_tags($title);

	if (!$userName) {
	    $userName = tra('Anonymous');
	} else {
	    $now = (int) date("U");

	    if ($this->getOne("select count(*) from 
			`tiki_user_postings` where `user`=?",
			array( $userName ),false))
	    {
		$query = "update `tiki_user_postings` ".
		    "set `last`=?, `posts` = `posts` + 1 where `user`=?";

		$this->query($query, array( $now, $userName ) );
	    } else {
		$posts = $this->getOne("select count(*) ".
			"from `tiki_comments` where `userName`=?",
			array( $userName),false);

		if (!$posts)
		    $posts = 1;

		$query = "insert into 
		    `tiki_user_postings`(`user`,`first`,`last`,`posts`) 
		    values( ?, ?, ?, ? )";
		$this->query($query,  array($userName, (int) $now, (int) $now,(int) $posts) );
	    }

	    // Calculate max
	    $max = $this->getOne("select max(`posts`) from `tiki_user_postings`",array());
	    $min = $this->getOne("select min(`posts`) from `tiki_user_postings`",array());

	    if ($min == 0)
		$min = 1;

	    $ids = $this->getOne("select count(*) from `tiki_user_postings`",array());
	    $tot = $this->getOne("select sum(`posts`) from `tiki_user_postings`",array());
	    $average = $tot / $ids;
	    $range1 = ($min + $average) / 2;
	    $range2 = ($max + $average) / 2;

	    $posts = $this->getOne("select `posts` ".
		    "from `tiki_user_postings` where `user`=?",
		    array($userName),false);

	    if ($posts == $max) {
		$level = 5;
	    } elseif ($posts > $range2) {
		$level = 4;
	    } elseif ($posts > $average) {
		$level = 3;
	    } elseif ($posts > $range1) {
		$level = 2;
	    } else {
		$level = 1;
	    }

	    $query = "update `tiki_user_postings` ".
		"set `level`=? where `user`=?";
	    $this->query($query, array( $level, $userName ) );
	}

	$hash = md5($title . $data);
	$query = "select `threadId` from `tiki_comments` where `hash`=?";
	$result = $this->query($query, array( $hash ) );

	// Check if we were passed a message-id.
	if ( ! $message_id )
	{
	    // Construct a message id via proctological
	    // extraction.  -rlpowell
	    $message_id = $userName . "-" .
		$parentId . "-" .
		substr( $hash, 0, 10 ) .
		"@" . $_SERVER["SERVER_NAME"];
	}

	if (!$result->numRows()) {
	    $now = (int) date("U");

	    // Break out the type and object parameters.
	    $object = explode( ":", $objectId, 2);

	    $query = "insert into
		`tiki_comments`(`objectType`, `object`,
		`commentDate`, `userName`, `title`, `data`, `votes`,
		`points`, `hash`, `parentId`, `average`, `hits`,
		`type`, `summary`, `smiley`, `user_ip`,
		`message_id`, `in_reply_to` )
		values ( ?, ?, ?, ?, ?, ?,
			0, 0, ?, ?, 0, 0, ?, ?, 
			?, ?, ?, ?)";

	    $result = $this->query($query, 
		    array( $object[0], $object[1], $now, $userName,
		    $title, $data, $hash, (int) $parentId, $type,
		    $summary, $smiley, $_SERVER["REMOTE_ADDR"],
		    $message_id, $in_reply_to )
		    );
	} else {
	    // If we have been asked to get the old page threadId, we don't quit here.
	    if( ! $getold )
	    {
		return false;
	    }
	}

	$threadId = $this->getOne("select `threadId` from
		`tiki_comments` where `hash`=?", array( $hash ) );
	return $threadId;
    }

    function remove_comment($threadId) {
	$query = "delete from `tiki_comments` where `threadId`=? or `parentId`=?";

	$result = $this->query($query, array( (int) $threadId, (int) $threadId ) );
	$query = "delete from `tiki_forum_attachments` where `threadId`=?";
	$this->query($query, array( (int) $threadId ) );
	$this->remove_reported($threadId);
	return true;
    }

    function vote_comment($threadId, $user, $vote) {

	// Select user points for the user who is voting (it may be anonymous!)
	$query = "select `points`,`voted` from `tiki_userpoints` where `user`=?";

	$result = $this->query($query, array( $user ) );

	if ($result->numRows()) {
	    $res = $result->fetchRow();

	    $user_points = $res["points"];
	    $user_voted = $res["voted"];
	} else {
	    $user_points = 0;

	    $user_voted = 0;
	}

	// Calculate vote weight (the Karma System)
	if ($user_voted == 0) {
	    $user_weight = 1;
	} else {
	    $user_weight = $user_points / $user_voted;
	}

	$vote_weight = ($vote * $user_weight) / 5;
	//print("User weight: $user_weight<br/>");
	//print("Vote: $vote vote_weight: $vote_weight<br/>");

	// Get the user that posted the comment being voted
	$query = "select `userName` from `tiki_comments` where `threadId`=?";
	$comment_user = $this->getOne($query, array( (int) $threadId ) );

	if ($comment_user && ($comment_user == $user)) {
	    // The user is voting a comment posted by himself then bail out
	    return false;
	}

	//print("Comment user: $comment_user<br/>");
	if ($comment_user) {
	    // Update the user points adding this new vote
	    $query = "select `user` from `tiki_userpoints` where `user`=?";

	    $result = $this->query($query, array( $comment_user ) );

	    if ($result->numRows()) {
		$query = "update `tiki_userpoints`
		    set `points` = `points` + ?, `voted`=`voted`+1
		    where `user`=?";
		$result = $this->query($query, array( $vote, $user ) );
	    } else {
		$query = "insert into
		    `tiki_userpoints`(`user`,`points`,`voted`)
		    values( ?, ?, 1 )";
		$result = $this->query($query, array( $comment_user, $vote ) );
	    }
	}

	$query = "update `tiki_comments`
	    set `points` = `points` + ?, `votes` = `votes`+1
	    where `threadId`=?";
	$result = $this->query($query, array( $vote_weight, $threadId ) );
	$query = "update `tiki_comments` set `average` = `points`/`votes`
	    where `threadId`=?";
	$result = $this->query($query, array( $threadId ) );
	return true;
    }
}

function compare_replies($ar1, $ar2) {
    if (($ar1['type'] == 's' && $ar2['type'] == 's') ||
		    ($ar1['type'] != 's' && $ar2['type'] != 's')) {
	return $ar1["replies"]["numReplies"] - $ar2["replies"]["numReplies"];
    } else {
	return $ar1['type'] == 's' ? -1 : 1;
    }
}

function compare_lastPost($ar1, $ar2) {
    if (($ar1['type'] == 's' && $ar2['type'] == 's') ||
		    ($ar1['type'] != 's' && $ar2['type'] != 's')) {
	return $ar1["lastPost"] - $ar2["lastPost"];
    } else {
	return $ar1['type'] == 's' ? -1 : 1;
    }
}

function r_compare_replies($ar1, $ar2) {
    if (($ar1['type'] == 's' && $ar2['type'] == 's') ||
		    ($ar1['type'] != 's' && $ar2['type'] != 's')) {
	return $ar2["replies"]["numReplies"] - $ar1["replies"]["numReplies"];
    } else {
	return $ar1['type'] == 's' ? -1 : 1;
    }
}

function r_compare_lastPost($ar1, $ar2) {
    if (($ar1['type'] == 's' && $ar2['type'] == 's') ||
		    ($ar1['type'] != 's' && $ar2['type'] != 's')) {
	return $ar2["lastPost"] - $ar1["lastPost"];
    } else {
	return $ar1['type'] == 's' ? -1 : 1;
    }
}

?>
