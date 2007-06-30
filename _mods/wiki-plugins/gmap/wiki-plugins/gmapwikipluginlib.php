<?php

/*

Name:			Library for "Google Map page plugin for Tiki Wiki/CMS/Groupware."
Description:	Creates a Google Map to map a geo-coded location of a wiki page, backlinked pages, or pages in a structure.
				See notes below for requirements and additional instructions.
Author:			Nelson Ko (nelson@wordmaster.org)
License:		LGPL
Version:		1.0 ( 2007-06-30)

Refer to main file wikiplugin_gmap.php in <tikiroot>/lib/wiki-plugins/ for more info
This library is needed for that, and should be deployed to <tikiroot>/lib/

*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class Gmapwikiplugin extends TikiLib {
#  var $db;  // The PEAR db object used to access the database    

    function Gmapwikiplugin($db) {
	if (!$db) {
	    die ("Invalid db object passed to GmapwikipluginLib constructor");
	}
	$this->db = $db;
    }

	function get_page_preference($pageName, $name, $default = '') {	
		$pageid = $this->get_page_id_from_name($pageName);
					
	    $query = "select `value` from `wikiplugin_gmap` where `pref`=? and `pageid`=?";
	    $result = $this->getOne($query, array( "$name", "$pageid"));
	    
	    if (!$result) {
			return $default;
	    } else {
			return $result;
	    }		
    }
    	 
    function set_page_preference($pageName, $name, $value) {		
		$pageid = $this->get_page_id_from_name($pageName);
		
		$query = "delete from `wikiplugin_gmap` where `pageid`=? and `pref`=?";
		$bindvars=array($pageid,$name);
		$result = $this->query($query, $bindvars, -1,-1,false);
		
		$query = "insert into `wikiplugin_gmap`(`pageid`,`pref`,`value`) values(?, ?, ?)";
		$bindvars[]=$value;
		$result = $this->query($query, $bindvars);
		
		return true;
    }
        		 
    function copy_page_preferences($pageFrom, $pageTo) {	
  		// first get preferences
  		$pageid = $this->get_page_id_from_name($pageFrom);  
  		$pageid_to = $this->get_page_id_from_name($pageTo);  
  		
  		$query = "select `pref`,`value` from `wikiplugin_gmap` where `pageid` = ?";
  		$result = $this->query($query, array( $pageid ));
  		
  		if (!$result->numRows())
	    	return false;
		$ret = array();	
		while ($res = $result->fetchRow()) {
	    	$ret[] = $res;
		}
  		foreach ($ret as $pref) {
			$query = "insert into `wikiplugin_gmap`(`pageid`,`pref`,`value`) values(?, ?, ?)";
			$bindvars = array($pageid_to, $pref["pref"], $pref["value"]);
			$this->query($query, $bindvars);
		}
		
		return true;
    }

}
    
global $dbTiki;
$gmapwikipluginlib = new Gmapwikiplugin($dbTiki);

?>
