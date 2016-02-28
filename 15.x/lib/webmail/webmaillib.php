<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class WebMailLib extends TikiLib
{

	// current account row from database (tiki_user_mail_accounts)
	// [accountId, user, account, pop, current, port, username, pass, msgs, smtp, useAuth, smtpPort, flagsPublic, autoRefresh, imap, mbox, maildir, useSSL, fromEmail]
	var $current_account = array();

	// slightly complicated sub-query to check for public messages
	var $SQL_CLAUSE_FOR_PUBLIC_MAILBOX = '(select count(*) from `tiki_user_mail_accounts` where `tiki_user_mail_accounts`.`accountId`=`tiki_webmail_messages`.`accountId` and `flagsPublic` = \'y\')';

	function remove_webmail_message($current, $user, $msgid)
	{
		$query = "delete from `tiki_webmail_messages` where `mailId`=? and (`user`=? or $this->SQL_CLAUSE_FOR_PUBLIC_MAILBOX)";	// mailId is message-id
		$result = $this->query($query, array($msgid, $user));
	}

	function replace_webmail_message($current, $user, $msgid)
	{

		$query = "select count(*) from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and (`user`=? or $this->SQL_CLAUSE_FOR_PUBLIC_MAILBOX)";

		if ($this->getOne($query, array((int)$current, $msgid, $user)) == 0) {
			$query = "insert into `tiki_webmail_messages`(`accountId`,`mailId`,`user`,`isRead`,`isFlagged`,`isReplied`) values(?,?,?,'n','n','n')";
			$result = $this->query($query, array((int)$current, $msgid, $user));
		}
	}

	function set_mail_flag($current, $user, $msgid, $flag, $value)
	{
		// flag can be: isRead,isFlagged,isReplied, value: y/n

		if ($flag == 'isFlagged') {
			if ($value == 'y') {
				$fMsg = "$user," . mktime() . ", ";
			} else {
				$fMsg = '';
			}
		}

		$query ="SELECT * FROM `tiki_webmail_messages` WHERE `accountId` = ? AND `mailId` = ? AND (`user`=? or $this->SQL_CLAUSE_FOR_PUBLIC_MAILBOX)";
		$result = $this->query($query, array((int)$current, $msgid, $user));
		$row = $result->fetchRow();

		if ($row != NULL) {
			// Update is select found a match
			if ($flag == 'isFlagged') {
				$query ="UPDATE `tiki_webmail_messages` SET `$flag` = '$value', `flaggedMsg` = '$fMsg'" .
									" WHERE `accountId` = ? AND `mailId` = ? AND (`user`=? or $this->SQL_CLAUSE_FOR_PUBLIC_MAILBOX)";
			} else {
				$query ="UPDATE `tiki_webmail_messages` SET `$flag` = '$value'" .
								" WHERE `accountId` = ? AND `mailId` = ? AND (`user`=? or $this->SQL_CLAUSE_FOR_PUBLIC_MAILBOX)";
			}
			$result = $this->query($query, array((int)$current, $msgid, $user));
		} else {
			// Otherwise insert, we have no flags for this massesge yet
			$query = "insert into `tiki_webmail_messages`(`$flag`,`accountId`,`mailId`,`user`,`flaggedMsg`) values (?,?,?,?,?)";
			$result = $this->query($query, array($value, (int)$current, $msgid, $user, $fMsg));
		}
		return true;
	}

	function get_mail_flags($current, $user, $msgid)
	{

		$query = "select `isRead`,`isFlagged`,`isReplied` from `tiki_webmail_messages`" .
							" where `accountId`=? and `mailId`=? and (`user`=? or $this->SQL_CLAUSE_FOR_PUBLIC_MAILBOX)";
		$result = $this->query($query, array((int)$current,$msgid,$user));

		if (!$result->numRows()) {
			return array('n', 'n', 'n');	// seems odd to return a valid array on error?
		}

		$res = $result->fetchRow();
		return array(
				$res["isRead"],
				$res["isFlagged"],
				$res["isReplied"]
		);
	}

	function current_webmail_account($user, $accountId)
	{
		global $tikilib;

		$query = "update `tiki_user_mail_accounts` set `current`='n' where `user`=?";
		$result = $this->query($query, array($user));

		$acc = $this->get_webmail_account($user, $accountId);
		if ($acc && $acc['flagsPublic'] == 'y' && $acc['user'] != $user ) {
			$tikilib->set_user_preference($user, 'mailCurrentAccount', $accountId);
		} else {
			$query = "update `tiki_user_mail_accounts` set `current`='y' where `user`=? and `accountId`=?";
			$this->query($query, array($user,(int)$accountId ));
			$tikilib->set_user_preference($user, 'mailCurrentAccount', 0);
		}
		$this->get_current_webmail_account($user);
	}

	function list_webmail_accounts($user, $offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `flagsPublic` = 'n' and `user`=? and (`account` like ?)";
			$bindvars = array($user, $findesc);
		} else {
			$mid = " where `flagsPublic` = 'n' and `user`=?";
			$bindvars = array($user);
		}

		$query = "select * from `tiki_user_mail_accounts` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_mail_accounts` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_webmail_group_accounts($user, $offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `flagsPublic` <> 'n' and (`account` like ?)";
			$bindvars = array($findesc);
		} else {
			$mid = " where `flagsPublic` <> 'n'";
			$bindvars = array();
		}

		$query = "select * from `tiki_user_mail_accounts` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_mail_accounts` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function count_webmail_accounts($user, $includePublic = true)
	{
		$query_cant = 'SELECT COUNT(*) FROM `tiki_user_mail_accounts` WHERE `user`=?';
		if ($includePublic) {
			$query_cant .= ' OR `flagsPublic` = \'y\'';
		}
		$bindvars = array($user);
		$cant = $this->getOne($query_cant, $bindvars);
		return $cant;
	}

	function replace_webmail_account(
			$accountId,
			$user,
			$account,
			$pop,
			$port,
			$username,
			$pass,
			$msgs,
			$smtp,
			$useAuth,
			$smtpPort,
			$flagsPublic,
			$autoRefresh,
			$imap,
			$mbox,
			$maildir,
			$useSSL,
			$fromEmail)
	{

		// Check the name
		if ($accountId) {
			$query = "update `tiki_user_mail_accounts` set `user`=?, `account`=?, `pop`=?, `port`=?," .
							" `smtpPort`=?, `username`=?, `pass`=?, `smtp`=?, `useAuth`=?, `msgs`=?, `flagsPublic`=?," .
							" `autoRefresh`=? , `imap`=?, `mbox`=?, `maildir`=?, `useSSL`=?, `fromEmail`=?" .
							" where `accountId`=? and `user`=?";

			$bindvars = array(
								$user,
								$account,
								$pop,
								(int)$port,
								(int)$smtpPort,
								$username,
								$pass,
								$smtp,
								$useAuth,
								$msgs,
								$flagsPublic,
								(int)$autoRefresh,
								$imap,
								$mbox,
								$maildir,
								$useSSL,
								$fromEmail,
								(int)$accountId,
								$user
			);
			$result = $this->query($query, $bindvars);
		} else {

			$query = "insert into `tiki_user_mail_accounts`" .
				" (`user`,`account`,`pop`,`port`,`smtpPort`,`username`,`pass`," .
				" `smtp`,`useAuth`,`msgs`,`flagsPublic`,`autoRefresh`, `imap`," .
				" `mbox`, `maildir`, `useSSL`, `fromEmail`)" .
				" values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

			$bindvars = array(
							$user,
							$account,
							$pop,
							$port,
							$smtpPort,
							$username,
							$pass,
							$smtp,
							$useAuth,
							$msgs,
							$flagsPublic,
							$autoRefresh,
							$imap,
							$mbox,
							$maildir,
							$useSSL,
							$fromEmail
			);
			$result = $this->query($query, $bindvars);
		}
		if ($accountId == $this->get_current_webmail_accountId($user)) {
			$this->current_account = array();	// reload
			$this->get_webmail_account($user, $accountId);
		}
		return true;
	}

	function new_webmail_account(
			$user,
			$account,
			$pop,
			$port,
			$username,
			$pass,
			$msgs,
			$smtp,
			$useAuth,
			$smtpPort,
			$flagsPublic,
			$autoRefresh,
			$imap,
			$mbox,
			$maildir,
			$useSSL,
			$fromEmail)
	{

		$query = "insert into `tiki_user_mail_accounts`(`user`,`account`,`pop`,`port`," .
						"`smtpPort`,`username`,`pass`,`smtp`,`useAuth`,`msgs`,`flagsPublic`," .
						"`autoRefresh`, `imap`, `mbox`, `maildir`, `useSSL`, `fromEmail`)" .
						" values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

		$bindvars = array(
						$user,
						$account,
						$pop,
						$port,
						$smtpPort,
						$username,
						$pass,
						$smtp,
						$useAuth,
						$msgs,
						$flagsPublic,
						$autoRefresh,
						$imap,
						$mbox,
						$maildir,
						$useSSL,
						$fromEmail
		);
		$result = $this->query($query, $bindvars);

		$query = 'SELECT `accountID` FROM `tiki_user_mail_accounts` WHERE' .
						' `user`=? AND `account`=? AND `pop`=? AND `port`=? AND `smtpPort`=? AND' .
						' `username`=? AND `pass`=? AND `smtp`=? AND `useAuth`=? AND `msgs`=? AND' .
						' `flagsPublic`=? AND `autoRefresh`=? AND `imap`=? AND `mbox`=? AND' .
						' `maildir`=? AND `useSSL`=? AND `fromEmail`=?';
		$accountID = $this->getOne($query, $bindvars);

		return $accountID;
	}



	function get_current_webmail_accountId($user)
	{
		global $tikilib;

		$pubAc = $tikilib->get_user_preference($user, 'mailCurrentAccount');
		if (empty($pubAc)) {
			$pubAc = 0;
		}
		// check database even if pref is set in case it's changed
		$query = "select accountId from `tiki_user_mail_accounts` where (`current`='y' and `user`=?) or (`flagsPublic`='y' and `accountId`=?)";
		$result = $this->getOne($query, array($user, $pubAc));

		return $result;

	}

	function get_current_webmail_account($user)
	{
		return $this->get_webmail_account($user);
	}

	function remove_webmail_account($user, $accountId)
	{
		$query = "delete from `tiki_user_mail_accounts` where `accountId`=? and `user`=?";
		$result = $this->query($query, array((int)$accountId,$user));
		return true;

	}

	function get_webmail_account($user, $accountId = 0)
	{
		if ($accountId == 0) {
			$accountId = $this->get_current_webmail_accountId($user);
		}

		$query = "select * from `tiki_user_mail_accounts` where `accountId`=? and (`user`=? or `flagsPublic`='y')";
		$result = $this->query($query, array((int)$accountId,$user));

		if (!$result->numRows()) {
			$res = array();
		} else {
			$res = $result->fetchRow();
		}

		if ($accountId == $this->get_current_webmail_accountId($user)) {
			$this->current_account = $res;
		}
		return $res;
	}

	/**
	 * @param string $user
	 * @param string $accountName
	 * @return int accountId or 0 if no single account with that name
	 */
	function get_webmail_account_by_name($user, $accountName)
	{

		$query = "select accountId from `tiki_user_mail_accounts` where `account`=? and (`user`=? or `flagsPublic`='y')";
		$result = $this->fetchAll($query, array( $accountName, $user));

		if (count($result) == 1) {
			return (int) $result[0]['accountId'];
		} else {
			return 0;
		}
	}

	/**
	 * @param $user			current user
	 * @param $accountid	can be 0 (uses current account)
	 * @param $reload		force reload from mail server?
	 * @return array		of partial message headers
	 */
	function refresh_mailbox($user, $accountid, $reload)
	{

		if (!empty($accountid)) {
			$this->current_account = $this->get_webmail_account($user, $accountid);
		} else {
			$this->current_account = $this->get_current_webmail_account($user);
		}

		if (empty($this->current_account)) {
			return Array();
		}

		// start getting mail

		$timeout = -1;
		if ($this->current_account['autoRefresh'] > 0) {
			$timeout = time() - $this->current_account['autoRefresh'];
		}

		$serialized_params = $this->current_account['accountId'] . ':' .
													$this->current_account['user'] . ':' .
													$this->current_account['account'];

		if (isset($_SESSION['webmailinbox'][$serialized_params]) && ((!isset($reload) || !$reload) && (isset($_SESSION['webmailinbox'][$serialized_params]['timestamp']) && $_SESSION['webmailinbox'][$serialized_params]['timestamp'] >  $timeout))) {
			$webmail_list = $_SESSION['webmailinbox'][$serialized_params]['webmail_list'];

		} else {	// no cached list or timed out

			// get mail the zend way

			try {
				$mail = $this->get_mail_storage($this->current_account);

			} catch (Exception $e) {
				// do something better with the error
				unset($_SESSION['webmailinbox'][$serialized_params]['webmail_list']);
				unset($_SESSION['webmailinbox'][$serialized_params]['timestamp']);
				throw($e);
			}

			if (!isset($mail)) {
				return tra('Is the mailbox not initialised??');
			}

			$webmail_list = array();

			foreach ($mail as $messageNum => $message) {

				$headers = $message->getHeaders();		// quicker than the Zend accessors?
				$wmail = Array();	// Tiki Webmail row

				$wmail['from'] = $headers['from'];
				$wmail['to'] = $headers['to'];
				$wmail['subject'] = $headers['subject'];
				$wmail['date'] = $headers['date'];
				$wmail["timestamp"] = strtotime($headers['date']);

				$from = preg_split('/[<>]/', $wmail['from'], -1, PREG_SPLIT_NO_EMPTY);
				$wmail['sender']['name'] = $from[0];
				$wmail['sender']['email'] = $from[1];
				if (empty($wmail['sender']['email'])) {
					$wmail['sender']['email'] = $wmail['sender']['name'];
				} else if (!strstr($wmail['sender']['email'], '@')) {
					$e = $wmail['sender']['name'];
					$wmail['sender']['name'] = $wmail['sender']['email'];
					$wmail['sender']['email'] =  $e;
				}
				$wmail['sender']['name'] = htmlspecialchars($wmail['sender']['name']);

				if (!empty($headers['message-id'])) {
					$wmail['realmsgid'] = preg_replace('/[<>]/', '', $headers['message-id']);
				} else {
					$wmail['realmsgid'] = $wmail['timestamp'] . '.' . $wmail['sender']['email'];	// TODO better?
				}

				if (empty($wmail['subject'])) {
					$wmail['subject'] = '[' . tra('No subject'). ']';
				}
				$wmail['subject'] = htmlspecialchars($wmail['subject']);

				$wmail['msgid'] = $messageNum;

				// TODO
				$wmail['has_attachment'] = false;
				//				$l = $pop3->_cmdList($i);
				$wmail['size'] = 0;

				// Add to output
				$webmail_list[] = $wmail;

			}

			$_SESSION['webmailinbox'][$serialized_params]['webmail_list'] = $webmail_list;
			$_SESSION['webmailinbox'][$serialized_params]['timestamp'] = time();

			$mail->close();
		}		// endif no cached list or timed out

		return $webmail_list;
	}	// end refresh_mailbox()

	function get_mail_storage($webmail_account)
	{
		if (!empty($this->current_account['imap'])) {

			// connecting with Imap
			return new Zend\Mail\Storage\Imap(
				array(
					'host'     => $this->current_account["imap"],
					'user'     => $this->current_account["username"],
					'password' => $this->current_account["pass"],
					'port'	 => $this->current_account["port"],
					'ssl'		 => $this->current_account["useSSL"] == 'y' ? 'SSL' : false)
			);

		} else if (!empty($this->current_account['mbox'])) {

			// connecting with Mbox locally
			return new Zend\Mail\Storage\Mbox(
				array('filename' => $this->current_account["mbox"])
			);

		} else if (!empty($this->current_account['maildir'])) {

			// connecting with Maildir locally
			return new Zend\Mail\Storage\Maildir(
				array('dirname' => $this->current_account["mbox"])
			);

		} else if (!empty($this->current_account['pop'])) {

			// connecting with Pop3
			return new Zend\Mail\Storage\Pop3(
				array(
					'host'     => $this->current_account["pop"],
					'user'     => $this->current_account["username"],
					'password' => $this->current_account["pass"],
					'port'	 => $this->current_account["port"],
					'ssl'		 => $this->current_account["useSSL"] == 'y' ? 'SSL' : false)
			);
		}
		// not returned yet?
		throw new Zend\Mail\Storage\Exception\RuntimeException('No server to check');
	}	// end get_mail_storage()

	/**
	 * @param $part - a part a message or the message itself
	 * @return array with the decoded body of the part
	 */
	private function decode_mail_part($part)
	{
		$result = null;
		$c = $part->getContent();
		// deal with transfer encoding
		try {	// no headerExists() func a part (why?)
			$enc = $part->contentTransferEncoding;
		} catch (Zend\Mail\Exception\ExceptionInterface $e) {
			$enc = '';
		}
		try {	// no headerExists() func a part (why?)
			$ct = $part->contentType;
		} catch (Zend\Mail\Exception\ExceptionInterface $e) {
			$ct = '';
		}
		try {
			switch ($enc) {
				case 'quoted-printable':
					$c = quoted_printable_decode($c);
					break;
				case 'base64':
					$c = base64_decode($c);
					break;
				case '7bit':
				case '8bit':
				default:
					$c = $c;
			}
			// deal with charset
			if (preg_match('/charset\s*=\s*[\'"](.*)[\'"]/i', $ct, $m) && count($m) > 1) {
				$charset = $m[1];
			}
			if (!empty($charset) && strtolower($charset) != 'utf-8') {
				$c = utf8_encode($c);
			}
			$result = array('body' => trim($c), 'contentType' => strtok($ct, ';'));

			if (strtok($ct, ';') == 'text/plain' && !$getAllParts) {
				return $result;
			}
		} catch (Zend\Mail\Exception\ExceptionInterface $e) {
			// ignore?
		}
		return $result;
	}
	/**
	 * @param $user			current user
	 * @param $accountid	can be 0
	 * @param $msgId		message to get
	 * @param $getAllParts	if false returns the plain text body as a string - if true return an array of all parts
	 * @return string/array	the message body/bodies
	 */
	function get_mail_content($user, $accountid, $msgId, $getAllParts = false)
	{
		global $webmail_account;	// TODO remove global and refactor

		if (!empty($accountid)) {
			$this->current_account = $this->get_webmail_account($user, $accountid);
		} else {
			$this->current_account = $this->get_current_webmail_account($user);
		}

		if (empty($this->current_account)) {
			return '';
		}

		// connecting with Zend
		try {
			$mail = $this->get_mail_storage($this->current_account);
		} catch (Exception $e) {
			// do something better with the error
			return '';
		}

		if (empty($mail)) {
			return '';
		}

		$foundPart = null;
		$message = $mail->getMessage($msgId);
		$cont = array();

		// parse parts - initially from http://framework.zend.com/manual/en/zend.mail.read.html
		foreach (new RecursiveIteratorIterator($message) as $part) {
			$cont[] = $this->decode_mail_part($part);
		}
		if (empty($cont)) {
			$cont[] = $this->decode_mail_part($message);	// no parts, so try the whole message
		}
		if (empty($cont)) {
			$cont[] = array('body' => tra('No mail body found'), 'contentType' => 'text/plain');
		}

		return $getAllParts ? $cont : $cont[0];
	}	// end get_mail_content()

} # class WebMailLib
$webmaillib = new WebMailLib;
