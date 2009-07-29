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
	//create_ws_object ($ws_id, $name, $type, $description);
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
	 
	$smarty->assign('mid', 'tiki-manage-workspaces.tpl');
	$smarty->display('tiki.tpl');
}

else
{
	require_once 'lib/userslib.php';
	
	$smarty->assign('title', "Workspaces Management");
	
	// List Workspaces Tab
	if ( !isset($_REQUEST['maxRecords']))
		$_REQUEST['maxRecords'] = 25;
	if ( !isset($_REQUEST['offset']))
		$_REQUEST['offset'] = 0;
	
	$maxRecords = $_REQUEST['maxRecords']; 
	$offset = $_REQUEST['offset'];
	
	$listWS_temp = $wslib->list_all_ws($offset, $maxRecords, 'name_asc', "", "", "");
	$listWS = array();
	foreach ($listWS_temp["data"] as $res)
	{
		$res['href'] = "tiki-manage-workspaces.php?editWS=".$res["categId"];
		$listWS[] = $res;
	}
	$smarty->assign('listWS', $listWS);
	
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
	
	$smarty->assign('prev_page',$href_prev);
	$smarty->assign('next_page',$href_next);
	
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