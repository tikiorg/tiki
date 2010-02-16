<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @class NotificationLib
 *
 * This class provides an events notification
 *
 * @license GNU LGPL
 * @copyright Tiki Community
 * @date created:
 */
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
// callback_type is 1, 3 or 5 - all other values are reserved
define ("TIKI_CALLBACK_EARLY", 1);
define ("TIKI_CALLBACK_STANDARD", 3);
define ("TIKI_CALLBACK_LATE", 5);
if (!isset($Debug)) $Debug = false;
/**
 * This class provides an events notification
 *
 * If an object wishes to register early, standard and
 * callbacks for the smae event, it must use different
 * function names.  Any attempt to use the same
 * combination of object, function and event name will
 * simply cause the previously-registered callback to
 * be cancelled and the new one to be registered.
 * 
 * @since 1.x
 */
class NotificationLib extends TikiLib
{
	function list_mail_events($offset, $maxRecords, $sort_mode, $find) {
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`event` like ? or `email` like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = " ";
			$bindvars=array();
		}
		$query = "select * from `tiki_user_watches` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_watches` $mid";
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
	function update_mail_address($user, $oldMail, $newMail) {
		$query = "update `tiki_user_watches` set `email`=? where `user`=? and `email`=?";
		$result = $this->query($query,array($user,$newMail,$oldMail));
	}
	function get_mail_events($event, $object) {
		global $tikilib;
		global $categlib; require_once('lib/categories/categlib.php');
		$query = 'select * from `tiki_user_watches` where `event`=? and (`object`=? or `object`=?)';
		$result = $this->query($query, array($event, $object, '*') );
		$ret = array();
		$map = CategLib::map_object_type_to_permission();
		while ($res = $result->fetchRow()) {
			if (empty($res['user']) || $tikilib->user_has_perm_on_object($res['user'], $object, $res['type'], $map[$res['type']])) {
				$ret[] = $res['email'];
			}
		}
		return $ret;
	}

}
$notificationlib = new NotificationLib;
