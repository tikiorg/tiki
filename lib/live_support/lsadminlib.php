<?php

class LsAdminlib extends Tikilib {
	function LsAdminlib($db) {
		if (!$db) {
			die ("Invalid db object passed to LsAdminlib constructor");
		}

		$this->db = $db;
	}

	function add_operator($user) {
		$query = "replace into tiki_live_support_operators(user,accepted_requests,status,longest_chat,shortest_chat,average_chat,last_chat,time_online,votes,points,status_since)
  														values('$user',0,'offline',0,0,0,0,0,0,0,0)";

		$this->query($query);
	}

	function remove_operator($user) {
		$query = "delete from `tiki_live_support_operators` where `user`='$user'";

		$this->query($query);
	}

	function is_operator($user) {
		return $this->getOne("select count(*) from `tiki_live_support_operators` where `user`=?", array($user));
	}

	function get_operators($status) {
		$query = "select * from `tiki_live_support_operators` where `status`='$status'";

		$result = $this->query($query);
		$ret = array();
		$now = date("U");

		while ($res = $result->fetchRow()) {
			$res['elapsed'] = $now - $res['status_since'];

			$ret[] = $res;
		}

		return $ret;
	}

	function post_support_message($username, $user, $user_email, $title, $data, $priority, $module, $resolution, $assigned_to = '')
		{
		die ("MISSING CODE");
	}

	function list_support_messages($offset, $maxRecords, $sort_mode, $find, $where) {
		$sort_mode = str_replace("_desc", " desc", $sort_mode);

		$sort_mode = str_replace("_asc", " asc", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where (data like $findesc or username like $findesc)";
		} else {
			$mid = "";
		}

		if ($where) {
			if ($mid) {
				$mid = ' and ' . $where;
			} else {
				$mid = ' where ' . $where;
			}
		}

		$query = "select * from `tiki_live_support_messages` $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from `tiki_live_support_messages` $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_modules() {
		$query = "select * from tiki_live_support_modules";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	/* functions for transcripts */
	function list_support_requests($offset, $maxRecords, $sort_mode, $find, $where) {
		$sort_mode = str_replace("_desc", " desc", $sort_mode);

		$sort_mode = str_replace("_asc", " asc", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where (reason like $findesc or user like $findesc or operator like $findesc)";
		} else {
			$mid = "";
		}

		if ($where) {
			if ($mid) {
				$mid = ' and ' . $where;
			} else {
				$mid = ' where ' . $where;
			}
		}

		$query = "select * from `tiki_live_support_requests` $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from `tiki_live_support_requests` $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['msgs'] = $this->getOne("select count(*) from `tiki_live_support_events` where `reqId`='" . $res['reqId'] . "'");

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_all_tiki_users() {
		$query = "select distinct(tiki_user) from tiki_live_support_requests";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res['tiki_user'];
		}

		return $ret;
	}

	function get_all_operators() {
		$query = "select distinct(operator) from tiki_live_support_requests";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res['operator'];
		}

		return $ret;
	}

	function get_events($reqId) {
		$query = "select tlr.operator_id,tlr.user_id,tle.data,tle.timestamp,tlr.user,tlr.operator,tlr.tiki_user,tle.senderId from `tiki_live_support_events` tle, tiki_live_support_requests tlr where tle.reqId=tlr.reqId and (senderId=user_id or senderId=operator_id) and tlr.reqId='$reqId'";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}
}

$lsadminlib = new LsAdminlib($dbTiki);

?>
