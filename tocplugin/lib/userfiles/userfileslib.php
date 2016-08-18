<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 *
 */
class UserFilesLib extends TikiLib
{

    /**
     * @param $user
     * @return int
     */
    function userfiles_quota($user)
	{
		if ($user == 'admin') {
			return 0;
		}

		$part1 = $this->getOne("select sum(`filesize`) from `tiki_userfiles` where `user`=?", array($user));
		$part2 = $this->getOne("select sum(`size`) from `tiki_user_notes` where `user`=?", array($user));
		return $part1 + $part2;
	}

    /**
     * @param $user
     * @param $name
     * @param $filename
     * @param $filetype
     * @param $filesize
     * @param $data
     * @param $path
     */
    function upload_userfile($user, $name, $filename, $filetype, $filesize, $data, $path)
	{
		$query = "insert into `tiki_userfiles`(`user`,`name`,`filename`,`filetype`,`filesize`,`data`,`created`,`hits`,`path`)
    values(?,?,?,?,?,?,?,?,?)";
		$this->query($query, array($user,$name,$filename,$filetype,(int) $filesize,$data,(int) $this->now,0,$path));
	}

    /**
     * @param $user
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_userfiles($user, $offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`filename` like ?)";
			$bindvars=array($user,$findesc);
		} else {
			$mid = " ";
			$bindvars=array($user);
		}

		$query = "select `fileId`,`user`,`name`,`filename`,`filetype`,`filesize`,`created`,`hits` from `tiki_userfiles` where `user`=? $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_userfiles` where `user`=? $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $user
     * @param $fileId
     * @return mixed
     */
    function get_userfile($user, $fileId)
	{
		$query = "select * from `tiki_userfiles` where `user`=? and `fileId`=?";

		$result = $this->query($query, array($user,(int) $fileId));
		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $user
     * @param $fileId
     */
    function remove_userfile($user, $fileId)
	{
		global $prefs;

		$path = $this->getOne("select `path` from `tiki_userfiles` where `user`=? and `fileId`=?", array($user,(int) $fileId));

		if ($path) {
			@unlink($prefs['uf_use_dir'] . $path);
		}

		$query = "delete from `tiki_userfiles` where `user`=? and `fileId`=?";
		$this->query($query, array($user,(int) $fileId));
	}
}
$userfileslib = new UserFilesLib;
