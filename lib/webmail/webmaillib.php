<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

define('GROUP_USER_NAME',"groupemailuser");
define('GROUP_USER_ID', 999999);


class WebMailLib extends TikiLib {
	
	var $current_account_group='';
	
	function WebMailLib($db) {
		parent::TikiLib($db);
	}

	function remove_webmail_message($current, $user, $msgid) {
		
//		if ($this->is_current_webmail_account_public ($user)){
//			print("group-rem");
//			$user=GROUP_USER_NAME;
//			$current=GROUP_USER_ID;
//		}
		
//		$query = "delete from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and `user`=?";
		$query = "delete from `tiki_webmail_messages` where `mailId`=?";	// FIXME - looks like this deletes other users' messages - $msgid is the index in a single mailbox afaik (jonnyb)
		$result = $this->query($query, array($msgid));
	}

	function replace_webmail_message($current,$user,$msgid) {
//		print($current,$user,$msgid);

		if ($this->is_current_webmail_account_public ($user)){
			$user=GROUP_USER_NAME;
			$current=GROUP_USER_ID;
		}

		$query = "select count(*) from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and `user`=?";

		if ($this->getOne($query,array((int)$current,$msgid,$user)) == 0) {
			$query = "insert into `tiki_webmail_messages`(`accountId`,`mailId`,`user`,`isRead`,`isFlagged`,`isReplied`) values(?,?,?,'n','n','n')";
			$result = $this->query($query,array((int)$current,$msgid,$user));
		}
	}

	function set_mail_flag($current, $user, $msgid, $flag, $value) {
		// flag can be: isRead,isFlagged,isReplied, value: y/n

//print($this->current_account_group);
		if ($flag == 'isFlagged') {
			if($value == 'y'){
				$fMsg = "$user,". mktime().", ";
			} else {
				$fMsg = '';
			}
		}

		if ($this->is_current_webmail_account_public ($user)){
			$user=GROUP_USER_NAME;
			$current=GROUP_USER_ID;
		}

		//MatWho 16/09/08 - Fixed mailId removed (int) as mail ids are strings
		$query ="SELECT * FROM tiki_webmail_messages WHERE accountId = ? AND mailId = ? AND user = ?";
		$result = $this->query($query,array((int)$current,$msgid,$user));
		$foundMatch = $result->fetchInto($row, DB_FETCHMODE_ASSOC);
		
		if ($row != NULL) {
		    // Update is select found a match
			if ($flag == 'isFlagged'){
				$query ="UPDATE tiki_webmail_messages SET `$flag` = '$value', `flaggedMsg` = '$fMsg' WHERE accountId = ? AND mailId = ? AND user = ?";
			} else {
				$query ="UPDATE tiki_webmail_messages SET `$flag` = '$value' WHERE accountId = ? AND mailId = ? AND user = ?";
			}
			$result = $this->query($query,array((int)$current,$msgid,$user));
		} else {
			// Otherwise insert, we have no flags for this massesge yet
			$query = "insert into `tiki_webmail_messages`(`$flag`,`accountId`,`mailId`,`user`,`flaggedMsg`) values (?,?,?,?,?)";
			$result = $this->query($query,array($value,(int)$current,$msgid,$user,$fMsg));
		}
		return true;
	}

	function get_mail_flags($current, $user, $msgid) {
		if ($this->is_current_webmail_account_public ($user)){
			$user=GROUP_USER_NAME;
			$current=GROUP_USER_ID;
		}
		
 		if ($this->is_current_webmail_account_public ($user)){
			$query = "select `isRead`,`isFlagged`,`isReplied` from `tiki_webmail_messages` where `mailId`=?";
			$result = $this->query($query, array($msgid));
		} else {
			$query = "select `isRead`,`isFlagged`,`isReplied` from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and user=?";
			$result = $this->query($query, array((int)$current,$msgid,$user));
		}
		
		if (!$result->numRows()) {
			return array(
				'n',
				'n',
				'n'
			);
		}

		$res = $result->fetchRow();
		return array(
			$res["isRead"],
			$res["isFlagged"],
			$res["isReplied"]
		);
	}

	function current_webmail_account($user, $accountId) {
		$query = "update `tiki_user_mail_accounts` set `current`='n' where `user`=?";
		$result = $this->query($query, array($user));

		$query = "update `tiki_user_mail_accounts` set `current`='y' where `user`=? and `accountId`=?";
		$result = $this->query($query, array($user,(int)$accountId ));
	}

