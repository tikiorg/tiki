<?php

class ShoutboxLib extends TikiLib {
	function ShoutboxLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to ShoutboxLib constructor");
		}
		$this->db = $db;
	}

	function list_shoutbox($offset, $maxRecords, $sort_mode, $find) {
		if ($find) {
			$mid = " where (`message` like ?)";
			$bindvars = array('%'.$find.'%');
		} else {
			$mid = "";
			$bindvars = array();
		}

		$query = "select * from `tiki_shoutbox` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_shoutbox` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			if (!$res["user"])
				$res["user"] = tra('Anonymous');

			$res["message"] = htmlspecialchars($res["message"]);
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_shoutbox($msgId, $user, $message) {
		$hash = md5($message);
		$cant = $this->getOne("select count(*) from `tiki_shoutbox` where `hash`=? and `user`=?", array($hash,$user));
		if ($cant) {
			return;
		}
		$message = strip_tags($message);
		$now = date("U");
		if ($msgId) {
			$query = "update `tiki_shoutbox` set `user`=?, `message`=?, `hash`=? where `msgId`=?";
			$bindvars = array($user,$message,$hash,(int)$msgId);
		} else {
			$query = "delete from `tiki_shoutbox` where `user`=? and `timestamp`=? and `hash`=?";
			$bindvars = array($user,(int)$now,$hash);
			$this->getOne($query,$bindvars,false);
			$query = "insert into `tiki_shoutbox`(`message`,`user`,`timestamp`,`hash`) values(?,?,?,?)";
			$bindvars = array($message,$user,(int)$now,$hash);
		}

		$result = $this->query($query,$bindvars);
		return true;
	}

	function remove_shoutbox($msgId) {
		$query = "delete from `tiki_shoutbox` where `msgId`=?";
		$result = $this->query($query,array((int)$msgId));
		return true;
	}

	function get_shoutbox($msgId) {
		$query = "select * from `tiki_shoutbox` where `msgId`=?";
		$result = $this->query($query,array((int)$msgId));
		if (!$result->numRows()) {
			return false;
		}
		$res = $result->fetchRow();
		return $res;
	}
}

$shoutboxlib = new ShoutboxLib($dbTiki);

?>
