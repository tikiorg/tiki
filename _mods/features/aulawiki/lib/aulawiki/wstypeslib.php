<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

require_once ('roleslib.php');
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
class WorkspaceTypesLib extends TikiLib {
	function WsTypesLib($db) {
		if (!$db) {
			die("Invalid db object passed to WsTypesLib constructor");
		}
		$this->db = $db;
	}

	function add_workspace_type($code, $name, $desc, $menuid, $active, $roles, $resources, $userwstype, $hide,$anonymous,$registered) {
		$query = "insert into aulawiki_workspace_types(code,name,description,menuid,active,resources,userwstype,hide,anonymous,registered,uid) values(?,?,?,?,?,?,?,?,?,?,?)";
		$uid = md5(uniqid(rand()));
		$result = $this->db->query($query, array ($code, $name, $desc, $menuid, $active, $resources, $userwstype, $hide,$anonymous,$registered, $uid));
		$wstype = $this->get_workspace_type_by_uid($uid);
		$this->add_workspace_type_roles($wstype["id"], $roles, $code);
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
		$query = "select * from aulawiki_workspace_types $mid order by $sort_mode";
		$query_cant = "select count(*) from aulawiki_workspace_types";

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
		$query = "select * from aulawiki_workspace_types where uid=?";
		$result = $this->db->query($query, array ($uid));
		$res = $result->fetchRow();
		$roles = $this->get_workspace_type_roles($res["id"]);
		$res["roles"] = $roles;
		return $res;
	}

	function get_workspace_type_by_id($id) {
		$query = "select * from aulawiki_workspace_types where id=?";
		$result = $this->db->query($query, array ($id));
		$res = $result->fetchRow();
		$roles = $this->get_workspace_type_roles($res["id"]);
		$res["roles"] = $roles;
		return $res;
	}

	function get_workspace_type_by_code($code) {
		$query = "select * from aulawiki_workspace_types where code=?";
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
		$query = "delete from aulawiki_workspace_types where id=?";
		$result = $this->db->query($query, array ($id));
	}

	function update_workspace_type($id, $code, $name, $desc, $menuid, $active, $roles, $userwstype, $hide,$anonymous,$registered) {
		//Add roles
		$this->del_workspace_type_roles($id);
		$this->add_workspace_type_roles($id, $roles, $code);

		$query = "update aulawiki_workspace_types set name=?,description=?,menuid=?,active=?,userwstype=?,hide=?,anonymous=?,registered=? where id=?";
		$result = $this->db->query($query, array ($name, $desc, $menuid, $active, $userwstype, $hide,$anonymous,$registered,$id));
		return true;
	}

	function update_workspace_type_resources($id, $resources) {

		$query = "update aulawiki_workspace_types set resources=? where id=?";
		$result = $this->db->query($query, array ($resources, $id));
		return true;
	}

	function list_active_types() {
		$query = "select * from aulawiki_workspace_types where active=?";

		$result = $this->query($query, array ('y'));

		$ret = array ();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_workspace_type_roles($typeId) {
		$query = "select er.* from aulawiki_role_wstype rw,aulawiki_roles er where rw.typeId=? and rw.roleName=er.name";
		$result = $this->db->query($query, array ($typeId));
		$ret = array ();

		while ($res = $result->fetchRow()) {
			//$ret[] = $res["roleName"];
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
		$query = "delete from aulawiki_role_wstype where typeId=?";
		$result = $this->db->query($query, array ($wstypeId));
	}

	function add_workspace_type_roles($typeId, $roles, $code) {
		global $dbTiki;
		require_once ('lib/aulawiki/eduuserlib.php');
		$eduuserlib = new EduUserLib($dbTiki);
		foreach ($roles as $key => $rol) {
			$query = "insert into aulawiki_role_wstype(roleName,typeId) values(?,?)";
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

}
?>