	function list_webmail_accounts($user, $offset, $maxRecords, $sort_mode, $find) {
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `flagsPublic` <> 'y' and `user`=? and (`account` like ?)";
			$bindvars = array($user, $findesc);
		} else {
			$mid = " where `flagsPublic` <> 'y' and `user`=?";
			$bindvars = array($user);
		}

		$query = "select * from `tiki_user_mail_accounts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_mail_accounts` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_webmail_group_accounts($user, $offset, $maxRecords, $sort_mode, $find) {
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `flagsPublic` <> 'n' and `user`=? and (`account` like ?)";
			$bindvars = array($user, $findesc);
		} else {
			$mid = " where `flagsPublic` <> 'n' and `user`=?";
			$bindvars = array($user);
		}

		$query = "select * from `tiki_user_mail_accounts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_mail_accounts` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}


// MatWho 16/09/08 added flagsPublic
	function replace_webmail_account($accountId, $user, $account, $pop, $port, $username, $pass, $msgs, $smtp, $useAuth, $smtpPort, $flagsPublic, $autoRefresh)
		{
		// Check the name
		if ($accountId) {
			$query = "update `tiki_user_mail_accounts` set `user`=?, `account`=?, `pop`=?, `port`=?, `smtpPort`=?, `username`=?, `pass`=?, `smtp`=?, `useAuth`=?, `msgs`=?, `flagsPublic`=?, `autoRefresh`=? where `accountId`=? and `user`=?";
			$bindvars = array($user,$account,$pop,(int)$port,(int)$smtpPort,$username,$pass,$smtp,$useAuth,$msgs,$flagsPublic,(int)$autoRefresh,(int)$accountId, $user);
			$result = $this->query($query,$bindvars);
		} else {
			$query = "delete from `tiki_user_mail_accounts` where `accountId`=? and `user`=?";
			$bindvars = array((int)$accountId, $user);
			$result = $this->query($query, $bindvars, -1, -1, false);

			$query = "insert into `tiki_user_mail_accounts`(`user`,`account`,`pop`,`port`,`smtpPort`,`username`,`pass`,`smtp`,`useAuth`,`msgs`,`flagsPublic`,`autoRefresh`) values(?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars = array($user,$account,$pop,$port,$smtpPort,$username,$pass,$smtp,$useAuth,$msgs,$flagsPublic,$autoRefresh);
			$result = $this->query($query, $bindvars);
		}
		return true;
	}

	function new_webmail_account($user, $account, $pop, $port, $username, $pass, $msgs, $smtp, $useAuth, $smtpPort, $flagsPublic, $autoRefresh)
		{

			$query = "insert into `tiki_user_mail_accounts`(`user`,`account`,`pop`,`port`,`smtpPort`,`username`,`pass`,`smtp`,`useAuth`,`msgs`,`flagsPublic`,`autoRefresh`) values(?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars = array($user,$account,$pop,$port,$smtpPort,$username,$pass,$smtp,$useAuth,$msgs,$flagsPublic,$autoRefresh);
			$result = $this->query($query, $bindvars);


			$accountID = $this->getOne("SELECT `accountID` FROM `tiki_user_mail_accounts` WHERE `user`=$user AND `account`=$account AND `pop`=$pop AND `port`=$port AND `smtpPort`=smtpPort AND `username`=username AND `pass`=$pass AND `smtp`=$smpt AND `useAuth`=$useAuth AND `msgs`=$msg AND `flagsPublic`=$flagesPublic AND `autoRefresh`=$autoRefresh");

		return $accountID;
	}
	
	

	function get_current_webmail_account($user) {
		$query = "select * from `tiki_user_mail_accounts` where `current`='y' and `user`=?";
		$result = $this->query($query, array($user));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		$this->current_account_group = $res["flagsPublic"];
		return $res;
	}
	
	function is_current_webmail_account_public ($user) {
		if($this->current_account_group == ''){
			$query = "select * from `tiki_user_mail_accounts` where `current`='y' and `user`=?";
			$result = $this->query($query, array($user));
			if (!$result->numRows())
				return false;
			$res = $result->fetchRow();
			$this->current_account_group = $res["flagsPublic"];
			return $res["flagsPublic"];
		} else {
			if ($this->current_account_group=='y'){
				return true;
			} else {
				return false;
			}
		}
	}

	function remove_webmail_account($user, $accountId) {
		$query = "delete from `tiki_user_mail_accounts` where `accountId`=? and `user`=?";
		$result = $this->query($query, array((int)$accountId,$user));
		return true;

	}

	function get_webmail_account($user, $accountId) {
		$query = "select * from `tiki_user_mail_accounts` where `accountId`=? and `user`=?";
		$result = $this->query($query, array((int)$accountId,$user));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}
	
	/**
	 * @param $user			current user
	 * @param $accountid	can be 0
	 * @param $reload		force reload from mail server?
	 * @return array		of partial message headers
	 */
	function refresh_mailbox($user, $accountid, $reload) {
		global $webmail_account;	// TODO remove global
		
		if (!empty($accountid)) {
			$webmail_account = $this->get_webmail_account($user, $accountid);
		} else {
			$webmail_account = $this->get_current_webmail_account($user);
		}
		
		if (empty($webmail_account)) {
			return Array();
		}
		
		// start getting mail
		
		$timeout = -1;
		if ($webmail_account['autoRefresh'] > 0) {
			$timeout = time() - $webmail_account['autoRefresh'];
		}
		
		$serialized_params = $webmail_account['accountId'].':'.$webmail_account['user'].':'.$webmail_account['account'];
		
		if (isset($_SESSION['webmailinbox'][$serialized_params]) && ((!isset($reload) || !$reload) || (isset($_SESSION['webmailinbox'][$serialized_params]['timestamp']) && $_SESSION['webmailinbox'][$serialized_params]['timestamp'] >  $timeout))) {
			$webmail_list = $_SESSION['webmailinbox'][$serialized_params]['webmail_list'];
		
		} else {	// no cached list or timed out

			require_once('lib/core/lib/Zend/Log.php');
//			require_once('lib/core/lib/Zend/Log/Writer/Stream.php');
//			$writer = new Zend_Log_Writer_Stream('/tmp/zend.log');
			require_once('lib/core/lib/Zend/Log/Writer/Null.php');
			$writer = new Zend_Log_Writer_Null;						// stub disabling logging - still needs to be faster...
			$logger = new Zend_Log($writer);
			$ts = microtime(true);
			$logger->log('Init mail process '.$ts, Zend_Log::INFO);
			
			// get mail the zend way
			
			// connecting with Pop3
			require_once('lib/core/lib/Zend/Mail/Storage/Pop3.php');
			try {
				$mail = new Zend_Mail_Storage_Pop3(
					array('host'     => $webmail_account["pop"],
			              'user'     => $webmail_account["username"],
			              'password' => $webmail_account["pass"]));
			} catch (Exception $e) {
				// do something better with the error
				$logger->log('Zend_Mail_Storage_Pop3 failed: '.$e->messsage.' '.(microtime(true)-$ts), Zend_Log::INFO);
				return Array();
			}
			
			if (empty($mail)) {
				return Array();
			}
			$logger->log('Got mails '.count($mail).' '.(microtime(true)-$ts), Zend_Log::INFO);
			
			$webmail_list = array();
						
			foreach ($mail as $messageNum => $message) {
			//for ($messageNum = 1; $messageNum <= count($mail); $messageNum++) {
			//	$message = $mail[$messageNum];  (no quicker)
			
				$logger->log('Start mail process '.$messageNum.' '.(microtime(true)-$ts), Zend_Log::INFO);
				$headers = $message->getHeaders();		// quicker than the Zend accessors?
				$wmail = Array();	// Tiki Webmail row
				
				$wmail['from'] = $headers['from'];
				$wmail['to'] = $headers['to'];
				$wmail['subject'] = decode_subject_utf8($headers['subject']);
				$wmail['date'] = $headers['date'];
				$wmail["timestamp"] = strtotime($headers['date']);
				
				$mail->noop(); // keep alive
				$from = preg_split('/[<>]/', $wmail['from'], -1,PREG_SPLIT_NO_EMPTY);
				$wmail['sender']['name'] = $from[0];
				$wmail['sender']['email'] = $from[1];
				if (empty($wmail['sender']['email'])) {
					$wmail['sender']['email'] = $wmail['sender']['name'];
				} else if (!strstr($wmail['sender']['email'], '@')) {
					$e = $wmail['sender']['name'];
					$wmail['sender']['name'] = $wmail['sender']['email'];
					$wmail['sender']['email'] =  $wmail['sender']['name'];
				}
				$wmail['sender']['name'] = htmlspecialchars($wmail['sender']['name']);

//				$l = $pop3->_cmdList($i);
//				$wmail['size'] = $l['size'];

				if (!empty($headers['message-id'])) {
					$wmail['realmsgid'] = ereg_replace('[<>]','', $headers['message-id']);
				} else {
					$wmail['realmsgid'] = $wmail['timestamp'].'.'.$wmail['sender']['email'];	// TODO better?
				}
				
				if (empty($wmail['subject'])) {
					$wmail['subject'] = '[' . tra('No subject'). ']';
				}
				$wmail['subject'] = htmlspecialchars($wmail['subject']);
					
				$wmail['msgid'] = $messageNum;
				$webmail_list[] = $wmail;
//				if ($messageNum > count($mail) - 1) {
//					$a = 1;	// for debugging
//				}
				$mail->noop(); // keep alive
				
				$logger->log('End mail process   '.$messageNum.' '.(microtime(true)-$ts), Zend_Log::INFO);
			}
				
			$_SESSION['webmailinbox'][$serialized_params]['webmail_list'] = $webmail_list;
			$_SESSION['webmailinbox'][$serialized_params]['timestamp'] = time();
			
			$mail->close();
		}		// endif no cached list of timed out
	
		return $webmail_list;
	}
	
	/**
	 * @param $user			current user
	 * @param $accountid	can be 0
	 * @param $msgId		message to get
	 * @return string		the message body
	 */
	function get_mail_content($user, $accountid, $msgId) {
		global $webmail_account;	// TODO remove global and refactor
		
		if (!empty($accountid)) {
			$webmail_account = $this->get_webmail_account($user, $accountid);
		} else {
			$webmail_account = $this->get_current_webmail_account($user);
		}
		
		if (empty($webmail_account)) {
			return '';
		}
		
		// get single mail			
		// connecting with Pop3
		require_once('lib/core/lib/Zend/Mail/Storage/Pop3.php');
		try {
			$mail = new Zend_Mail_Storage_Pop3(
				array('host'     => $webmail_account["pop"],
		              'user'     => $webmail_account["username"],
		              'password' => $webmail_account["pass"]));
		} catch (Exception $e) {
			// do something better with the error
			return '';
		}
		
		if (empty($mail)) {
			return '';
		}
		
//		$message = $mail[$msgId];
//		$cont = $message->getContent();
//		
//		$ct = $message->contentType;
//		if (preg_match('/boundary=[\'"](.*)[\'"]/', $ct, $m) == 1) {
//			$boundary = $m[1];
//			include_once('lib/core/lib/Zend/Mime/Message.php');
//			$zmm = Zend_Mime_Message::createFromMessage($cont, $boundary);
//		}
				
		// output first text/plain part - from http://framework.zend.com/manual/en/zend.mail.read.html
		$foundPart = null;
		foreach (new RecursiveIteratorIterator($mail->getMessage($msgId)) as $part) {
		    try {
		        if (strtok($part->contentType, ';') == 'text/plain') {
		            $foundPart = $part;
		            break;
		        }
		    } catch (Zend_Mail_Exception $e) {
		        // ignore
		    }
		}
		$c = '';
		if (!empty($foundPart)) {
			$c = $foundPart->getContent();
			$enc = $foundPart->contentTransferEncoding;
			if (!empty($enc)) {
				$c = $this->decodeBody($c, $enc);
			}
//			if ($enc = 'quoted-printable') {
//				include_once('lib/core/lib/Zend/Mime/Decode.php');
//				ini_set('iconv.internal_encoding', 'UTF-8');
//				$c = Zend_Mime_Decode::decodeQuotedPrintable($c);
//			}
		} else {
			$message = $mail[$msgId];
			$c = $message->getContent();
			$enc = '';
		}

//		require_once ("lib/mail/mimelib.php");	// Can't seem to get anything in Zend to do this :(
//		$c = mime::decodeBody($c, $enc);		// blows up on quoted-printable :(
		
		return $c;
	}
	
	// WARNING - copied from mimelib - temp fix
	function decodeBody($input, $encoding = '7bit') {
		switch ($encoding) {
		case '7bit':
			return $input;
			break;
		case 'quoted-printable':
			$input = preg_replace("/=\r?\n/", '', $input);
			if (preg_match_all('/=[A-Z0-9]{2}/', $input, $matches)) {
				$matches = array_unique($matches[0]);
				foreach ($matches as $value) {
					$input = str_replace($value, chr(hexdec(substr($value, 1))), $input);
				}
			}
			return $input;
			break;
		case 'base64':
			return base64_decode($input);
			break;
		default:
			return $input;
		}
	}

	
} # class WebMailLib
$webmaillib = new WebMailLib($dbTiki);
