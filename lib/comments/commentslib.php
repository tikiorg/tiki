<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

// A library to handle comments on object (notes, articles, etc)
class Comments extends TikiLib
{
	var $time_control = 0;

	/* Functions for the forums */
	function report_post($forumId, $parentId, $threadId, $user, $reason = '') {
		$reported = $this->table('tiki_forums_reported');

		$data = array(
			'forumId' => $forumId,
			'parentId' => $parentId,
			'threadId' => $threadId,
			'user' => $user,
		);
		$reported->delete($data);

		$reported->insert(array_merge($data, array(
			'timestamp' => $this->now,
			'reason' => $reason,
		)));
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
			tfr.`reason`, tfr.`user`, `title`, SUBSTRING(`data` FROM 1 FOR 100) as `snippet` from `tiki_forums_reported`
				tfr,  `tiki_comments` tc where tfr.`threadId` = tc.`threadId`
				and `forumId`=? $mid order by ".
				$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_forums_reported` tfr,
			`tiki_comments` tc where tfr.`threadId` = tc.`threadId` and
				`forumId`=? $mid";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function is_reported($threadId) {
		return $this->table('tiki_forums_reported')->fetchCount(array('threadId' => (int) $threadId));
	}

	function remove_reported($threadId) {
		$this->table('tiki_forums_reported')->delete(array('threadId' => (int) $threadId));
	}

	function get_num_reported($forumId) {
		return $this->getOne("select count(*) from `tiki_forums_reported` tfr, `tiki_comments` tc where tfr.`threadId` = tc.`threadId` and `forumId`=?", array( (int) $forumId));
	}

	function mark_comment($user, $forumId, $threadId) {
		if (!$user)
			return false;

		$reads = $this->table('tiki_forum_reads');

		$reads->delete(array('user' => $user, 'threadId' => $threadId));
		$reads->insert(array(
			'user' => $user,
			'threadId' => (int) $threadId,
			'forumId' => (int) $forumId,
			'timestamp' => $this->now,
		));
	}

	function unmark_comment($user, $forumId, $threadId) {
		$this->table('tiki_forum_reads')->delete(array(
			'user' => $user,
			'threadId' => (int) $threadId,
		));
	}

	function is_marked($threadId) {
		global $user;

		if (!$user)
			return false;

		return $this->table('tiki_forum_reads')->fetchCount(array(
			'user' => $user,
			'threadId' => $threadId,
		));
	}

	/* Add an attachment to a post in a forum */
	function add_thread_attachment( $forum_info, $threadId, &$errors, $name, $type, $size, $inbound_mail = 0, $qId=0, $fp = '', $data = '') {
		global $smarty, $tiki_p_admin_forum, $tiki_p_forum_attach, $smarty;

		if( !($forum_info['att'] == 'att_all'
				|| ($forum_info['att'] == 'att_admin' && $tiki_p_admin_forum == 'y')
				|| ($forum_info['att'] == 'att_perm' && $tiki_p_forum_attach == 'y') ))	{
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('Permission denied'));
			$smarty->display("error.tpl");
			die;
		}
		if (!empty($prefs['forum_match_regex']) && !preg_match($prefs['forum_match_regex'], $name)) {
			$errors[] = tra('Invalid filename (using filters for filenames)');
			return 0;
		}
		if ($size > $forum_info['att_max_size'] && ! $inbound_mail ) {
			$errors[] = tra('Cannot upload this file - maximum upload size exceeded');
			return 0;
		}
		$fhash = '';
		if ($forum_info['att_store'] == 'dir') {
			$fhash = md5(uniqid('.'));
			// Just in case the directory doesn't have the trailing slash
			if (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) == '\\') {
				$forum_info['att_store_dir'] = substr($forum_info['att_store_dir'],	0, strlen($forum_info['att_store_dir']) - 1). '/';
			} elseif (substr($forum_info['att_store_dir'], strlen($forum_info['att_store_dir']) - 1, 1) != '/') {
				$forum_info['att_store_dir'] .= '/';
			}

			@$fw = fopen($forum_info['att_store_dir'] . $fhash, "wb");
			if (!$fw && ! $inbound_mail ) {
				$errors[] = tra('Cannot write to this file:').' '.$forum_info['att_store_dir'] . $fhash;
				return 0;
			}
		}
		if( $fp ) {
			while (!feof($fp)) {
				if ($forum_info['att_store'] == 'db') {
					$data .= fread($fp, 8192 * 16);
				} else {
					$data = fread($fp, 8192 * 16);
					fwrite($fw, $data);
				}
			}
			fclose ($fp);
		} else {
			if ($forum_info['att_store'] == 'dir') {
				fwrite($fw, $data);
			}
		}

		if ($forum_info['att_store'] == 'dir') {
			fclose ($fw);
			$data = '';
		}

		return $this->attach_file($threadId, $qId, $name, $type, $size, $data, $fhash, $forum_info['att_store_dir'], $forum_info['forumId']);
	}

	function attach_file($threadId, $qId, $name, $type, $size, $data, $fhash, $dir, $forumId) {
		if ($fhash) {
			// Do not store data if we have a file
			$data = '';
		}

		$this->table('tiki_forum_attachments')->insert(array(
			'threadId' => $threadId,
			'qId' => $qId,
			'filename' => $name,
			'filetype' => $type,
			'filesize' => $size,
			'data' => $data,
			'path' => $fhash,
			'created' => $this->now,
			'dir' => $dir,
			'forumId' => $forumId,
		));
		return true;
		// Now the file is attached and we can proceed.
	}

	function get_thread_attachments($threadId, $qId) {
		$conditions = array();

		if ($threadId) {
			$conditions['threadId'] = $threadId;
		} else {
			$conditions['qId'] = $qId;
		}

		$attachments = $this->table('tiki_forum_attachments');
		return $attachments->fetchAll($attachments->all(), $conditions);
	}

	function get_thread_attachment($attId) {
		$forumId = $this->table('tiki_forum_attachments')->fetchOne('forumId', array('attId' => $attId));
		$forum_info = $this->get_forum($forumId);

		$res['forum_info'] = $forum_info;
		return $res;
	}

	function remove_thread_attachment($attId) {
		$this->table('tiki_forum_attachments')->delete('forumId', array('attId' => $attId));
	}

	function parse_output(&$obj, &$parts, $i) {
		if (!empty($obj->parts)) {
			$temp_max = count($obj->parts);
			for ($i = 0; $i < $temp_max; $i++)
				$this->parse_output($obj->parts[$i], $parts, $i);
		} else {
			$ctype = $obj->ctype_primary . '/' . $obj->ctype_secondary;

			switch ($ctype) {
				case 'text/plain':
				case 'TEXT/PLAIN':
					if (!empty($obj->disposition)AND $obj->disposition == 'attachment') {
						$names = explode(';', $obj->headers["content-disposition"]);

						$names = explode('=', $names[1]);
						$aux['name'] = $names[1];
						$aux['content-type'] = $obj->headers["content-type"];
						$aux['part'] = $i;
						$parts['attachments'][] = $aux;
					} else {
						if(isset($obj->ctype_parameters) && ($obj->ctype_parameters['charset'] == "iso-8859-1" || $obj->ctype_parameters['charset'] == "ISO-8859-1"))
						{
							$parts['text'][] = utf8_encode($obj->body);
						} else {
							$parts['text'][] = $obj->body;
						}
					}

					break;

				case 'text/html':
				case 'TEXT/HTML':
					if (!empty($obj->disposition)AND $obj->disposition == 'attachment') {
						$names = explode(';', $obj->headers["content-disposition"]);

						$names = explode('=', $names[1]);
						$aux['name'] = $names[1];
						$aux['content-type'] = $obj->headers["content-type"];
						$aux['part'] = $i;
						$parts['attachments'][] = $aux;
					} else {
						$parts['html'][] = $obj->body;
					}

					break;

				default:
					$names = explode(';', $obj->headers["content-disposition"]);

					$names = explode('=', $names[1]);
					$aux['name'] = $names[1];
					$aux['content-type'] = $obj->headers["content-type"];
					$aux['part'] = $i;
					$parts['attachments'][] = $aux;
			}
		}
	}

	function process_inbound_mail($forumId) {
		// require_once ("lib/webmail/pop3.php");
		require_once ("lib/webmail/net_pop3.php");

		require_once ("lib/mail/mimelib.php");
		//require_once ("lib/webmail/mimeDecode.php");
		include_once ("lib/webmail/htmlMimeMail.php");
		$info = $this->get_forum($forumId);

		// for any reason my sybase test machine adds a space to
		// the inbound_pop_server field in the table.
		$info["inbound_pop_server"]=trim($info["inbound_pop_server"]);

		if (!$info["inbound_pop_server"] || empty($info["inbound_pop_server"]))
			return;

		$pop3 = new Net_POP3();
		$pop3->connect($info["inbound_pop_server"]);
		$pop3->login($info["inbound_pop_user"], $info["inbound_pop_password"]);

		if (!$pop3)
			return;

		$mailSum = $pop3->numMsg();

		//we don't want the operation to time out... this would result in the same messages being imported over and over...
		//(messages are only removed from the pop server on a gracefull connection termination... ie .not php or webserver a timeout)
		//$maximport should be in a admin config screen, but I don't know how to do that yet.
		$maxImport = 10;
		if ($mailSum > $maxImport ) $mailSum = $maxImport;

		for ($i = 1; $i <= $mailSum; $i++) {
			//echo 'loop ' . $i;

			$aux = $pop3->getParsedHeaders( $i );

			// If the connection is done, or the mail has an error, or whatever,
			// we try to delete the current mail (because something is wrong with it)
			// and continue on. --rlpowell
			if( $aux == FALSE ) {
				$pop3->deleteMsg( $i );
				continue;
			}

			//echo '<pre>';
			//print_r ($aux);
			//echo '</pre>';

			if (!isset($aux['From']))
			{
				if( isset($aux['Return-path']) )
				{
					$aux['From'] = $aux['Return-path'];
				}
				else
				{
					$aux['From'] = "";
					$aux['Return-path'] = "";
				}
			}

			//try to get the date from the email:
			$postDate = strtotime($aux['Date']);
			if ($postDate == false) $postDate = $this->now;

			//save the original email address, if we don't get a user match, then we
			//can at least give some info about the poster.
			$original_email = $aux["From"];

			//fix mailman addresses, or there is no chance to get a match
			$aux["From"] = str_replace(' at ', '@', $original_email);


			preg_match('/<?([-!#$%&\'*+\.\/0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\.\/0-9=?A-Z^_`a-z{|}~]+)>?/', $aux["From"], $mail);

			// should we throw out emails w/ invalid (possibly obfusicated) email addressses?
			//this should be an admin option, but I don't know how to put it there yet.
			$throwOutInvalidEmails = false;
			if( ! array_key_exists( 1, $mail ) )
			{
				if ( $throwOutInvalidEmails ) continue;
			}

			$email = $mail[1];

			$full = $pop3->getMsg( $i );
			$message = $pop3->getBody( $i );

			//print( "<pre>" );
			//print_r( $full );
			//print( "</pre>" );

			$output = mime::decode($full);
			//unset ($parts);
			//$this->parse_output($output, $parts, 0);

			//print( "<pre>" );print_r( $output );print_r( "</pre>" );

			if (isset($output["text"][0])) {
				$body = $output["text"][0];
			} elseif (isset($output['parts'][0]["text"][0])) {
				$body = $output['parts'][0]["text"][0];
			} elseif (isset($output['body'])) {
				$body = $output['body'];
			} elseif (isset($output['parts'][0]['html'][0])) {// some html message does not have a text part
				$body = $this->htmldecode(strip_tags(preg_replace('/\n\r/', '', $output['parts'][0]['html'][0])));
			} elseif (isset($output['parts'][0]['parts'][0]['text'][0])) {
				$body = $output['parts'][0]['parts'][0]['text'][0];
			} else {
				$body = "";
			}

			// print( "<pre>" );
			// print_r( $body );
			// print( "</pre>" );

			// Remove 're:' and [forum]. -rlpowell
			$title = trim(
					preg_replace( "/[rR][eE]:/", "", 
						preg_replace( "/\[[-A-Za-z _:]*\]/", "", 
							$output['header']['subject'] 
							)
						)
					);

			//Todo: check permissions
			$message_id = substr($output['header']["message-id"], 1,
					strlen($output['header']["message-id"])-2);

			if( isset( $output['header']["in-reply-to"] ) )
			{
				$in_reply_to = substr($output['header']["in-reply-to"], 1,
						strlen($output['header']["in-reply-to"])-2);
			} else {
				$in_reply_to = '';
			}
			// Determine user from email
			$userName = $this->table('users_users')->fetchOne('login', array('email' => $email));

			//use anonomus name feature if we don't have a real name
			if (!$userName) $anonName = $original_email;

			// Determine if the thread already exists.
			$parentId = $this->table('tiki_comments')->fetchOne('threadId', array(
				'object' => $forumId,
				'objectType' => 'forum',
				'parentId' => 0,
				'title' => $title,
			));

			if (!$parentId)
			{
				/*		
						This doesn't make any sense to me... why would we say an inbound email is a'thread to discuss a page'?
						I've updated this to just make a new thread w/ the original email info by seting $parentId = 0

				// No thread already; create it.

				$temp_msid = '';

				$parentId = $this->post_new_comment(
				'forum:' . $forumId, 0,
				$userName, $title, 
				sprintf(tra("Use this thread to discuss the %s page."), "[tiki-index.php?page=$title|$title]"),
				$temp_msid, $in_reply_to
				);

				$this->register_forum_post($forumId,0);

				// First post is in reply to this one
				$in_reply_to = $temp_msid;
				 */
				$parentId = 0;

			}

			// post
			$threadid = $this->post_new_comment( 'forum:' . $forumId,
					$parentId, $userName, $title, $body,
					$message_id, $in_reply_to, 'n', '', '', '', $anonName, $postDate);

			$this->register_forum_post($forumId, $parentId);

			// Process attachments
			if( array_key_exists( 'parts', $output ) && count( $output['parts'] ) > 1 ) {
				$forum_info = $this->get_forum( $forumId );
				$errors = array();
				foreach( $output['parts'] as $part ) {
					if (array_key_exists( 'disposition', $part )) {
						if ($part['disposition'] == 'attachment') {
							if( strlen( $part['d_parameters']['filename'] ) > 0 ) {
								$part_name = $part['d_parameters']['filename'];
							} else {
								$part_name = "Unnamed File";
							}
							$this->add_thread_attachment($forum_info, $threadid, $errors,	$part_name, $part['type'], strlen( $part['body'] ),	1, '', $part['body'] );
						} elseif ($part['disposition'] == 'inline') {
							foreach ($part['parts'] as $p) {
								$this->add_thread_attachment($forum_info, $threadid, $errors, '-', $p['type'], strlen( $p['body'] ),	1, '', $p['body'] );
							}
						}
					}
				}
			}

			// Deal with mail notifications.
			if( array_key_exists( 'outbound_mails_reply_link', $info )
					&& $info['outbound_mails_for_inbound_mails'] == 'y' )
			{
				//phpinfo();
				include_once('lib/notifications/notificationemaillib.php');
				sendForumEmailNotification('forum_post_thread',
						$threadid, $info,
						$title, $body, $userName,
						$title, $message_id, $in_reply_to,
						$threadid, $parentId);
			}
			$pop3->deleteMsg( $i );
		}
		$pop3->disconnect();
	}

	/* queue management */
	function replace_queue($qId, $forumId, $object, $parentId, $user, $title, $data, $type = 'n', $topic_smiley = '', $summary = '',
			$topic_title = '', $in_reply_to = '', $anonymous_name='', $tags='', $email='') {
		// timestamp

		$hash2 = md5($title . $data);

		$queue = $this->table('tiki_forums_queue');

		if ($qId == 0 && $queue->fetchCount(array('hash' => $hash2))) {
			return false;
		}
		if (!$user && $anonymous_name) {
			$user = $anonymous_name;
		}

		$data = array(
			'object' => $object,
			'parentId' => $parentId,
			'user' => $user,
			'title' => $title,
			'data' => $data,
			'forumId' => $forumId,
			'type' => $type,
			'hash' => $hash2,
			'topic_title' => $topic_title,
			'topic_smiley' => $topic_smiley,
			'summary' => $summary,
			'timestamp' => (int)$this->now,
			'in_reply_to' => $in_reply_to,
			'tags' => $tags,
			'email' => $email
		);

		if ($qId) {
			$queue->update($data, array(
				'qId' => $qId,
			));

			return $qId;
		} else {
			$qId = $queue->insert($data);
		}

		return $qId;
	}

	function get_num_queued($object) {
		return $this->table('tiki_forums_queue')->fetchCount(array('object' => $object));
	}

	function list_forum_queue($object, $offset, $maxRecords, $sort_mode, $find) {
		$queue = $this->table('tiki_forums_queue');

		$conditions = array(
			'object' => $object,
		);

		if ($find) {
			$conditions['search'] = $queue->findIn($find, array('title', 'data'));
		}

		$ret = $queue->fetchAll($queue->all(), $conditions, $maxRecords, $offset, $queue->sortMode($sort_mode));
		$cant = $queue->fetchCount($conditions);

		foreach ( $ret as &$res ) {
			$res['parsed'] = $this->parse_comment_data($res['data']);

			$res['attachments'] = $this->get_thread_attachments(0, $res['qId']);
		}

		return array(
			'data' => $ret,
			'cant' => $cant,
		);
	}

	function queue_get($qId) {
		$res = $this->table('tiki_forums_queue')->fetchFullRow(array('qId' => $qId));
		$res['attchments'] = $this->get_thread_attachments(0, $qId);

		return $res;
	}

	function remove_queued($qId) {
		$this->table('tiki_forums_queue')->delete(array('qId' => $qId));
		$this->table('tiki_forum_attachments')->delete(array('qId' => $qId));
	}

	//Approve queued message -> post as new comment
	function approve_queued($qId) {
		global $userlib, $tikilib, $prefs;
		$info = $this->queue_get($qId);

		$message_id = '';
		if ($userlib->user_exists($info['user'])) {
			$u = $w = $info['user'];
			$a = '';
		} else {
			$u = '';
			$a = $info['user'];
			$w = $a. ' '. tra('(not registered)', $prefs['site_language']);
		}
		$threadId = $this->post_new_comment(
			'forum:' . $info['forumId'], $info['parentId'],
			$u, $info['title'], $info['data'], 
			$message_id, $info['in_reply_to'],
			$info['type'],
			$info['summary'], $info['topic_smiley'], '', $a
		);
		if (!$threadId) {
			return null;
		}
		// Deal with mail notifications
		include_once('lib/notifications/notificationemaillib.php');
		$forum_info = $this->get_forum($info['forumId']);
		sendForumEmailNotification(empty($info['in_reply_to'])?'forum_post_topic':'forum_post_thread', $info['forumId'], $forum_info, $info['title'], $info['data'], $info['user'], $info['title'], $message_id, $info['in_reply_to'], isset($info['parentId'])?$info['parentId']: $threadId, isset($info['parentId'])?$info['parentId']: 0, $threadId);
		
		if ($info['email']) {
			$tikilib->add_user_watch($w, 'forum_post_thread', $threadId, 'forum topic', '' . ':' . $info['title'], 'tiki-view_forum_thread.php?forumId=' . $info['forumId'] . '&amp;comments_parentId=' . $threadId, $info['email']);
		}
		if ($info['tags']) {
			$cat_type = 'forum post';
			$cat_objid = $threadId;
			$cat_desc = substr($info['data'], 0, 200);
			$cat_name = $info['title'];
			$cat_href='tiki-view_forum_thread.php?comments_parentId=' . $threadId . '&forumId=' . $info['forumId'];
			$_REQUEST['freetag_string'] = $info['tags'];
			include ('freetag_apply.php');
		}

		$this->table('tiki_forum_attachments')->update(array(
			'threadId' => $threadId,
			'qId' => 0,
		), array(
			'qId' => $qId,
		));
		$this->remove_queued($qId);

		return $threadId;
	}

	function get_forum_topics($forumId, $offset = 0, $max = -1, $sort_mode = 'commentDate_asc', $include_archived = false, $who = '', $type = '', $reply_state = '', $forum_info='') {
		$info = $this->build_forum_query( $forumId, $offset, $max, $sort_mode, $include_archived, $who, $type, $reply_state, $forum_info );

		$query = "select a.`threadId`,a.`object`,a.`objectType`,a.`parentId`,
			a.`userName`,a.`commentDate`,a.`hits`,a.`type`,a.`points`,
			a.`votes`,a.`average`,a.`title`,a.`data`,a.`hash`,a.`user_ip`,
			a.`summary`,a.`smiley`,a.`message_id`,a.`in_reply_to`,a.`comment_rating`,a.`locked`, ";
		$query .= $info['query'];

		$ret = $this->fetchAll($query, $info['bindvars'], $max, $offset);

		foreach ( $ret as &$res ) {
			$tid = $res['threadId'];
			if ($res["lastPost"]!=$res["commentDate"]) {
				// last post data is for tiki-view_forum.php. 
				// you can see the title and author of last post
				$query = "select * from `tiki_comments`
					where `parentId` = ? and `commentDate` = ?
					order by `threadId` desc";
				$r2 = $this->query($query, array($tid, $res['lastPost']));
				$res['lastPostData'] = $r2->fetchRow();
			}

			// Has the user read it?
			$res['is_marked'] = $this->is_marked($tid);
		}

		return $ret;
	}

	function count_forum_topics($forumId, $offset = 0, $max = -1, $sort_mode = 'commentDate_asc', $include_archived = false, $who = '', $type = '', $reply_state = '') {
		$info = $this->build_forum_query( $forumId, $offset, $max, $sort_mode, $include_archived, $who, $type, $reply_state );

		$query = "SELECT COUNT(*) FROM (SELECT `a`.`threadId`, {$info['query']}) a";

		return $this->getOne( $query, $info['bindvars'] );
	}

	private function build_forum_query($forumId, $offset, $max, $sort_mode, $include_archived, $who, $type, $reply_state, $forum_info='') {
		if ($sort_mode == 'points_asc') {
			$sort_mode = 'average_asc';
		}
		if ($this->time_control) {
			$limit = time() - $this->time_control;
			$time_cond = " and a.`commentDate` > ? ";
			$bind_time = array((int) $limit);
		} else {
			$time_cond = '';
			$bind_time = array();
		}
		if (!empty($who)) {
			//get a list of threads the user has posted in
			//this needs to be a separate query otherwise it'll run once for every row in the db!
			$user_thread_ids_query = "SELECT DISTINCT IF(parentId=0, threadId, parentId) threadId FROM tiki_comments WHERE object = ? AND userName = ? ORDER BY threadId DESC";
			$user_thread_ids_params = array($forumId, $who);
			$user_thread_ids_result = $this->query($user_thread_ids_query, $user_thread_ids_params, 1000);

			if ($user_thread_ids_result->numRows()) {
				$time_cond .= ' and a.`threadId` IN (';
				$user_thread_ids = array();
				while ($res = $user_thread_ids_result->fetchRow()) {
					$user_thread_ids[] = $res['threadId'];
				}
				$time_cond .= implode(",", $user_thread_ids);
				$time_cond .= ") ";
			}
		}
		if (!empty($type)) {
			$time_cond .= ' and a.`type` = ? ';
			$bind_time[] = $type;
		}

		global $categlib; require_once 'lib/categories/categlib.php';
		if( $jail = $categlib->get_jail() ) {
			$categlib->getSqlJoin( $jail, 'forum', '`a`.`object`', $join, $where, $bind_vars );
		} else {
			$join = '';
			$where = '';
		}
		$select = '';
		if (!empty($forum_info['att_list_nb']) && $forum_info['att_list_nb'] == 'y') {
			$select = ', count(distinct(tfa.`attId`)) as nb_attachments ';
			$join .= 'left join `tiki_comments` tca on (tca.`parentId`=a.`threadId` or (tca.`parentId`=0 and tca.`threadId`=a.`threadId`))left join `tiki_forum_attachments` tfa on (tfa.`threadId`=tca.`threadId`)';
		}

		$ret = array();
		$query = 
			$this->ifNull("a.`archived`", "'n'")." as `archived`,".
			$this->ifNull("max(b.`commentDate`)","a.`commentDate`")." as `lastPost`,".
			$this->ifNull("a.`type`='s'", 'false')." as `sticky`, count(distinct b.`threadId`) as `replies` $select
				from `tiki_comments` a left join `tiki_comments` b 
				on b.`parentId`=a.`threadId` $join
				where 1 = 1 $where" . ( $forumId ? 'AND a.`object`=?' : '' )
				.(( $include_archived ) ? '' : ' and (a.`archived` is null or a.`archived`=?)')
				." and a.`objectType` = 'forum'
				and a.`parentId` = ? $time_cond group by a.`threadId`";

		if ($reply_state == 'none') {
			$query .= ' HAVING `replies` = 0 ';
		}
		// Prevent ambiguous field database errors
		if (strpos($sort_mode, 'commentDate') !== false) {
			$sort_mode = str_replace('commentDate', 'a.commentDate', $sort_mode);
		}
		if (strpos($sort_mode, 'hits') !== false) {
			$sort_mode = str_replace('hits', 'a.hits', $sort_mode);
		}
		if (strpos($sort_mode, 'title') !== false) {
			$sort_mode = str_replace('title', 'a.title', $sort_mode);
		}
		if (strpos($sort_mode, 'type') !== false) {
			$sort_mode = str_replace('type', 'a.type', $sort_mode);
		}
		if (strpos($sort_mode, 'userName') !== false) {
			$sort_mode = str_replace('userName', 'a.userName', $sort_mode);
		}
		$query .="order by `sticky` desc, ".$this->convertSortMode($sort_mode).", `threadId`";

		if( $forumId ) {
			$bind_vars[] = (string) $forumId;
		}
		if ( ! $include_archived ) $bind_vars[] = 'n';
		$bind_vars[] = 0;

		return array(
			'query' => $query,
			'bindvars' => array_merge($bind_vars, $bind_time),
		);
	}

	function get_last_forum_posts($forumId, $maxRecords = -1){
		$comments = $this->table('tiki_comments');
		
		return $comments->fetchAll($comments->all(), array(
			'objectType' => 'forum',
			'object' => $forumId,
		), $maxRecords, 0, array('commentDate' => 'DESC'));
	}

	function replace_forum($forumId=0, $name='', $description='', $controlFlood='n',
			$floodInterval=120, $moderator='admin', $mail='', $useMail='n',
			$usePruneUnreplied='n', $pruneUnrepliedAge=2592000, $usePruneOld='n',
			$pruneMaxAge=259200, $topicsPerPage=10, $topicOrdering='lastPost_desc',
			$threadOrdering='', $section='', $topics_list_reads='y',
			$topics_list_replies='y', $topics_list_pts='n',
			$topics_list_lastpost='y', $topics_list_author='y', $vote_threads='n',
			$show_description='n', $inbound_pop_server='', $inbound_pop_port=110,
			$inbound_pop_user='', $inbound_pop_password='', $outbound_address='',
			$outbound_mails_for_inbound_mails='n', $outbound_mails_reply_link='n',
			$outbound_from='', $topic_smileys='n', $topic_summary='n', $ui_avatar='y',
			$ui_flag='y', $ui_posts='n', $ui_level='n', $ui_email='n', $ui_online='n',
			$approval_type='all_posted', $moderator_group='', $forum_password='',
			$forum_use_password='n', $att='att_no', $att_store='db', $att_store_dir='',
			$att_max_size=1000000, $forum_last_n=0, $commentsPerPage='', $threadStyle='',
			$is_flat='n', $att_list_nb='n', $topics_list_lastpost_title='y', $topics_list_lastpost_avatar='n', $topics_list_author_avatar='n') {

		$data = array(
			'name' => $name,  	
			'description' => $description,
			'controlFlood' => $controlFlood,
			'floodInterval' => (int) $floodInterval,
			'moderator' => $moderator,
			'mail' => $mail,
			'useMail' => $useMail,
			'section' => $section,
			'usePruneUnreplied' => $usePruneUnreplied,
			'pruneUnrepliedAge' => (int) $pruneUnrepliedAge,
			'usePruneOld' => $usePruneOld,
			'vote_threads' => $vote_threads,
			'topics_list_reads' => $topics_list_reads,
			'topics_list_replies' => $topics_list_replies,
			'show_description' => $show_description,
			'inbound_pop_server' => $inbound_pop_server,
			'inbound_pop_port' => $inbound_pop_port,
			'inbound_pop_user' => $inbound_pop_user,
			'inbound_pop_password' => $inbound_pop_password,
			'outbound_address' => $outbound_address,
			'outbound_mails_for_inbound_mails' => $outbound_mails_for_inbound_mails,
			'outbound_mails_reply_link' => $outbound_mails_reply_link,
			'outbound_from' => $outbound_from,
			'topic_smileys' => $topic_smileys,
			'topic_summary' => $topic_summary,
			'ui_avatar' => $ui_avatar,
			'ui_flag' => $ui_flag,
			'ui_posts' => $ui_posts,
			'ui_level' => $ui_level,
			'ui_email' => $ui_email,
			'ui_online' => $ui_online,
			'approval_type' => $approval_type,
			'moderator_group' => $moderator_group,
			'forum_password' => $forum_password,
			'forum_use_password' => $forum_use_password,
			'att' => $att,
			'att_store' => $att_store,
			'att_store_dir' => $att_store_dir,
			'att_max_size' => (int) $att_max_size,
			'topics_list_pts' => $topics_list_pts,
			'topics_list_lastpost' => $topics_list_lastpost,
			'topics_list_lastpost_title' => $topics_list_lastpost_title,
			'topics_list_lastpost_avatar' => $topics_list_lastpost_avatar,
			'topics_list_author' => $topics_list_author,
			'topics_list_author_avatar' => $topics_list_author_avatar,
			'topicsPerPage' => (int) $topicsPerPage,
			'topicOrdering' => $topicOrdering,
			'threadOrdering' => $threadOrdering,
			'pruneMaxAge' => (int) $pruneMaxAge,
			'forum_last_n' => (int) $forum_last_n,
			'commentsPerPage' => $commentsPerPage,
			'threadStyle' => $threadStyle,
			'is_flat' => $is_flat,
			'att_list_nb' => $att_list_nb,
		);

		$forums = $this->table('tiki_forums');
		if ($forumId) {
			$forums->update($data, array(
				'forumId' => (int) $forumId,
			));
		} else {
			$data['created'] = $this->now;
			$forumId = $forums->insert($data);
		}

		global $prefs;
		require_once('lib/search/refresh-functions.php');
		refresh_index('forums', $forumId);

		return $forumId;
	}

	function get_forum($forumId) {
		$res = $this->table('tiki_forums')->fetchFullRow(array('forumId' => $forumId));
		if ( !empty($res) ) {
			$res['is_locked'] = $this->is_object_locked('forum:'.$forumId) ? 'y' : 'n';
		}

		return $res;
	}

	function remove_forum($forumId) {
		$this->table('tiki_forums')->delete(array('forumId' => $forumId));
		$this->remove_object("forum", $forumId);
		$this->table('tiki_forum_attachments')->delete(array('forumId' => $forumId));
		return true;
	}

	function list_forums($offset=0, $maxRecords=-1, $sort_mode='name_asc', $find = '') {
		global $user;

		$bindvars=array();

		$categlib = TikiLib::lib('categ');
		if( $jail = $categlib->get_jail() ) {
			$categlib->getSqlJoin($jail, 'forum', '`tiki_forums`.`forumId`', $join, $where, $bindvars);
		} else {
			$join = '';
			$where = '';
		}

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " AND `tiki_forums`.`name` like ? or `tiki_forums`.`description` like ? ";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_forums` $join WHERE 1=1 $where $mid order by `section` asc,".$this->convertSortMode('`tiki_forums`.' . $sort_mode);
		$result = $this->fetchAll($query, $bindvars);
		$result = Perms::filter( array( 'type' => 'forum' ), 'object', $result, array( 'object' => 'forumId' ), 'forum_read' );
		$count = 0;
		$cant = 0;
		$off = 0;
		$comments = $this->table('tiki_comments');

		foreach( $result as &$res ) {
			$cant++; // Count the whole number of forums the user has access to

			if ( ( $maxRecords > -1 && $count >= $maxRecords ) || $off++ < $offset ) continue;

			$forum_age = ceil(($this->now - $res["created"]) / (24 * 3600));

			// Get number of topics on this forum
			$res['threads'] = $this->count_comments_threads('forum:'.$res['forumId']);

			// Get number of posts on this forum
			$res['comments'] = $this->count_comments('forum:'.$res['forumId']);

			// Get number of users that posted at least one comment on this forum
			$res['users'] = $comments->fetchOne($comments->expr('count(distinct `userName`)'), array(
				'object' => $res['forumId'],
				'objectType' => 'forum',
			));

			// Get lock status
			$res['is_locked'] = $this->is_object_locked('forum:'.$res['forumId']) ? 'y' : 'n';

			// Get data of the last post of this forum
			if ( $res['comments'] > 0 ) {
				$res['lastPostData'] = $comments->fetchFullRow(array(
					'object' => $res['forumId'],
					'objectType' => 'forum',
				), array('commentDate' => 'DESC'));
				$res['lastPost'] = $res['lastPostData']['commentDate'];
			} else {
				unset($res['lastPost']);
			}

			// Generate stats based on this forum's age
			if ( $forum_age > 0 ) {
				$res['age'] = $forum_age;
				$res['posts_per_day'] = $res['comments'] / $forum_age;
				$res['users_per_day'] = $res['users'] / $forum_age;
			} else {
				$res['age'] = 0;
				$res['posts_per_day'] = 0;
				$res['users_per_day'] = 0;
			}

			++$count;
		}

		$retval = array();
		$retval["data"] = $result;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_forums_by_section($section, $offset, $maxRecords, $sort_mode, $find) {
		$conditions = array(
			'section' => $section,
		);

		$forums = $this->table('tiki_forums');
		$comments = $this->table('tiki_comments');

		if ($find) {
			$conditions['search'] = $forums->findIn($find, array('name', 'description'));
		}

		$ret = $forums->fetchAll($forums->all(), $conditions, $maxRecords, $offset, $forums->sortMode($sort_mode));
		$cant = $forums->fetchCount($conditions);

		foreach ( $ret as &$res ) {
			$forum_age = ceil(($this->now - $res["created"]) / (24 * 3600));

			$res["age"] = $forum_age;

			if ($forum_age) {
				$res["posts_per_day"] = $res["comments"] / $forum_age;
			} else {
				$res["posts_per_day"] = 0;
			}

			// Now select users
			$res['users'] = $comments->fetchOne($comments->expr('count(distinct `userName`)'), array(
				'object' => $res['forumId'],
				'objectType' => 'forum',
			));

			if ($forum_age) {
				$res["users_per_day"] = $res["users"] / $forum_age;
			} else {
				$res["users_per_day"] = 0;
			}

			$res['lastPostData'] = $comments->fetchFullRow(array(
				'object' => $res['forumId'],
				'objectType' => 'forum',
			), array('commentDate' => 'DESC'));
		}

		return array(
			'data' => $ret,
			'cant' => $cant,
		);
	}

	function user_can_edit_post( $user, $threadId ) {
		$result = $this->table('tiki_comments')->fetchOne('userName', array('threadId' => $threadId));

		return $result == $user;
	}

	function user_can_post_to_forum($user, $forumId) {
		// Check flood interval for the forum
		$forum = $this->get_forum($forumId);

		if ($forum["controlFlood"] != 'y')
			return true;

		if ($user) {
			$comments = $this->table('tiki_comments');
			$maxDate = $comments->fetchOne($comments->max('commentDate'), array(
				'object' => $forumId,
				'objectType' => 'forum',
				'userName' => $user,
			));

			if (!$maxDate) {
				return true;
			}

			return $maxDate + $forum["floodInterval"] <= $this->now;
		} else {
			// Anonymous users
			if (!isset($_SESSION["lastPost"])) {
				return true;
			} else {

				if ($_SESSION["lastPost"] + $forum["floodInterval"] > $this->now) {
					return false;
				} else {
					return true;
				}
			}
		}
	}

	function register_forum_post($forumId, $parentId) {
		$forums = $this->table('tiki_forums');

		$forums->update(array(
			'comments' => $forums->increment(1),
		), array(
			'forumId' => (int) $forumId,
		));

		$lastPost = $this->getOne("select max(`commentDate`) from
				`tiki_comments`,`tiki_forums` where `object` = `forumId` and `objectType` = 'forum' and
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
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$forums = $this->table('tiki_forums');

			$forums->update(array(
				'hits' => $forums->increment(1),
			), array(
				'forumId' => (int) $forumId,
			));

			$this->forum_prune($forumId);
		}

		return true;
	}

	function comment_add_hit($threadId) {
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$comments = $this->table('tiki_comments');

			$comments->update(array(
				'hits' => $comments->increment(1),
			), array(
				'threadId' => (int) $threadId,
			));
		}

		return true;
	}

	function get_all_children($threadId, $generations = 99) {
		$comments = $this->table('tiki_comments');

		$children = array();
		$threadId = (array) $threadId;

		for ($current_generation = 0; $current_generation < $generations; $current_generation++) {
			$children_this_generation = $comments->fetchColumn('threadId', array(
				'parentId' => $comments->in($threadId),
			));

			$children[] = $children_this_generation;

			if (!$children_this_generation) {
				break;
			}

			$threadId = $children_this_generation;
		}

		return array_unique($children);
	}

	function forum_prune($forumId) {
		$comments = $this->table('tiki_comments');

		$forum = $this->get_forum($forumId);

		if ($forum["usePruneUnreplied"] == 'y') {
			$age = $forum["pruneUnrepliedAge"];

			// Get all unreplied threads
			// Get all the top_level threads
			$oldage = $this->now - $age;

			$result = $comments->fetchColumn('threadId', array(
				'parentId' => 0,
				'commentDate' => $comments->lesserThan((int) $oldage),
				'object' => $forumId,
				'objectType' => 'forum',
			));

			$result = array_filter($result);

			foreach ($result as $id) {
				// Check if this old top level thread has replies
				$cant = $comments->fetchCount(array('parentId' => (int) $id));

				// Remove this old thread without replies
				if ($cant == 0) {
					$this->remove_comment($id);
				}
			}
		}

		if ($forum["usePruneOld"] == 'y') { // this is very dangerous as you can delete some posts in the middle or root of a tree strucuture
			$maxAge = $forum["pruneMaxAge"];

			$old = $this->now - $maxAge;

			// this aims to make it safer, by pruning only those with no children that are younger than age threshold
			$result = $comments->fetchColumn('threadId', array(
				'object' => $forumId,
				'objectType' => 'forum',
				'commentDate' => $comments->lesserThan($old),
			));
			foreach ($results as $threadId) {
				$children = $this->get_all_children($threadId);
				if ($children) {
					$maxDate = $comments->fetchOne($comments->max('commentDate'), array(
						'threadId' => $comments->in($children),
					));
					if ($maxDate < $old) {
						$this->remove_comment($threadId);
					}
				} else {
					$this->remove_comment($threadId);
				}
			}
		}

		if ($forum["usePruneUnreplied"] == 'y' || $forum["usePruneOld"] == 'y') {	// Recalculate comments and threads
			$count = $comments->fetchCount(array(
				'objectType' => 'forum',
				'object' => (int) $forumId,
			));
			$this->table('tiki_forums')->update(array('comments' => $count), array('forumId' => (int) $forumId));
		}
		return true;
	}

	function get_user_forum_comments($user, $max, $type = '') {
		// get parent title as well, especially useful in flat forum
		$parentinfo = '';
		$mid = '';
		if ($type == 'replies') {
			$parentinfo .= ", b.`title` as parentTitle";
			$mid .= " inner join `tiki_comments` b on b.`threadId` = a.`parentId`";
		}
		$mid .= " where a.`objectType`='forum' AND a.`userName`=?";
		if ($type == 'topics') {
			$mid .= " AND a.`parentId`=0";
		} elseif ($type == 'replies') {
			$mid .= " AND a.`parentId`>0";
		}
		$query = "select a.`threadId`, a.`object`, a.`title`, a.`parentId`, a.`commentDate` $parentinfo, a.`userName` from `tiki_comments` a $mid ORDER BY a.`commentDate` desc";

		$result = $this->fetchAll($query, array($user), $max);
		$ret = Perms::filter( array( 'type' => 'forum' ), 'object', $result, array( 'object' => 'object', 'creator' => 'userName' ), 'forum_read' );

		return $ret;
	}

	// FORUMS END
	function get_comment($id, $message_id=null, $forum_info=null) {
		$comments = $this->table('tiki_comments');
		if ($message_id) {
			$res = $comments->fetchFullRow(array('message_id' => $message_id));
		}
		else {
			$res = $comments->fetchFullRow(array('threadId' => $id));
		}

		if($res) { //if there is a comment with that id
			$this->add_comments_extras($res, $forum_info);
		}

		return $res;
	}

	/**
	* Returns the forum-id for a comment
	*/
	function get_comment_forum_id($commentId) {
		return $this->table('tiki_comments')->fetchOne('object', array(
			'threadId' => $commentId,
		));
	}

	function add_comments_extras(&$res, $forum_info=null) { 
		// this function adds some extras to the referenced array. 
		// This array should already contain the contents of the tiki_comments table row
		// used in $this->get_comment and $this->get_comments
		global $prefs;

		$res["parsed"] = $this->parse_comment_data($res["data"]);

		// these could be cached or probably queried along with the original query of the tiki_comments table
		if ($forum_info == null || $forum_info['ui_posts'] == 'y' || $forum_info['ui_level'] == 'y') {
			$res2 = $this->table('tiki_user_postings')->fetchRow(array('posts', 'level'), array(
				'user' => $res['userName'],
			));
			$res['user_posts'] = $res2['posts'];
			$res['user_level'] = $res2['level'];
		}
		// 'email is public' never has 'y' value, because it is now used to choose the email scrambling method
		// ... so, we need to test if it's not equal to 'n'
		if (($forum_info == null || $forum_info['ui_email'] == 'y') && $this->get_user_preference($res['userName'], 'email is public', 'n') != 'n') {
			$res['user_email'] = TikiLib::lib('user')->get_user_email($res['userName']);
		} else {
			$res['user_email'] = '';
		}

		$res['attachments'] = $this->get_thread_attachments($res['threadId'], 0);
		// is the 'is_reported' really used? can be queried with orig table i think
		$res['is_reported'] = $this->is_reported($res['threadId']);
		$res['user_online'] = 'n';
		if ($res['userName']) {
			$res['user_online'] = $this->is_user_online($res['userName'])? 'y' : 'n';
		}
		if ($prefs['feature_contribution'] == 'y') {
			$contributionlib = TikiLib::lib('contribution');
			$res['contributions'] = $contributionlib->get_assigned_contributions($res['threadId'], 'comment');
		}
	}

	function get_comment_father($id) {
		static $cache;
		if ( isset($cache[$id]) ) {
			return $cache[$id];
		}
		return $cache[$id] = $this->table('tiki_comments')->fetchOne('parentId', array('threadId' => $id));
	}

	/**
	 * Return the number of comments for a specific object.
	 * No permission check is done to verify if the user has permission
	 * to see the object itself or its comments.
	 * 
	 * @param string $objectId example: 'blog post:2'
	 * @param string $approved 'y' or 'n'
	 * @return int the number of comments
	 */
	function count_comments($objectId, $approved = 'y') {
		global $tiki_p_admin_comments, $prefs;

		$comments = $this->table('tiki_comments');

		$conditions = array(
			'objectType' => 'forum',
		);

		$object = explode( ":", $objectId, 2);
		if ( $object[0] == 'topic' ) {
			$conditions['parentId'] = $object[1];
		} else {
			$conditions['objectType'] = $object[0];
			$conditions['object'] = $object[1];
		}

		if ( $tiki_p_admin_comments != 'y' ) {
			$conditions['approved'] = $approved;
		}

		if ($prefs['comments_archive'] == 'y' && $tiki_p_admin_comments != 'y') {
			$conditions['archived'] = 'n';
		}

		return $comments->fetchCount($conditions);
	}
	
	
	function order_comments_by_count($type = 'wiki', $lang = '', $maxRecords = -1) {
		global $prefs;
		$bind = array();
		if ($type == 'article') {
			if ($prefs['feature_articles'] != 'y')
				return false;
			$query = "SELECT count(*),`tiki_articles`.`articleId`,`tiki_articles`.`title` FROM `tiki_comments` INNER JOIN `tiki_articles` ON `tiki_comments`.`object`=`tiki_articles`.`articleId` WHERE `tiki_comments`.`objectType`='article' and `tiki_comments`.`approved`='y' and `tiki_articles`.`ispublished`='y'";
		
			if($lang != ''){
				$query = $query. " and `tiki_articles`.`lang`=?";
				$bind[] = $lang;
			}
			
			$query = $query. " GROUP BY `tiki_comments`.`object` ORDER BY count(*) DESC";
		}
		elseif ($type == 'blog') {
			if ($prefs['feature_blogs'] != 'y')
				return false;
			$query = "SELECT count(*),`tiki_blog_posts`.`postId`,`tiki_blog_posts`.`title` FROM `tiki_comments` INNER JOIN `tiki_blog_posts` ON `tiki_comments`.`object`=`tiki_blog_posts`.`postId` WHERE `tiki_comments`.`objectType`='blog post' and `tiki_comments`.`approved`='y' GROUP BY `tiki_comments`.`object` ORDER BY count(*) DESC";
		}
		else {
			//Default to Wiki
			if ($prefs['feature_wiki'] != 'y')
				return false;
			$query = "SELECT count(*),`tiki_pages`.`pageName` FROM `tiki_comments` INNER JOIN `tiki_pages` ON `tiki_comments`.`object`=`tiki_pages`.`pageName` WHERE `tiki_comments`.`objectType`='wiki page' and `tiki_comments`.`approved`='y'";
	
			if($lang != ''){
				$query = $query. " and `tiki_pages`.`lang`=?";
				$bind[] = $lang;
			}
		
			$query = $query. " GROUP BY `tiki_comments`.`object` ORDER BY count(*) DESC";
		}
		
		$ret = $this->fetchAll($query, $bind, $maxRecords);
		return array('data' => $ret);
	}

	function count_comments_threads($objectId) {
		$object = explode( ":", $objectId, 2);
		return $this->table('tiki_comments')->fetchCount(array(
			'objectType' => $object[0],
			'object' => $object[1],
			'parentId' => 0,
		));
	}
	
	function get_comment_replies($id, $sort_mode, $offset, $orig_offset, $maxRecords, $orig_maxRecords, $threshold = 0, $find = '', $message_id = "", $forum = 0, $approved = 'y' ) {
		global $tiki_p_admin_comments, $prefs;
		$retval = array();

		if( $maxRecords <= 0 && $orig_maxRecords != 0)
		{
			$retval['numReplies'] = 0;
			$retval['totalReplies'] = 0;
			return $retval;
		}

		if( $forum )
		{
			$real_id = $message_id;
		} else {
			$real_id = (int) $id;
		}

		$query = "select `threadId` from `tiki_comments`";

		$initial_sort_mode = $sort_mode;
		if ( $prefs['rating_advanced'] == 'y' ) {
			global $ratinglib; require_once 'lib/rating/ratinglib.php';
			$query .= $ratinglib->convert_rating_sort($sort_mode, 'comment', '`threadId`');
		}

		if( $forum )
		{
			$query = $query . " where `in_reply_to`=? and `average`>=? ";
		} else {
			$query = $query . " where `parentId`=? and `average`>=? ";
		}
		$bind = array($real_id, (int)$threshold);

		if ( $tiki_p_admin_comments != 'y' ) {
			$query .= 'and `approved`=? ';
			$bind[] = $approved;
		}
		if ($find)
		{
			$findesc = '%' . $find . '%';

			$query = $query . " and (`title` like ? or `data` like ?) ";
			$bind[] = $findesc;
			$bind[] = $findesc;

		}

		$query = $query . " order by " . $this->convertSortMode($sort_mode);

		if($sort_mode != 'commentDate_desc') {
			$query.=",`commentDate` desc";
		}

		$result = $this->query($query, $bind);


		$ret = array();

		global $userlib;

		while ($res = $result->fetchRow()) {
			$res = $this->get_comment( $res['threadId'] );

			/* Trim to maxRecords, including replies! */
			if( $offset >= 0 && $orig_offset != 0 )
			{
				$offset = $offset - 1;
			}
			$maxRecords = $maxRecords - 1;

			if( $offset >= 0 && $orig_offset != 0)
			{
				$res['doNotShow'] = 1;
			}

			if( $maxRecords <= 0 && $orig_maxRecords != 0)
			{
				$ret[] = $res;
				break;
			}

			if( $forum )
			{
				$res['replies_info'] =
					$this->get_comment_replies($res['parentId'],
							$initial_sort_mode, $offset, $orig_offset, $maxRecords, $orig_maxRecords, $threshold, $find,
							$res['message_id'], $forum);
			} else {
				$res['replies_info'] =
					$this->get_comment_replies($res['threadId'],
							$initial_sort_mode, $offset, $orig_offset, $maxRecords, $orig_maxRecords, $threshold, $find);
			}

			if( $offset >= 0 && $orig_offset != 0 )
			{
				$offset = $offset - $res['replies_info']['totalReplies'];
			}
			$maxRecords = $maxRecords - $res['replies_info']['totalReplies'];

			if( $offset >= 0 && $orig_offset != 0)
			{
				$res['doNotShow'] = 1;
			}

			if( $maxRecords <= 0 && $orig_maxRecords != 0)
			{
				$ret[] = $res;
				break;
			}

			$ret[] = $res;
		}

		$retval['replies'] = $ret;

		$retval['numReplies'] = count( $ret );
		$retval['totalReplies'] = $this->total_replies( $ret, count( $ret ) );

		return $retval;
	}

	function total_replies( $reply_array, $seed = 0 ) {
		$retval = $seed;

		foreach ( $reply_array as $key=>$res )
		{
			if( is_array( $res ) && array_key_exists( 'replies_info', $res ) )
			{
				if( array_key_exists( 'numReplies', $res['replies_info'] ) )
				{
					$retval = $retval + $res['replies_info']['numReplies'];
				}
				$retval = $retval + $this->total_replies( $res['replies_info']['replies'] );
			}
		}

		return $retval;
	}

	function flatten_comment_replies(&$replies, &$rep_flat, $level = 0) {
		$reps = $replies['numReplies'];
		for ($i = 0; $i < $reps; $i++) {
			$replies['replies'][$i]['level'] = $level;
			$rep_flat[] = &$replies['replies'][$i];
			if (isset($replies['replies'][$i]['replies_info'])) {
				$this->flatten_comment_replies(
					$replies['replies'][$i]['replies_info'],
					$rep_flat, $level + 1);
			}
		}
	}

	function pick_cookie() {
		$cookies = $this->table('tiki_cookies');
		$cant = $cookies->fetchCount('tiki_cookies', array());

		if (!$cant)
			return '';

		$bid = rand(0, $cant - 1);
		$cookie = $cookies->fetchAll(array('cookie'), array(), 1, $bid);
		$cookie = reset($cookie);
		$cookie = reset($cookie);
		$cookie = str_replace("\n", "", $cookie);
		return 'Cookie: ' . $cookie;
	}

	function parse_comment_data($data) {
		global $prefs, $tikilib, $section;

		if (($prefs['feature_forum_parse'] == 'y' && $section == 'forums') || $prefs['section_comments_parse'] == 'y') {
			return $this->parse_data($data);
		}

		// Cookies
		if (preg_match_all("/\{cookie\}/", $data, $rsss)) {
			$temp_max = count($rsss[0]);
			for ($i = 0; $i < $temp_max; $i++) {
				$cookie = $this->pick_cookie();

				$data = str_replace($rsss[0][$i], $cookie, $data);
			}
		}

		// Fix up special characters, so it can link to pages with ' in them. -rlpowell
		$data = htmlspecialchars( $data, ENT_QUOTES );
		$data = preg_replace("/\[([^\|\]]+)\|([^\]]+)\]/", '<a class="commentslink" href="$1">$2</a>', $data);
		// Segundo intento reemplazar los [link] comunes
		$data = preg_replace("/\[([^\]\|]+)\]/", '<a class="commentslink" href="$1">$1</a>', $data);

		// smileys
		$data = $tikilib->parse_smileys($data);

		$data = preg_replace("/---/", "<hr/>", $data);
		// replace --- with <hr/>
		return nl2br($data);
	}

	/*****************/
	function set_time_control($time) {
		$this->time_control = $time;
	}

	/**
	 * Get comments for a particular object
	 * 
	 * @param string $objectId objectType:objectId (example: 'wiki page:HomePage' or 'blog post:1') 
	 * @param int $parentId only return child comments of $parentId
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string $sort_mode
	 * @param string $find search comment title and data
	 * @param int $threshold
	 * @param string $style
	 * @param int $reply_threadId
	 * @param string $approved if user doesn't have tiki_p_admin_comments this param display or not only approved comments (default to 'y')
	 * @return array
	 */
	function get_comments($objectId, $parentId, $offset = 0, $maxRecords = 0, $sort_mode = 'commentDate_asc', $find = '', $threshold = 0, $style = 'commentStyle_threaded', $reply_threadId=0, $approved='y') {
		global $userlib, $tiki_p_admin_comments, $prefs;

		// $start_time = microtime(true);
		// Turn maxRecords into maxRecords + offset, so we can increment it without worrying too much.
		$maxRecords = $offset + $maxRecords;

		$orig_maxRecords = $maxRecords;
		$orig_offset = $offset;

		if ($sort_mode == 'points_asc') {
			$sort_mode = 'average_asc';
		}

		if ($this->time_control) {
			$limit = $this->now - $this->time_control;

			$time_cond = " and `commentDate` > ? ";
			$bind_time = array($limit);
		} else {
			$time_cond = '';
			$bind_time = array();
		}

		$old_sort_mode = '';

		if (in_array($sort_mode, array( 'replies_desc', 'replies_asc' ) ) ) {
			$old_offset = $offset;

			$old_maxRecords = $maxRecords;
			$old_sort_mode = $sort_mode;
			$sort_mode = 'title_desc';
			$offset = 0;
			$maxRecords = -1;
		}

		// Break out the type and object parameters.
		$object = explode( ":", $objectId, 2);
		$bindvars = array_merge(array($object[0], $object[1], (float) $threshold), $bind_time);

		if ( $tiki_p_admin_comments != 'y' ) {
			$queue_cond = 'and tc1.`approved`=?';
			$bindvars[] = $approved;
		} else {
			$queue_cond = '';
		}

		if ($prefs['comments_archive'] == 'y' && $tiki_p_admin_comments != 'y') {
			$queue_cond .= ' AND tc1.`archived`=?';
			$bindvars[] = 'n';
		}

		$query = "select count(*) from `tiki_comments` as tc1 where
			`objectType`=? and `object`=? and `average` < ? $time_cond $queue_cond";
		$below = $this->getOne($query, $bindvars);

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where tc1.`objectType` = ? and tc1.`object`=? and
			tc1.`parentId`=? and tc1.`average`>=? and (tc1.`title`
				like ? or tc1.`data` like ?) ";
			$bind_mid=array($object[0], $object[1], (int) $parentId, (int) $threshold, $findesc, $findesc);
		} else {
			$mid = " where tc1.`objectType` = ? and tc1.`object`=? and tc1.`parentId`=? and tc1.`average`>=? ";
			$bind_mid=array($object[0], $object[1], (int) $parentId, (int) $threshold);
		}
		if ( $tiki_p_admin_comments != 'y' ) {
			$mid .= ' '.$queue_cond;
			$bind_mid[] = $approved;

			if ($prefs['comments_archive'] == 'y') {
				$bind_mid[] = 'n';
			}
		}

		$initial_sort_mode = $sort_mode;
		if ( $prefs['rating_advanced'] == 'y' ) {
			global $ratinglib; require_once 'lib/rating/ratinglib.php';
			$join = $ratinglib->convert_rating_sort($sort_mode, 'comment', '`tc1`.`threadId`');
		} else {
			$join = '';
		}


		if( $object[0] == "forum" && $style != 'commentStyle_plain' )
		{
			$query = "select `message_id` from `tiki_comments` where `threadId` = ?";
			$parent_message_id = $this->getOne($query, array( $parentId ) );

			$adminFields = '';
			if ($tiki_p_admin_comments == 'y') {
				$adminFields = ', tc1.`user_ip`';
			}
			$query = "select tc1.`threadId`, tc1.`object`, tc1.`objectType`, tc1.`parentId`, tc1.`userName`, tc1.`commentDate`, tc1.`hits`, tc1.`type`, tc1.`points`, tc1.`votes`, tc1.`average`, tc1.`title`, tc1.`data`, tc1.`hash`, tc1.`summary`, tc1.`smiley`, tc1.`message_id`, tc1.`in_reply_to`, tc1.`comment_rating`, tc1.`approved`, tc1.`locked`$adminFields  from `tiki_comments` as tc1
				left outer join `tiki_comments` as tc2 on tc1.`in_reply_to` = tc2.`message_id`
				and tc1.`parentId` = ?
				and tc2.`parentId` = ?
				$join
				$mid 
				and (tc1.`in_reply_to` = ?
						or (tc2.`in_reply_to` = '' or tc2.`in_reply_to` is null or tc2.`message_id` is null or tc2.`parentId` = 0))
				$time_cond order by ".$this->convertSortMode($sort_mode).", tc1.`threadId`";
			$bind_mid_cant = $bind_mid;
			$bind_mid = array_merge(array($parentId, $parentId), $bind_mid, array($parent_message_id));

			$query_cant = "select count(*) from `tiki_comments` as tc1 $mid $time_cond";
		} else {
			$query_cant = "select count(*) from `tiki_comments` as tc1 $mid $time_cond";
			$query = "select * from `tiki_comments` as tc1 $join $mid $time_cond order by ".$this->convertSortMode($sort_mode).",`threadId`";
			$bind_mid_cant = $bind_mid;
		}

		$ret = array();
		$logins = array();
		$threadIds = array();

		if ($reply_threadId > 0 && $style == 'commentStyle_threaded') {
			$ret[] = $this->get_comments_fathers($reply_threadId, $ret);
			$cant = 1;
		} else {
			$ret = $this->fetchAll($query, array_merge($bind_mid, $bind_time));
			$cant = $this->getOne($query_cant, array_merge($bind_mid_cant, $bind_time));
			foreach ( $ret as $i=>$row ) {
				$this->add_comments_extras($ret[$i]);
			}
		}

		foreach ( $ret as $key=>$res )
		{
			if( $offset > 0  && $orig_offset != 0 )
			{
				$ret[$key]['doNotShow'] = 1;
			}

			if( $maxRecords <= 0  && $orig_maxRecords != 0 )
			{
				array_splice( $ret, $key );
				break;
			}

			// Get the grandfather
			if ($res["parentId"] > 0) {
				$ret[$key]["grandFather"] = $this->get_comment_father($res["parentId"]);
			} else {
				$ret[$key]["grandFather"] = 0;
			}

			/* Trim to maxRecords, including replies! */
			if( $offset >= 0 && $orig_offset != 0 ) {
				$offset = $offset - 1;
			}
			$maxRecords = $maxRecords - 1;

			if( !( $maxRecords <= 0 && $orig_maxRecords != 0 ) ) {
				// Get the replies
				if ($parentId == 0 || $style != 'commentStyle_threaded' || $object[0] == "forum")
				{
					if( $object[0] == "forum" )
					{
						// For plain style, don't handle replies at all.
						if( $style == 'commentStyle_plain' )
						{
							$ret[$key]['replies_info']['numReplies'] = 0;
							$ret[$key]['replies_info']['totalReplies'] = 0;
						} else {
							$ret[$key]['replies_info'] = $this->get_comment_replies($res["parentId"], $initial_sort_mode, $offset, $orig_offset, $maxRecords, $orig_maxRecords, $threshold, $find, $res["message_id"], 1);
						}
					} else {
						$ret[$key]['replies_info'] = $this->get_comment_replies($res["threadId"], $initial_sort_mode, $offset, $orig_offset, $maxRecords, $orig_maxRecords, $threshold, $find );
					}

					/* Trim to maxRecords, including replies! */
					if( $offset >= 0 && $orig_offset != 0 )
					{
						$offset = $offset - $ret[$key]['replies_info']['totalReplies'];
					}
					$maxRecords = $maxRecords - $ret[$key]['replies_info']['totalReplies'];
				}
			}

			if (empty($res["data"])) {
				$ret[$key]["isEmpty"] = 'y';
			} else {
				$ret[$key]["isEmpty"] = 'n';
			}

			// to be able to distinct between a tiki user and a anonymous name
			if (!$userlib->user_exists($ret[$key]['userName'])) {
				$ret[$key]['anonymous_name'] = $ret[$key]['userName'];
			}
		}

		if ($old_sort_mode == 'replies_asc') {
			usort($ret, 'compare_replies');
		}

		if ($old_sort_mode == 'replies_desc') {
			usort($ret, 'r_compare_replies');
		}

		if( in_array( $old_sort_mode, array( 'replies_desc', 'replies_asc' ) ) ) {
			$ret = array_slice($ret, $old_offset, $old_maxRecords);
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["below"] = $below;
		$retval["cant"] = $cant;

		$msgs = count($retval['data']);
		for ($i = 0; $i < $msgs; $i++) {
			$r = &$retval['data'][$i]['replies_info'];
			$retval['data'][$i]['replies_flat'] = array();
			$rf = &$retval['data'][$i]['replies_flat'];
			$this->flatten_comment_replies($r, $rf);
		}

		return $retval;
	}

	/**
	 * Return the number of arquived comments for an object
	 *
	 * @param int|string $objectId
	 * @param string $objectType
	 * @return int the number of archived comments for an object
	 */
	function count_object_archived_comments($objectId, $objectType) {
		return $this->table('tiki_comments')->fetchCount(array(
			'object' => $objectId,
			'objectType' => $objectType,
			'archived' => 'y',
		));
	}

	/**
	 * Return all comments. Administrative functions to get all the comments
	 * of some types + enlarge find. No perms checked as it is only for admin
	 * 
	 * @param string|array $type one type or array of types (if empty function will return comments for all types except forum) 
	 * @param int $offset
	 * @param int $maxRecords
	 * @param string $sort_mode
	 * @param string $find search comment title, data, user name, ip and object
	 * @param string $parent
	 * @param string $approved set it to y or n to return only approved or rejected comments (leave empty to return all comments)
	 * @param bool $toponly
	 * @param array|int $objectId limit comments return to one object id or array of objects ids
	 */
	function get_all_comments($type = '', $offset = 0, $maxRecords = -1, $sort_mode = 'commentDate_asc', $find = '', $parent='', $approved='', $toponly=false, $objectId='') {
		$join = '';
		if ( empty($type) ) {
			// If no type has been specified, get all comments except those used for forums which must not be handled here
			$mid = 'tc.`objectType`!=?';
			$bindvars[] = 'forum';
		} else {
			if (is_array($type)) {
				$mid = 'tc.`objectType` in ('.implode(',', array_fill(0, count($type), '?')).')';
				$bindvars = $type;
			} else {
				$mid = 'tc.`objectType`=?';
				$bindvars[] = $type;
			}
		}

		if ($find) {
			$find = "%$find%";
			$mid .= ' and (tc.`title` like ? or tc.`data` like ? or tc.`userName` like ? or tc.`user_ip` like ? or tc.`object` like ?)';
			$bindvars[] = $find;
			$bindvars[] = $find;
			$bindvars[] = $find;
			$bindvars[] = $find;
			$bindvars[] = $find;
		}

		if ( ! empty($approved) ) {
			$mid .= ' and tc.`approved`=?';
			$bindvars[] = $approved;
		}
		if (!empty($objectId)) {
			if (is_array($objectId)) {
				$mid .= ' and tc.`object` in ('.implode(',', array_fill(0, count($objectId), '?')).')';
				$bindvars = array_merge($bindvars, $objectId);
			} else {
				$mid .= ' and tc.`object`=?';
				$bindvars[] = $objectId;
			}
		}

		if ($parent!='') {
			$join = ' left join `tiki_comments` tc2 on(tc2.`threadId`=tc.`parentId`)';
		}

		if( $toponly ) {
			$mid .= ' and tc.`parentId` = 0 ';
		}
		if ($type == 'forum') {
			$join .= ' left join `tiki_forums` tf on (tf.`forumId`=tc.`object`)';
			$left = ', tf.`name` as parentTitle';
		} else {
			$left = ', tc.`title` as parentTitle';
		}

		global $categlib; require_once 'lib/categories/categlib.php';
		if( $jail = $categlib->get_jail() ) {
			$categlib->getSqlJoin( $jail, '`objectType`', '`object`', $jail_join, $jail_where, $jail_bind, '`objectType`' );
		} else {
			$jail_join = '';
			$jail_where = '';
			$jail_bind = array();
		}

		$query = "select tc.* $left from `tiki_comments` tc $join $jail_join where $mid $jail_where order by ".$this->convertSortMode($sort_mode);
		$ret = $this->fetchAll($query, array_merge( $bindvars, $jail_bind ), $maxRecords, $offset);
		$query = "select count(*) from `tiki_comments` tc $jail_join where $mid $jail_where";
		$cant = $this->getOne($query, array_merge( $bindvars, $jail_bind ));
		foreach ( $ret as &$res ) {
			$res['href'] = $this->getHref($res['objectType'], $res['object'], $res['threadId']);
			$res['parsed'] = $this->parse_comment_data($res['data']);
		}
		return array('cant'=>$cant, 'data'=>$ret);
	}

	/**
	 * Return the relative URL for a particular comment
	 * 
	 * @param string $type Object type (e.g. 'wiki page')
	 * @param int|string $object object id (can be string for wiki pages or int for objects of other types)
	 * @param int $threadId Id of a specific comment or forum thread
	 * @return void|string void if unrecognized type or URL string otherwise
	 */
	function getHref($type, $object, $threadId) {
		switch ($type) {
			case 'wiki page':
				$href = 'tiki-index.php?page=';
				$object = urlencode($object);
				break;
			case 'article':
				$href = 'tiki-read_article.php?articleId=';
				break;
			case 'faq':
				$href = 'tiki-view_faq.php?faqId=';
				break;
			case 'blog':
				$href = 'tiki-view_blog.php?blogId=';
				break;
			case 'blog post':
				$href = 'tiki-view_blog_post.php?postId=';
				break;
			case 'forum':
				$href = 'tiki-view_forum_thread.php?forumId=';
				break;
			case 'file gallery':
				$href = 'tiki-list_file_gallery.php?galleryId=';
				break;
			case 'image gallery':
				$href = 'tiki-browse_gallery.php?galleryId=';
				break;
			case 'poll':
				$href = 'tiki-poll_results.php?pollId=';
				break;
			default:
				break;
		}
		
		if (empty($href)) {
			return;
		}
		
		$href .= $object."&amp;threadId=$threadId&amp;comzone=show#threadId$threadId";
		
		return $href;
	}

	/* @brief: gets the comments of the thread and of all its fathers (ex cept first one for forum)
 	*/
	function get_comments_fathers($threadId, $ret = null, $message_id = null) {
		$com = $this->get_comment($threadId, $message_id);

		if ($com['objectType'] == 'forum' && $com['parentId'] == 0 ) {// don't want the 1 level
			return $ret;
		}
		if ($ret) {
			$com['replies_info']['replies'][0] = $ret;
			$com['replies_info']['numReplies'] = 1;
			$com['replies_info']['totalReplies'] = 1;
		}
		if ($com['objectType'] == 'forum' && $com['in_reply_to']) {
			return $this->get_comments_fathers(null, $com, $com['in_reply_to']);
		}
		else if ($com['parentId'] > 0) {
			return $this->get_comments_fathers($com['parentId'], $com);
		}
		else{
			return $com;
		}
	}

	function lock_comment($threadId) {
		$this->table('tiki_comments')->update(array(
			'locked' => 'y',
		), array(
			'threadId' => $threadId,
		));
	}

	function set_comment_object($threadId, $objectId) {
		// Break out the type and object parameters.
		$object = explode( ":", $objectId, 2);

		$data = array(
			'objectType' => $object[0],
			'object' => $object[1],
		);
		$this->table('tiki_comments')->update($data, array('threadId' => $threadId));
		$this->table('tiki_comments')->update($data, array('parentId' => $threadId));
	}

	function set_parent($threadId, $parentId) {
		$comments = $this->table('tiki_comments');
		$parent_message_id = $comments->fetchOne('message_id', array('threadId' => $parentId));
		$comments->update(array(
			'parentId' => (int) $parentId,
			'in_reply_to' => $parent_message_id,
		), array(
			'threadId' => (int) $threadId,
		));
	}

	function unlock_comment($threadId) {
		$this->table('tiki_comments')->update(array(
			'locked' => 'n',
		), array(
			'threadId' => (int) $threadId,
		));
	}

	// Lock all comments of an object
	function lock_object_thread($objectId, $status = 'y') {
		if ( empty($objectId) ) return false;
		$object = explode( ":", $objectId, 2);
		if ( count($object) < 2 ) return false;

		// Add object if not already exists, because it's currently only done when using categories feature
		// We suppose it's already done when unlocking the object, because it is needed to be locked
		if ( $status == 'y' ) {
			TikiLib::lib('object')->add_object($object[0], $object[1]);
		}

		$this->table('tiki_objects')->update(array(
			'comments_locked' => $status,
		), array(
			'type' => $object[0],
			'itemId' => $object[1],
		));
	}

	// Unlock all comments of an object
	function unlock_object_thread($objectId) {
		return $this->lock_object_thread($objectId, 'n');
	}

	// Get the status of an object (Lock / Unlock)
	function is_object_locked($objectId) {
		if ( empty($objectId) ) return false;
		$object = explode( ":", $objectId, 2);
		if ( count($object) < 2 ) return false;
		return 'y' == $this->table('tiki_objects')->fetchOne('comments_locked', array(
			'type' => $object[0],
			'itemId' => $object[1],
		));
	}

	function update_comment_links($data, $objectType, $threadId) {
		if ($objectType == 'forum' ) {
			$type = 'forum post'; // this must correspond to that used in tiki_objects
		} else {
			$type = $objectType . ' comment'; // comment types are not used in tiki_objects yet but maybe in future
		}
		$pages = $this->get_pages($data);
		$linkhandle = "objectlink:$type:$threadId";
		$this->clear_links($linkhandle);
		foreach ($pages as $a_page) {
			$this->replace_link($linkhandle, $a_page);
		}
	}

	function update_comment($threadId, $title, $comment_rating, $data, $type = 'n', $summary = '', $smiley = '', $objectId='', $contributions='') {
		global $prefs;

		$comments = $this->table('tiki_comments');
		$hash = md5($title . $data);
		$existingThread = $comments->fetchColumn('threadId', array('hash' => $hash));

		// if exactly same title and data comment does not already exist, and is not the current thread
		if (!$result->numRows() || in_array($threadId, $existingThread))
		{
			$object = explode( ":", $objectId, 2);
			if ($prefs['feature_actionlog'] == 'y') {
				$comment= $this->get_comment($threadId);
				include_once('lib/diff/difflib.php');
				$bytes = diff2($comment['data'] , $data, 'bytes');
				global $logslib; include_once('lib/logs/logslib.php');
				if ($object[0] == 'forum')
					$logslib->add_action('Updated', $object[1], $object[0], "comments_parentId=$threadId&amp;$bytes#threadId$threadId", '', '', '', '',  $contributions);
				else
					$logslib->add_action('Updated', $object[1], 'comment', "type=".$object[0]."&amp;$bytes#threadId$threadId", '', '', '', '', $contributions);
			}
			$comments->update(array(
				'title' => $title,
				'comment_rating' => (int) $comment_rating,
				'data' => $data,
				'type' => $type,
				'summary' => $summary,
				'smiley' => $smiley,
				'hash' => $hash,
			), array(
				'threadId' => (int) $threadId,
			));
			if ($prefs['feature_contribution'] == 'y') {
				$contributionlib = TikiLib::lib('contribution');
				$contributionlib->assign_contributions($contributions, $threadId, 'comment', $title, '', '');
			}

			$type = $this->update_index($object[0], $threadId);
			$href = $this->getHref($object[0], $object[1], $threadId);
			global $tikilib;
			$tikilib->object_post_save( array('type'=>$type, 'object'=>$threadId, 'description'=>'', 'href'=>$href, 'name'=>$title), array('content' => $data));
			$this->update_comment_links($data, $object[0], $threadId);
		} // end hash check
	}

	/**
	 * Post a new comment (forum post or comment on some Tiki object)
	 * 
	 * @param string $objectId object type and id separated by two colon ('wiki page:HomePage' or 'blog post:2')
	 * @param int $parentId id of parent comment of this comment 
	 * @param string $userName if empty $anonumous_name is used
	 * @param string $title
	 * @param string $data
	 * @param unknown_type $message_id
	 * @param unknown_type $in_reply_to
	 * @param unknown_type $type
	 * @param unknown_type $summary
	 * @param unknown_type $smiley
	 * @param unknown_type $contributions
	 * @param string $anonymous_name name when anonymous user post a comment (optional) 
	 * @param int $postDate when the post was created (defaults to now)
	 * @param string $anonymous_email optional
	 * @param string $anonymous_website optional
	 * @return int $threadId id of the new comment
	 */
	function post_new_comment($objectId, $parentId, $userName,
		$title, $data, &$message_id, $in_reply_to = '', $type = 'n',
		$summary = '', $smiley = '', $contributions = '', $anonymous_name = '',
		$postDate = '', $anonymous_email = '', $anonymous_website = ''
	)
	{
		global $prefs, $tiki_p_admin_comments;

		if ($postDate == '') $postDate = $this->now;

		if (!$userName) {
			$_SESSION["lastPost"] = $postDate;
		}

		// Check for banned userName or banned IP or IP in banned range

		// Check for duplicates.
		$title = strip_tags($title);

		if ($anonymous_name) {
			$userName = $anonymous_name;
		} elseif (! $userName) {
			$userName = tra('Anonymous');
		} elseif ($userName) {
			$postings = $this->table('tiki_user_postings');
			$count = $postings->fetchCount(array('user' => $userName));

			if ($count) {
				$postings->update(array(
					'last' => (int) $postDate,
					'posts' => $postings->increment(1),
				), array(
					'user' => $userName,
				));
			} else {
				$posts = $this->table('tiki_comments')->fetchCount(array(
					'userName' => $userName,
				));

				if (!$posts) {
					$posts = 1;
				}

				$postings->insert(array(
					'user' => $userName,
					'first' => (int) $postDate,
					'last' => (int) $postDate,
					'posts' => (int) $posts,
				));
			}

			// Calculate max
			$max = $postings->fetchOne($postings->max('posts'), array());
			$min = $postings->fetchOne($postings->min('posts'), array());

			$min = max($min, 1);

			$ids = $postings->fetchCount(array());
			$tot = $postings->fetchOne($postings->sum('posts'), array());
			$average = $tot / $ids;
			$range1 = ($min + $average) / 2;
			$range2 = ($max + $average) / 2;

			$posts = $postings->fetchOne('posts', array('user' => $userName));

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

			$postings->update(array(
				'level' => $level,
			), array(
				'user' => $userName,
			));
		}

		$hash = md5($title . $data);

		// Check if we were passed a message-id.
		if ( ! $message_id )
		{
			// Construct a message id via proctological
			// extraction. -rlpowell
			$message_id = $userName . "-" .
				$parentId . "-" .
				substr( $hash, 0, 10 ) .
				"@" . $_SERVER["SERVER_NAME"];
		}

		// Break out the type and object parameters.
		$object = explode( ":", $objectId, 2);
		// Handle comments moderation (this should not affect forums and user with admin rights on comments)
		$approved = ( $tiki_p_admin_comments == 'y' || $object[0] == 'forum' || $prefs['feature_comments_moderation'] != 'y' ) ? 'y' : 'n';

		$comments = $this->table('tiki_comments');
		$threadId = $comments->fetchOne('threadId', array('hash' => $hash));

		// If this post was not already found.
		if (! $threadId) {
			$threadId = $comments->insert(array(
				'objectType' => $object[0],
				'object' => $object[1],
				'commentDate' => (int) $postDate,
				'userName' => $userName,
				'title' => $title,
				'data' => $data,
				'votes' => 0,
				'points' => 0,
				'hash' => $hash,
				'email' => $anonymous_email,
				'website' => $anonymous_website,
				'parentId' => (int) $parentId,
				'average' => 0,
				'hits' => 0,
				'type' => $type,
				'summary' => $summary,
				'user_ip' => $this->get_ip_address(),
				'message_id' => $message_id,
				'in_reply_to' => $in_reply_to,
				'approved' => $approved,
				'locked' => 'n',
			));
		}

		global $prefs;
		if ($prefs['feature_actionlog'] == 'y') {
			$logslib = TikiLib::lib('logs');
			global $tikilib;
			if ($parentId == 0)
				$l = strlen($data);
			else
				$l = $tikilib->strlen_quoted($data);
			if ($object[0] == 'forum')
				$logslib->add_action(($parentId == 0)? 'Posted': 'Replied', $object[1], $object[0], 'comments_parentId='.$threadId.'&amp;add='.$l, '', '', '', '', $contributions);
			else
				$logslib->add_action(($parentId == 0)? 'Posted': 'Replied', $object[1], 'comment', 'type='.$object[0].'&amp;add='.$l.'#threadId'.$threadId, '', '', '', '', $contributions);
		}

		if ($prefs['feature_contribution'] == 'y') {
			$contributionlib = TikiLib::lib('contribution');
			$contributionlib->assign_contributions($contributions, $threadId, 'comment', $title, '', '');
		}

		$type = $this->update_index($object[0], $threadId, $parentId);
		$href = $this->getHref($object[0], $object[1], $threadId);
		global $tikilib;
		$tikilib->object_post_save( array('type'=>$type, 'object'=>$threadId, 'description'=>'', 'href'=>$href, 'name'=>$title), array( 'content' => $data ));
		$this->update_comment_links($data, $object[0], $threadId);

		return $threadId;
		//return $return_result;
	}

	// Check if a particular topic exists.
	function check_for_topic( $title, $data ) {
		$hash = md5($title . $data);
		$comments = $this->table('tiki_comments');
		return $comments->fetchOne($comments->min('threadId'), array('hash' => $hash));
	}

	function approve_comment($threadId, $status = 'y')
	{
		if ( $threadId == 0 ) return false;

		return (bool) $this->table('tiki_comments')->update(array(
			'approved' => $status,
		), array(
			'threadId' => $threadId,
		));
	}
	function reject_comment($threadId) {
		return $this->approve_comment($threadId, 'r');
	}

	function remove_comment($threadId) {
		if ($threadId == 0) {
			return false;
		}
		global $prefs;

		$comments = $this->table('tiki_comments');
		$threadOrParent = $comments->expr('`threadId` = ? OR `parentId` = ?', array((int) $threadId, (int) $threadId));
		$result = $comments->fetchAll($comments->all(), array(
			'threadId' => $threadOrParent,
		));
		foreach ($result as $row) {
			if ($res['objectType'] == 'forum') {
				$this->remove_object('forum post', $res['threadId']);
				if ($prefs['feature_actionlog'] == 'y') {
					$logslib = TikiLib::lib('logs');
					$logslib->add_action('Removed', $res['object'], 'forum', "comments_parentId=$threadId&amp;del=".strlen($res['data']));
				}
			} else {
				$this->remove_object($res['objectType'].' comment', $res['threadId']);
				if ($prefs['feature_actionlog'] == 'y') {
					$logslib = TikiLib::lib('logs');
					$logslib->add_action('Removed', $res['object'], 'comment', 'type='.$res['objectType'].'&amp;del='.strlen($res['data'])."threadId#$threadId");
				}
			}
			if ($prefs['feature_contribution'] == 'y') {
				$contributionlib = TikiLib::lib('contribution');
				$contributionlib->remove_comment($res['threadId']);
			}

			$this->table('tiki_user_watches')->deleteMultiple(array(
				'object' => (int) $threadId,
				'type' => 'forum topic',
			));
			$this->table('tiki_group_watches')->deleteMultiple(array(
				'object' => (int) $threadId,
				'type' => 'forum topic',
			));
		}

		$comments->deleteMultiple(array(
			'threadId' => $threadOrParent,
		));
		//TODO in a forum, when the reply to a post (not a topic) id deletd, the replies to this post are not deleted

		$this->remove_reported($threadId);
		$this->table('tiki_forum_attachments')->deleteMultiple(array(
			'threadId' => (int) $threadId,
		));

		return true;
	}

	function vote_comment($threadId, $user, $vote) {
		$userpoints = $this->table('tiki_userpoints');
		$comments = $this->table('tiki_comments');

		// Select user points for the user who is voting (it may be anonymous!)
		$res = $userpoints->fetchRow(array('points', 'voted'), array('user' => $user));

		if ($res) {
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

		// Get the user that posted the comment being voted
		$comment_user = $comments->fetchOne('userName', array('threadId' => (int) $threadId));

		if ($comment_user && ($comment_user == $user)) {
			// The user is voting a comment posted by himself then bail out
			return false;
		}

		//print("Comment user: $comment_user<br />");
		if ($comment_user) {
			// Update the user points adding this new vote
			$count = $userpoints->fetchCount(array('user' => $comment_user));

			if ($count) {
				$userpoints->update(array(
					'points' => $userpoints->increment($vote),
					'voted' => $userpoints->increment(1),
				), array(
					'user' => $user,
				));
			} else {
				$userpoints->insert(array(
					'user' => $comment_user,
					'points' => $vote, 
					'voted' => 1,
				));
			}
		}

		$comments->update(array(
			'points' => $comments->increment($vote_weight),
			'votes' => $comments->increment(1),
		), array(
			'threadId' => $threadId,
		));
		$query = "update `tiki_comments` set `average` = `points`/`votes`
			where `threadId`=?";
		$result = $this->query($query, array( $threadId ) );
		return true;
	}

	function duplicate_forum($forumId, $name, $description='') {
		$forum_info = $this->get_forum($forumId);
		$newForumId = $this->replace_forum(0, $name, $description, $forum_info['controlFlood'],
			$forum_info['floodInterval'], $forum_info['moderator'], $forum_info['mail'], $forum_info['useMail'],
			$forum_info['usePruneUnreplied'], $forum_info['pruneUnrepliedAge'], $forum_info['usePruneOld'],
			$forum_info['pruneMaxAge'], $forum_info['topicsPerPage'], $forum_info['topicOrdering'],
			$forum_info['threadOrdering'], $forum_info['section'], $forum_info['topics_list_reads'],
			$forum_info['topics_list_replies'], $forum_info['topics_list_pts'],
			$forum_info['topics_list_lastpost'], $forum_info['topics_list_author'], $forum_info['vote_threads'],
			$forum_info['show_description'], $forum_info['inbound_pop_server'], $forum_info['inbound_pop_port'],
			$forum_info['inbound_pop_user'], $forum_info['inbound_pop_password'], $forum_info['outbound_address'],
			$forum_info['outbound_mails_for_inbound_mails'], $forum_info['outbound_mails_reply_link'],
			$forum_info['outbound_from'], $forum_info['topic_smileys'], $forum_info['topic_summary'], $forum_info['ui_avatar'],
			$forum_info['ui_flag'], $forum_info['ui_posts'], $forum_info['ui_level'], $forum_info['ui_email'], $forum_info['ui_online'],
			$forum_info['approval_type'], $forum_info['moderator_group'], $forum_info['forum_password'],
			$forum_info['forum_use_password'], $forum_info['att'], $forum_info['att_store'], $forum_info['att_store_dir'],
			$forum_info['att_max_size'], $forum_info['forum_last_n'], $forum_info['commentsPerPage'], $forum_info['threadStyle'],
										   $forum_info['is_flat'], $forum_info['att_list_nb'], $forum_info['topics_list_lastpost_title'], $forum_info['topics_list_lastpost_avatar'], $forum_info['topics_list_author_avatar']);

		return $newForumId;		
	}

	/**
	 * Archive thread or comment (only admins can archive
	 * comments or see them). This is used both for forums
	 * and comments.
	 *
	 * @param int $threadId the comment or thread id
	 * @param int $parentId
	 * @return bool
	 */
	function archive_thread($threadId, $parentId = 0) {
		if ( $threadId > 0 && $parentId >= 0 ) {
			return $this->table('tiki_comments')->update(array(
				'archived' => 'y',
			), array(
				'threadId' => (int) $threadId,
				'parentId' => (int) $parentId,
			));
		}
		return false;
	}

	/**
	 * Unarchive thread or comment (only admins can archive
	 * comments or see them).
	 * 
	 * @param int $threadId the comment or thread id
	 * @param int $parentId
	 * @return bool
	 */
	function unarchive_thread($threadId, $parentId = 0) {
		if ( $threadId > 0 && $parentId >= 0 ) {
			return $this->table('tiki_comments')->update(array(
				'archived' => 'n',
			), array(
				'threadId' => (int) $threadId,
				'parentId' => (int) $parentId,
			));
		}
		return false;
	}

	function list_directories_to_save() {
		$dirs = array();
		$forums = $this->list_forums();
		foreach ($forums['data'] as $forum) {
			if (!empty($forum['att_store_dir'])) {
				$dirs[] = $forum['att_store_dir'];
			}
		}
		return $dirs;
	}

	function get_outbound_emails() {
		$forums = $this->table('tiki_forums');
		$ret = $forums->fetchAll(array(
			'forumId',
			'outbound_address' => 'mail',
		), array(
			'useMail' => 'y',
			'mail' => $forums->not(''),
		));
		$result = $forums->fetchAll(array('forumId', 'outbound_address'), array(
			'outbound_address' => $forums->not(''),
		));
		return array_merge($ret,$result);
	}

	/* post a topic or a reply in forum
	 * @param array forum_info 
	 * @param array $params: list of options($_REQUEST)
 	 * @return the threadId
	 * @return $feedbacks, $errors */
	function post_in_forum($forum_info, &$params, &$feedbacks, &$errors) {
		global $smarty, $tiki_p_admin_forum, $tiki_p_forum_post_topic, $tiki_p_forum_post, $prefs, $user, $tiki_p_forum_autoapp, $captchalib;

		if (!empty($params['comments_grandParentId'])) {
			$parent_id = $params['comments_grandParentId'];
		} elseif (!empty($params['comments_parentId'])) {
			$parent_id = $params['comments_parentId'];
		} else {
			$parent_id = 0;
		}
		if (!($tiki_p_admin_forum == 'y' || ($parent_id == 0 && $tiki_p_forum_post_topic == 'y') || ($parent_id > 0 && $tiki_p_forum_post == 'y'))) {
			$errors[] = tra('Permission denied');
			return 0;
		}
		if ( $forum_info['is_locked'] == 'y' ) {
			$smarty->assign('msg', tra("This forum is locked"));
			$smarty->display("error.tpl");
			die;
		}
		$parent_comment_info = $this->get_comment($parent_id);
		if ( $parent_comment_info['locked'] == 'y' ) {
			$smarty->assign('msg', tra("This thread is locked"));
			$smarty->display("error.tpl");
			die;
		}

		if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
			$errors[] = $captchalib->getErrors();
		}
		if ($forum_info['controlFlood'] == 'y' && !$this->user_can_post_to_forum($user, $forumId) ) {
			$errors = sprintf(tra('Please wait %d seconds between posts'). $forum_info['floodInterval']);
		}
		if ($tiki_p_admin_forum != 'y' && $forum_info['forum_use_password'] != 'n' && $params['password'] != $forum_info['forum_password']) {
			$errors[] = tra('Wrong password. Cannot post comment');
		}
		if ( $parent_id > 0 && $forum_info['is_flat'] == 'y' && $params['comments_grandParentId'] > 0 ) {
			$errors[] = tra("This forum is flat and doesn't allow replies to other replies");
		}
		if ($prefs['feature_contribution'] == 'y' && $prefs['feature_contribution_mandatory_forum'] == 'y' && empty($params['contributions'])) {
			$errors[] = tra('A contribution is mandatory');
		}
		if ( ( $prefs['comments_notitle'] != 'y' && empty($params['comments_title']) ) || ( empty($params['comments_data']) && $prefs['feature_forums_allow_thread_titles'] != 'y' ) ) {
			$errors[] = tra('You have to enter a title and text');
		}
		if (!empty($params['anonymous_email']) && !validate_email($params['anonymous_email'], $prefs['validateEmail'])) {
			$errors[] = tra('Invalid Email');
		}
		// what do we do???

		if (!empty($errors)) {
			return 0;
		}
		// Remove HTML tags and empty lines at the end of the posted comment
		$params['comments_data'] = rtrim(strip_tags($params['comments_data'])); 
		if ($tiki_p_admin_forum != 'y') {// non admin can only post normal
			$params['comment_topictype'] = 'n';
			if ($forum_info['topic_summary'] != 'y')
				$params['comment_topicsummary'] = '';
			if ($forum_info['topic_smileys'] != 'y')
				$params['comment_topicsmiley'] = '';
		}
		if ( isset($params['comments_postComment_anonymous']) && ! empty($user) && $prefs['feature_comments_post_as_anonymous'] == 'y' ) {
			$params['comments_postComment'] = $params['comments_postComment_anonymous'];
			$user = '';
		}
		if (!isset($params['comment_topicsummary']))
			$params['comment_topicsummary'] = '';
		if (!isset($params['comment_topicsmiley']))
			$params['comment_topicsmiley'] = '';
		if ( isset($params['anonymous_name']) ) {
			$params['anonymous_name'] = trim(strip_tags($params['anonymous_name']));
		} else {
			$params['anonymous_name'] = '';
		}
		if (!isset($params['freetag_string'])) {
			$params['freetag_string'] = '';
		}
		if (!isset($params['anonymous_email'])) {
			$params['anonymous_email'] = '';
		}
		if ( isset($params['comments_reply_threadId']) && ! empty($params['comments_reply_threadId']) ) {
			$reply_info = $this->get_comment($params['comments_reply_threadId']);
			$in_reply_to = $reply_info['message_id'];
		} else {
			$in_reply_to = '';
		}
		$comments_objectId = 'forum:'.$params['forumId'];

		if (($tiki_p_forum_autoapp != 'y')
				&& ($forum_info['approval_type'] == 'queue_all' || (!$user && $forum_info['approval_type'] == 'queue_anon'))) {
			$threadId = 0;
			$feedbacks[] = tra('Your message has been queued for approval and will be posted after a moderator approves it.');
			$qId = $this->replace_queue(0, $forum_info['forumId'], $comments_objectId, $parent_id,
					$user, $params['comments_title'], $params['comments_data'], $params['comment_topictype'],
					$params['comment_topicsmiley'], $params['comment_topicsummary'], $params['comments_title'], $in_reply_to, $params['anonymous_name'], $params['freetag_string'], $params['anonymous_email']);
		} else { // not in queue mode
			$qId = 0;

			if ($params['comments_threadId'] == 0) { // new post
				$message_id = '';


				// The thread/topic does not already exist
				if( ! $params['comments_threadId'] ) {
					$threadId =	$this->post_new_comment($comments_objectId, $parent_id, $user, $params['comments_title'], $params['comments_data'], $message_id, $in_reply_to,
							$params['comment_topictype'],	$params['comment_topicsummary'], $params['comment_topicsmiley'], isset($params['contributions'])? $params['contributions']: '',	$params['anonymous_name']	);
					// The thread *WAS* successfully created.

					if( $threadId ) {
						// Deal with mail notifications.
						include_once('lib/notifications/notificationemaillib.php');
						sendForumEmailNotification(empty($params['comments_reply_threadId'])?'forum_post_topic':'forum_post_thread', $params['forumId'], $forum_info, $params['comments_title'], $params['comments_data'], $user, $params['comments_title'], $message_id, $in_reply_to, isset($params['comments_parentId'])?$params['comments_parentId']: $threadId, isset($params['comments_parentId'])?$params['comments_parentId']: 0, isset($params['contributions'])? $params['contributions']: '', $threadId);
						// Set watch if requested
						if ($prefs['feature_user_watches'] == 'y') {
							if ($user && isset($params['set_thread_watch']) && $params['set_thread_watch'] == 'y') {
								$this->add_user_watch($user, 'forum_post_thread', $threadId, 'forum topic', $forum_info['name'] . ':' . $params['comments_title'], 'tiki-view_forum_thread.php?forumId=' . $forum_info['forumId'] . '&amp;comments_parentId=' . $threadId);
							} elseif (!empty($params['anonymous_email'])) { // Add an anonymous watch, if email address supplied.
								$this->add_user_watch($params['anonymous_name']. ' ' . tra('(not registered)', $prefs['site_language']), 'forum_post_thread', $threadId, 'forum topic', $forum_info['name'] . ':' . $params['comments_title'], 'tiki-view_forum_thread.php?forumId=' . $forum_info['forumId'] . '&amp;comments_parentId=' . $threadId, $params['anonymous_email'], isset($prefs['language']) ? $prefs['language'] : '');
							}
						}

						// TAG Stuff
						$cat_type = 'forum post';
						$cat_objid = $threadId;
						$cat_desc = substr($params['comments_data'],0,200);
						$cat_name = $params['comments_title'];
						$cat_href='tiki-view_forum_thread.php?comments_parentId=' . $threadId . '&forumId=' . $params['forumId'];
						include ('freetag_apply.php');
					}
				}

				$this->register_forum_post($forum_info['forumId'], 0);
			} elseif ($tiki_p_admin_forum == 'y' || $this->user_can_edit_post($user, $params['comments_threadId'])) {
				$threadId = $params['comments_threadId'];
				$this->update_comment($threadId, $params['comments_title'], '', ($params['comments_data']), $params['comment_topictype'], $params['comment_topicsummary'], $params['comment_topicsmiley'], $comments_objectId, isset($params['contributions'])? $params['contributions']: '');
			}
		}
		if (!empty($threadId) || !empty($qId)) {
			// PROCESS ATTACHMENT HERE
			if (isset($_FILES['userfile1']) && !empty($_FILES['userfile1']['name'])) {
				if (is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
					$fp = fopen($_FILES['userfile1']['tmp_name'], 'rb');
					$ret = $this->add_thread_attachment($forum_info, $threadId, $errors,	$_FILES['userfile1']['name'], $_FILES['userfile1']['type'],	$_FILES['userfile1']['size'], 0, $qId, $fp, '' );
					fclose($fp);
				} else {
					$errors[] = $this->uploaded_file_error($_FILES['userfile1']['error']);
				}
			} //END ATTACHMENT PROCESSING
		}
		if (!empty($errors)) {
			return 0;
		} elseif ($qId) {
			return $qId;
		} else {
			return $threadId;
		}
	}

	function get_all_thread_attachments($threadId, $offset=0, $maxRecords=-1, $sort_mode='created_desc') {
		$query = 'select tfa.* from `tiki_forum_attachments` tfa, `tiki_comments` tc where tc.`threadId`=tfa.`threadId` and ((tc.`threadId`=? and tc.`parentId`=?) or tc.`parentId`=?) order by '.$this->convertSortMode($sort_mode);
		$bindvars = array($threadId, 0, $threadId);
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$query = 'select count(*) from `tiki_forum_attachments` tfa, `tiki_comments` tc where tc.`threadId`=tfa.`threadId` and ((tc.`threadId`=? and tc.`parentId`=?) or tc.`parentId`=?)';
		$cant = $this->getOne($query, $bindvars);
		return array('cant' => $cant, 'data' => $ret);
	}

	private function update_index($type, $threadId, $parentId = null) {
		require_once('lib/search/refresh-functions.php');

		refresh_index('comments', $threadId);

		if ($type == 'forum') {
			$type = 'forum post';

			$root = $this->find_root($parentId ? $parentId : $threadId);
			refresh_index($type, $root);

			return $type;
		} else {
			return $type.' comment';
		}
	}

	private function find_root($threadId) {
		$parent = $this->table('tiki_comments')->fetchOne('parentId', array('threadId' => $threadId));

		if ($parent) {
			return $this->find_root($parent);
		} else {
			return $threadId;
		}
	}
}

function compare_replies($ar1, $ar2)
{
	if (($ar1['type'] == 's' && $ar2['type'] == 's') ||
			($ar1['type'] != 's' && $ar2['type'] != 's')) {
		return $ar1["replies_info"]["numReplies"] - $ar2["replies_info"]["numReplies"];
	} else {
		return $ar1['type'] == 's' ? -1 : 1;
	}
}

function compare_lastPost($ar1, $ar2)
{
	if (($ar1['type'] == 's' && $ar2['type'] == 's') ||
			($ar1['type'] != 's' && $ar2['type'] != 's')) {
		return $ar1["lastPost"] - $ar2["lastPost"];
	} else {
		return $ar1['type'] == 's' ? -1 : 1;
	}
}

function r_compare_replies($ar1, $ar2)
{
	if (($ar1['type'] == 's' && $ar2['type'] == 's') ||
			($ar1['type'] != 's' && $ar2['type'] != 's')) {
		return $ar2["replies_info"]["numReplies"] - $ar1["replies_info"]["numReplies"];
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


