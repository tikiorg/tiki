<?php
require_once ('../../tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

require_once ('lib/categories/categlib.php');
require_once ('lib/userslib.php');

class wslib 
{
	
	private $wsContainerId = $prefs['ws_container_id'];
	
	// This function will set a container for WS in the category table and return its ID
	function init_ws ($newName)
	{
		if (empty($categlib->get_category_id($newName)))
			return $wsContainer = $categlib->add_category(0,$newName,'Workspaces Container Category');
		else
			return -1; //Error - a category with the same name already exists (REALLY HARD TO HAPPEN)
	}
	
	// Get the name and its parent WS and create a new WS (NOTE: parentID = WSContainerID)
	function add_ws ($newName, $parent)
	{
		$query = "select `categId` from `tiki_categories` where `name`=? and `parentId`=?";
		if (empty($id = $this->getOne($query,array($newName,(int) $wsContainerId))))
		{
			return $wsId = $categlib->add_category($wsContainerId,$newName,$parent);
		}
		else
			return -1; //Error - A WS with the same name already exists
	}
	
	// Remove a WS
	function remove_ws ($ws_id)
	{
		$newParent=$categlib->get_category_description($ws_id);
		// All its sub-workspaces will level up 
		$query="update `tiki_categories` set `description` = replace (`description`,?,?)";	
		$levelup = query($query,array((string)$ws_id,$newParent);
		return $result = $categlib->remove_category($ws_id);
	}
	
	// Add an object to a WS
	function add_ws_object ($ws_id,$object_id,$type)
	{
		return $result = $categlib->categorize_any($type, $object_id, $ws_id );
	} 
	
	// Remove an object from a WS
	function remove_ws_object ($ws_id,$object_id)
	{
		return $result = $categlib->remove_object_from_category($object_id, $ws_id);
	}
	
	// Add a group into a WS
	function add_ws_group ($ws_id,$groupname)
	{
		return $result = $userslib -> assign_object_permission($groupname,$ws_id,'Workspace','tiki_p_ws_view');
	}
	
	//Get the groups that have access in a WS
	function get_ws_groups ($ws_id)
	{
		$objectId = md5($objectType . strtolower($objectId));
		$query = "select `groupName` from `users_objectpermissions` where `permName`=? and `objectId`=?";
		$bindvars = array('tiki_p_ws_view', $objectType);
		$result = $this->query($query, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow())
		{
		   	 $ret[] = $res;
		}
		return $ret;
}


