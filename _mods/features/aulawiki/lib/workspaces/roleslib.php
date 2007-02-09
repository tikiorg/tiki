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

class WorkspaceRolesLib extends TikiLib {
	function WorkspaceRolesLib($db) {
		$this->TikiLib($db);
	}

	function add_role($name, $desc, $permgroup) {
		$query = "insert into tiki_workspace_roles(name,description,permgroup,uid) values(?,?,?,?)";
		$uid = md5(uniqid(rand()));
		$result = $this->db->query($query, array ($name, $desc, $permgroup, $uid));
		if (DB :: isError($result)) {
			//$this->sql_error($query, $result);
		} else { //create role group
			global $userlib;
			$userlib->add_group("ROLEGRP".$name, $name." role members", '');
		}
		return $uid;
	}

	function get_role_list($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $find = '') {
		$sort_mode = $this->convert_sortmode($sort_mode);

		if ($find) {
			$mid = " where `name` like ? or `description` like ?";
			$bindvars = array ('%'.$find.'%', '%'.$find.'%');
		} else {
			$mid = '';
			$bindvars = array ();
		}
		$query = "select * from tiki_workspace_roles $mid order by $sort_mode";
		$query_cant = "select count(*) from tiki_workspace_roles";

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

	function get_role_by_uid($uid) {
		$query = "select * from tiki_workspace_roles where uid=?";
		$result = $this->db->query($query, array ($uid));
		$res = $result->fetchRow();
		return $res;
	}

	function get_role_by_name($name) {
		$query = "select * from tiki_workspace_roles where name=?";
		$result = $this->db->query($query, array ($name));
		$res = $result->fetchRow();
		return $res;
	}

	function del_role($uid) {
		$role = $this->get_role_by_uid($uid);
		$query = "delete from tiki_workspace_roles where uid=?";
		$result = $this->db->query($query, array ($uid));
		global $userlib;
		$userlib->remove_group("ROLEGRP".$role["name"]);

	}

	function update_role($uid, $name, $desc, $permgroup) {
		$role = $this->get_role_by_uid($uid);
		$query = "update tiki_workspace_roles set name=?,description=?,permgroup=? where uid=?";
		$result = $this->db->query($query, array ($name, $desc, $permgroup, $uid));
		if ($name != $role["name"]) {
				global $userlib;
				$userlib->remove_group("ROLEGRP".$role["name"]);
				$userlib->add_group("ROLEGRP".name, $name." role members", '');
		}
		return true;
	}

}
?>