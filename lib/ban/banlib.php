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

class BanLib extends TikiLib
{
	function get_rule($banId) {
		$query = "select * from `tiki_banning` where `banId`=?";

		$result = $this->query($query,array($banId));
		$res = $result->fetchRow();
		$aux = array();
		$query2 = "select `section` from `tiki_banning_sections` where `banId`=?";
		$result2 = $this->query($query2,array($banId));
		$aux = array();

		while ($res2 = $result2->fetchRow()) {
			$aux[] = $res2['section'];
		}

		$res['sections'] = $aux;
		return $res;
	}

	function remove_rule($banId) {
		$query = "delete from `tiki_banning` where `banId`=?";

		$this->query($query,array($banId));
		$query = "delete from `tiki_banning_sections` where `banId`=?";
		$this->query($query,array($banId));
	}

	function list_rules($offset, $maxRecords, $sort_mode, $find, $where = '') {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where ((`message` like ?) or (`title` like ?))";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		// DB abstraction: TODO
		if ($where) {
			if ($mid) {
				$mid .= " and ($where) ";
			} else {
				$mid = "where ($where) ";
			}
		}

		$query = "select * from `tiki_banning` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_banning` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$query2 = "select * from `tiki_banning_sections` where `banId`=?";
			$result2 = $this->query($query2,array($res['banId']));

			while ($res2 = $result2->fetchRow()) {
				$aux[] = $res2;
			}

			$res['sections'] = $aux;
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		$query = "select `banId` from `tiki_banning` where `use_dates`=? and `date_to` < FROM_UNIXTIME(?)";
		$result = $this->query($query,array('y',$this->now));

		while ($res = $result->fetchRow()) {
			$this->remove_rule($res['banId']);
		}

		return $retval;
	}

	/*
	banId integer(12) not null auto_increment,
	  mode enum('user','ip'),
	  title varchar(200),
	  ip1 integer(3),
	  ip2 integer(3),
	  ip3 integer(3),
	  ip4 integer(3),
	  user varchar(200),
	  date_from timestamp,
	  date_to timestamp,
	  use_dates char(1),
	  message text,
	  primary key(banId)
	  */
	function replace_rule($banId, $mode, $title, $ip1, $ip2, $ip3, $ip4, $user, $date_from, $date_to, $use_dates, $message,
		$sections) {

		if ($banId) {
			$query = " update `tiki_banning` set
  			`title`=?,
  			`ip1`=?,
  			`ip2`=?,
  			`ip3`=?,
  			`ip4`=?,
  			`user`=?,
  			`date_from` = FROM_UNIXTIME(?),
  			`date_to` = FROM_UNIXTIME(?),
  			`use_dates` = ?,
  			`message` = ?
  			where `banId`=?
  		";

			$this->query($query,array($title,$ip1,$ip2,$ip3,$ip4,$user,$date_from,$date_to,$use_dates,$message,$banId));
		} else {
			$query = "insert into `tiki_banning`(`mode`,`title`,`ip1`,`ip2`,`ip3`,`ip4`,`user`,`date_from`,`date_to`,`use_dates`,`message`,`created`)
		values(?,?,?,?,?,?,?,FROM_UNIXTIME(?),FROM_UNIXTIME(?),?,?,?)";
			$this->query($query,array($mode,$title,$ip1,$ip2,$ip3,$ip4,$user,$date_from,$date_to,$use_dates,$message,$this->now));
			$banId = $this->getOne("select max(`banId`) from `tiki_banning` where `created`=?",array($this->now));
		}

		$query = "delete from `tiki_banning_sections` where `banId`=?";
		$this->query($query,array($banId));

		foreach ($sections as $section) {
			$query = "insert into `tiki_banning_sections`(`banId`,`section`) values(?,?)";

			$this->query($query,array($banId,$section));
		}
	}
}
$banlib = new BanLib;
