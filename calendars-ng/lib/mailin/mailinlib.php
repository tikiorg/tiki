<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class MailinLib extends TikiLib {
	function MailinLib($db) {
		$this->TikiLib($db);
	}

	function list_mailin_accounts($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " where `account` like ?";
			$bindvars = array($findesc);
		} else {
			$mid = "  ";
			$bindvars = array();
		}

		$query = "select * from `tiki_mailin_accounts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_mailin_accounts` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_active_mailin_accounts($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " where `active`=? and `account` like ?";
			$bindvars = array("y",$findesc);
		} else {
			$mid = " where `active`=?";
			$bindvars = array("y");
		}

		$query = "select * from `tiki_mailin_accounts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_mailin_accounts` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

  function replace_mailin_account($accountId, $account, $pop, $port, $username, $pass, $smtp, $useAuth, $smtpPort, $type, $active, $anonymous, $attachments, $article_topicId = NULL, $article_type = NULL, $discard_after=NULL) {
    if ($accountId) {
      $bindvars = array($account,$pop,(int)$port,(int)$smtpPort,$username,$pass,$smtp,$useAuth,$type,$active,$anonymous,$attachments,(int)$article_topicId,$article_type,$discard_after,(int)$accountId);
      $query = "update `tiki_mailin_accounts` set `account`=?, `pop`=?, `port`=?, `smtpPort`=?, `username`=?, `pass`=?, `smtp`=?, `useAuth`=?, `type`=?, `active`=?, `anonymous`=?, `attachments`=?, `article_topicId`=?, `article_type`=? , `discard_after`=? where `accountId`=?";
      $result = $this->query($query,$bindvars);
    } else {
      $bindvars = array($account,$pop,(int)$port,(int)$smtpPort,$username,$pass,$smtp,$useAuth,$type,$active,$anonymous,$attachments,(int)$article_topicId,$article_type);
      $query = "delete from `tiki_mailin_accounts` where `account`=? and `pop`=? and `port`=? and `smtpPort`=? and `username`=? and `pass`=? and `smtp`=? and `useAuth`=? and `type`=? and `active`=? and `anonymous`=? and `attachments`=? and `article_topicId`=?, `article_type`=?";
      $result = $this->query($query,$bindvars,-1,-1,false);
      $query = "insert into `tiki_mailin_accounts`(`account`,`pop`,`port`,`smtpPort`,`username`,`pass`,`smtp`,`useAuth`,`type`,`active`,`anonymous`,`attachments`,`article_topicId`,`article_type`) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
      $result = $this->query($query,$bindvars);
    }
    return true;
  }

	function remove_mailin_account($accountId) {
		$query = "delete from `tiki_mailin_accounts` where `accountId`=?";
		$result = $this->query($query,array((int)$accountId));
		return true;
	}

	function get_mailin_account($accountId) {
		$query = "select * from `tiki_mailin_accounts` where `accountId`=?";
		$result = $this->query($query,array((int)$accountId));
		if (!$result->numRows()) { 
			return false;
		}
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}
}
global $dbTiki;
$mailinlib = new MailinLib($dbTiki);

?>
