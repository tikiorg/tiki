<?php
// CVS: $Id: objectlib.php,v 1.1 2005-12-06 18:08:04 lfagundes Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// this is an abstract class
class ObjectLib extends TikiLib {

    function add_object($type, $itemId, $description, $name, $href) {
	$description = strip_tags($description);
	$name = strip_tags($name);
	$now = date("U");
	
	$query = "insert into `tiki_objects`(`type`,`itemId`,`description`,`name`,`href`,`created`,`hits`)
    values(?,?,?,?,?,?,?)";
	$result = $this->query($query,array($type,(string) $itemId,$description,$name,$href,(int) $now,0));
	$query = "select `objectId` from `tiki_objects` where `created`=? and `type`=? and `itemId`=?";
	$id = $this->getOne($query,array((int) $now,$type,(string) $itemId));
	return $id;
    }

}