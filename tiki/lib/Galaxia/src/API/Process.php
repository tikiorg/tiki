<?php
//!! 
//! 
/*!

*/
class Process extends Base {
	var $name;
	var $description;
	var $version;
	var $normalizedName;
	
	function Process($db) 
	{
		$this->db=$db;
	}
	
	function getProcess($pId)
	{
		$query = "select * from galaxia_processes where pId=$pId";
		$result = $this->query($query);
	    if(!$result->numRows()) return false;
    	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    	$this->name = $res['name'];
    	$this->description = $res['description'];
    	$this->normalizedName = $res['normalized_name'];
    	$this->version = $res['version'];
	}
	
	function getNormalizedName()
	{
	  	return $this->normalizedName;
	}
	
	function getName()
	{
		return $this->name;
	}
	
	function getVersion()
	{
		return $this->version;
	}
}
?>