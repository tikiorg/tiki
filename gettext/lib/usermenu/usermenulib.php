<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class UserMenuLib extends TikiLib
{

	function add_bk($user) {
		$query = "select tubu.`name`,`url` from `tiki_user_bookmarks_urls` tubu, `tiki_user_bookmarks_folders` tubf where tubu.`folderId`=tubf.`folderId` and tubf.`parentId`=? and tubu.`user`=?";

		$result = $this->query($query,array(0,$user));
		$start = $this->get_max_position($user) + 1;

		while ($res = $result->fetchRow()) {
			// Check for duplicate URL
			if (!$this->getOne("select count(*) from `tiki_user_menus` where `url`=?",array($res['url']))) {
				$this->replace_usermenu($user, 0, $res['name'], $res['url'], $start, 'w');

				$start++;
			}
		}

		$query = "select tubu.`name`,`url` from `tiki_user_bookmarks_urls` tubu where tubu.`folderId`=? and tubu.user=?";
		$result = $this->query($query,array(0,$user));
		$start = $this->get_max_position($user) + 1;

		while ($res = $result->fetchRow()) {
			// Check for duplicate URL
			if (!$this->getOne("select count(*) from `tiki_user_menus` where `url`=?",array($res['url']))) {
				$this->replace_usermenu($user, 0, $res['name'], $res['url'], $start, 'w');

				$start++;
			}
		}
	}

	function list_usermenus($user, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`name` like ? or url like ?)";
			$bindvars=array($user,$findesc,$findesc);
		} else {
			$mid = " ";
			$bindvars=array($user);
		}

		$query = "select * from `tiki_user_menus` where `user`=? $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_menus` where `user`=? $mid";
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

	function get_usermenu($user, $menuId) {
		$query = "select * from `tiki_user_menus` where `user`=? and `menuId`=?";

		$result = $this->query($query,array($user,$menuId));
		$res = $result->fetchRow();
		return $res;
	}

	function get_max_position($user) {
		return $this->getOne("select max(position) from `tiki_user_menus` where `user`=?",array($user));
	}

	function replace_usermenu($user, $menuId, $name, $url, $position, $mode) {

		if ($menuId) {
			$query = "update `tiki_user_menus` set `name`=?, `position`=?, `url`=?, `mode`=? where `user`=? and `menuId`=?";

			$this->query($query,array($name,$position,$url,$mode,$user,$menuId));
			return $menuId;
		} else {
			$query = "insert into `tiki_user_menus`(`user`,`name`,`url`,`position`,`mode`) values(?,?,?,?,?)";

			$this->query($query,array($user,$name,$url,$position,$mode));
			$Id = $this->getOne("select max(`menuId`) from `tiki_user_menus` where `user`=? and `url`=? and `name`=?",array($user,$url,$name));
			return $Id;
		}
	}

	function remove_usermenu($user, $menuId) {
		$query = "delete from `tiki_user_menus` where `user`=? and `menuId`=?";

		$this->query($query,array($user,$menuId));
	}
}
$usermenulib = new UserMenuLib;
