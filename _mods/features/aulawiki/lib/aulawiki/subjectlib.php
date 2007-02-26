<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
class SubjectLib extends TikiDB {
	var $db; // The PEAR db object used to access the database
	function SubjectLib($db) {
		if (!$db) {
			die("Invalid db object passed to AsignaturasLib constructor");
		}
		$this->db = $db;
	}

	function add_subject($code, $name, $desc, $workspaceId, $studieId) {
		$query = "insert into aulawiki_subjects(code,name,description,workspaceId,studieId,uid) values(?,?,?,?,?,?)";
		$uid = md5(uniqid(rand()));
		$result = $this->db->query($query, array ($code, $name, $desc, $workspaceId, $studieId, $uid));

		return $uid;
	}

	function get_subjects_list($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $find = '') {
		$sort_mode = $this->convert_sortmode($sort_mode);

		if ($find) {
			$mid = " where `name` like ? or `code` like ?";
			$bindvars = array ('%'.$find.'%', '%'.$find.'%');
		} else {
			$mid = '';
			$bindvars = array ();
		}
		$query = "select * from aulawiki_subjects $mid order by $sort_mode";
		$query_cant = "select count(*) from aulawiki_subjects";

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

	function get_subject_by_uid($uid) {
		$query = "select * from aulawiki_subjects where uid=?";
		$result = $this->db->query($query, array ($uid));
		$res = $result->fetchRow();
		return $res;
	}

	function get_subject_by_id($id) {
		$query = "select * from aulawiki_subjects where subjectId=?";
		$result = $this->db->query($query, array ($id));
		$res = $result->fetchRow();
		return $res;
	}

	function get_subject_by_code($code) {
		$query = "select * from aulawiki_subjects where code=?";
		$result = $this->db->query($query, array ($code));
		$res = $result->fetchRow();
		return $res;
	}

	function del_subject($id) {
		$query = "delete from aulawiki_subjects where subjectId=?";
		$result = $this->db->query($query, array ($id));
	}

	function update_subject($id, $code, $name, $desc, $workspaceId, $studieId) {
		$query = "update aulawiki_subjects set code=?,name=?,description=?,workspaceId=?,studieId=? where subjectId=?";
		$result = $this->db->query($query, array ($code, $name, $desc, $workspaceId, $studieId, $id));
		return true;
	}

}
?>