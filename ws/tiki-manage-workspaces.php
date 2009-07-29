<?php
 
require_once('tiki-setup.php');
require_once('lib/workspaces/wslib.php');

$title = "WorkSpace Management";
$smarty->assign('headtitle', tra($title));

// Check if the user can admin WS

if ( isset($_REQUEST['wsName']))
{	
	$name = $_REQUEST['wsName'];
	$description = $_REQUEST["wsDesc"];
	$parentWS= $_REQUEST["parentWS"];
	$groupDesc = $_REQUEST["groupDesc"];
	$adminPerms = $_REQUEST["adminPerms"];
	
	// If selected, create a new group
	if ($_REQUEST['groupSelect'] == "new")
	{
		$groupName = $_REQUEST['newGroup'];
		$noCreate = false;
	} 
	// else, will select a previously created group
	else if ($_REQUEST['groupSelect'] == "old")
	{
		$groupName = $_REQUEST['oldGroup'];
		$noCreate = true;
	}
	
	// Check if the name and the group are written
	if (($name == "") || ($groupName == ""))
	{
		if ($name == "")
		{
				$smarty->assign('msg', tra("Workspace can not be blank"));
				$smarty->display("error.tpl");
				die;
		}
		else if ($groupName == "")
		{
				$smarty->assign('msg', tra("Group name can not be blank"));
				$smarty->display("error.tpl");
				die;
		}
	}
	
	// Check if a WS with the same name exists in the same level
	$wsid = $wslib->get_ws_id($name, $parentWS);
	if (!empty($wsid))
	{
			$smarty->assign('msg', tra("There already exists a Workspace with that name in the same level"));
			$smarty->display("error.tpl");
			die;
	}
	
	// If everything is ok, then the Workspace is created
	else
	{
		$perms = array($adminPerms);
		$groups = array();
		$groups[] = array(
					"groupName" => $groupName,
					"groupDescription" => $groupDesc,
					"noCreateNewGroup" => $noCreate,
					"additionalPerms" => $perms
					);
		$wslib->create_ws ($name, $groups, $parentWS, $description);
		
		header("Location: ./tiki-manage-workspaces.php");
	}
}

else if ( isset($_REQUEST['editedWS']))
{
	$wsId = $_REQUEST['editedWS'];
	
	$wsName = $_REQUEST['wsNewName'];	
	$wsDesc = $_REQUEST['wsNewDesc'];
	
	$wslib->update_ws_data($wsId, $wsName, $wsDesc);
	
	header("Location: ./tiki-manage-workspaces.php?editWS=".$wsId);
}

else if ( isset($_REQUEST['addObjectinWS']))
{
	$wsId = $_REQUEST['addObjectinWS'];
	$name = $_REQUEST['objectName'];
	$type = $_REQUEST['selectType'];
	$description = $_REQUEST['objectDesc'];
	
	$wslib->create_ws_object ($wsId, $name, $type, $description);
	header("Location: ./tiki-manage-workspaces.php?editWS=".$wsId);
}

