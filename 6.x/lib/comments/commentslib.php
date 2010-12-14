<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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

		$query = "delete from `tiki_forums_reported` where `forumId`=? and `parentId`=? and `threadId`=? and `user`=?";
		$bindvars=array($forumId, $parentId, $threadId, $user);

		$this->query($query, $bindvars, -1, -1, false);

		$query = "insert into `tiki_forums_reported`(`forumId`,
			`parentId`, `threadId`, `user`, `reason`, `timestamp`)
			values(?,?,?,?,?,?)";
		$bindvars=array($forumId, $parentId, $threadId, $user, $reason, (int)$this->now);
		$this->query($query, $bindvars);
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
		return $this->getOne("select count(*) from `tiki_forums_reported` where `threadId`=?", array($threadId));
	}

	function remove_reported($threadId) {
		$query = "delete from `tiki_forums_reported` where `threadId`=?";

		$this->query($query, array((int) $threadId));
	}

	function get_num_reported($forumId) {
		return $this->getOne("select count(*) from `tiki_forums_reported` tfr, `tiki_comments` tc where tfr.`threadId` = tc.`threadId` and `forumId`=?", array( (int) $forumId));
	}

	function mark_comment($user, $forumId, $threadId) {
		if (!$user)
			return false;

		$query = "delete from `tiki_forum_reads` where `user`=? and `threadId`=?";
		$bindvars=array($user,(int) $threadId);
		$this->query($query, $bindvars, -1, -1, false);

		$query = "insert into `tiki_forum_reads`(`user`,`threadId`,`forumId`,`timestamp`)
			values(?,?,?,?)";
		$bindvars=array($user,(int) $threadId,(int) $forumId,(int) $this->now);
		$this->query($query, $bindvars);
	}

	function unmark_comment($user, $forumId, $threadId) {
		$query = "delete from `tiki_forum_reads` where `user`=? and `threadId`=?";

		$this->query($query, array($user, (int) $threadId));
	}

	function is_marked($threadId) {
		global $user;

		if (!$user)
			return false;

		return $this->getOne("select count(*) from `tiki_forum_reads` where `user`=? and `threadId`=?", array($user, $threadId));
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

		$query = "insert into
			`tiki_forum_attachments`(`threadId`, `qId`, `filename`,
					`filetype`, `filesize`, `data`, `path`, `created`, `dir`,
					`forumId`)
			values(?,?,?,?,?,?,?,?,?,?)";
		$this->query($query, array($threadId, $qId, $name, $type, $size, $data, $fhash, $this->now, $dir, $forumId));
		return true;
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
		return $this->fetchAll($query, $bindvars);
	}

	function get_thread_attachment($attId) {
		$query = "select * from `tiki_forum_attachments` where `attId`=?";

		$result = $this->query($query, array($attId));
		$res = $result->fetchRow();
		$forum_info = $this->get_forum($res['forumId']);

		$res['forum_info'] = $forum_info;
		return $res;
	}

	function remove_thread_attachment($attId) {
		$query = "delete from `tiki_forum_attachments` where `attId`=?";

		$this->query($query, array($attId));
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
		include_once ("lib/webmail/class.rc4crypt.php");
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
			$userName = $this->getOne("select `login` from `users_users` where `email`=?", array($email));

			//use anonomus name feature if we don't have a real name
			if (!$userName) $anonName = $original_email;

			// Determine if the thread already exists.
			$parentId = $this->getOne(
					"select `threadId` from `tiki_comments` where
					`object`=? and `objectType` = 'forum' and
					`parentId`=0 and `title`=?",
					array($forumId, $title) 
					);

			// print( "<pre>parentid:" );
			// print_r( $parentId );
			// print( "</pre>" );

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

		if ($qId == 0 && $this->getOne("select count(*) from
					`tiki_forums_queue` where `hash`=?", array($hash2)))
			return false;
		if (!$user && $anonymous_name) {
			$user = $anonymous_name;
		}

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
				`timestamp` = ?,
				`in_reply_to` = ?,
				`tags` = ?,
				`email` = ?,
				where `qId`=?
					";

			$this->query($query, array($object, $parentId, $user, $title, $data, $forumId, $type, $hash2, $topic_title, $topic_smiley, $summary,(int)$this->now, $in_reply_to ,$tags, $email, $qId));
			return $qId;
		} else {
			$query = "insert into
				`tiki_forums_queue`(`object`, `parentId`, `user`,
						`title`, `data`, `type`, `topic_smiley`, `summary`,
						`timestamp`, `topic_title`, `hash`, `forumId`, `in_reply_to`, `tags`, `email`)
				values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$this->query($query, array($object, $parentId, $user,
						$title, $data, $type, $topic_smiley, $summary, (int)$this->now,
						$topic_title, $hash2, $forumId, $in_reply_to, $tags, $email));
			$qId = $this->getOne("select max(`qId`) from
					`tiki_forums_queue` where `hash`=? and
					`timestamp`=?", array($hash2,(int)$this->now));
		}

		return $qId;
	}

	function get_num_queued($object) {
		return $this->getOne("select count(*) from
				`tiki_forums_queue` where `object`=?", array($object));
	}

	function list_forum_queue($object, $offset, $maxRecords, $sort_mode, $find) {
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and `title` like $findesc or `data` like $findesc";
			$bindvars=array($object, $findesc, $findesc);
		} else {
			$mid = "";
			$bindvars=array($object);
		}

		$query = "select * from `tiki_forums_queue` where `object`=? $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_forums_queue` where `object`=? $mid";

		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset );
		$cant = $this->getOne($query_cant, $bindvars );

		foreach ( $ret as &$res ) {
			$res['parsed'] = $this->parse_comment_data($res['data']);

			$res['attachments'] = $this->get_thread_attachments(0, $res['qId']);
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function queue_get($qId) {
		$query = "select * from `tiki_forums_queue` where `qId`=?";

		$result = $this->query($query, array((int) $qId));
		$res = $result->fetchRow();
		$res['attchments'] = $this->get_thread_attachments(0, $res['qId']);
		return $res;
	}

	function remove_queued($qId) {
		$query = "delete from `tiki_forums_queue` where `qId`=?";

		$this->query($query, array((int) $qId));
		$query = "delete from `tiki_forum_attachments` where `qId`=?";
		$this->query($query, array((int) $qId));
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
		$query = "update `tiki_forum_attachments` set `threadId`=?, `qId`=? where `qId`=?";
		$this->query($query, array($threadId, 0, $qId));
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
		$mid = " where `objectType` = ? and `object`=? ";
		$bind_mid = array('forum', $forumId);
		$sort_mode = 'commentDate_desc';

		$query = "select * from `tiki_comments` $mid order by ".$this->convertSortMode($sort_mode);
		return $this->fetchAll($query, $bind_mid, $maxRecords, 0);
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

		if ($forumId)
		{
			$query = "update `tiki_forums` set
				`name` = ?,  	
			`description` = ?,
			`controlFlood` = ?,
			`floodInterval` = ?,
			`moderator` = ?,
			`mail` = ?,
			`useMail` = ?,
			`section` = ?,
			`usePruneUnreplied` = ?,
			`pruneUnrepliedAge` = ?,
			`usePruneOld` = ?,
			`vote_threads` = ?,
			`topics_list_reads` = ?,
			`topics_list_replies` = ?,
			`show_description` = ?,
			`inbound_pop_server` = ?,
			`inbound_pop_port` = ?,
			`inbound_pop_user` = ?,
			`inbound_pop_password` = ?,
			`outbound_address` = ?,
			`outbound_mails_for_inbound_mails` = ?,
			`outbound_mails_reply_link` = ?,
			`outbound_from` = ?,
			`topic_smileys` = ?,
			`topic_summary` = ?,
			`ui_avatar` = ?,
			`ui_flag` = ?,
			`ui_posts` = ?,
			`ui_level` = ?,
			`ui_email` = ?,
			`ui_online` = ?,
			`approval_type` = ?,
			`moderator_group` = ?,
			`forum_password` = ?,
			`forum_use_password` = ?,
			`att` = ?,
			`att_store` = ?,
			`att_store_dir` = ?,
			`att_max_size` = ?, 
			`topics_list_pts` = ?,
			`topics_list_lastpost` = ?,
			`topics_list_lastpost_title` = ?,
			`topics_list_lastpost_avatar` = ?,
			`topics_list_author` = ?,
			`topics_list_author_avatar` = ?,
			`topicsPerPage` = ?,
			`topicOrdering` = ?,
			`threadOrdering` = ?,
			`pruneMaxAge` = ?,
			`forum_last_n` = ?,
			`commentsPerPage` = ?,
			`threadStyle` = ?,
			`is_flat` = ?,
			`att_list_nb` = ?
				where `forumId` = ?";
			$result = $this->query(
					$query,
					array(
						$name,  	
						$description,
						$controlFlood,
						(int) $floodInterval,
						$moderator,
						$mail,
						$useMail,
						$section,
						$usePruneUnreplied,
						(int) $pruneUnrepliedAge,
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
						$outbound_mails_for_inbound_mails,
						$outbound_mails_reply_link,
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
						(int) $att_max_size,
						$topics_list_pts,
						$topics_list_lastpost,
						$topics_list_lastpost_title,
						$topics_list_lastpost_avatar,
						$topics_list_author,
						$topics_list_author_avatar,
						(int) $topicsPerPage,
						$topicOrdering,
						$threadOrdering,
						(int) $pruneMaxAge,
						(int) $forum_last_n,
						$commentsPerPage,
						$threadStyle,
						$is_flat,
						$att_list_nb,
						(int) $forumId
							)
							);
		} else {
			$query = "insert into `tiki_forums`(`name`, `description`,
				`created`, `lastPost`, `comments`,
				`controlFlood`,`floodInterval`, `moderator`, `hits`, `mail`,
				`useMail`, `usePruneUnreplied`, `pruneUnrepliedAge`,
				`usePruneOld`,`pruneMaxAge`, `topicsPerPage`,
				`topicOrdering`, `threadOrdering`,`section`,
				`topics_list_reads`, `topics_list_replies`,
				`topics_list_pts`, `topics_list_lastpost`, `topics_list_lastpost_title`, `topics_list_lastpost_avatar`,
				`topics_list_author`, `topics_list_author_avatar`,`vote_threads`, `show_description`,
				`inbound_pop_server`,`inbound_pop_port`,`inbound_pop_user`,`inbound_pop_password`,
				`outbound_address`, `outbound_mails_for_inbound_mails`,
				`outbound_mails_reply_link`, `outbound_from`,
				`topic_smileys`,`topic_summary`,
				`ui_avatar`, `ui_flag`, `ui_posts`, `ui_level`, `ui_email`,
				`ui_online`, `approval_type`, `moderator_group`,
				`forum_password`, `forum_use_password`, `att`, `att_store`,
				`att_store_dir`, `att_max_size`,`forum_last_n`, `commentsPerPage`, `threadStyle`,
				`is_flat`, `att_list_nb`) 
					values (?,?,?,?,?,?,?,?,?,?,
							?,?,?,?,?,?,?,?,?,?,
							?,?,?,?,?,?,?,?,?,?,
							?,?,?,?,?,?,?,?,?,?,
							?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array($name, $description, (int) $this->now, (int) $this->now, 0,
					$controlFlood, (int) $floodInterval, $moderator, 0, $mail,
					$useMail, $usePruneUnreplied, (int) $pruneUnrepliedAge,
					$usePruneOld, (int) $pruneMaxAge, (int) $topicsPerPage,  $topicOrdering,
					$threadOrdering, $section, $topics_list_reads,
					$topics_list_replies, $topics_list_pts,
							$topics_list_lastpost, $topics_list_lastpost_title, $topics_list_lastpost_avatar, $topics_list_author,  $topics_list_author_avatar, $vote_threads,
					$show_description, $inbound_pop_server, $inbound_pop_port,
					$inbound_pop_user, $inbound_pop_password, $outbound_address,
					$outbound_mails_for_inbound_mails,
					$outbound_mails_reply_link,
					$outbound_from,  $topic_smileys, $topic_summary, $ui_avatar,
					$ui_flag, $ui_posts, $ui_level, $ui_email, $ui_online,
					$approval_type, $moderator_group, $forum_password,
					$forum_use_password, $att, $att_store, $att_store_dir,
					(int) $att_max_size,(int) $forum_last_n, $commentsPerPage, $threadStyle,
					$is_flat, $att_list_nb);

			$result = $this->query($query, $bindvars);
			$forumId = $this->getOne("select max(`forumId`)
					from `tiki_forums` where `name`=? and `created`=?",
					array($name,(int) $this->now));
		}

		global $prefs;
		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('forums', $forumId);
		}

		return $forumId;
	}

	function get_forum($forumId) {
		$query = "select * from `tiki_forums` where `forumId`=?";

		$result = $this->query($query, array((int) $forumId));
		$res = $result->fetchRow();
		if ( !empty($res) ) $res['is_locked'] = $this->is_object_locked('forum:'.$forumId) ? 'y' : 'n';

		return $res;
	}

	function remove_forum($forumId) {
		$query = "delete from `tiki_forums` where `forumId`=?";
		$result = $this->query($query, array((int) $forumId ) );
		$this->remove_object("forum", $forumId);
		$query = "delete from `tiki_forum_attachments` where `forumId`=?";
		$this->query($query, array((int) $forumId ) );
		return true;
	}

	function list_forums($offset=0, $maxRecords=-1, $sort_mode='name_asc', $find = '') {
		global $user;

		$bindvars=array();

		global $categlib; require_once 'lib/categories/categlib.php';
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
		foreach( $result as &$res ) {
			$cant++; // Count the whole number of forums the user has access to

			if ( ( $maxRecords > -1 && $count >= $maxRecords ) || $off++ < $offset ) continue;

			$forum_age = ceil(($this->now - $res["created"]) / (24 * 3600));

			// Get number of topics on this forum
			$res['threads'] = $this->count_comments_threads('forum:'.$res['forumId']);

			// Get number of posts on this forum
			$res['comments'] = $this->count_comments('forum:'.$res['forumId']);

			// Get number of users that posted at least one comment on this forum
			$res['users'] = $this->getOne(
				'select count(distinct `userName`) from `tiki_comments` where `object`=? and `objectType`=?',
				array($res['forumId'], 'forum')
			);

			// Get lock status
			$res['is_locked'] = $this->is_object_locked('forum:'.$res['forumId']) ? 'y' : 'n';

			// Get data of the last post of this forum
			if ( $res['comments'] > 0 ) {
				$result2 = $this->query(
					'select * from `tiki_comments` where `object`= ? and `objectType` = ? order by `commentDate` desc',
					array($res['forumId'], 'forum'), 1);

				$res['lastPostData'] = $result2->fetchRow();
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
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `section`=? and `name` like ? or `description` like ?";
			$bindvars=array($section, $findesc, $findesc);
		} else {
			$mid = " where `section`=? ";
			$bindvars=array($section);
		}

		$query = "select * from `tiki_forums` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_forums`";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, array());

		foreach ( $ret as &$res ) {
			$forum_age = ceil(($this->now - $res["created"]) / (24 * 3600));

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
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function user_can_edit_post( $user, $threadId ) {
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

			if ($maxDate + $forum["floodInterval"] > $this->now) {
				return false;
			} else {
				return true;
			}
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
		$query = "update `tiki_forums` set `comments`=`comments`+1 where `forumId`=?";

		$result = $this->query($query, array((int) $forumId));

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
			$query = "update `tiki_forums` set `hits`=`hits`+1 where
				`forumId`=?";

			$result = $this->query($query, array( (int) $forumId ) );
			$this->forum_prune($forumId);
		}

		return true;
	}

	function comment_add_hit($threadId) {
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_comments` set `hits`=`hits`+1 where
				`threadId`=?";

			$result = $this->query($query, array( (int) $threadId ) );
			//$this->forum_prune($forumId);
		}

		return true;
	}

	function get_all_children($threadId, $generations = 99) {
		$children = array();
		$current_generation = 0;
		if (!is_array($threadId)) $threadId = array($threadId);
		while ($current_generation < $generations) {
			$children_this_generation = array();
			foreach ($threadId as $t) {
				$query = "select `threadId` from `tiki_comments` where `parentId`=?";
				$result = $this->query($query, array($t));
				while ($res = $result->fetchRow()) {
					$children_this_generation[] = $res["threadId"];
				}
			}
			$children[] = $children_this_generation; 
			if (!$children_this_generation) return array_unique($children);
			$current_generation++;
			$threadId = $children_this_generation;
		}
		return array_unique($children);
	}

	function forum_prune($forumId) {
		$forum = $this->get_forum($forumId);

		if ($forum["usePruneUnreplied"] == 'y') {
			$age = $forum["pruneUnrepliedAge"];

			// Get all unreplied threads
			// Get all the top_level threads
			$oldage = $this->now - $age;
			$query = "select `threadId` from `tiki_comments` where
				`parentId`=0 and `commentDate`<? and `object`=? and `objectType` = 'forum'";
			$result = $this->query($query, array( (int) $oldage, $forumId ));

			while ($res = $result->fetchRow()) {
				// Check if this old top level thread has replies
				$id = $res["threadId"];
				if ($id == 0)
					continue;	// in the case there is an error ...

				$query2 = "select count(*) from `tiki_comments`
					where `parentId`=?";
				$cant = $this->getOne($query2, array( (int) $id ));

				// Remove this old thread without replies
				if ($cant == 0) $this->remove_comment($id);

			} // end while
		}

		if ($forum["usePruneOld"] == 'y') { // this is very dangerous as you can delete some posts in the middle or root of a tree strucuture
			$maxAge = $forum["pruneMaxAge"];

			$old = $this->now - $maxAge;
			$query = "select * from `tiki_comments` where `object`=?
				and `objectType` = 'forum' and `commentDate`<?";
			$result = $this->query($query, array($forumId, (int) $old));
			// this aims to make it safer, by pruning only those with no children that are younger than age threshold
			while ($res = $result->fetchRow()) {
				$children = $this->get_all_children($res['threadId']);
				if ($children) {
					$csv_children = implode(',', $children);
					$query = "select max(`commentDate`) from `tiki_comments` where `threadId` in (?)";
					$maxDate = $this->getOne($query, array( $csv_children ) );
					if ($maxDate < $old) $this->remove_comment($res['threadId']);
				} else {
					$this->remove_comment($res['threadId']);
				}
			}
		}

		if ($forum["usePruneUnreplied"] == 'y' || $forum["usePruneOld"] == 'y') {	// Recalculate comments and threads
			$query = "select count(*) from `tiki_comments` where `objectType` = 'forum' and `object`=?";
			$comments = $this->getOne($query, array( $forumId ) );
			$query = "update `tiki_forums` set `comments`=? where `forumId`=?";
			$result = $this->query($query, array( (int) $comments, (int) $forumId) );
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
		if ($message_id) {
			$query = "select * from `tiki_comments` where `message_id`=?";
			$result = $this->query($query, array($message_id ) );
		}
		else {
			$query = "select * from `tiki_comments` where `threadId`=?";
			$result = $this->query($query, array( (int) $id ) );
		}
		$res = $result->fetchRow();
		if($res) { //if there is a comment with that id
			$this->add_comments_extras($res, $forum_info);
		}

		return $res;
	}

	/**
	* Returns the forum-id for a comment
	*/
	function get_comment_forum_id($commentId) {
		$query = "select object from `tiki_comments` where `threadId`=?";
		$result = $this->getOne($query, array($commentId) );
		return $result;
	}

	function add_comments_extras(&$res, $forum_info=null) { 
		// this function adds some extras to the referenced array. 
		// This array should already contain the contents of the tiki_comments table row
		// used in $this->get_comment and $this->get_comments
		global $prefs;

		$res["parsed"] = $this->parse_comment_data($res["data"]);

		// these could be cached or probably queried along with the original query of the tiki_comments table
		if ($forum_info == null || $forum_info['ui_posts'] == 'y' || $forum_info['ui_level'] == 'y') {
			$result2=$this->query("select `posts`, `level` from `tiki_user_postings` where `user`=?", array( $res['userName'] ) );
			$res2=$result2->fetchRow();
			$res['user_posts'] = $res2['posts'];
			$res['user_level'] = $res2['level'];
		}
		// 'email is public' never has 'y' value, because it is now used to choose the email scrambling method
		// ... so, we need to test if it's not equal to 'n'
		if (($forum_info == null || $forum_info['ui_email'] == 'y') && $this->get_user_preference($res['userName'], 'email is public', 'n') != 'n') {
			$res['user_email'] = $this->getOne("select `email` from `users_users` where `login`=?", array( $res['userName'] ) );
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
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$res['contributions'] = $contributionlib->get_assigned_contributions($res['threadId'], 'comment');
		}
	}

	function get_comment_father($id) {
		static $cache;
		if ( isset($cache[$id]) ) {
			return $cache[$id];
		}
		$query = "select `parentId` from `tiki_comments` where `threadId`=?";

		$ret = $this->getOne($query, array( $id ));
		$cache[$id] = $ret;

		return $ret;
	}

	function count_comments($objectId, $approved = 'y') {
		global $tiki_p_admin_comments;


		$object = explode( ":", $objectId, 2);
		$query = 'select count(*) from `tiki_comments` where `objectType`=?';
		if ( $object[0] == 'topic' ) {
			$bindvars = array('forum');
			$query .= ' and `parentId`=?';

		} else {
			$bindvars = array($object[0]);
			$query .= ' and `object`=?';
		}
		$bindvars[] = $object[1];

		if ( $tiki_p_admin_comments != 'y' ) {
			$query .= ' and `approved`=?';
			$bindvars[] = $approved;


		}

		return $this->getOne($query, $bindvars);
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
			$query = "SELECT count(*),`tiki_blog_posts`.`postId`,`tiki_blog_posts`.`title` FROM `tiki_comments` INNER JOIN `tiki_blog_posts` ON `tiki_comments`.`object`=`tiki_blog_posts`.`postId` WHERE `tiki_comments`.`objectType`='post' and `tiki_comments`.`approved`='y' GROUP BY `tiki_comments`.`object` ORDER BY count(*) DESC";
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
		$query = "select count(*) from `tiki_comments` where `objectType`=? and `object`=? and `parentId`=0";
		$cant = $this->getOne($query, $object );
		return $cant;
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
		$cant = $this->getOne("select count(*) from `tiki_cookies`", array());

		if (!$cant)
			return '';

		$bid = rand(0, $cant - 1);
		$cookie = $this->query("select `cookie` from `tiki_cookies`", array(), 1, $bid);
		$cookie = str_replace("\n", "", $cookie);
		return 'Cookie: ' . $cookie . '';
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

			$query = "select tc1.`threadId`, tc1.`object`, tc1.`objectType`, tc1.`parentId`, tc1.`userName`, tc1.`commentDate`, tc1.`hits`, tc1.`type`, tc1.`points`, tc1.`votes`, tc1.`average`, tc1.`title`, tc1.`data`, tc1.`hash`, tc1.`user_ip`, tc1.`summary`, tc1.`smiley`, tc1.`message_id`, tc1.`in_reply_to`, tc1.`comment_rating`, tc1.`approved`, tc1.`locked`  from `tiki_comments` as tc1
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

	/* administrative functions to get all the comments of some types + enlarge find
	 * no perms checked as it is only for admin */
	function get_all_comments($type, $offset = 0, $maxRecords = -1, $sort_mode = 'commentDate_asc', $find = '', $parent='', $approved='', $toponly=false, $objectId='') {
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

		// Blog hack -- to fix
		foreach ( $bindvars as $k => $v ) {
			if ( $v == 'blog post' ) $bindvars[$k] = 'post';
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

	function getHref($type, $object, $threadId) {
		switch ($type) {
			case 'wiki page': $href = 'tiki-index.php?page='; break;
			case 'article': $href = 'tiki-read_article.php?articleId='; break;
			case 'faq': $href = 'tiki-view_faq.php?faqId='; break;
			case 'blog': $href = 'tiki-view_blog.php?blogId='; break;
			case 'post': $href = 'tiki-view_blog_post.php?postId='; break;
			case 'forum': $href = 'tiki-view_forum_thread.php?forumId='; break;
			case 'file gallery': $href = 'tiki-list_file_gallery.php?galleryId='; break;
			case 'image gallery': $href = 'tiki-browse_gallery.php?galleryId='; break;
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
		$query = "update `tiki_comments`
			set `locked`='y' where `threadId`=?";

		$this->query($query, array( (int) $threadId ) );
	}

	function set_comment_object($threadId, $objectId) {
		// Break out the type and object parameters.
		$object = explode( ":", $objectId, 2);

		$query = "update `tiki_comments`
			set `objectType` = ?, `object`=? where `threadId`=? or
			`parentId`=?";
		$this->query($query, array( $object[0], $object[1],
					(int) $threadId, (int) $threadId ) );
	}

	function set_parent($threadId, $parentId) {
		$query = "select `message_id` from `tiki_comments` where `threadId` = ?";
		$parent_message_id = $this->getOne($query, array( $parentId ) );

		$query = "update `tiki_comments`
			set `parentId`=?, `in_reply_to`=? where `threadId`=?";

		$this->query($query, array( (int) $parentId, $parent_message_id, (int) $threadId ) );
	}

	function unlock_comment($threadId) {
		$query = "update `tiki_comments`
			set `locked`='n' where `threadId`=?";

		$this->query($query, array( (int) $threadId ) );
	}

	// Lock all comments of an object
	function lock_object_thread($objectId, $status = 'y') {
		if ( empty($objectId) ) return false;
		$object = explode( ":", $objectId, 2);
		if ( count($object) < 2 ) return false;

		// Add object if not already exists, because it's currently only done when using categories feature
		// We suppose it's already done when unlocking the object, because it is needed to be locked
		if ( $status == 'y' ) {
			global $objectlib; require_once('lib/objectlib.php');
			$objectlib->add_object($object[0], $object[1]);
		}

		$query = "UPDATE `tiki_objects` SET `comments_locked`=? WHERE `Type`=? AND `itemId`=?";
		return $this->query($query, array( $status, $object[0], $object[1] ));
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
		return $this->getOne('SELECT `comments_locked` FROM `tiki_objects` WHERE `type`=? AND `itemId`=?', array( $object[0], $object[1] )) == 'y';
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

		$hash = md5($title . $data);
		$query = "select `threadId` from `tiki_comments` where `hash`=?";
		$result = $this->query($query, array( $hash ) );
		$existingThread = array();
		while ($res = $result->fetchRow()) {
			$existingThread[] = $res['threadId'];
		}

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
			$query = "update `tiki_comments` set `title`=?, `comment_rating`=?,
				`data`=?, `type`=?, `summary`=?, `smiley`=?, `hash`=?
					where `threadId`=?";
			$result = $this->query($query, array( $title, (int) $comment_rating, $data, $type,
						$summary, $smiley, $hash, (int) $threadId ) );
			if ($prefs['feature_contribution'] == 'y') {
				global $contributionlib; include_once('lib/contribution/contributionlib.php');
				$contributionlib->assign_contributions($contributions, $threadId, 'comment', $title, '', '');
			}

			if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
				require_once('lib/search/refresh-functions.php');
				refresh_index('comments', $threadId);
			}
			if ($object[0] == 'forum') {
				$type = 'forum post';
			} else {
				$type = $object[0].' comment';
			}
			$href = $this->getHref($object[0], $object[1], $threadId);
			$this->object_post_save( array('type'=>$type, 'object'=>$threadId, 'description'=>'', 'href'=>$href, 'name'=>$title), array('content' => $data));
			$this->update_comment_links($data, $object[0], $threadId);
		} // end hash check
	}

	function post_new_comment($objectId, $parentId, $userName,
		$title, $data, &$message_id, $in_reply_to = '', $type = 'n',
		$summary = '', $smiley = '', $contributions = '', $anonymous_name = '',
		$postDate = '', $anonymous_email, $anonymous_website
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

		if (!$userName) {
			if ($anonymous_name) {
				//$userName = $anonymous_name . ' ' . tra('(not registered)');
				$userName = $anonymous_name;
			} else {
				$userName = tra('Anonymous');
			}
		} else {

			if ($this->getOne("select count(*) from 
				`tiki_user_postings` where `user`=?",
				array( $userName ), false))
			{
				$query = "update `tiki_user_postings` ".
					"set `last`=?, `posts` = `posts` + 1 where `user`=?";

				$this->query($query, array( (int)$postDate, $userName ) );
			} else {
				$posts = $this->getOne("select count(*) ".
						"from `tiki_comments` where `userName`=?",
						array( $userName), false);

				if (!$posts)
					$posts = 1;

				$query = "insert into 
					`tiki_user_postings`(`user`,`first`,`last`,`posts`) 
					values( ?, ?, ?, ? )";
				$this->query($query, array($userName, (int) $postDate, (int) $postDate,(int) $posts) );
			}

			// Calculate max
			$max = $this->getOne("select max(`posts`) from `tiki_user_postings`", array());
			$min = $this->getOne("select min(`posts`) from `tiki_user_postings`", array());

			if ($min == 0)
				$min = 1;

			$ids = $this->getOne("select count(*) from `tiki_user_postings`", array());
			$tot = $this->getOne("select sum(`posts`) from `tiki_user_postings`", array());
			$average = $tot / $ids;
			$range1 = ($min + $average) / 2;
			$range2 = ($max + $average) / 2;

			$posts = $this->getOne("select `posts` ".
				"from `tiki_user_postings` where `user`=?",
				array($userName), false);

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
		// If this post was not already found.
		if (!$result->numRows())
		{
			$query = "insert into
				`tiki_comments`(`objectType`, `object`,
						`commentDate`, `userName`, `title`, `data`, `votes`,
						`points`, `hash`, `email`, `website`, `parentId`, `average`, `hits`,
						`type`, `summary`, `smiley`, `user_ip`,
						`message_id`, `in_reply_to`, `approved`, `locked`)
				values ( ?, ?, ?, ?, ?, ?,
						0, 0, ?, ?, ?, ?, 0, 0, ?, ?, 
						?, ?, ?, ?, ?, 'n')";
			$result = $this->query($query, 
					array( $object[0], (string) $object[1],(int) $postDate, $userName,
						$title, $data, $hash, $anonymous_email, $anonymous_website, (int) $parentId, $type,
						$summary, $smiley, $this->get_ip_address(),
						$message_id, (string) $in_reply_to, $approved)
					);
		}

		$threadId = $this->getOne("select `threadId` from
				`tiki_comments` where `hash`=?", array( $hash ) );

		/* Force an index refresh of the data */
		include_once("lib/search/refresh-functions.php");
		refresh_index_comments( $threadId );

		global $prefs;
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
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
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			$contributionlib->assign_contributions($contributions, $threadId, 'comment', $title, '', '');
		}

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('comments', $threadId);
		}
		if ($object[0] == 'forum') {
			$type = 'forum post';
		} else {
			$type = $object[0].' comment';
		}
		$href = $this->getHref($object[0], $object[1], $threadId);
		$this->object_post_save( array('type'=>$type, 'object'=>$threadId, 'description'=>'', 'href'=>$href, 'name'=>$title), array( 'content' => $data ));
		$this->update_comment_links($data, $object[0], $threadId);

		return $threadId;
		//return $return_result;
	}

	// Check if a particular topic exists.
	function check_for_topic( $title, $data ) {
		$hash = md5($title . $data);
		$threadId = $this->getOne("select `threadId` from
				`tiki_comments` where `hash`=?
				order by `threadId` asc", array( $hash ) );
		return $threadId;
	}

	function approve_comment($threadId, $status = 'y')
	{
		if ( $threadId == 0 ) return false;

		$query = "UPDATE `tiki_comments` SET `approved`=? WHERE `threadId`=?";
		return $this->query($query, array($status, (int)$threadId)) !== false;
	}
	function reject_comment($threadId) {
		return $this->approve_comment($threadId, 'r');

	}
	
	function remove_comment($threadId) {
		if ($threadId == 0)
			return false;
		global $prefs;

		$query = "select * from `tiki_comments` where `threadId`=? or `parentId`=?";
		$result = $this->query($query, array((int)$threadId, (int)$threadId));
		while ($res = $result->fetchRow()) {
			if ($res['objectType'] == 'forum') {
				$this->remove_object('forum post', $res['threadId']);
				if ($prefs['feature_actionlog'] == 'y') {
					global $logslib; include_once('lib/logs/logslib.php');
					$logslib->add_action('Removed', $res['object'], 'forum', "comments_parentId=$threadId&amp;del=".strlen($res['data']));
				}
			} else {
				$this->remove_object($res['objectType'].' comment', $res['threadId']);
				if ($prefs['feature_actionlog'] == 'y') {
					global $logslib; include_once('lib/logs/logslib.php');
					$logslib->add_action('Removed', $res['object'], 'comment', 'type='.$res['objectType'].'&amp;del='.strlen($res['data'])."threadId#$threadId");
				}
			}
			if ($prefs['feature_contribution'] == 'y') {
				global $contributionlib;require_once('lib/contribution/contributionlib.php');
				$contributionlib->remove_comment($res['threadId']);
			}
			$query = "delete from `tiki_user_watches` where `object`=? and `type`= ?";
			$this->query($query, array((int)$threadId, 'forum topic'));
			$query = "delete from `tiki_group_watches` where `object`=? and `type`= ?";
			$this->query($query, array((int)$threadId, 'forum topic'));
		}

		$query = "delete from `tiki_comments` where `threadId`=? or `parentId`=?";
		//TODO in a forum, when the reply to a post (not a topic) id deletd, the replies to this post are not deleted

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
		//print("User weight: $user_weight<br />");
		//print("Vote: $vote vote_weight: $vote_weight<br />");

		// Get the user that posted the comment being voted
		$query = "select `userName` from `tiki_comments` where `threadId`=?";
		$comment_user = $this->getOne($query, array( (int) $threadId ) );

		if ($comment_user && ($comment_user == $user)) {
			// The user is voting a comment posted by himself then bail out
			return false;
		}

		//print("Comment user: $comment_user<br />");
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

	function archive_thread($threadId, $parentId = 0) {
		if ( $threadId > 0 && $parentId >= 0 ) {
			$query = 'update `tiki_comments` set `archived`=? where `threadId`=? and `parentId`=?';
			return $this->query($query, array( 'y', (int)$threadId, (int)$parentId ) );
		}
		return false;
	}

	function unarchive_thread($threadId, $parentId = 0) {
		if ( $threadId > 0 && $parentId >= 0 ) {
			$query = 'update `tiki_comments` set `archived`=? where `threadId`=? and `parentId`=?';
			return $this->query($query, array( 'n', (int)$threadId, (int)$parentId ) );
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
		$ret = array();
		$query = "select `forumId`, `mail` as outbound_address from `tiki_forums` where `useMail`=? and `mail` != ''";
		$ret = $this->fetchAll($query, array('y'));
		$query = "select `forumId`, `outbound_address` from `tiki_forums` where `outbound_address` != '' and `outbound_address` is not null";
		$result = $this->fetchAll($query);
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

	/* post a comment
	 * @param string comments_objectId
	 * @param array $params: list of options($_REQUEST)
	 * @return the threadId
	 * @return $feedbacks, $errors */
	function post_in_object($comments_objectId, &$params, &$feedbacks, &$errors) {
		global $smarty, $tiki_p_admin, $tiki_p_admin_comments, $tiki_p_post_comments, $tiki_p_edit_comments, $prefs, $user, $captchalib;

		if (!empty($params['comments_parentId'])) {
			$parent_id = $params['comments_parentId'];
		} else {
			$parent_id = 0;
		}
		if (!($tiki_p_post_comments == 'y')) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('Permission denied'));
			$smarty->display("error.tpl");
			die;
		}
		if (!empty($params['comments_threadId'])) {
			if (!($tiki_p_edit_comments == 'y' || $this->user_can_edit_post($user, $params['comments_threadId']))) {
				$smarty->assign('errortype', 401);
				$smarty->assign('msg', tra('Permission denied'));
				$smarty->display("error.tpl");
				die;
			}
		}
		if ( $prefs['feature_comments_locking'] == 'y' ) {
			if ( $this->is_object_locked($comments_objectId) ) {
				$smarty->assign('msg', tra("Those comments are locked"));
				$smarty->display("error.tpl");
				die;
			}
			$parent_comment_info = $this->get_comment($parent_id);
			if ( $parent_comment_info['locked'] == 'y' ) {
				$smarty->assign('msg', tra("This thread is locked"));
				$smarty->display("error.tpl");
				die;
			}
		}

		if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
			$errors[] = $captchalib->getErrors();
		}

		if ($prefs['feature_contribution'] == 'y' && $prefs['feature_contribution_mandatory_comment'] == 'y' && empty($params['contributions'])) {
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
		if (!isset($params['anonymous_website'])) {
			$params['anonymous_website'] = '';
		}

		if ( isset($params['comments_reply_threadId']) && ! empty($params['comments_reply_threadId']) ) {
			$reply_info = $this->get_comment($params['comments_reply_threadId']);
			$in_reply_to = $reply_info['message_id'];
		} else {
			$in_reply_to = '';
		}
		if ( isset($params['comments_postComment_anonymous']) && ! empty($user) && $prefs['feature_comments_post_as_anonymous'] == 'y' ) {
			$params['comments_postComment'] = $params['comments_postComment_anonymous'];
			$user = '';
		}
		if ($params['comments_threadId'] == 0) { // new post
			$message_id = '';

			$threadId =	$this->post_new_comment($comments_objectId, $parent_id, $user, $params['comments_title'], $params['comments_data'], $message_id, $in_reply_to,
				'n', '', '', isset($params['contributions'])? $params['contributions']: '',	$params['anonymous_name'], '', $params['anonymous_email'], $params['anonymous_website']);

		} elseif ($tiki_p_edit_comments == 'y' || $this->user_can_edit_post($user, $params['comments_threadId'])) {
			$threadId = $params['comments_threadId'];
			$this->update_comment($threadId, $params['comments_title'], '', ($params['comments_data']), 'n', '', '', $comments_objectId, isset($params['contributions'])? $params['contributions']: '');
		}
		if (!empty($errors)) {
			return 0;
		} else {
			$approved = ($tiki_p_admin_comments == 'y' || $prefs['feature_comments_moderation'] != 'y') ? 'y' : 'n';
			if ($approved == 'n') {
				$feedbacks[] = tra('Your message has been queued for approval and will be posted after a moderator approves it.');
			}
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


