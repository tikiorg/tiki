<?php

class UserMenuLib extends TikiLib {
	function UserMenuLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to UserMenuLib constructor");
		}

		$this->db = $db;
	}

	function add_bk($user) {
		$query = "select tubu.name,url from tiki_user_bookmarks_urls tubu, tiki_user_bookmarks_folders tubf where tubu.folderId=tubf.folderId and tubf.parentId=0 and tubu.user='$user'";

		$result = $this->query($query);
		$start = $this->get_max_position($user) + 1;

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			// Check for duplicate URL
			if (!$this->getOne("select count(*) from tiki_user_menus where url='" . $res['url'] . "'")) {
				$this->replace_usermenu($user, 0, $res['name'], $res['url'], $start, 'w');

				$start++;
			} else {
			}
		}

		$query = "select tubu.name,url from tiki_user_bookmarks_urls tubu where tubu.folderId=0 and tubu.user='$user'";
		$result = $this->query($query);
		$start = $this->get_max_position($user) + 1;

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			// Check for duplicate URL
			if (!$this->getOne("select count(*) from tiki_user_menus where url='" . $res['url'] . "'")) {
				$this->replace_usermenu($user, 0, $res['name'], $res['url'], $start, 'w');

				$start++;
			} else {
			}
		}
	}

	function list_usermenus($user, $offset, $maxRecords, $sort_mode, $find) {
		$sort_mode = str_replace("_desc", " desc", $sort_mode);

		$sort_mode = str_replace("_asc", " asc", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " and (name like $findesc or url like $findesc)";
		} else {
			$mid = " ";
		}

		$query = "select * from tiki_user_menus where user='$user' $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from tiki_user_menus where user='$user' $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_usermenu($user, $menuId) {
		$query = "select * from tiki_user_menus where user='$user' and menuId='$menuId'";

		$result = $this->query($query);
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}

	function get_max_position($user) {
		return $this->getOne("select max(position) from tiki_user_menus where user='$user'");
	}

	function replace_usermenu($user, $menuId, $name, $url, $position, $mode) {
		$name = addslashes($name);

		$url = addslashes($url);
		$now = date("U");

		if ($menuId) {
			$query = "update tiki_user_menus set name='$name', position=$position, url='$url', mode='$mode' where user='$user' and menuId=$menuId";

			$this->query($query);
			return $menuId;
		} else {
			$query = "insert into tiki_user_menus(user,name,url,position,mode) values('$user','$name','$url',$position,'$mode')";

			$this->query($query);
			$Id = $this->getOne("select max(menuId) from tiki_user_menus where user='$user' and url='$url' and name='$name'");
			return $Id;
		}
	}

	function remove_usermenu($user, $menuId) {
		$query = "delete from tiki_user_menus where user='$user' and menuId=$menuId";

		$this->query($query);
	}
}

$usermenulib = new UserMenuLib($dbTiki);

?>