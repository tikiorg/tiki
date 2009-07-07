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
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false)
{
	header("location: index.php");
	exit;
}

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
	function __construct()
	{
		global $dbTiki;
		parent::CategLib($dbTiki);
	}

    // This function will set a container for WS in the category table and return its ID
	function init_ws()
	{
		return parent::add_category(0, '', 'Workspaces Container');
	}
    
    // Create a new WS (NOTE: parentID will be always WSContainerID)
	function add_ws ($name, $parentWS, $groupName, $permList=null)
	{
		include_once('lib/userslib.php');
		global $prefs;
		$wsContainerId = (int) $prefs['ws_container'];
		
		// If the group doesn't exist, then it's created. Otherwise, nothing will happen.
		$userlib->add_group($groupName);
		
		// The workspace is created
		$wsID = parent::add_category($wsContainerId,$name,(string) $parentWS);
		
		// It's given the tiki_p_ws_view permission to the selected group
		$this->set_permission_for_group_in_ws($wsID,$groupName,'tiki_p_ws_view');
		
		// If the group will have additional admin permissions in the current ws
		if ($permList != null)
			$this->set_permissions_for_group_in_ws($wsID,$groupName,$permList);
		
		return $wsID;
	}
	
    // Remove a WS
	function remove_ws ($ws_id)
	{
		global $prefs;
		$wsContainerId = (int) $prefs['ws_container'];
		$newParent = parent::get_category_description($ws_id);
		$query = "update `tiki_categories` set `description`=? where 
		`description`=? and `parentId`=?";
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
		$query = "select `categId` from `tiki_categories` where `name`=? and 
		`description`=? and `parentId`=?";
		$wsContainerId = (int) $prefs['ws_container'];
		$bindvars = array($name, $parentWS, $wsContainerId);
		return $this->getOne($query, $bindvars);
	}
	
    // Give a set of permissions to a group for a specific WS (view, addresources, addgroups,...)
	function set_permissions_for_group_in_ws ($ws_id,$groupName,$permList)
	{
		$objectType = 'ws';
		$hashWS = md5($objectType . strtolower($ws_id));
		
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
    	function list_groups_that_can_access_in_ws ($ws_id)
    	{    	
    		$objectType = 'ws';
		$hashWS = md5($objectType . strtolower($ws_id));
		
		$query = "select `groupName` from `users_objectpermissions` where 
		`objectId`=? and `permName`='tiki_p_ws_view'";
		$bindvars = array($hashWS);
		
		return $this->query($query,$bindvars);    	
    	}
    	
    // List all WS that a group have access
	function list_ws_that_can_be_accessed_by_group ($groupName)
	{	
		$query = "select `objectId` from `users_objectpermissions` where 
		`groupName`=? and `permName`='tiki_p_ws_view'";
		$bindvars = array($groupName);
		
		return $this->query($query,$bindvars); 	
	}
	
	function list_all_ws($offset, $maxRecords, $sort_mode = 'name_asc', $find, $type, $objid)
	{
		$cats = $this->get_object_categories($type, $objid);

		global $prefs;
		if ($idws = $prefs['ws_container'] )
		if ($find)
		{
			$findesc = '%' . $find . '%';
			$bindvals=array($findesc);
			$mid = " where `name` like ? and `parentId`=$idws";
		}
		else 
		{
      			$bindvals=array();
			$mid = "where `parentId`=$idws";
		}
		
		$query = "select * from `tiki_categories` $mid";// order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_categories` $mid ";
		$result = $this->query($query,$bindvals,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvals);
		$ret = array();
		
		while ($res = $result->fetchRow()) 
		{
			if (!empty($cats) && in_array($res["categId"], $cats)) 
				$res["incat"] = 'y';
			else
				$res["incat"] = 'n';
      
			$catpath = $this->get_category_path($res["categId"]);
			$tepath = array();	
			foreach ($catpath as $cat)
				$tepath[] = $cat['name'];
			$categpath = implode("::",$tepath);
			$categpathforsort = implode("!!",$tepath); // needed to prevent cat::subcat to be sorted after cat2::subcat 
			$res["categpath"] = $categpath;
			$res["tepath"] = $tepath;
			$res["deep"] = count($tepath);
			$res['name'] = $this->get_category_name($res['categId']);
			global $userlib;
			if ($userlib->object_has_one_permission($res['categId'], 'category')) 
				$res['has_perm'] = 'y';
			 else 
				$res['has_perm'] = 'n';
			$ret["$categpathforsort"] = $res;
		}

		ksort($ret);
		
		$retval = array();
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}
	

}

global $dbTiki;
$wslib = new wslib( );
