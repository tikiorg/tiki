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

	function __construct()
	{
		global $dbTiki, $prefs;
		parent::CategLib($dbTiki);

		$this->ws_container = (int) $prefs['ws_container'];
		$this->objectType = 'ws';
	}

    // This function will set a container for WS in the category table and return its ID
	function init_ws()
	{
		return parent::add_category(0, '', 'Workspaces Container');
	}
    
    // Create a new WS (NOTE: parentID will be always WSContainerID)
	function add_ws ($name, $parentWS, $groupName, $permList=null)
	{
		require_once('lib/userslib.php');
		global $userlib;
		
		// If the group doesn't exist, then it's created. Otherwise, nothing will happen.
		$userlib->add_group($groupName);
		
		// The workspace is created
		$wsID = parent::add_category($this->ws_container,$name,(string) $parentWS);
		
		// It's given the tiki_p_ws_view permission to the selected group in the new ws
		$this->set_permissions_for_group_in_ws($wsID,$groupName,array('tiki_p_ws_view'));
		
		// It's added additional admin permissions to the group in the new ws
		if ($permList != null)
			$this->set_permissions_for_group_in_ws($wsID,$groupName,$permList);
		
		return $wsID;
	}
	
    // Remove a WS
	function remove_ws ($ws_id)
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
		$query = "select `categId` from `tiki_categories` where `name`=? and 
		`description`=? and `parentId`=?";
		$bindvars = array($name, $parentWS, $this->ws_container);
		return $this->getOne($query, $bindvars);
	}
	
    // Give a set of permissions to a group for a specific WS (view, addresources, addgroups,...)
	function set_permissions_for_group_in_ws ($ws_id,$groupName,$permList)
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
    	function list_groups_that_can_access_in_ws ($ws_id)
    	{    	
		$hashWS = md5($this->objectType . strtolower($ws_id));
		
		$query = "select `groupName` from `users_objectpermissions` where 
		`objectId`=? and `permName`='tiki_p_ws_view'";
		$bindvars = array($hashWS);
		$result = $this->query($query,$bindvars);
		
		while ($ret = $result->fetchRow())
			$listWSGroups[] = $ret;
		return $listWSGroups;
    	}
    	
    // List all WS that a group have access
	function list_ws_that_can_be_accessed_by_group ($groupName)
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
			{
				$workspaceID = $res["categId"];
				$listGroupWS["$workspaceID"] = $res;
			}
		}
		return $listGroupWS;
	}
	
    // List the WS that a user have access
	function	list_ws_that_user_have_access ($user)
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
					$ws[] = $wsres;
		}
		return $ws;
	}
	
	function list_all_ws($offset, $maxRecords, $sort_mode = 'name_asc', $find, $type, $objid)
	{
		$cats = $this->get_object_categories($type, $objid);
		$idws = $this->ws_container;
		
		if ($this->ws_container)
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
		echo ("\n<br>");

		ksort($ret);
		
		$retval = array();
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		
		return $retval;
	}
	
	
}

$wslib = new wslib();
