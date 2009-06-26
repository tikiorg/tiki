<?php
/** \file
 * $Id: /cvsroot/tikiwiki/tiki/lib/workspaces/wslib.php by mangapower
 *
 * \brief Workspaces support class
 *
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class wslib 
{
	
	private $wsContainerId = $prefs['ws_container_id'];
	
	function __autoload() 
	{
	     require_once ('lib/categories/categlib.php');
	     require_once ('tiki-setup.php')
	}

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
	}
	
	// Add a object to a WS
	function add_ws_object ($ws_id,$object_id)
	{
		
	} 
}


