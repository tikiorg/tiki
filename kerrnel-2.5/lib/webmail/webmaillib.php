<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class WebMailLib extends TikiLib {
	function WebMailLib($db) {
		parent::TikiLib($db);
	}

	function remove_webmail_message($current, $user, $msgid) {
		$query = "delete from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and `user`=?";
		$result = $this->query($query, array((int)$current,(int)$msgid,$user));
	}

	function replace_webmail_message($current,$user,$msgid) {
		$query = "select count(*) from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and `user`=?";

		if ($this->getOne($query,array((int)$current,(int)$msgid,$user)) == 0) {
			$query = "insert into `tiki_webmail_messages`(`accountId`,`mailId`,`user`,`isRead`,`isFlagged`,`isReplied`) values(?,?,?,'n','n','n')";
			$result = $this->query($query,array((int)$current,(int)$msgid,$user));
		}
	}

	function set_mail_flag($current, $user, $msgid, $flag, $value) {
		// flag can be: isRead,isFlagged,isReplied, value: y/n
		$query = "delete from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and `user`=?";
		$result = $this->query($query,array((int)$current,(int)$msgid,$user));

		$query = "insert into `tiki_webmail_messages`(`$flag`,`accountId`,`mailId`,`user`) values (?,?,?,?)";
		$result = $this->query($query,array($value,(int)$current,(int)$msgid,$user));
		return true;
	}

	function get_mail_flags($current, $user, $msgid) {
		$query = "select `isRead`,`isFlagged`,`isReplied` from `tiki_webmail_messages` where `accountId`=? and `mailId`=? and user=?";
		$result = $this->query($query, array((int)$current,(int)$msgid,$user));

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

			$mid = " where `user`=? and (`account` like ?)";
			$bindvars = array($user, $findesc);
		} else {
			$mid = " where `user`=?";
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

	function replace_webmail_account($accountId, $user, $account, $pop, $port, $username, $pass, $msgs, $smtp, $useAuth, $smtpPort)
		{
		// Check the name
		if ($accountId) {
			$query = "update `tiki_user_mail_accounts` set `user`=?, `account`=?, `pop`=?, `port`=?, `smtpPort`=?, `username`=?, `pass`=?, `smtp`=?, `useAuth`=?, `msgs`=? where `accountId`=? and `user`=?";
			$bindvars = array($user,$account,$pop,(int)$port,(int)$smtpPort,$username,$pass,$smtp,$useAuth,$msgs,(int)$accountId, $user);
			$result = $this->query($query,$bindvars);
		} else {
			$query = "delete from `tiki_user_mail_accounts` where `accountId`=? and `user`=?";
			$bindvars = array((int)$accountId, $user);
			$result = $this->query($query, $bindvars, -1, -1, false);

			$query = "insert into `tiki_user_mail_accounts`(`user`,`account`,`pop`,`port`,`smtpPort`,`username`,`pass`,`smtp`,`useAuth`,`msgs`) values(?,?,?,?,?,?,?,?,?,?)";
			$bindvars = array($user,$account,$pop,$port,$smtpPort,$username,$pass,$smtp,$useAuth,$msgs);
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
