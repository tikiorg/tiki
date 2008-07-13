<?php

class LsAdminlib extends TikiLib {
	function LsAdminlib($db) {
		$this->TikiLib($db);
	}

	function add_operator($user) {
		$this->getOne("delete from `tiki_live_support_operators` where `user`=?",array($user),false);
		$query = "insert into `tiki_live_support_operators`(`user`,`accepted_requests`,`status`,`longest_chat`,`shortest_chat`,`average_chat`,`last_chat`,`time_online`,`votes`,`points`,`status_since`)
  			values(?,?,?,?,?,?,?,?,?,?,?)";

		$this->query($query,array($user,0,'offline',0,0,0,0,0,0,0,0));
	}

	function remove_operator($user) {
		$query = "delete from `tiki_live_support_operators` where `user`=?";

		$this->query($query,array($user));
	}

	function is_operator($user) {
		return $this->getOne("select count(*) from `tiki_live_support_operators` where `user`=?", array($user));
	}

	function get_operators($status) {
		$query = "select * from `tiki_live_support_operators` where `status`=?";

		$result = $this->query($query,array($status));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res['elapsed'] = $this->now - $res['status_since'];

			$ret[] = $res;
		}

		return $ret;
	}

	function post_support_message($username, $user, $user_email, $title, $data, $priority, $module, $resolution, $assigned_to = '')
		{
		// very nice that (redflo)
		die ("MISSING CODE");
	}

	function list_support_messages($offset, $maxRecords, $sort_mode, $find, $where) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`data` like $findesc or `username` like $findesc)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		if ($where) {
			if ($mid) {
				$mid = ' and ' . $where;
			} else {
				$mid = ' where ' . $where;
			}
		}

		$query = "select * from `tiki_live_support_messages` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_live_support_messages` $mid";
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

	function get_modules() {
		$query = "select * from `tiki_live_support_modules`";

		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	/* functions for transcripts */
	function list_support_requests($offset, $maxRecords, $sort_mode, $find, $where) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`reason` like ? or `user` like ? or `operator` like ?)";
			$bindvars=array($findesc,$findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		if ($where) {
			if ($mid) {
				$mid = ' and ' . $where;
			} else {
				$mid = ' where ' . $where;
			}
		}

		$query = "select * from `tiki_live_support_requests` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_live_support_requests` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['msgs'] = $this->getOne("select count(*) from `tiki_live_support_events` where `reqId`=?",array($res['reqId']));

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_all_tiki_users() {
		$query = "select distinct(`tiki_user`) from `tiki_live_support_requests`";

		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res['tiki_user'];
		}

		return $ret;
	}

	function get_all_operators() {
		$query = "select distinct(`operator`) from `tiki_live_support_requests`";

		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res['operator'];
		}

		return $ret;
	}

	function get_events($reqId) {
		$query = "select tlr.`operator_id`,tlr.`user_id`,tle.`data`,tle.`timestamp`,tlr.`user`,tlr.`operator`,tlr.`tiki_user`,tle.`senderId` from `tiki_live_support_events` tle, `tiki_live_support_requests` tlr where tle.`reqId`=tlr.`reqId` and (`senderId`=tlr.`user_id` or tle.`senderId`=tlr.`operator_id`) and tlr.`reqId`=?";

		$result = $this->query($query,array($reqId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}
}
global $dbTiki;
$lsadminlib = new LsAdminlib($dbTiki);

?>
