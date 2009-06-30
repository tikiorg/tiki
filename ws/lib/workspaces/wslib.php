<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * TikiWiki CMS/GroupWare
 *
 * LICENSE
 *
 * This source file is subject to the LGPL license that is bundled
 * with this package in the file license.txt. If your distribuition 
 * doesn't have the license file, please go to http://license.com to see 
 * the complete license of the software.
 *
 * @category   	workspaces
 * @package   	lib
 * @author	Benjamin Palacios (mangapower) <mangapowerx@gmail.com>
 */

include_once 'lib/categories/categlib.php';

/**
 * wslib
 *
 * @category	TikiWiki
 * @package	lib/workspaces
 * @version	$Id
 */
class wslib extends CategLib 
{
	function __construct()
	{
		global $dbTiki;
		parent::CategLib($dbTiki);
	}

    // This function will set a container for WS in the category table and return its ID
	function init_ws($name)
	{
		return parent::add_category(0, $name, 'Workspaces Container');
	}
    
    // Get the name and its parent WS and create a new WS (NOTE: parentID = WSContainerID)
	function add_ws ($name, $parentWS)
	{
		global $prefs;
		$wsContainerId = (int) $prefs['ws_container'];
		return parent::add_category($wsContainerId,$name,(string) $parentWS);
	}
	
    // Remove a WS
	function remove_ws ($ws_id)
	{
		global $prefs;
		$wsContainerId = (int) $prefs['ws_container'];
		$newParent = parent::get_category_description($ws_id);
		$query = "update `tiki_categories` set `description`=? where `description`=? and `parentId`=?";
		$bindvars = array($newParent,$ws_id,$wsContainerId);
	    // All its sub-workspaces will level up	
		$result = $this->query($query,$bindvars);
		
		$objectType = 'ws';
		$hashWS = md5($objectType . strtolower($ws_id));
		$query = "delete from `users_objectpermissions` where `objectId` = ?"; 
		$bindvars = array($hashWS);
	    // Remove the WS permissions stored in objectpermissions
	   	$result = $this->query($query,$bindvars);
		
	    // Remove the WS permissions stored in objectpermissions
		return parent::remove_category($ws_id);
	}
	
    // Add an object to a WS
	function add_ws_object ($ws_id,$itemId,$type)
	{
		return parent::categorize_any($type, $itemId, $ws_id );
	} 
	
    // Remove an object from a WS
	function remove_ws_object ($ws_id,$ws_ObjectId)
	{
		return parent::remove_object_from_category($ws_ObjectId, $ws_id);
	}

    // Get a WS Id
	public function get_ws_id($name, $parentWS)
	{
		global $prefs;
		$query = "select `categId` from `tiki_categories` where `name`=? and `description`=? and `parentId`=?";
		$wsContainerId = (int) $prefs['ws_container'];
		$bindvars = array($name, $parentWS, $wsContainerId);
		return $this->getOne($query, $bindvars);
	}
	
    // Add a group into a WS
	function add_ws_group ($ws_id,$groupName)
	{
		$objectType = 'ws';
		$hashWS = md5($objectType . strtolower($ws_id));

	    // If already exists, overwrite 
		$query = "delete from `users_objectpermissions`
		where `groupName` = ? and
		`permName` = ? and
		`objectId` = ?";
		$result = $this->query($query, array($groupName, 'tiki_p_ws_view',$hashWS), -1, -1, false);

		$query = "insert into `users_objectpermissions`(`groupName`,
		`objectId`, `objectType`, `permName`)
		values(?, ?, ?, ?)";		
		$result = $this->query($query, array($groupName, $hashWS,'ws', 'tiki_p_ws_view'));
		
		return true;
	}
    
}
