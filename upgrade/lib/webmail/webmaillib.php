<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class WebMailLib extends TikiLib {
	
	var $current_account_group='n';
	
	function WebMailLib($db) {
		parent::TikiLib($db);
	}

	function remove_webmail_message($current, $user, $msgid) {
		$query = "delete from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and `user`=?";
		$result = $this->query($query, array((int)$current,$msgid,$user));
	}

	function replace_webmail_message($current,$user,$msgid) {
//		print($current,$user,$msgid);
		$query = "select count(*) from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and `user`=?";

		if ($this->getOne($query,array((int)$current,$msgid,$user)) == 0) {
			$query = "insert into `tiki_webmail_messages`(`accountId`,`mailId`,`user`,`isRead`,`isFlagged`,`isReplied`) values(?,?,?,'n','n','n')";
			$result = $this->query($query,array((int)$current,$msgid,$user));
		}
	}

	function set_mail_flag($current, $user, $msgid, $flag, $value) {
		// flag can be: isRead,isFlagged,isReplied, value: y/n

		//MatWho 16/09/08 - Fixed mailId removed (int) as mail ids are strings
		$query ="SELECT * FROM tiki_webmail_messages WHERE accountId = ? AND mailId = ? AND user = ?";
		$result = $this->query($query,array((int)$current,$msgid,$user));
		$foundMatch = $result->fetchInto($row, DB_FETCHMODE_ASSOC);
		
		if ($row != NULL) {
		    // Update is select found a match
			$query ="UPDATE tiki_webmail_messages SET `$flag` = '$value' WHERE accountId = ? AND mailId = ? AND user = ?";
			$result = $this->query($query,array((int)$current,$msgid,$user));
		} else {
			// Otherwise insert, we have no flags for this massesge yet
			$query = "insert into `tiki_webmail_messages`(`$flag`,`accountId`,`mailId`,`user`) values (?,?,?,?)";
			$result = $this->query($query,array($value,(int)$current,$msgid,$user));
		}
		return true;
	}

	function get_mail_flags($current, $user, $msgid) {
		
		if ($this->current_account_group=='y'){
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

	function get_current_webmail_account($user) {
		$query = "select * from `tiki_user_mail_accounts` where `current`='y' and `user`=?";
		$result = $this->query($query, array($user));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
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
} # class WebMailLib

$webmaillib = new WebMailLib($dbTiki);

?>
