<?php

class MenuLib extends TikiLib {
	function MenuLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to MenuLib constructor");
		}

		$this->db = $db;
	}

	function list_menus($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `tiki_menus` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_menus` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query = "select count(*) from `tiki_menu_options` where `menuId`=?";

			$res["options"] = $this->getOne($query,array((int)$res["menuId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_menu($menuId, $name, $description, $type) {
		// Check the name
		if (isset($menuId)) {
			$query = "update `tiki_menus` set `name`=?,`description`=?,`type`=? where `menuId`=?";
			$bindvars=array($name,$description,$type,(int)$menuId);
		} else {
			// was: replace into. probably we need a delete here
			$query = "insert into `tiki_menus`(`name`,`description`,`type`) values(?,?,?)";
			$bindvars=array($name,$description,$type);
		}

		$result = $this->query($query,$bindvars);
		return true;
	}

	function get_max_option($menuId) {
		$query = "select max(`position`) from `tiki_menu_options` where `menuId`=?";

		$max = $this->getOne($query,array((int)$menuId));
		return $max;
	}

	function replace_menu_option($menuId, $optionId, $name, $url, $type, $position, $section, $perm, $groupname) {
		if ($optionId) {
			$query = "update `tiki_menu_options` set `name`=?,`url`=?,`type`=?,`position`=?,`section`=?,`perm`=?,`groupname`=?  where `optionId`=?";
			$bindvars=array($name,$url,$type,$position,$section,$perm,$groupname,$optionId);
		} else {
			$query = "insert into `tiki_menu_options`(`menuId`,`name`,`url`,`type`,`position`,`section`,`perm`,`groupname`) values(?,?,?,?,?,?,?,?)";
			$bindvars=array((int)$menuId,$name,$url,$type,$position,$section,$perm,$groupname);
		}

		$result = $this->query($query, $bindvars);
		return true;
	}

	function remove_menu($menuId) {
		$query = "delete from `tiki_menus` where `menuId`=?";

		$result = $this->query($query,array((int)$menuId));
		$query = "delete from `tiki_menu_options` where `menuId`=?";
		$result = $this->query($query,array((int)$menuId));
		return true;
	}

	function remove_menu_option($optionId) {
		$query = "delete from `tiki_menu_options` where `optionId`=?";

		$result = $this->query($query,array((int)$optionId));
		return true;
	}

	function get_menu_option($optionId) {
		$query = "select * from `tiki_menu_options` where `optionId`=?";

		$result = $this->query($query,array((int)$optionId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}
}

$menulib = new MenuLib($dbTiki);

?>
