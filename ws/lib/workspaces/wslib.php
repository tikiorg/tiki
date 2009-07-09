<?php
/**
 * wslib.php - TikiWiki CMS/GroupWare
 *
 * This library enable the basic management of workspaces (WS)
 * 
 * @package	lib
 * @author	Benjamin Palacios Gonzalo (mangapower) <mangapowerx@gmail.com>
 * @license	http://www.opensource.org/licenses/lgpl-2.1.php
 */

//Controlling Access
require_once 'tiki-setup.php';
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

//Rest of Imports
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
    private $ws_container;
    private $objectType;

    public function __construct()
	{
		global $dbTiki, $prefs;
		parent::CategLib($dbTiki);

		$this->ws_container = (int) $prefs['ws_container'];
		$this->objectType = 'ws';
	}

    // This function will set a container for WS in the category table and return its ID
    public function init_ws()
    {
	if (!$this->ws_container)
	    return parent::add_category(0, '', 'Workspaces Container');
	else return $this->ws_container;
    }
    
    // Create a new WS (NOTE: parentID will be always WSContainerID)
    public function add_ws ($name, $parentWS, $groupName, $additionalPerms=null)
    {
	$wsID = parent::add_category($this->ws_container,$name,(string) $parentWS);
	$this->add_ws_group ($wsID, $name, $groupName, $additionalPerms);
	return $wsID;
    }

    //For creating new groups in ws
    public function add_ws_group ($idWS, $wsName=null, $nameGroup, $additionalPerms=null) 
    {
	global $userlib; require_once 'lib/userslib.php';

	if (!$wsName)
	    $wsName = $this->get_ws_name($idWS);

	$groupName = ((string) $idWS)."::".$wsName."::".$nameGroup; //With this you can create two groups with same name in different ws

	if ($userlib->add_group($groupName)) 
	{
    	    // It's given the tiki_p_ws_view permission to the selected group in the new ws
	    $this->set_permissions_for_group_in_ws($wsID,$groupName,array('tiki_p_ws_view'));
	
    	    // It's added additional admin permissions to the group in the new ws
	    if ($additionalPerms != null)
		$this->set_permissions_for_group_in_ws($wsID,$groupName,$additionalPerms);

	    return true;
	}
	else
	    return false;
    }
	
    // Remove a WS -For now, not very useful, we need to create it again!-
    public function remove_ws ($ws_id)
    {
	$newParent = parent::get_category_description($ws_id);
	$query = "update `tiki_categories` set `description`=? where 
	`description`=? and `parentId`=?";
	$bindvars = array($newParent,$ws_id,$this->ws_container);
    	// All its sub-workspaces will level up	
	$result = $this->query($query,$bindvars);
	
	$hashWS = md5($this->objectType . strtolower($ws_id));
	$query = "delete from `users_objectpermissions` where `objectId` = ?"; 
	$bindvars = array($hashWS);
    	// Remove the WS permissions stored in objectpermissions
   	$result = $this->query($query,$bindvars);
		
	// Remove the WS permissions stored in objectpermissions
	return parent::remove_category($ws_id);
    }
	
    // Add an object to a WS
	public function add_ws_object ($ws_id,$itemId,$type)
	{
		return parent::categorize_any($type, $itemId, $ws_id );
	} 
	
    // Remove an object from a WS
	public function remove_ws_object ($ws_id,$ws_ObjectId)
	{
		return parent::remove_object_from_category($ws_ObjectId, $ws_id);
	}

    // Get a WS Id
	public function get_ws_id($name, $parentWS)
	{
		$query = "select `categId` from `tiki_categories` where `name`=? and `parentId`=?";
		$bindvars = array($name, $parentWS, $this->ws_container);
		return $this->getOne($query, $bindvars);
	}

	//Get a WS name by its id
	public function get_ws_name($idWS)
	{
	    $query = "select `categId` from `tiki_categories` where `categId`=? ";
	    $bindvars = array($idWS);
	    return $this->getOne($query, $bindvars);
	}
	
    // Give a set of permissions to a group for a specific WS (view, addresources, addgroups,...)
	public function set_permissions_for_group_in_ws ($ws_id,$groupName,$permList)
	{
		$hashWS = md5($this->objectType . strtolower($ws_id));
		
		foreach ($permList as $permName)
		{
		    // If already exists, overwrite 
			$query = "delete from `users_objectpermissions`
			where `groupName` = ? and
			`permName` = ? and
			`objectId` = ?";
			$this->query($query, array($groupName, $permName,$hashWS), -1, -1, false);
	
			$query = "insert into `users_objectpermissions`(`groupName`,
			`objectId`, `objectType`, `permName`)
			values(?, ?, ?, ?)";		
			$this->query($query, array($groupName, $hashWS,'ws', $permName));
		}	
		return true;
	}
	
    // List the groups that have access to a WS
    	public function list_groups_that_can_access_in_ws ($ws_id)
    	{    	
		$hashWS = md5($this->objectType . strtolower($ws_id));
		
		$query = "select `groupName` from `users_objectpermissions` where 
		`objectId`=? and `permName`='tiki_p_ws_view'";
		$bindvars = array($hashWS);
		$result = $this->query($query,$bindvars);
		
		while ($res = $result->fetchRow())
			$listWSGroups[] = $res;
		return $listWSGroups;
    	}
    	
    // List all WS that a group have access
	public function list_ws_that_can_be_accessed_by_group ($groupName)
	{	
		$query = "select `objectId` from `users_objectpermissions` where (`groupName`=? and `permName`='tiki_p_ws_view') ";
		$bindvars = array($groupName);
		$result = $this->query($query,$bindvars);
		
		while ($res = $result->fetchRow())
			$groupWS[] = $res["objectId"];
		
		$idws = $this->ws_container;
		$query = "select * from `tiki_categories` where `parentId`= $idws";
		$bindvars = array();
		$listWS = $this->query($query,$bindvars);
		
		while ($res = $listWS->fetchRow()) 
		{
			$ws_id = $res["categId"];
			$hashWS = md5($this->objectType . strtolower($ws_id));
			
			if (in_array($hashWS,$groupWS))
				$listGroupWS[$res["categId"]] = $res;
		}
		return $listGroupWS;
	}

	//List all WS - Needs more integration
	public function list_all_ws ($offset, $maxRecords, $sort_mode= 'name_asc', $find, $type, $objid)
	{
		return parent::list_all_categories ($offset, $maxRecords, $sort_mode = 'name_asc', $find, $type, $objid);
	}
	
    // List all WS that can be accessed by a user
	public function list_ws_that_user_have_access ($user)
	{
		require_once('lib/userslib.php');
		global $userlib;
		
		$ws = array();		
		
		$groups = $userlib->get_user_groups($user);		
		foreach ($groups as $groupName)
		{
			$groupWS =  $this->list_ws_that_can_be_accessed_by_group ($groupName);
			foreach ($groupWS as $wsres)
				if (!in_array($wsres,$ws))
					$ws[$wsres["categId"]] = $wsres;
		}
		return $ws;
	}
	
    // List the objects stored in a workspace
	function list_ws_objects ($ws_id)
	{
		$query = "select `catObjectId` from `tiki_category_objects` where `categId`= ?";
		$bindvars = array($ws_id);
		$result = $this->query($query,$bindvars);
		while ($res = $result->fetchRow())
			$listObjects[] = $res["catObjectId"];
		
		foreach ($listObjects as $objectId)
		{
			$query = "select * from `tiki_objects` where `objectId`= ?";
			$bindvars = array($objectId);
			$result = $this->query($query,$bindvars);
			while ($res = $result->fetchRow())
				$listWSObjects[] = $res;
		}
		
		return $listWSObjects;
	}
    // Get the stored perms for a object for a specific group
	function get_object_perms_for_group ($objId,$objectType,$groupName)
	{
		$objectId = md5($objectType . strtolower($objId));
		$query = "select `permName` from `users_objectpermissions` where `groupName`=? and `objectId`=? and `objectType`=?";
		$bindvars = array($groupName,$objectId,$objectType);
		$result = $this->query($query,$bindvars);
		while ($res = $result->fetchRow())
			$objectPermsGroup[] = $res["permName"];
		return $objectPermsGroup;
	}
	
    // List the objects stored in a workspace for a specific user
	function list_ws_objects_for_user ($ws_id,$user)
	{
		require_once('lib/userslib.php');
		global $userlib;
		
		$listWSObjects = $this->list_ws_objects($ws_id);
		
		foreach ($listWSObjects as $object)
		{
			$objId = $object["itemId"];
			$objectType = $object["type"];
			$groups = $userlib->get_user_groups($user);
			
			$gotPerm = false;		
			foreach ($groups as $groupName)
			{
				if (!$gotPerm)
				{
					$objectPermsGroup = $this->get_object_perms_for_group ($objId,$objectType,$groupName);
					if (in_array('tiki_p_view',$objectPermsGroup))
					{
						$listWSObjectsUser[] = $object;
						$gotPerm = true;
					}
				}
			}
		} 
		
		return $listWSObjectsUser;
	}
}

$wslib = new wslib();
