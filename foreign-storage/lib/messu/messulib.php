<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class Messu extends TikiLib
{

	/**
	 * Put sent message to 'sent' box
	 */
	function save_sent_message($user, $from, $to, $cc, $subject, $body, $priority, $replyto_hash='') {
		global $smarty, $userlib, $prefs;

		$subject = strip_tags($subject);
		$body = strip_tags($body, '<a><b><img><i>');
		// Prevent duplicates
		$hash = md5($subject . $body);

		if ($this->getOne("select count(*) from `messu_sent` where `user`=? and `user_from`=? and `hash`=?",array($user,$from,$hash))) {
			return false;
		}

		$query = "insert into `messu_sent`(`user`,`user_from`,`user_to`,`user_cc`,`subject`,`body`,`date`,`isRead`,`isReplied`,`isFlagged`,`priority`,`hash`,`replyto_hash`) values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->query($query,array($user,$from,$to,$cc,$subject,$body,(int) $this->now,'n','n','n',(int) $priority,$hash,$replyto_hash));

		return true;
	}

	/**
	 * Send a message to a user
	 */
	function post_message($user, $from, $to, $cc, $subject, $body, $priority, $replyto_hash='', $replyto_email='', $bcc_sender = '') {
		global $smarty, $userlib, $prefs;

		$subject = strip_tags($subject);
		$body = strip_tags($body, '<a><b><img><i>');
		// Prevent duplicates
		$hash = md5($subject . $body);

		if ($this->getOne("select count(*) from `messu_messages` where `user`=? and `user_from`=? and `hash`=?",array($user,$from,$hash))) {
			return false;
		}
		
		$query = "insert into `messu_messages`(`user`,`user_from`,`user_to`,`user_cc`,`subject`,`body`,`date`,`isRead`,`isReplied`,`isFlagged`,`priority`,`hash`,`replyto_hash`) values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->query($query,array($user,$from,$to,$cc,$subject,$body,(int) $this->now,'n','n','n',(int) $priority,$hash,$replyto_hash));

		// Now check if the user should be notified by email
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $this->httpPrefix( true ). $foo["path"];
		$machine = str_replace('messu-compose', 'messu-mailbox', $machine);
		if ($this->get_user_preference($user, 'minPrio', 6) <= $priority) {
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			$email = $userlib->get_user_email($user);
			if ($email) {
				include_once('lib/webmail/tikimaillib.php');
				$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
				$smarty->assign('mail_machine', $machine);
				$smarty->assign('mail_date', $this->now);
				$smarty->assign('mail_user', stripslashes($user));
				$smarty->assign('mail_from', stripslashes($from));
				$smarty->assign('mail_subject', stripslashes($subject));
				$smarty->assign('mail_body', stripslashes($body));
				$mail = new TikiMail($user);
				$lg = $this->get_user_preference($user, 'language', $prefs['site_language']);
				if (empty($subject)) {
					$s = $smarty->fetchLang($lg, 'mail/messu_message_notification_subject.tpl');
					$mail->setSubject(sprintf($s, $_SERVER["SERVER_NAME"]));
				} else {
					$mail->setSubject($subject);
				}
				$mail_data = $smarty->fetchLang($lg, 'mail/messu_message_notification.tpl');
				$mail->setText($mail_data);
				
				$from_email = $userlib->get_user_email($from);
				if ($bcc_sender === 'y' && !empty($from_email)) {
					$mail->setHeader("Bcc", $from_email);
				}
				if ($replyto_email !== 'y' && $userlib->get_user_preference($from,'email is public','n') == 'n') {
					$from_email = '';	// empty $from_email if not to be used - saves getting it twice
				}
				if (!empty($from_email)) {
					$mail->setHeader("Reply-To", $from_email);
				}
				if (!empty($prefs['sender_email'])) {
					$mail->setHeader("From", $prefs['sender_email']);
				} else if (!empty($from_email)) {
					$mail->setHeader("From", $from_email);
				}

				if (!$mail->send(array($email), 'mail'))
					return false; //TODO echo $mail->errors;
			}
		}
		return true;
	}

	/**
	 * Get a list of messages from users mailbox or users mail archive (from
	 * which depends on $dbsource)
	 */
	function list_user_messages($user, $offset, $maxRecords, $sort_mode, $find, $flag = '', $flagval = '', $prio = '', $dbsource, $replyto_hash='', $orig_or_reply='r') {
		if ($dbsource=='') $dbsource="messages";
		$bindvars = array($user);
		$mid="";
		if ($prio) {
			$mid = " and priority=? ";
			$bindvars[] = $prio;
		}
		if ($replyto_hash) {
			// find replies
			if ($orig_or_reply == 'r') {
				$mid .= " and replyto_hash=? ";
			// find original for the reply
			} else {
				$mid .= " and hash=? ";
			}
			$bindvars[] = $replyto_hash;
		}
		if ($flag) {
			// Process the flags
			$mid.= " and `$flag`=? ";
			$bindvars[] = $flagval;
		}
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid.= " and (`subject` like ? or `body` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$query = "select * from `messu_".$dbsource."` where `user`=? $mid order by ".$this->convertSortMode($sort_mode).",".$this->convertSortMode("msgId_desc");
		$query_cant = "select count(*) from `messu_".$dbsource."` where `user`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["len"] = strlen($res["body"]);

			if (empty($res['subject']))
				$res['subject'] = tra('NONE');

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/**
	 * Get the number of messages in the users mailbox or mail archive (from
	 * which depends on $dbsource)
	 */
	function count_messages($user, $dbsource='messages') {
		if ($dbsource=='') $dbsource="messages";
		$bindvars = array($user);
		$query_cant = "select count(*) from `messu_".$dbsource."` where `user`=?";
		$cant = $this->getOne($query_cant,$bindvars);
		return $cant;
	}

	/**
	 * Update message flagging
	 */ 
	function flag_message($user, $msgId, $flag, $val, $dbsource="messages") {
		if (!$msgId)
			return false;
		if ($dbsource=='') $dbsource="messages";
		$query = "update `messu_".$dbsource."` set `$flag`=? where `user`=? and `msgId`=?";
		$this->query($query,array($val,$user,(int)$msgId));
	}
	
	/**
	 * Mark a message as replied
	 */
	function mark_replied($user, $replyto_hash, $dbsource="sent") {
		if ((!$replyto_hash) || ($replyto_hash==''))
			return false;
		if ($dbsource=='') $dbsource="sent";
		$query = "update `messu_".$dbsource."` set `isReplied`=? where `user`=? and `hash`=?";
		$this->query($query,array('y', $user, $replyto_hash));
	}	  

	/**
	 * Delete message from mailbox or users mail archive (from which depends on
	 * $dbsource)
	 */
	function delete_message($user, $msgId, $dbsource="messages") {
		if (!$msgId)
			return false;
		if ($dbsource=='') $dbsource="messages";
		$query = "delete from `messu_".$dbsource."` where `user`=? and `msgId`=?";
		$this->query($query,array($user,(int)$msgId));
	}

	/**
	 * Move message from mailbox to users mail archive
	 */
	function archive_message($user, $msgId, $dbsource="messages") {
		if (!$msgId)
			return false;
		if ($dbsource=='') $dbsource="messages";
		$query = "insert into `messu_archive` select * from `messu_".$dbsource."` where `user`=? and `msgId`=?";
		$this->query($query,array($user,(int)$msgId));

		$query = "delete from `messu_".$dbsource."` where `user`=? and `msgId`=?";
		$this->query($query,array($user,(int)$msgId));
	}

	/**
	 * Move read message older than x days from mailbox to users mail archive
	 */
	function archive_messages($user, $days, $dbsource="messages") {
		if ($days<1)
			return false;
		if ($dbsource=='') $dbsource="messages";
		$age = $this->now - ($days * 3600 * 24);
		
		// TODO: only move as much msgs into archive as there is space left in there
		$query = "insert into `messu_archive` select * from `messu_".$dbsource."` where `user`=? and `isRead`=? and `date`<=?";
		$this->query($query,array($user, 'y',(int)$age));

		$query = "delete from `messu_".$dbsource."` where `user`=? and `isRead`=? and `date`<=?";
		$this->query($query,array($user, 'y',(int)$age));
	}

	/**
	 * Move forward to the next message and get it from the database
	 */ 
	function get_next_message($user, $msgId, $sort_mode, $find, $flag, $flagval, $prio, $dbsource="messages") {
		if (!$msgId)
			return 0;
		if ($dbsource=='') $dbsource="messages";
		
		$mid = "";
		$bindvars = array($user,(int)$msgId);
		if ($prio) {
			$mid.= " and priority=? ";
			$bindvars[] = $prio;
		}

		if ($flag) {
			// Process the flags
			$mid.= " and `$flag`=? ";
			$bindvars[] = $flagval;
		}
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid.= " and (`subject` like ? or `body` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$query = "select min(`msgId`) as `nextmsg` from `messu_".$dbsource."` where `user`=? and `msgId` > ? $mid";
		$result = $this->query($query,$bindvars,1,0);
		$res = $result->fetchRow();

		if (!$res)
			return false;

		return $res['nextmsg'];
	}

	/**
	 * Move backward to the next message and get it from the database
	 */ 
	function get_prev_message($user, $msgId, $sort_mode, $find, $flag, $flagval, $prio, $dbsource="messages") {
		if (!$msgId)
			return 0;
		if ($dbsource=='') $dbsource="messages";
		
		$mid = "";
		$bindvars = array($user,(int)$msgId);
		if ($prio) {
			$mid.= " and priority=? ";
			$bindvars[] = $prio;
		}

		if ($flag) {
			// Process the flags
			$mid.= " and `$flag`=? ";
			$bindvars[] = $flagval;
		}
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid.= " and (`subject` like ? or `body` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$query = "select max(`msgId`) as `prevmsg` from `messu_".$dbsource."` where `user`=? and `msgId` < ? $mid";
		$result = $this->query($query,$bindvars,1,0);
		$res = $result->fetchRow();

		if (!$res)
			return false;

		return $res['prevmsg'];
	}

	/**
	 * Get a message from the users mailbox or his mail archive (from which
	 * depends on $dbsource)
	 */
	 function get_message($user, $msgId, $dbsource='messages') {
		if ($dbsource=='') $dbsource="messages";
		$bindvars = array($user,(int)$msgId);
		$query = "select * from `messu_".$dbsource."` where `user`=? and `msgId`=?";
		$result = $this->query($query,$bindvars);
		$res = $result->fetchRow();
		$res['parsed'] = $this->parse_data($res['body']);
		$res['len'] = strlen($res['parsed']);

		if (empty($res['subject']))
			$res['subject'] = tra('NONE');

		return $res;
	}

	/**
	 * Get message from the users mailbox or his mail archive (from which
	 * depends on $dbsource)
	 */
	 function get_messages($user, $dbsource='messages', $subject='', $to='', $from='') {
		if ($dbsource=='') $dbsource="messages";
		$bindvars[] = array($user);

		$mid = "";

		// find mails with a specific subject
		if ($subject<>'') {
			$findesc = '%'.$subject.'%';		
			$bindvars[] = $findesc;
			$mid.= " and `subject` like ?";
		}
		// find mails to a specific user (to, cc, bcc)
		if ($to<>'') {
			$findesc = '%'.$to.'%';		
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$mid.= " and (`user_to` like ? or `user_cc` like ? or `user_bcc` like ?)";
		}
		// find mails from a specific user
		if ($from<>'') {
			$findesc = '%'.$from.'%';		
			$bindvars[] = $findesc;
			$mid.= " and `user_from` like ?";
		}
		$query = "select * from `messu_".$dbsource."` where `user`=? $mid";
		
		$result = $this->query($query,$bindvars);
		while ($res = $result->fetchRow()) {
			$res['parsed'] = $this->parse_data($res['body']);
			$res['len'] = strlen($res['parsed']);
			if (empty($res['subject']))
				$res['subject'] = tra('NONE');
			$ret[] = $res;
		}
		return $ret;
	}

}
$messulib = new Messu;
