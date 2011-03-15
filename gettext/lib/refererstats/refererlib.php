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

class RefererLib extends TikiLib
{

	function clear_referer_stats() {
		$query = "delete from tiki_referer_stats";

		$result = $this->query($query);
	}

	function list_referer_stats($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');
			$mid = " where (`referer` like ?)";
			$bindvars = array($findesc);
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_referer_stats` $mid order by ".$this->convertSortMode($sort_mode);;
		$query_cant = "select count(*) from `tiki_referer_stats` $mid";
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
}
$refererlib = new RefererLib;
