<?php
// CVS: $Id: objectlib.php,v 1.2 2005-12-06 20:10:53 lfagundes Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// this is an abstract class
class ObjectLib extends TikiLib {

    function add_object($type, $itemId, $description = '', $name = '', $href = '') {
	$description = strip_tags($description);
	$name = strip_tags($name);
	$now = date("U");

	$query = "select `objectId` from `tiki_objects` where `type`=? and `itemId`=?";
	$objectId = $this->getOne($query, array($type, $itemId));

	if ($objectId) {
	    if (!empty($description) || !empty($name) || !empty($href)) {
		$query = "update `tiki_objects` set `description`=?,`name`=?,`href`=? where `objectId`=?";
		$this->query($query,array($description,$name,$href,$objectId));
	    }
	    return $objectId;
	} else {
	
	    $query = "insert into `tiki_objects`(`type`,`itemId`,`description`,`name`,`href`,`created`,`hits`)
    values(?,?,?,?,?,?,?)";
	    $result = $this->query($query,array($type,(string) $itemId,$description,$name,$href,(int) $now,0));
	    $query = "select `objectId` from `tiki_objects` where `created`=? and `type`=? and `itemId`=?";
	    $objectId = $this->getOne($query,array((int) $now,$type,(string) $itemId));
	    return $objectId;
	}
    }

    function get_object_id($type, $itemId) {
	$query = "select `objectId` from `tiki_objects` where `type`=? and `itemId`=?";
	return $this->getOne($query, array($type, $itemId));
    }

}