else if ( isset($_REQUEST['editWS']))
{
	$smarty->assign('editWS', "y");	
	
	$wsId = $_REQUEST['editWS'];
	$smarty->assign('wsId', $wsId);
	
	$wsName = $wslib->get_ws_name($wsId);
	$smarty->assign('wsName', $wsName);
	
	$smarty->assign('title', "Edit '".$wsName."'");
	
	$wsDesc = $wslib->get_ws_description($wsId);
	$smarty->assign('wsDesc',$wsDesc);
	
	// Get maxRecord and offset for groups
	if ( !isset($_REQUEST['maxRecordGroup']))
		$_REQUEST['maxRecordGroup'] = 10;
	if ( !isset($_REQUEST['offsetGroup']))
		$_REQUEST['offsetGroup'] = 0;
	$maxRecordGroup = $_REQUEST['maxRecordGroup'];
	$offsetGroup = $_REQUEST['offsetGroup'];
	// List the groups that have access in the WS
	$listWSGroups = $wslib->list_groups_that_can_access_in_ws ($wsId);
	$numGroups = $wslib->count_groups_in_ws ($wsId);
	$smarty->assign('groups',$listWSGroups);
	if ($offsetGroup > 0)
	{
		$offsetGroup_prev = (int) $offsetGroup- (int) $maxRecordGroup;
		$href_prev = "tiki-manage-workspaces.php?editWS=".$wsId."&maxRecordGroup=".$maxRecordGroup."&offsetGroup=".$offsetGroup_prev;
	}
	if (((int) $offsetGroup + (int) $maxRecordGroup) <= (int) $numGroups)
	{
		$offsetGroup_next = (int) $offsetGroup+ (int) $maxRecordGroup;
		$href_next = "tiki-manage-workspaces.php?editWS=".$wsId."&maxRecordGroup=".$maxRecordGroup."&offsetGroup=".$offsetGroup_next;
	}
	$smarty->assign('prev_pageGroup',$href_prev);
	$smarty->assign('next_pageGroup',$href_next);
	
	// Get maxRecord and offset for objects
	if ( !isset($_REQUEST['maxRecordObj']))
		$_REQUEST['maxRecordObj'] = 10;
	if ( !isset($_REQUEST['offsetObj']))
		$_REQUEST['offsetObj'] = 0;
	$maxRecordObj = $_REQUEST['maxRecordObj'];
	$offsetObj = $_REQUEST['offsetObj'];
	// List the objects that the user has access within the WS
	$listWSObjects = $wslib->list_ws_objects ($wsId, $maxRecordGroup, $offsetGroup);
	$numObjects = $wslib->count_objects_in_ws ($wsId);
	$smarty->assign('resources',$listWSObjects);
	if ($offsetObj > 0)
	{
		$offsetObj_prev = (int) $offsetObj- (int) $maxRecordObj;
		$href_prev = "tiki-manage-workspaces.php?editWS=".$wsId."&maxRecordObj=".$maxRecordObj."&offsetObj=".$offsetObj_prev;
	}
	if (((int) $offsetObj + (int) $maxRecordObj) <= (int) $numObjects)
	{
		$offsetObj_next = (int) $offsetObj+ (int) $maxRecordObj;
		$href_next = "tiki-manage-workspaces.php?editWS=".$wsId."&maxRecordObj=".$maxRecordObj."&offsetObj=".$offsetObj_next;
	}
	$smarty->assign('prev_pageObj',$href_prev);
	$smarty->assign('next_pageObj',$href_next);
	 
	$smarty->assign('mid', 'tiki-manage-workspaces.tpl');
	$smarty->display('tiki.tpl');
}

else
{
	require_once 'lib/userslib.php';
	
	$smarty->assign('title', "Workspaces Management");
	
	// List Workspaces Tab
	if ( !isset($_REQUEST['maxRecords']))
		$_REQUEST['maxRecords'] = 15;
	if ( !isset($_REQUEST['offset']))
		$_REQUEST['offset'] = 0;
	
	$maxRecords = $_REQUEST['maxRecords']; 
	$offset = $_REQUEST['offset'];
	
	$listWS = $wslib->list_all_ws($offset, $maxRecords, 'name_asc', "", "", "");
	$smarty->assign('listWS', $listWS["data"]);
	
	if ($offset > 0)
	{
		$offset_prev = (int) $offset- (int) $maxRecords;
		$href_prev = "tiki-manage-workspaces.php?maxRecords=".$maxRecords."&offset=".$offset_prev;
	}
	if (((int) $offset + (int) $maxRecords) <= (int) $listWS["cant"])
	{
		$offset_next = (int) $offset+ (int) $maxRecords;
		$href_next = "tiki-manage-workspaces.php?maxRecords=".$maxRecords."&offset=".$offset_next;
	}
	
	$smarty->assign('prev_pageWS',$href_prev);
	$smarty->assign('next_pageWS',$href_next);
	
	// Add Workspace Tab	
	$listGroups = $userlib->get_groups();
	$smarty->assign('listGroups', $listGroups);
	
	$listParentWS = $wslib->list_all_ws(-1,-1,'name_asc',null,'','');
	$smarty->assign('listParentWS', $listParentWS);
	
	$listPerms = $wslib->get_ws_adminperms ();
	$smarty->assign('listPerms', $listPerms);
	 
	$smarty->assign('mid', 'tiki-manage-workspaces.tpl');
	$smarty->display('tiki.tpl');
}

?>