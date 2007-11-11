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
		$this->TikiLib($db);
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
	/**
	 *  Request a callback
	 *  @param event the event about which we want to be
	 *  notified
	 *  @param callback type - one of "early", "standard", "late".
	 *  Early: Request a callback before the standard callback
	 *  functions are called.  This is useful for setting
	 *  up temporary data structures needed by the standard
	 *  callback functions, or pre-processing event data.
	 *  Standard: Request a standard callback
	 *  Late: Request a callback after the standard callback
	 *  functions are called. This is useful for destroying
	 *  temporary data structures needed by the standard
	 *  callback functions, or post-processing event data.
	 *  @param method the callback function to call when
	 *  the event is raised
	 *  @param self the object raising the event
	 *  @access public
	 */
	function register_callback( $event,
                                    $callback_type, 
                                    $method, 
                                    $class ) {
        	// clobber any already registered callbacks
        	$this->unregister_callback($event, $callback_type, $method, $class);
        	// if (is_signal($event) && is_callable(array(get_class($class), $method))) {
        	//  case $callback_type
        	//    early:
        	//    standard:
        	//    late:
        	//      insert into tiki_signals( $event, $callback_type, $self, $func );
        	//      break;
        	//    default:
        	//      return fault;
    		// }
 	}
	/**
	 *  Raise a Tikiwiki event
	 *  @param event the event to raise
	 *  @param data the data associated with the event
	 *  @param self the object raising the event
	 */
	function raise_event( $event, $data, $raisedBy ) {
		global $Debug;
		if ($Debug) print "event raised: $event<br />";
		$maxRecords = 3*100;
       		// get list of early objects that want to be notified about
       		// this event, order by callback_type.order
       		$query = "select * from `tiki_events` where event like ? order by `callback_type`, `order`";
		$bindvars=array('%'.$event.'%');
		$query_cant = "select count(*) from `tiki_events` where event like ?";
		$result = $this->query($query,$bindvars,$maxRecords);
                $cant = $this->getOne($query_cant,$bindvars);
		$continue = true;
                while ($continue && $res = $result->fetchRow()) {
			$class = $res['object'];
			$method = $res['method'];
			global $$class;
                        include_once( $res['file'] );
			if ($Debug) print $class . "=>" . $method ."<br />";
			if ( is_callable(array(get_class($$class), $method)) ) {
				if ($Debug) print $class . "=>" . $method . "<br />";
				$continue = $$class->$method($raisedBy, $data);
			}
                }
	}
 
	/**
	 *  Cancel callback requests.
	 *  @param event the event to cencel
	 *  @param callback_type the callback type - see register_callback
	 *  @param func the function to call
	 *  @param self the object requesting the callback
	 *  @return true if database has changed
	 */
	function unregister_callback( $event, $callback_type, $func, $self ) {
       		// delete from tiki_signal where event == $event and func == $func and self == $self;
       		return $success;
	}
}
global $dbTiki;
$notificationlib = new NotificationLib($dbTiki);
?>
