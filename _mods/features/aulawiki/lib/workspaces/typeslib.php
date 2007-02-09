<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

//require_once ('roleslib.php');
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
class WorkspaceTypesLib extends TikiLib {
	function WorkspaceTypesLib($db) {
		$this->TikiLib($db);
	}

	function add_workspace_type($code, $name, $desc, $menuid, $active, $resources, $userwstype, $hide) {
		$query = "insert into tiki_workspace_types(code,name,description,menuid,active,resources,userwstype,hide,uid) values(?,?,?,?,?,?,?,?,?)";
		$uid = md5(uniqid(rand()));
		$result = $this->db->query($query, array ($code, $name, $desc, $menuid, $active, $resources, $userwstype, $hide, $uid));
		$wstype = $this->get_workspace_type_by_uid($uid);
		return $uid;
	}

	function get_workspace_type_list($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $find = '') {
		$sort_mode = $this->convert_sortmode($sort_mode);

		if ($find) {
			$mid = " where `name` like ? or `description` like ?";
			$bindvars = array ('%'.$find.'%', '%'.$find.'%');
		} else {
			$mid = '';
			$bindvars = array ();
		}
		$query = "select * from tiki_workspace_types $mid order by $sort_mode";
		$query_cant = "select count(*) from tiki_workspace_types";

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

	function get_workspace_type_by_uid($uid) {
		$query = "select * from tiki_workspace_types where uid=?";
		$result = $this->db->query($query, array ($uid));
		$res = $result->fetchRow();
		$roles = $this->get_workspace_type_roles($res["id"]);
		$res["roles"] = $roles;
		return $res;
	}

	function get_workspace_type_by_id($id) {
		$query = "select * from tiki_workspace_types where id=?";
		$result = $this->db->query($query, array ($id));
		$res = $result->fetchRow();
		$roles = $this->get_workspace_type_roles($res["id"]);
		$res["roles"] = $roles;
		return $res;
	}

	function get_workspace_type_by_code($code) {
		$query = "select * from tiki_workspace_types where code=?";
		$result = $this->db->query($query, array ($code));
		$res = $result->fetchRow();
		$roles = $this->get_workspace_type_roles($res["id"]);
		$res["roles"] = $roles;
		return $res;
	}

	function del_workspace_type($id) {
		$wstype = $this->get_workspace_type_by_id($id);
		$this->del_workspace_type_roles($wstype["id"]);
		/*global $userlib;
		$userlib->remove_group("WSTYPEGRP".$wstype["code"]);*/
		$query = "delete from tiki_workspace_types where id=?";
		$result = $this->db->query($query, array ($id));
	}

	function update_workspace_type($id, $code, $name, $desc, $menuid, $active, $userwstype, $hide) {
		//Add roles
		/*$this->del_workspace_type_roles($id);
		$this->add_workspace_type_roles($id, $roles, $code);
*/
		$query = "update tiki_workspace_types set name=?,description=?,menuid=?,active=?,userwstype=?,hide=? where id=?";
		$result = $this->db->query($query, array ($name, $desc, $menuid, $active, $userwstype, $hide,$id));
		return true;
	}

	function update_workspace_type_resources($id, $resources) {

		$query = "update tiki_workspace_types set resources=? where id=?";
		$result = $this->db->query($query, array ($resources, $id));
		return true;
	}

	function list_active_types() {
		$query = "select * from tiki_workspace_types where active=?";

		$result = $this->query($query, array ('y'));

		$ret = array ();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_workspace_type_roles($typeId) {
		$query = "select er.*,rw.wstypePermGroup from tiki_workspace_role_wstype rw,tiki_workspace_roles er where rw.typeId=? and rw.roleName=er.name";
		$result = $this->db->query($query, array ($typeId));
		$ret = array ();

		while ($res = $result->fetchRow()) {
			$ret[$res["name"]] = $res;
		}

		return $ret;
	}

	function del_workspace_type_roles($wstypeId) {
		//TODO: remove unused rolegroups
		/*	global $userlib;
			$roles = $wstype["roles"];
			foreach ($roles as $key => $rol) {
				$userlib->remove_group("RWSTYPEGRP".$wstype["code"]."-".$rol);
			}
			*/
		$query = "delete from tiki_workspace_role_wstype where typeId=?";
		$result = $this->db->query($query, array ($wstypeId));
	}

	function del_workspace_type_role($wstypeId,$roleName) {
		$query = "delete from tiki_workspace_role_wstype where typeId=? and roleName=?";
		$result = $this->db->query($query, array ($wstypeId,$roleName));
	}
	
	function add_workspace_type_roles($typeId, $roles, $code) {
		global $dbTiki;

		foreach ($roles as $key => $rol) {
			$query = "insert into tiki_workspace_role_wstype(roleName,typeId) values(?,?)";
			$result = $this->db->query($query, array ($rol, $typeId));
			/* 
				global $userlib;
				$userlib->add_group("RWSTYPEGRP".$code."-".$rol, $code."-".$rol." role&workspace type members", '');
				$eduuserlib->remove_inclusion("RWSTYPEGRP".$code."-".$rol, "ROLEGRP".$rol);
				$userlib->group_inclusion("RWSTYPEGRP".$code."-".$rol, "ROLEGRP".$rol);
			*/
		}
		return TRUE;
	}
	
	function add_workspace_type_role($typeId, $roleName, $permGroup) {
		global $dbTiki;

		$query = "insert into tiki_workspace_role_wstype(roleName,typeId,wstypePermGroup) values(?,?,?)";
		$result = $this->db->query($query, array ($roleName, $typeId,$permGroup));
			/* 
				global $userlib;
				$userlib->add_group("RWSTYPEGRP".$code."-".$rol, $code."-".$rol." role&workspace type members", '');
				$eduuserlib->remove_inclusion("RWSTYPEGRP".$code."-".$rol, "ROLEGRP".$rol);
				$userlib->group_inclusion("RWSTYPEGRP".$code."-".$rol, "ROLEGRP".$rol);
			*/
		return TRUE;
	}

}
?>