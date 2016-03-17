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

include_once('lib/polls/polllib_shared.php');

/**
 * PollLib 
 * 
 * @uses PollLibShared
 */
class PollLib extends PollLibShared
{

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_polls($offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`title` like ?)";
			$bindvars = array($findesc);
		} else {
			$mid = "";
			$bindvars = array();
		}

		$query = "select * from `tiki_polls` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_polls` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query = "select count(*) from `tiki_poll_options` where `pollId`=?";

			$res["options"] = $this->getOne($query, array($res["pollId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_active_polls($offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`active`=? or `active`=? or `active`=?) and `publishDate`<=? and (`title` like ?)";
			$bindvars=array('a', 'c', 'o', (int) $this->now, $findesc);
		} else {
			$mid = " where (`active`=? or `active`=? or `active`=?) and `publishDate`<=? ";
			$bindvars=array('a', 'c', 'o', (int) $this->now);
		}

		$query = "select * from `tiki_polls` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_polls` $mid";
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
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_all_polls($offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `publishDate`<=? and (`title` like ?)";
			$bindvars = array((int) $this->now, $findesc);
		} else {
			$mid = " where `publishDate`<=? ";
			$bindvars=array((int) $this->now);
		}

		$query = "select * from `tiki_polls` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_polls` $mid";
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

	function set_last_poll()
	{
		$query = "select max(`publishDate`) from `tiki_polls` where `publishDate`<=?";
		$last = $this->getOne($query, array((int) $this->now));
		$query = "update `tiki_polls` set `active`=? where `publishDate`=?";
		$result = $this->query($query, array('c', $last));
	}

	function close_all_polls()
	{
		$query = "select max(`publishDate`) from `tiki_polls` where `publishDate`<=?";
		$last = $this->getOne($query, array((int) $this->now));
		$query = "update `tiki_polls` set `active`=? where `publishDate`<=?";
		$result = $this->query($query, array('x', (int) $this->now));
	}

	function active_all_polls()
	{
		$query = "update `tiki_polls` set `active`=? where `publishDate`<=?";
		$result = $this->query($query, array('a', (int) $this->now));
	}

    /**
     * @param $optionId
     * @return bool
     */
    function remove_poll_option($optionId)
	{
		$query = "delete from `tiki_poll_options` where `optionId`=?";
		$result = $this->query($query, array($optionId));
		return true;
	}

    /**
     * @param $optionId
     * @return bool
     */
    function get_poll_option($optionId)
	{
		$query = "select * from `tiki_poll_options` where `optionId`=?";
		$result = $this->query($query, array($optionId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

}

