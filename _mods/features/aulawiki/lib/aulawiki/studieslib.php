<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

class StudiesLib extends TikiDB {
	var $db; // The PEAR db object used to access the database
	function StudiesLib($db) {
		if (!$db) {
			die("Invalid db object passed to AsignaturasLib constructor");
		}
		$this->db = $db;
	}

	function add_studies($code, $name, $desc, $parentId, $type) {
		$query = "insert into aulawiki_studies(code,name,description,parentId,type,uid) values(?,?,?,?,?,?)";
		$uid = md5(uniqid(rand()));
		$result = $this->db->query($query, array ($code, $name, $desc, $parentId, $type, $uid));
		return $uid;
	}

	function get_studies_list($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $find = '') {
		$sort_mode = $this->convert_sortmode($sort_mode);

		if ($find) {
			$mid = " where `name` like ? or `code` like ?";
			$bindvars = array ('%'.$find.'%', '%'.$find.'%');
		} else {
			$mid = '';
			$bindvars = array ();
		}
		$query = "select * from aulawiki_studies $mid order by $sort_mode";
		$query_cant = "select count(*) from aulawiki_studies";

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

	function get_studies_by_uid($uid) {
		$query = "select * from aulawiki_studies where uid=?";
		$result = $this->db->query($query, array ($uid));
		$res = $result->fetchRow();
		return $res;
	}

	function get_studies_by_id($id) {
		$query = "select * from aulawiki_studies where id=?";
		$result = $this->db->query($query, array ($id));
		$res = $result->fetchRow();
		return $res;
	}

	function get_studies_by_code($code) {
		$query = "select * from aulawiki_studies where code=?";
		$result = $this->db->query($query, array ($code));
		$res = $result->fetchRow();
		return $res;
	}

	function del_studies($id) {
		$query = "delete from aulawiki_studies where id=?";
		$result = $this->db->query($query, array ($id));
	}

	function update_studies($id, $code, $name, $desc, $parentId, $type) {
		$query = "update aulawiki_studies set code=?,name=?,description=?,parentId=?,type=? where id=?";
		$result = $this->db->query($query, array ($code, $name, $desc, $parentId, $type, $id));
		return true;
	}

}
?>