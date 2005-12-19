<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once('lib/polls/polllib_shared.php');

class PollLib extends PollLibShared {
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

	function set_last_poll() {
		$now = date("U");
		$query = "select max(`publishDate`) from `tiki_polls` where `publishDate`<=?";
		$last = $this->getOne($query,array((int) $now));
		$query = "update `tiki_polls` set `active`=? where `publishDate`=?";
		$result = $this->query($query,array('c',$last));
	}

	function close_all_polls() {
		$now = date("U");
		$query = "select max(`publishDate`) from `tiki_polls` where `publishDate`<=?";
		$last = $this->getOne($query,array((int) $now));
		$query = "update `tiki_polls` set `active`=? where `publishDate`<=?";
		$result = $this->query($query,array('x',(int) $now));
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
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

}
global $dbTiki;
$polllib = new PollLib($dbTiki);

?>
