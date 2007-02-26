<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
include_once ('typeslib.php');
include_once ('resourceslib.php');

class WorkspaceLib extends TikiDB {
	var $db; // The PEAR db object used to access the database
	function WorkspaceLib($db) {
		$this->TikiDB($db);
	}
	
	
	function get_href_to_workspace($workspace){
		return "tiki-workspaces_desktop.php?workspaceId=".$workspace["workspaceId"];
	}
	
	function add_workspace($code, $name, $desc, $startDate, $endDate, $closed, $parentId, $type, $categoryId, $owner = null, $isuserws = "n", $hide = "n") {
		$now = date("U");
		$query = "insert into tiki_workspaces(code,name,description,created,startDate,endDate,closed,parentId,type,categoryId,owner,isuserws,hide,uid) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$uid = md5(uniqid(rand()));
		$result = $this->db->query($query, array ($code, $name, $desc, (int) $now, (int) $startDate, (int) $endDate, $closed, $parentId, $type, $categoryId, $owner, $isuserws, $hide, $uid));
		return $uid;
	}
	function get_workspace_list($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $find = '') {
		$sort_mode = $this->convert_sortmode($sort_mode);
		$mid = '';
		$bindvars = array ();
		if ($find) {
			$mid = " where (`name` like ? or `code` like ?)";
			$bindvars = array ('%'.$find.'%', '%'.$find.'%');
		}
	
		$query = "select * from tiki_workspaces $mid order by $sort_mode";
		$query_cant = "select count(*) from tiki_workspaces $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$nres = 0;
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
			$nres ++;
		}
		//echo $cant;
		$retval = array ();
		if ($nres > 0) {
			$retval["data"] = $ret;
		}
		$retval["cant"] = $cant;
		return $retval;
	}
	
	function get_child_workspaces($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $parentWS = 0) {
		$sort_mode = $this->convert_sortmode($sort_mode);
		$mid = '';
		$bindvars = array ();
	
		if (isset ($parentWS)) {
			if ($mid == '') {
				$mid = " where ";
			} else {
				$mid = " and ";
			}
			$mid = $mid."parentId=?";
			$bindvars[] = $parentWS;
		}
		
		$query = "select * from tiki_workspaces $mid order by $sort_mode";
		$query_cant = "select count(*) from tiki_workspaces $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$nres = 0;
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
			$nres ++;
		}
		//echo $cant;
		$retval = array ();
		if ($nres > 0) {
			$retval["data"] = $ret;
		}
		$retval["cant"] = $cant;
		return $retval;
	}
	
	function list_active_workspaces() {
		$query = "select tiki_workspaces.*,tiki_workspace_types.name as typename from tiki_workspaces,tiki_workspace_types where tiki_workspaces.type=tiki_workspace_types.id and tiki_workspaces.closed=? and tiki_workspaces.startDate<? and tiki_workspaces.endDate>?";
		$result = $this->query($query, array ('n', date("U"), date("U")));
		$ret = array ();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}
	
	function get_workspace_by_uid($uid) {
		$query = "select * from tiki_workspaces where uid=?";
		$result = $this->db->query($query, array ($uid));
		$res = $result->fetchRow();
		return $res;
	}
	function get_workspace_by_id($id) {
		$query = "select * from tiki_workspaces where workspaceId=?";
		$result = $this->db->query($query, array ($id));
		$res = $result->fetchRow();
		return $res;
	}
	function get_workspace_by_code($code) {
		$query = "select * from tiki_workspaces where code=?";
		$result = $this->db->query($query, array ($code));
		$res = $result->fetchRow();
		return $res;
	}
	function get_workspace_by_catid($catid) {
		$query = "select * from tiki_workspaces where categoryId=?";
		$result = $this->db->query($query, array ($catid));
		$res = $result->fetchRow();
		return $res;
	}
	
	function del_workspace($id) {
		$query = "delete from tiki_workspaces where workspaceId=?";
		$result = $this->db->query($query, array ($id));
	}
	function update_workspace($id, $code, $name, $desc, $startDate, $endDate, $closed, $parentId, $type, $categoryId, $owner, $isuserws, $hide) {
		$query = "update tiki_workspaces set code=?,name=?,description=?,startDate=?,endDate=?,closed=?,parentId=?,type=?,categoryId=?,owner=?,isuserws=?,hide=? where workspaceId=?";
		$result = $this->db->query($query, array ($code, $name, $desc, $startDate, $endDate, $closed, $parentId, $type, $categoryId, $owner, $isuserws, $hide, $id));
		return true;
	}
	//Wokspace logic
	function get_workspace_path($workspaceId) {
		if ($workspaceId == 0) {
			$topWs = array ();
			$topWs["workspaceId"] = 0;
			$topWs["code"] = "TOP";
			$topWs["name"] = "TOP";
			$topWs["categoryId"] = 0;
			$path = array ();
			$path[] = $topWs;
			return $path;
		}
		$workspace = $this->get_workspace_by_id($workspaceId);
		$tmpPath = $this->get_workspace_path($workspace["parentId"]);
		$tmpPath[] = $workspace;
		return $tmpPath;
	}
	
	function create_workspace($code, $name, $desc, $startDate, $endDate, $closed, $parentId, $type, $parentCategoryId = null, $owner = null, $isuserws = "n", $hide = "n") {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$resourcesLib = new WorkspaceResourcesLib($this->db);
		$wsTypesLib = new WorkspaceTypesLib($this->db);
		$wsType = $wsTypesLib->get_workspace_type_by_id($type);
		$resources = unserialize($wsType["resources"]);
		$categoryId = $this->create_workspace_category($code, $name, $parentId, $parentCategoryId, $categlib);
		$this->create_workspace_groups($code, $wsType, $owner);
		$wsuid = $this->add_workspace($code, $name, $desc, $startDate, $endDate, $closed, $parentId, $type, $categoryId, $owner, $isuserws, $hide);
		$workspace = $this->get_workspace_by_uid($wsuid);
		//Categorize workspace
		$idCatObj = $categlib->add_categorized_object("workspace", $workspace["workspaceId"], $name, $code, "tiki-workspaces_desktop.php?workspaceId=".$workspace["workspaceId"]);
		$categlib->categorize($idCatObj, $categoryId);
		$this->assign_permissions($code, "workspace", $workspace["workspaceId"],$wsType);

		if (isset ($resources) && $resources != "" && count($resources) > 0) {
			foreach ($resources as $key => $resource) {
				$funcname = "create_".str_replace(" ", "", $resource["type"]);
				$id = null;
				if ($resource["type"] != "assignments") {
					$id = $resourcesLib-> $funcname ($code."-".$resource["name"], $resource["desc"], $categoryId);
				} else {
					$id = $resourcesLib-> $funcname ($code."-".$resource["name"], $resource["desc"], $categoryId, $workspace["workspaceId"]);
				}
				if (isset ($id)) {
					$this->assign_permissions($code, $resource["type"], $id,$wsType);
				}
			}
		}
		return $wsuid;
	}
	function remove_workspace($workspaceId) {
		if (isset($workspaceId) && $workspaceId!=0){
			global $categlib;
			include_once ('lib/categories/categlib.php');
			$resourcesLib = new WorkspaceResourcesLib($this->db);
			$ws = $this->get_workspace_by_id($workspaceId);
			if (isset ($ws) && $ws != "") {
				$this->remove_child_workspaces($workspaceId);
				$wsTypesLib = new WorkspaceTypesLib($this->db);
				$wsType = $wsTypesLib->get_workspace_type_by_id($ws["type"]);
				$this->remove_workspace_groups($ws["code"], $wsType);
				//$resourcesLib->remove_category_objects($ws["categoryId"]);
				
				$this->del_workspace($workspaceId);
				$this->remove_workspace_category($ws["categoryId"], $categlib);
				return true;
			} else {
				return false;
			}
		}
	}
	

	function remove_child_workspaces($parentws){
		$childs = $this->get_child_workspaces(0,-1,'name_desc',$parentws);
		if (isset($childs) && $childs!="" && $childs["cant"]>0){
			$childs = $childs["data"];
			if (isset($childs) && count($childs)>0){
				foreach ($childs as $key => $workspace) {
					$this->remove_workspace($workspace["workspaceId"]);
				}
			}
		}
	}
	
	function update_workspace_info($id, $code, $name, $desc, $startDate, $endDate, $closed, $parentId, $type, $categoryId, $parentCategoryId = null, $owner = null, $isuserws = "n", $hide = "n") {
		global $dbTiki;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$categlib3 = new CategLib($dbTiki);
		$resourcesLib = new WorkspaceResourcesLib($this->db);
		$oldws = $this->get_workspace_by_id($id);
		if(isset($oldws) && $oldws!=""){
			$categoryId = $this->update_workspace_category($oldws["categoryId"], $code, $name, $parentId, $parentCategoryId);
			$this->update_workspace($id, $code, $name, $desc, $startDate, $endDate, $closed, $parentId, $type, $categoryId, $owner, $isuserws, $hide);
			//Categorize workspace
			//$categlib3->uncategorize_object("workspace", $id);
			//$idCatObj = $categlib3->add_categorized_object("workspace", $id, $name, $code, "tiki-workspaces_desktop.php?workspaceId=".$id);
			//$categlib3->categorize($idCatObj, $categoryId);
			$wsTypesLib = new WorkspaceTypesLib($this->db);
			$wsType = $wsTypesLib->get_workspace_type_by_id($type);
			$resources = unserialize($wsType["resources"]);
			$this->create_workspace_groups($code, $wsType, $owner);
			//$this->create_workspace_user($code, $wsType);
			$this->assign_permissions($code, "workspace", $id,$wsType);
			if (isset ($resources) && $resources != "" && count($resources) > 0) {
				foreach ($resources as $key => $resource) {
					$funcname = "create_".str_replace(" ", "", $resource["type"]);
					$objid = null;
					if ($resource["type"] != "assignments") {
						$objid = $resourcesLib-> $funcname ($code."-".$resource["name"], $resource["desc"], $categoryId);
					} else {
						$objid = $resourcesLib-> $funcname ($code."-".$resource["name"], $resource["desc"], $categoryId, $id);
					}
					if (isset ($id)) {
						$this->assign_permissions($code,$resource["type"], $objid,$wsType);
					}
				}
			}
		}
	}
	function create_workspace_category($code, $name, $parentId, $parentCategoryId, $categlib) {
		if (!isset ($parentCategoryId) || $parentCategoryId == "") {
			$parentCategoryId = 0;
			if (isset ($parentId) && $parentId != "") {
				$parentws = $this->get_workspace_by_id($parentId);
				$parentCategoryId = $parentws["categoryId"];
			}
		}
		$categId = $categlib->add_category($parentCategoryId, $code, $name);
		return $categId;
	}
	function update_workspace_category($oldcatId, $code, $name, $parentId, $parentCategoryId) {
		global $dbTiki;
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$categlib2 = new CategLib($dbTiki);
		if (!isset ($parentCategoryId) || $parentCategoryId == "") {
			$parentCategoryId = 0;
			if (isset ($parentId) && $parentId != "") {
				$parentws = $this->get_workspace_by_id($parentId);
				$parentCategoryId = $parentws["categoryId"];
			}
		}
		$categId = $oldcatId;
		$oldcat = $categlib2->get_category($oldcatId);
		if (isset ($oldcat) && $oldcat != "") {
			$categlib2->update_category($oldcatId, $code, $name, $parentCategoryId);
		} /*else {
			$categId = $categlib2->add_category($parentCategoryId, $code, $name);
		}*/
		return $categId;
	}
	
	function remove_workspace_category($categoryId, $categlib) {
		if (isset($categoryId) && $categoryId!=0){
			$resourcesLib = new WorkspaceResourcesLib($this->db);
			$childCateg = $categlib->get_category_descendants($categoryId);
			foreach ($childCateg as $key => $categId) {
				$resourcesLib->remove_category_objects($categId);
			}
			
			$categlib->remove_category($categoryId);
		}
		return true;
	}
	
	function create_workspace_groups($code, $wsType, $wsuser = null) {
		global $userlib;
		$roles = $wsType["roles"];
		//print_r($roles);
		$userlib->add_group("WSGRP".$code, $code." workspace members".$code, '');
		foreach ($roles as $key => $rol) {
			if ($key!="Registered" && $key!="Anonymous"){
				$userlib->add_group("WSGRP".$code."-".$key, $code."-".$key." workspace members", '');
				$userlib->remove_all_inclusions("WSGRP".$code."-".$key);
				$userlib->group_inclusion("WSGRP".$code."-".$key, "WSGRP".$code);
				/*if (isset($rol["permgroup"]) && $rol["permgroup"]!=""){
					$userlib->group_inclusion("WSGRP".$code."-".$key, $rol["permgroup"]);
				}
				if (isset($rol["wstypePermGroup"]) && $rol["wstypePermGroup"]!=""){
					$userlib->group_inclusion("WSGRP".$code."-".$key, $rol["wstypePermGroup"]);
				}*/
				$userlib->group_inclusion("WSGRP".$code."-".$key, "ROLEGRP".$key);

				if ($key == "Owner" && $wsuser != null && isset ($wsType["userwstype"]) && $wsType["userwstype"] != "") {
					$userlib->assign_user_to_group($wsuser, "WSGRP".$code."-".$key);
				}
			}
		}
	}
	
	function remove_workspace_groups($code, $wsType) {
		global $userlib;
		$roles = $wsType["roles"];
		$userlib->remove_group("WSGRP".$code);
		foreach ($roles as $key => $rol) {
			$userlib->remove_group("WSGRP".$code."-".$key);
		}
	}
	
	/*
		function create_workspace_user($code, $wsType) {
			global $userlib;
			srand((double)microtime()*1000000);
			$password="";
			for($i=0;$i<30;$i++){
				$num = rand ( 40, 100);
				$password .= chr($num);
			}
			$userlib->add_user("WSUSER".$code, $password, "WSUSER".$code."@escire.com", '');
			$userlib->assign_user_to_group("WSUSER".$code, "WSTYPEGRP".$wsType["code"]);
		}
	
		function remove_workspace_user($code) {
			global $userlib;
			$userlib->remove_user("WSUSER".$code);
		}
	*/
	
	function assign_permissions($wscode, $objectType, $objectId,$wsType) {
		global $userlib;
		switch ($objectType)
			: case "blog" :
				$permType = "blogs";
				break;
			case "quiz":
				$permType = "quizzes";
				break;			
			case "faq" :
				$permType = "faqs";
				break;
			case "wiki page" :
			case "structure" :
				$permType = "wiki";
				$objectType = "wiki page";
				break;
			case "image gallery" :
				$permType = "image galleries";
				break;
			case "forum" :
				$permType = "forums";
				break;
			case "file gallery" :
				$permType = "file galleries";
				break;
			case "tracker" :
				$permType = "trackers";
				break;
			case "survey" :
				$permType = "surveys";
				break;
			default :
				$permType = $objectType;
		endswitch;
		
		$permsData = $userlib->get_permissions(0, -1, 'permName_desc', '', $permType);
		$perms = $permsData["data"];
		foreach ($wsType["roles"] as $key => $rol) {
			$group = $key;
			if ($key!="Registered" && $key!="Anonymous"){
				$group = "WSGRP".$wscode."-".$key;
			}
			if (isset($rol["wstypePermGroup"]) && $rol["wstypePermGroup"]!=""){
				$permgroup = $rol["wstypePermGroup"]; //Take group from wstype role 
			}else{
				$permgroup = $rol["permgroup"]; // Take group from role
			}
			//$levels = unserialize($rol["levels"]);
			//if (isset ($levels) && $levels != "") {
			if (isset ($permgroup) && $permgroup != "") {
				$permissionsTemplate = $userlib->get_group_permissions($permgroup);
				foreach ($perms as $permKey => $perm) {
					if (in_array($perm["permName"], $permissionsTemplate)) {
						//echo "<br>Asignar grupo:".$group." objectId:".$objectId." objectType:".$objectType." perm:".$perm["permName"];
						$userlib->assign_object_permission($group, $objectId, $objectType, $perm["permName"]);
					}
				}
			}
		}
		
		/*if(isset($wsType["anonymous"]) && $wsType["anonymous"]=="y"){
			$anonymous_perms = $userlib->get_group_permissions("Anonymous");
			foreach ($perms as $permKey => $perm) {
					if (in_array($perm["permName"], $anonymous_perms)) {
						//echo "<br>Asignar grupo:".$group." objectId:".$objectId." objectType:".$objectType." perm:".$perm["permName"];
						$userlib->assign_object_permission("Anonymous", $objectId, $objectType, $perm["permName"]);
					}
			}
		}
		
		if(isset($wsType["registered"]) && $wsType["registered"]=="y"){
			$registered_perms = $userlib->get_group_permissions("Registered");
			foreach ($perms as $permKey => $perm) {
					if (in_array($perm["permName"], $registered_perms)) {
						//echo "<br>Asignar grupo:".$group." objectId:".$objectId." objectType:".$objectType." perm:".$perm["permName"];
						$userlib->assign_object_permission("Registered", $objectId, $objectType, $perm["permName"]);
					}
			}
		}*/
	}
	
	function get_user_workspaces($wsuser) {
		global $userlib;
		$grupos = $userlib->get_user_groups($wsuser);
		$userWorkspaces = array ();
		foreach ($grupos as $key => $group) {
			if (substr($group, 0, 5) == "WSGRP") {
				$pos2 = 0;
				do{
					$pos = $pos2;
					$pos2 = strpos($group, "-",$pos2+1);
				}while($pos2>0);
					if ($pos === false || $pos == 0) {
						// not a role group
						$pos = strlen($group);
					}
					$wscode = substr($group, 5, $pos -5);
					$workspace = $this->get_workspace_by_code($wscode);
					if (isset ($workspace) && $workspace != "") {
						$userWorkspaces[$wscode] = $workspace;
					}else{
						$pos = strlen($group);
						$wscode = substr($group, 5, $pos -5);
						$workspace = $this->get_workspace_by_code($wscode);
						if (isset ($workspace) && $workspace != "") {
							$userWorkspaces[$wscode] = $workspace;
						}
					}
			}
		}
		return $userWorkspaces;
	}
	function get_current_workspace() {
		if (!isset ($_SESSION["currentWorkspace"]) && (!isset ($_REQUEST["workspaceId"]) || $_REQUEST["workspaceId"] == "") && !isset ($_REQUEST["workspaceCode"])) {
			return null;
		}
		global $dbTiki;
		require_once ('tiki-setup.php');
		require_once ('lib/workspaces/typeslib.php');
		$wsTypesLib = new WorkspaceTypesLib($dbTiki);
		$workspace = null;
		if (!isset ($_REQUEST["wsuser"]) && isset ($_SESSION["currentWorkspace"]) && isset ($_REQUEST["workspaceId"]) && $_SESSION["currentWorkspace"]["workspaceId"] == $_REQUEST["workspaceId"]) {
			return $_SESSION["currentWorkspace"];
		}
		elseif (isset ($_REQUEST["workspaceId"])) {
			$workspace = $this->get_workspace_by_id($_REQUEST["workspaceId"]);
			if ((isset ($workspace) && $workspace != "")) {
				$wstype = $wsTypesLib->get_workspace_type_by_id($workspace["type"]);
				$workspace["type"] = $wstype;
			}
		}
		elseif (isset ($_REQUEST["workspaceCode"])) {
			$workspace = $this->get_workspace_by_code($_REQUEST["workspaceCode"]);
			if ((!isset ($workspace) || $workspace == "") && strlen($_REQUEST["workspaceCode"])>3 && substr($_REQUEST["workspaceCode"],0,3)=="PWS") {
				$workspace = $this->create_user_portfolio($_REQUEST["workspaceCode"],$wsTypesLib);
			}
			if(isset($workspace) && $workspace!=""){
				$wstype = $wsTypesLib->get_workspace_type_by_id($workspace["type"]);
				$workspace["type"] = $wstype;
			}
		} else
			if (isset ($_SESSION["currentWorkspace"]) && $_SESSION["currentWorkspace"] != "") {
				$workspace = $_SESSION["currentWorkspace"];
			}
		if (!isset ($workspace) || $workspace == "") {
			return null;
		}
		$wstype = $workspace["type"];
		if (isset ($_REQUEST["wsuser"]) && isset ($wstype["userwstype"]) && $wstype["userwstype"] != "") {
			$subWorkspace = $this->get_workspace_by_code("LF".$workspace["code"].$_REQUEST["wsuser"]);
			$lfwstype = $wsTypesLib->get_workspace_type_by_id($wstype["userwstype"]);
			if (!isset ($subWorkspace) || $subWorkspace == "") {
				//LF = Learning Folder
				if (isset ($lfwstype["hide"]) && $lfwstype["hide"] != "") {
					$hiden = $lfwstype["hide"];
				} else {
					$hiden = "n";
				}
				//$endDate = date('U',mktime(0,0,0,date("m"),  date("d"),  date("Y")+1));
				$wsuid = $this->create_workspace("LF".$workspace["code"].$_REQUEST["wsuser"], $_REQUEST["wsuser"]." personal workspace", "", $workspace["startDate"], $workspace["endDate"], "n", $workspace["workspaceId"], $wstype["userwstype"], null, $_REQUEST["wsuser"], "y", $hiden);
				$subWorkspace = $this->get_workspace_by_uid($wsuid);
			}
			$wstype = $lfwstype;
			$workspace = $subWorkspace;
			$workspace["type"] = $wstype;
		}
		$_SESSION["currentWorkspace"] = $workspace;
		return $workspace;
	}
	
	function create_user_portfolio($wscode,$wsTypesLib){
		$userws = substr($wscode,3);
		global $userlib;
		if($userlib->user_exists($userws)){
			$wstype = $wsTypesLib->get_workspace_type_by_code("PORTFOLIO");
			$wstypeFolder = $wsTypesLib->get_workspace_type_by_code("FOLDER");
			if (isset ($wstype) && $wstype != "" && isset ($wstypeFolder) && $wstypeFolder != "") {
				$portfoliosws = $this->get_workspace_by_code("PORTFOLIOS");
				if (!isset ($portfoliosws) || $portfoliosws == "") { // Create PORTFOLIOS workspace folder
					$wstypeFolder = $wsTypesLib->get_workspace_type_by_code("FOLDER");
					$wsuid = $this->create_workspace("PORTFOLIOS", "Portfolios folder", "", date("U"), date("U"), "n", 0, $wstypeFolder["id"], null, "admin", "n", $wstypeFolder["hide"]);
					$portfoliosws = $this->get_workspace_by_uid($wsuid);
				}
				$wsuid = $this->create_workspace("PWS".$userws, $userws." portfolio", "", date("U"), date("U"), "n", $portfoliosws["workspaceId"], $wstype["id"], null, $userws, "n", $wstype["hide"]);
				
				$workspace = $this->get_workspace_by_uid($wsuid);
				return $workspace;
			}
		}
		return null;
	}
	
	function parentInSelWorspaces($workspace, $selectedWorkspaces) {
		$response = false;
		if (isset ($selectedWorkspaces) && count($selectedWorkspaces) > 0 && isset ($workspace) && $workspace != "") {
			foreach ($selectedWorkspaces as $key => $selws) {
				if ($workspace["parentId"] == $selws["workspaceId"]) {
					$response = true;
				}
				elseif ($workspace["workspaceId"] == $selws["workspaceId"]) {
					return false;
				}
			}
		}
		return $response;
	}
	
}
?>
