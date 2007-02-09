<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class AssignmentsLib extends TikiLib {
	function AssignmentsLib($db) {
		$this->TikiLib($db);
	}

	function add_assignment($workspaceId, $periodId, $gradeWeight, $name, $description, $wikipage, $createdby, $startDate, $endDate, $type) {
		$now = date("U");
		$query = "insert into aulawiki_assignment(workspaceId,periodId,gradeWeight,name,description,wikipage,createdby,creationDate,startDate,endDate,type,uid) values(?,?,?,?,?,?,?,?,?,?,?,?)";
		$uid = md5(uniqid(rand()));
		$result = $this->db->query($query, array ($workspaceId, $periodId, $gradeWeight, $name, $description, $wikipage, $createdby, (int) $now, (int) $startDate, (int) $endDate, $type, $uid));
		return $uid;
	}

	function find_assignments($offset = 0, $maxRecords = -1, $sort_mode = 'startDate_desc', $find = '', $workspaceId) {
		$sort_mode = $this->convert_sortmode($sort_mode);

		if ($find) {
			$mid = " where workspaceId = ? and (`name` like ? or `description` like ?)";
			$bindvars = array ($workspaceId, '%'.$find.'%', '%'.$find.'%');
		} else {
			$mid = ' where workspaceId = ?';
			$bindvars = array ($workspaceId);
		}
		$query = "select * from aulawiki_assignment $mid order by $sort_mode";
		$query_cant = "select count(*) from aulawiki_assigment $mid";

		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, array ());

		$nres = 0;
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
			$nres ++;
		}
		//echo $cant;
		$retval = array ();
		if ($nres > 0) {
			$retval["data"] = $ret;
		}
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_assignments($sort_mode = 'startDate_desc', $workspaceId, $periodId = "") {
		$sort_mode = $this->convert_sortmode($sort_mode);
		if ($periodId == "") {
			$mid = " where workspaceId = ? ";
			$bindvars = array ($workspaceId);
		} else {
			$mid = " where workspaceId = ? and periodId=?";
			$bindvars = array ($workspaceId, $periodId);
		}

		$query = "select * from aulawiki_assignment $mid order by $sort_mode";

		$result = $this->query($query, $bindvars);

		$nres = 0;
		$ret = array ();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
			$nres ++;
		}
		return $ret;
	}

	function get_assignment_by_uid($uid) {
		$query = "select * from aulawiki_assignment where uid=?";
		$result = $this->db->query($query, array ($uid));
		$res = $result->fetchRow();
		return $res;
	}

	function get_assignment_by_id($id) {
		$query = "select * from aulawiki_assignment where assignmentId=?";
		$result = $this->db->query($query, array ($id));
		$res = $result->fetchRow();
		return $res;
	}

	function del_assignment($id) {
		$query = "delete from aulawiki_assignment where assignmentId=?";
		$result = $this->db->query($query, array ($id));
	}

	function del_all_assignments($workspaceId) {
		$query = "delete from aulawiki_assignment,aulawiki_gradebook where aulawiki_assignment.assignmentId=aulawiki_gradebook.assignmentId and aulawiki_assignment.workspaceId=?";
		$result = $this->db->query($query, array ($workspaceId));
	}

	function update_assignment($periodId, $gradeWeight, $name, $description, $wikipage, $startDate, $endDate, $type, $id) {
		$query = "update aulawiki_assignment set periodId=?,gradeWeight=?,name=?,description=?,wikipage=?,startDate=?,endDate=?,type=? where assignmentId=?";
		$result = $this->db->query($query, array ($periodId, $gradeWeight, $name, $description, $wikipage, $startDate, $endDate, $type, $id));
		return true;
	}

	function get_gradebook($workspaceId, $periodId = "") {
		$sort_mode = $this->convert_sortmode('startDate_desc');

		if ($periodId == "") {
			$mid = " where aulawiki_assignment.workspaceId = ? and aulawiki_assignment.assignmentId=aulawiki_gradebook.assignmentId";
			$bindvars = array ($workspaceId);
		} else {
			$mid = " where aulawiki_assignment.workspaceId = ? and aulawiki_assignment.periodId = ? and aulawiki_assignment.assignmentId=aulawiki_gradebook.assignmentId";
			$bindvars = array ($workspaceId, $periodId);
		}

		$query = "select aulawiki_gradebook.* from aulawiki_assignment,aulawiki_gradebook $mid order by $sort_mode";

		$result = $this->query($query, $bindvars);

		$nres = 0;
		$ret = null;
		while ($res = $result->fetchRow()) {
			if (!isset ($ret[$res["userId"]])) {
				$ret[$res["userId"]] = array ();
			}
			$ret[$res["userId"]][$res["assignmentId"]] = $res;
			$nres ++;
		}
		return $ret;
	}

	function get_assignment_grades($assignmentId) {
		$sort_mode = $this->convert_sortmode('userId_desc');

		$query = "select * from aulawiki_gradebook where assignmentId=? order by $sort_mode";
		$bindvars = array ($assignmentId);
		$result = $this->query($query, $bindvars);

		$ret = null;
		while ($res = $result->fetchRow()) {
			$ret[$res["userId"]] = $res;
		}
		return $ret;
	}

	function add_usergrade($assignmentId, $userId, $comment, $grade) {
		$query = "insert into aulawiki_gradebook(assignmentId,userId,comment,grade) values(?,?,?,?)";
		$result = $this->db->query($query, array ($assignmentId, $userId, $comment, $grade));
		return;
	}

	function update_usergrade($assignmentId, $userId, $comment, $grade) {
		$query = "update aulawiki_gradebook set comment=?,grade=? where assignmentId=? and userId=?";
		$result = $this->db->query($query, array ($comment, $grade, $assignmentId, $userId));
		return true;
	}

	function del_usergrade($assignmentId, $userId) {
		$query = "delete from aulawiki_gradebook where assignmentId=? and userId=?";
		$result = $this->db->query($query, array ($assignmentId, $userId));
	}

}
?>