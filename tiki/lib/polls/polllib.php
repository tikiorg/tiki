<?php

class PollLib extends TikiLib {
	function PollLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("aInvalid db object passed to PollLib constructor");
		}

		$this->db = $db;
	}

	function list_polls($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`title` like ?)";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `tiki_polls` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_polls` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query = "select count(*) from `tiki_poll_options` where `pollId`=?";

			$res["options"] = $this->getOne($query,array($res["pollId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_active_polls($offset, $maxRecords, $sort_mode, $find) {
		$now = date("U");

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`active`=? or `active`=?) and `publishDate`<=? and (`title` like ?)";
			$bindvars=array('a','c',(int) $now,$findesc);
		} else {
			$mid = " where (`active`=? or `active`=?) and `publishDate`<=? ";
			$bindvars=array('a','c',(int) $now);
		}

		$query = "select * from `tiki_polls` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_polls` $mid";
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

	function list_current_polls($offset, $maxRecords, $sort_mode, $find) {
		$now = date("U");

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `active`=? and `publishDate`<=? and (`title` like ?)";
			$bindvars=array('c',(int) $now,$findesc);
		} else {
			$mid = " where `active`=? and `publishDate`<=? ";
			$bindvars=array('c',(int) $now);
		}

		$query = "select * from `tiki_polls` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_polls` $mid";
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

	function list_all_polls($offset, $maxRecords, $sort_mode, $find) {
		$now = date("U");

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `publishDate`<=? and (`title` like ?)";
			$bindvars=array((int) $now,$findesc);
		} else {
			$mid = " where `publishDate`<=? ";
			$bindvars=array((int) $now);
		}

		$query = "select * from `tiki_polls` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_polls` $mid";
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

	function list_poll_options($pollId, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `pollId`=? and (`title` like ?)";
			$bindvars=array((int) $pollId,$findesc);
		} else {
			$mid = " where `pollId`=?";
			$bindvars=array((int) $pollId);
		}

		$query = "select * from `tiki_poll_options` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_poll_options` $mid";
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

	function remove_poll($pollId) {
		$query = "delete from `tiki_polls` where `pollId`=?";

		$result = $this->query($query,array((int) $pollId));
		$query = "delete from `tiki_poll_options` where `pollId`=?";
		$result = $this->query($query,array((int) $pollId));
		$this->remove_object('poll', $pollId);
		return true;
	}

	function set_last_poll() {
		$now = date("U");

		$query = "select max(`publishDate`) from `tiki_polls` where `publishDate`<=?";
		$last = $this->getOne($query,array((int) $now));
		$query = "update `tiki_polls` set `active`='c' where `publishDate`=?";
		$result = $this->query($query,array($last));
	}

	function close_all_polls() {
		$now = date("U");

		$query = "select max(`publishDate`) from `tiki_polls` where `publishDate`<=?";
		$last = $this->getOne($query,array((int) $now));
		$query = "update `tiki_polls` set `active`='x' where `publishDate`<? and `publishDate`<=?";
		$result = $this->query($query,array('x',$last,array((int) $now)));
	}

	function active_all_polls() {
		$now = date("U");

		$query = "update `tiki_polls` set `active`=? where `publishDate`<=?";
		$result = $this->query($query,array('a',(int) $now));
	}

	function remove_poll_option($optionId) {
		$query = "delete from `tiki_poll_options` where `optionId`=?";

		$result = $this->query($query,array($optionId));
		return true;
	}

	function get_poll_option($optionId) {
		$query = "select * from `tiki_poll_options` where `optionId`=?";

		$result = $this->query($query,array($optionId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function replace_poll($pollId, $title, $active, $publishDate) {

		// Check the name
		if ($pollId) {
			$query = "update `tiki_polls` set `title`=?,`active`=?,`publishDate`=? where `pollId`=?";

			$result = $this->query($query,array($title,$active,$publishDate,$pollId));
		} else {
			// was a replace into ... nobody knows why 
			$query = "insert into tiki_polls(`title`,`active`,`publishDate`,`votes`)
                values(?,?,?,?)";

			$result = $this->query($query,array($title,$active,$publishDate,0));
			$pollId = $this->getOne("select max(`pollId`) from `tiki_polls` where `title`=? and `publishDate`=?",array($title,$publishDate));
		}

		return $pollId;
	}

	function replace_poll_option($pollId, $optionId, $title) {
		// Check the name
		if ($optionId) {
			$query = "update `tiki_poll_options` set `title`=? where `optionId`=?";
			$result = $this->query($query,array($title,$optionId));
		} else {
			// was a replace into ... why?
			$query = "insert into `tiki_poll_options`(`pollId`,`title`,`votes`)
                values(?,?,?)";
			$result = $this->query($query,array($pollId,$title,0));
		}

		return true;
	}

	function get_random_active_poll() {
		// Get pollid from polls where active = 'y' and publishDate is less than now
		$res = $this->list_current_polls(0, -1, 'title_desc', '');

		$data = $res["data"];
		$bid = rand(0, count($data) - 1);
		$pollId = $data[$bid]["pollId"];
		return $pollId;
	}
}

$polllib = new PollLib($dbTiki);

?>
