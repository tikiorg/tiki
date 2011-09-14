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

class ContributionLib extends TikiLib
{
	function add_contribution($name, $description = '') {
		$query = 'insert into `tiki_contributions`(`name`, `description`) values(?, ?)';
		$this->query($query, array($name, $description));
	}
	function get_contribution($contributionId) {
		$query = 'select * from `tiki_contributions` where `contributionId`=?';
		$result = $this->query($query, array((int)$contributionId));
		$ret = $result->fetchRow();
		return $ret;
	}
	function replace_contribution($contributionId, $name, $description='') {
		$query = 'update `tiki_contributions` set `name`= ?, `description`=? where `contributionId`=?';
		$this->query($query, array($name, $description, (int)$contributionId)); 
	}
	function remove_contribution($contributionId) {
		$query = 'delete from `tiki_contributions`where `contributionId`=?';
		$this->query($query, array($contributionId));
	}
	function list_contributions($offset=0, $maxRecords=-1, $sort_mode='name_asc', $find='') {
		$bindvars = array();
		if ($find) {
			$mid = " where (`name` like ?)";
			$bindvars[] = "%$find%";
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_contributions` $mid order by ".$this->convertSortMode($sort_mode);
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$query_cant = "select count(*) from `tiki_contributions` $mid";
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
	function assign_contributions($contributions, $itemId, $objectType, $description='', $name='', $href='') {
		global $objectlib; include_once('lib/objectlib.php');
		if (($objectId = $objectlib->get_object_id($objectType, $itemId)) == 0) {
			$objectId = $objectlib->insert_object($objectType, $itemId, $description, $name, $href);
		} else {
			$query = 'delete from `tiki_contributions_assigned` where `objectId`=?';
			$this->query($query, array((int)$objectId));
		}
		if (!empty($contributions)) {
			$query = 'insert `tiki_contributions_assigned` (`contributionId`, `objectId`) values(?,?)';
			foreach ($contributions as $contribution) {
				if ($contribution)
					$this->query($query, array((int)$contribution, (int)$objectId));
			}
		}
	}

	function get_assigned_contributions($itemId, $objectType) {
		$query = "select tc.* from `tiki_contributions` tc, `tiki_contributions_assigned` tca, `tiki_objects` tob where tob.`itemId`=? and tob.`type`=? and tca.`objectId`=tob.`objectId` and tca.`contributionId`= tc.`contributionId` order by tob.`type`desc, tc.`name` asc";
		$result = $this->query($query, array($itemId, $objectType));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;		
	}
	function change_assigned_contributions($itemIdOld, $objectTypeOld, $itemIdNew, $objectTypeNew, $description, $name, $href) {
		if ($this->get_assigned_contributions($itemIdOld, $objectTypeOld)) {
			global $objectlib; include_once('lib/objectlib.php');
			if (($objectId = $objectlib->get_object_id($objectTypeNew, $itemIdNew)) == 0)// create object
				$objectId = $objectlib->insert_object($objectTypeNew, $itemIdNew, $description, $name, $href);
			$query = 'update `tiki_contributions_assigned` tca left join `tiki_objects` tob on tob.`objectId`= tca.`objectId` set tca.`objectId`=? where tob.`itemId`=? and tob.`type`=?';
			$this->query($query, array((int)$objectId, $itemIdOld, $objectTypeOld));
		}
	}
	function remove_assigned_contributions($itemId, $objectType) {
		// works only if mysql> 4 $query = 'delete tca from `tiki_contributions_assigned` tca left join `tiki_objects`tob on tob.`objectId`=tca.`objectId` where tob.`itemId`= ? and tob.`type`= ?';
		global $objectlib; include_once('lib/objectlib.php');
		$objectId = $objectlib->get_object_id($objectType, $itemId);
		$query = 'delete from `tiki_contributions_assigned` where `objectId`= ?';
		$this->query($query, array($objectId));
	}
	function remove_page($page) {
		global $objectlib; include_once('lib/objectlib.php');
		$query = 'select * from `tiki_history` where `pageName` = ?';
		$result = $this->query($query, array($page));
		while ($res = $result->fetchRow()) {
			$this->remove_history($res['historyId']);
		}
		$this->remove_assigned_contributions($page, 'wiki page');
	}
	function remove_history($historyId) {
			//history object only created for contribution yet. You can remove object
		global $objectlib; include_once('lib/objectlib.php');
		$this->remove_assigned_contributions($historyId, 'history');
		$objectlib->delete_object('history', $historyId);
	}
	function remove_comment($commentId) {
			//history object only created for contribution yet. You can remove object
		global $objectlib; include_once('lib/objectlib.php');
		$this->remove_assigned_contributions($commentId, 'comment');
		$objectlib->delete_object('comment', $commentId);
	}
	function print_contributions($contributions) {
		$print = '';
		foreach($contributions as $contribution) {
			if ( !empty($print) ) {
				$print.= ',';
			}
			$res = $this->get_contribution($contribution);
			$print .= $res['name'];
		}
		return $print;
	}
	function update($action, $contributions, $delay=15) {
		global $tikilib;
		global $logslib; include_once('lib/logs/logslib.php');

		if ($action['objectType'] == 'wiki page' && $action['action'] != 'Removed') {
			// try to find an history
			$query = "select * from `tiki_history` where `pageName`=? and `lastModif` <=? and `lastModif` >= ? and `user`=?";
			$result = $tikilib->query($query,array($action['object'], $action['lastModif']+$delay, $action['lastModif'], $action['user']));
			if (($nb = $result->numRows()) == 1) {
				$res = $result->fetchRow();
				$this->assign_contributions($contributions, $res['historyId'], 'history');
			} elseif ($nb == 0) {
				$info = $tikilib->get_page_info($action['object']);
				if ($info['lastModif'] <= $action['lastModif']) { //it is the page
					$this->assign_contributions($contributions, $info['pageName'], 'wiki page');
				} else
					return false;
			} else
				return false;
		} else {
			if ($action['objectType'] == 'comment' || $action['objectType'] == 'forum' )
				if ($commentId = $logslib->get_comment_action($action)) {
					$this->assign_contributions($contributions, $commentId, 'comment');
				} else {
					return false;
				}
			else
				$this->assign_contributions($contributions, $action['object'], $action['objectType']);
		}
		return true;
	}
}
$contributionlib = new ContributionLib;
