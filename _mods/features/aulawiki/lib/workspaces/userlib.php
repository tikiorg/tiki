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

class WorkspaceUserLib extends TikiLib {
	function WorkspaceUserLib($db) {
		$this->TikiLib($db);
	}

	function list_groups($offset = 0, $maxRecords = -1, $sort_mode = 'groupName_desc', $find = '', $withpermissions = true) {
		$sort_mode = $this->convert_sortmode($sort_mode);

		// Return an array of users indicating name, email, last changed pages, versions, lastLogin 
		if ($find) {
			$mid = " where `groupName` like ?";
			$bindvars[] = "%".$find."%";
		} else {
			$mid = '';

			$bindvars = array ();
		}

		$query = "select `groupName` , `groupDesc` from `users_groups` $mid order by $sort_mode";
		$query_cant = "select count(*) from `users_groups`";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, false);

		$ret = array ();

		while ($res = $result->fetchRow()) {
			$aux = array ();

			$aux["groupName"] = $res["groupName"];
			$aux["groupDesc"] = $res["groupDesc"];
			$ret[] = $aux;
		}

		$retval = array ();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_inclusion($group, $inclusion) {

		$query = "delete from `tiki_group_inclusion` where `groupName` = ? and `includeGroup` = ?";
		$result = $this->query($query, array ($group, $inclusion));
		return true;
	}

	function get_descendant_groups($group, $includeParent = FALSE) {
		$engroup = urlencode($group);

		$query = "select `groupName`  from `tiki_group_inclusion` where `includeGroup`=?";
		$result = $this->query($query, array ($group));
		$ret = array ();
		if ($includeParent) {
			$ret[$group] = array ();
		}
		while ($res = $result->fetchRow()) {
			if ($includeParent) {
				$ret[$group][$res["groupName"]] = $res["groupName"];
			} else {
				$ret[] = $res["groupName"];
			}

			$ret2 = $this->get_descendant_groups($res["groupName"], $includeParent);
			$ret = array_merge($ret, $ret2);
		}
		if (!$includeParent) {
			$back = array_unique($ret);
		} else {
			$back = $ret;
		}

		return $back;
	}

	function get_includegrp_users($group) {

		$grupos = $this->get_descendant_groups($group);
		$grupos[] = $group;
		$usuarios = array ();

		$usuarios = array ();
		$usrgrp = array ();

		foreach ($grupos as $key => $group) {
			$usrgrp = $this->get_group_usersdata($group);
			$usuarios = array_merge($usuarios, $usrgrp);
		}

		$cleanUsrs = array();
		foreach ($usuarios as $key => $user) {
			$cleanUsrs[$user["userId"]] = $user;
		}
		return $cleanUsrs;
	}

	function get_group_usersdata($group) {
		//$query = "select uu.userId,uu.email,uu.login,uu.lastLogin,uu.registrationDate,up.value as name  from `users_users` uu left join `tiki_user_preferences` up on and uu.`login`=up.`user`, `users_usergroups` ug  where uu.`userId`=ug.`userId` and up.`prefName`=? and `groupName`=? order by uu.`login`";
		$query = "select uu.userId,uu.email,uu.login,uu.lastLogin,uu.registrationDate,up.value as name  from `users_users` uu left join `tiki_user_preferences` up on uu.`login`=up.`user` and up.`prefName`=?, `users_usergroups` ug  where uu.`userId`=ug.`userId` and ug.`groupName`=? order by uu.`login`";
		$params = array ();
		$params[] = "realName";
		$params[] = $group;
		$result = $this->query($query, $params);
		$ret = array ();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

}
?>