<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class PollLibShared extends TikiLib {
	var $db;
	function PollLibShared($db) {
		$this->db = $db;
	}

	function get_poll($pollId) {
		$query = "select * from `tiki_polls` where `pollId`=?";
		$result = $this->query($query,array((int)$pollId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
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

  function get_random_active_poll() {
		$now = date('U');
    $pollcount = $this->getOne("select count(*) from `tiki_polls` where `active`=? and `publishDate`<=? ", array('c',(int) $now));
		if ($pollcount) {
			$bid = rand(0, $pollcount - 1);
			$pollId = $this->getOne("select `pollId` from `tiki_polls` where `active`=? and `publishDate`<=? ", array('c',(int) $now),1,$bid);
			return $pollId;
		} else {
			return 0;
		}
  }

	function poll_vote($user, $pollId, $optionId) {
		$previous_vote = $this->get_user_vote("poll$pollId",$user);
		if (!$previous_vote || $previous_vote == 0) {
			$query = "update `tiki_polls` set `votes`=`votes`+1 where `pollId`=?";
			$result = $this->query($query,array((int)$pollId));
			$query = "update `tiki_poll_options` set `votes`=`votes`+1 where `optionId`=?";
			$result = $this->query($query,array((int)$optionId));
		} elseif ($previous_vote != $optionId) {
			$query = "update `tiki_poll_options` set `votes`=`votes`-1 where `optionId`=?";
			$result = $this->query($query,array((int)$previous_vote));
			$query = "update `tiki_poll_options` set `votes`=`votes`+1 where `optionId`=?";
			$result = $this->query($query,array((int)$optionId));
		}
	}
	
}

$polllib = new PollLibShared($dbTiki);

?>
