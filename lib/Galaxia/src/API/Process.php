<?php
//!! Process.php
//! A class representing a process
/*!
This class representes the process that is being executed when an activity
is executed. You can access this class methods using $process from any activity.
No need to instantiate a new object.
*/
class Process extends Base {
	var $name;
	var $description;
	var $version;
	var $normalizedName;
	
	function Process($db) {
		$this->db=$db;
	}
	
	/*!
	Loads a process form the database
	*/
	function getProcess($pId) {
		$query = "select * from `galaxia_processes` where `pId`=?";
		$result = $this->query($query,array($pId));
	    if(!$result->numRows()) return false;
    	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    	$this->name = $res['name'];
    	$this->description = $res['description'];
    	$this->normalizedName = $res['normalized_name'];
    	$this->version = $res['version'];
	}
	
	/*!
	Gets the normalized name of the process
	*/
	function getNormalizedName() {
	  	return $this->normalizedName;
	}
	
	/*!
	Gets the process name
	*/
	function getName() {
		return $this->name;
	}
	
	/*!
	Gets the process version
	*/
	function getVersion() {
		return $this->version;
	}
}
?>
