<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

include_once('lib/polls/polllib_shared.php');

class PollLib extends PollLibShared {
	function PollLib($db) {
		if (!$db) { die ("Invalid db object passed to PollLib constructor"); }
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
			$res["options"] = $this->getOne($query,array((int)$res["pollId"]));
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
			$mid = " where (`active`=? or `active`=?) and `publishDate`<=? and `releaseDate`>=? and (`title` like ?)";
			$bindvars=array('a','c',(int) $now,(int)$now,$findesc);
		} else {
			$mid = " where (`active`=? or `active`=?) and `publishDate`<=? and `releaseDate`>=?";
			$bindvars=array('a','c',(int) $now,(int)$now);
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
			$mid = " where `active`=? and `publishDate`<=? and `releaseDate`>=? and (`title` like ?)";
			$bindvars=array('c',(int) $now,(int)$now,$findesc);
		} else {
			$mid = " where `active`=? and `publishDate`<=? and `releaseDate`>=?";
			$bindvars=array('c',(int) $now,(int)$now);
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
			$mid = " where `publishDate`<=? and `releaseDate`>=? and (`title` like ?)";
			$bindvars=array((int) $now,(int)$now,$findesc);
		} else {
			$mid = " where `publishDate`<=? and `releaseDate`>=?";
			$bindvars=array((int) $now,(int)$now);
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
		$query = "select max(`publishDate`) from `tiki_polls` where `publishDate`<=? and `releaseDate`>=?";
		$last = $this->getOne($query,array((int) $now,(int)$now));
		$query = "update `tiki_polls` set `active`='c' where `publishDate`=?";
		$result = $this->query($query,array((int)$last));
	}

	function close_all_polls() {
		$now = date("U");
		$query = "select max(`publishDate`) from `tiki_polls` where `publishDate`<=?";
		$last = $this->getOne($query,array((int) $now));
		$query = "update `tiki_polls` set `active`='x' where `publishDate`<=?";
		$result = $this->query($query,array((int) $now));
	}

	function active_all_polls() {
		$now = date("U");
		$query = "update `tiki_polls` set `active`=? where `publishDate`<=? and `releaseDate`>=?";
		$result = $this->query($query,array('a',(int) $now,(int)$now));
	}

	function remove_poll_option($optionId) {
		$query = "delete from `tiki_poll_options` where `optionId`=?";
		$result = $this->query($query,array((int)$optionId));
		return true;
	}

	function get_poll_option($optionId) {
		$query = "select * from `tiki_poll_options` where `optionId`=?";
		$result = $this->query($query,array((int)$optionId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function replace_poll($pollId, $title, $description, $active, $publishDate, $releaseDate) {
		if ($pollId) {
			$query = "update `tiki_polls` set `title`=?,`description`=?,`active`=?,`publishDate`=?,`releaseDate`=? where `pollId`=?";
			$result = $this->query($query,array($title,$description,$active,(int)$publishDate,(int)$releaseDate,(int)$pollId));
		} else {
			$query = "insert into tiki_polls(`title`,`description`,`active`,`publishDate`,`releaseDate`,`votes`) values(?,?,?,?,?,?)";
			$result = $this->query($query,array($title,$description,$active,(int)$publishDate,(int)$releaseDate,0));
			$pollId = $this->getOne("select max(`pollId`) from `tiki_polls` where `title`=? and `publishDate`=?",array($title,(int)$publishDate));
		}
		return $pollId;
	}

	function replace_poll_option($pollId, $optionId, $title) {
		if ($optionId) {
			$query = "update `tiki_poll_options` set `title`=? where `optionId`=?";
			$result = $this->query($query,array($title,(int)$optionId));
		} else {
			$query = "insert into `tiki_poll_options`(`pollId`,`title`,`votes`) values(?,?,?)";
			$result = $this->query($query,array((int)$pollId,$title,0));
		}
		return true;
	}

	function get_random_active_poll() {
		$res = $this->list_current_polls(0, -1, 'title_desc', '');
		$data = $res["data"];
		$bid = rand(0, count($data) - 1);
		$pollId = $data[$bid]["pollId"];
		return $pollId;
	}
	
}

$polllib = new PollLib($dbTiki);

?>
