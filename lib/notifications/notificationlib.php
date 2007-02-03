<?php
/**
 * @class NotificationLib
 *
 * This class provides an events notification
 *
 * @license GNU LGPL
 * @copyright Tiki Community
 * @date created:
 * @date last-modified: 2005-08-26 13:01
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
class NotificationLib extends TikiLib {
	function NotificationLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to NotificationLib constructor");
		}

		$this->db = $db;
	}

	function list_mail_events($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`event` like ? or `email` like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = " ";
			$bindvars=array();
		}

		$query = "select * from `tiki_mail_events` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_mail_events` $mid";
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

	function add_mail_event($event, $object, $email) {
		$query = "insert into `tiki_mail_events`(`event`,`object`,`email`) values(?,?,?)";
		$result = $this->query($query, array($event,$object,$email) );
	}

	function remove_mail_event($event, $object, $email) {
		$query = "delete from `tiki_mail_events` where `event`=? and `object`=? and `email`=?";
		$result = $this->query($query,array($event,$object,$email));
	}
	
	function update_mail_address($oldMail, $newMail) {
		$query = "update `tiki_mail_events` set `email`=? where `email`=?";
		$result = $this->query($query,array($newMail,$oldMail));
	}

	function get_mail_events($event, $object) {
		$query = "select `email` from `tiki_mail_events` where `event`=? and (`object`=? or `object`='*')";
		$result = $this->query($query, array($event,$object) );
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res["email"];
		}

		return $ret;
	}

}

global $dbTiki;
$notificationlib = new NotificationLib($dbTiki);

?>
