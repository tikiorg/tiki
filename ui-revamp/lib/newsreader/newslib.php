<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once "lib/pear/NNTP.php";

class Newslib extends TikiLib {
	var $nntp;

	function Newslib($db) {
		$this->TikiLib($db);
	}

	function get_server($user, $serverId) {
		$query = "select * from `tiki_newsreader_servers` where `user`=? and `serverId`=?";
		$result = $this->query($query, array($user,(int)$serverId));
		$res = $result->fetchRow();
		return $res;
	}

	function news_mark($user, $serverId, $groupName) {
		if ($this->getOne( "select count(*) from `tiki_newsreader_marks` where `user`=? and `serverId`=? and `groupName`=?",array($user,(int)$serverId,$groupName))) {
			$query = "update `tiki_newsreader_marks` set `timestamp`=? where `user`=? and `serverId`=? and `groupName`=?";
			$this->query($query,array(array((int)$this->now,$user,(int)$serverId,$groupName)));
		} else {
			$query = "insert into `tiki_newsreader_marks`(`user`,`serverId`,`groupName`,`timestamp`) values(?,?,?,?)";
			$this->query($query,array($user,(int)$serverId,$groupName,(int)$this->now));
		}
	}

	function news_get_mark($user, $serverId, $groupName) {
		if ($this->getOne( "select count(*) from `tiki_newsreader_marks` where `user`=? and `serverId`=? and `groupName`=?",array($user,(int)$serverId,$groupName))) {
			return $this->getOne("select `timestamp` from `tiki_newsreader_marks` where `user`=? and `serverId`=? and `groupName`=?",array($user,(int)$serverId,$groupName));
		} else {
			return 0;
		}
	}

	function replace_server($user, $serverId, $server, $port, $username, $password) {
		if ($serverId) { $query = "update `tiki_newsreader_servers` set `server`=?, `port`=?, `username`=?, `password`=? where `user`=? and `serverId`=?";
			$this->query($query,array($server,(int)$port,$username,$password,$user,(int)$serverId));
			return $serverId;
		} else {
			$query = "insert into `tiki_newsreader_servers`(`user`,`serverId`,`server`,`port`,`username`,`password`) values(?,?,?,?,?,?)";
			$this->query($query,array($user,(int)$serverId,$server,(int)$port,$username,$password));
			$serverId = $this->getOne("select max(`serverId`) from `tiki_newsreader_servers` where `user`=? and `server`=?",array($user,$server));
			return $serverId;
		}
	}

	function remove_server($user, $serverId) {
		$query = "delete from `tiki_newsreader_servers` where `user`=? and `serverId`=?";
		$this->query($query,array($user,(int)$serverId));
	}

	function list_servers($user, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array($user);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " and (`title` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_newsreader_servers` where `user`=? $mid order by ".$this->convert_sortmode("$sort_mode").",".$this->convert_sortmode("serverId_desc");
		$query_cant = "select count(*) from `tiki_newsreader_servers` where `user`=? $mid";
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

	function news_select_group($group) {
		return $this->nntp->selectGroup($group);
	}

	function news_split_headers($id) {
		return $this->nntp->splitHeaders($id);
	}

	function news_get_body($id) {
		return $this->nntp->getBody($id);
	}

	function news_set_server($server, $port, $user, $pass) {
		$ret = $this->nntp->connect($server, $port, $user, $pass);

		if (PEAR::isError($ret)) {
			return false;
		} else {
			return true;
		}
	}

	function news_get_groups() {
		return $this->nntp->getGroups();
	}
}
global $dbTiki;
$newslib = new Newslib($dbTiki);

?>
