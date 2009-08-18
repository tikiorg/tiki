<?php
//Imports 
require_once('tiki-setup.php');
require_once('lib/workspaces/wslib.php');

//Check security
if (!($tiki_p_admin == 'y' || $tiki_p_admin_users == 'y')) { 
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if ( isset($_REQUEST['create']) )
{	
	$name = $_REQUEST['wsName'];
	$description = $_REQUEST["wsDesc"];
	$parentWS= $_REQUEST["parentWS"];
	$adminPerms = $_REQUEST["adminPerms"];
	
	// If selected, create a new group
	if ($_REQUEST['groupSelect'] == "new")
	{
		$groupName = $_REQUEST['newGroup'];
		$groupDesc = $_REQUEST["groupDesc"];
		$noCreate = false;
	} 
	// else, will select a previously created group
	else if ($_REQUEST['groupSelect'] == "old")
	{
	    $groupName = $_REQUEST['oldGroup'];
	    $noCreate = true;
	}
	
	// Check if the name and the group are written
	if (empty($name) || empty($groupName))
	{
	    $smarty->assign('msg', tra("Workspace name or group name can not be blank."));
	    $smarty->display("error.tpl");
	    die;
	}
		
	// Check if a WS with the same name exists in the same level
	$wsid = $wslib->get_ws_id($name, $parentWS);
	if (!empty($wsid))
	{
	    $smarty->assign('msg', tra("There already exists a Workspace with that name in the same level. Please choose another name."));
	    $smarty->display("error.tpl");
	    die;
	}
	
	//If everything is ok, then we proceed to create the WS
	$perms = array($adminPerms);
	$groups = array();
	$groups[] = array(
	    "groupName" => $groupName,
	    "groupDescription" => $groupDesc,
	    "noCreateNewGroup" => $noCreate,
	    "additionalPerms" => $perms
	);

	$wslib->create_ws ($name, $groups, $parentWS, $description);

	$smarty->assign('type', 'note');
	$smarty->assign('feedback', 'You have succesfully created the Workspace!');
}
// If WS Name and Description is edited
else if ( isset($_REQUEST['editedWS']))
{
	$wsId = $_REQUEST['editedWS'];
	
	$wsName = $_REQUEST['wsNewName'];	
	$wsDesc = $_REQUEST['wsNewDesc'];
	
	$wslib->update_ws_data($wsId, $wsName, $wsDesc);
	
	header("Location: ./tiki-manage-workspaces.php?editWS=".$wsId);
}
// If an object is added in the WS
else if ( isset($_REQUEST['addObjectinWS']))
{
	$wsId = $_REQUEST['addObjectinWS'];
	$name = $_REQUEST['objectName'];
	$type = $_REQUEST['selectType'];
	$description = $_REQUEST['objectDesc'];
	
	$wslib->create_ws_object ($wsId, $name, $type, $description);
	header("Location: ./tiki-manage-workspaces.php?editWS=".$wsId);
}
// If a group is added in the WS
else if ( isset($_REQUEST['addGroupinWS']))
{
	$wsId = $_REQUEST['addGroupinWS'];
	if ($_REQUEST['addAdminPerms'] != '')
		$additionalPerms = array($_REQUEST['addAdminPerms']);
	
	// If selected, create a new group
	if ($_REQUEST['addGroupSelect'] == "addNew")
	{
		$groupName = $_REQUEST['addNewGroup'];
		$groupDesc = $_REQUEST["addGroupDesc"];
		$wslib->add_ws_group ($wsId, null, $groupName, $groupDesc, $additionalPerms);
	} 
	// else, will select a previously created group
	else if ($_REQUEST['addGroupSelect'] == "addOld")
	{
		$groupName = $_REQUEST['addOldGroup'];
		$wslib->set_permissions_for_group_in_ws ($wsId, $groupName, array('tiki_p_ws_view'));
	    	if ($additionalPerms != null)
			$wslib->set_permissions_for_group_in_ws($wsId, $groupName, $additionalPerms);
	}
	
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
	if ((!isset($_REQUEST['maxRecordGroup'])) || ($_REQUEST['maxRecordGroup'] < 1))
		$_REQUEST['maxRecordGroup'] = 10;
	if ((!isset($_REQUEST['offsetGroup'])) || ($_REQUEST['offsetGroup'] < 0))
		$_REQUEST['offsetGroup'] = 0;
	$maxRecordGroup = $_REQUEST['maxRecordGroup'];
	$offsetGroup = $_REQUEST['offsetGroup'];
	// List the groups that have access in the WS
	$listWSGroups = $wslib->list_groups_that_can_access_in_ws ($wsId,$maxRecordGroup,$offsetGroup);
	$numGroups = $wslib->count_groups_in_ws ($wsId);
	$smarty->assign('groups',$listWSGroups);
	// Set Back and Next buttons for groups
	if ($offsetGroup > 0)
	{
		$offsetGroup_prev = (int) $offsetGroup- (int) $maxRecordGroup;
		$href_prev = "tiki-manage-workspaces.php?editWS=".$wsId."&maxRecordGroup=".$maxRecordGroup."&offsetGroup=".$offsetGroup_prev;
	}
	if (((int) $offsetGroup + (int) $maxRecordGroup) < (int) $numGroups)
	{
		$offsetGroup_next = (int) $offsetGroup + (int) $maxRecordGroup;
		$href_next = "tiki-manage-workspaces.php?editWS=".$wsId."&maxRecordGroup=".$maxRecordGroup."&offsetGroup=".$offsetGroup_next;
	}
	$smarty->assign('prev_pageGroup',$href_prev);
	$smarty->assign('next_pageGroup',$href_next);
	
	// Get maxRecord and offset for objects
	if ((!isset($_REQUEST['maxRecordObj'])) || ($_REQUEST['maxRecordObj'] < 1))
		$_REQUEST['maxRecordObj'] = 10;
	if ((!isset($_REQUEST['offsetObj'])) || ($_REQUEST['offsetObj'] < 0))
		$_REQUEST['offsetObj'] = 0;
	$maxRecordObj = $_REQUEST['maxRecordObj'];
	$offsetObj = $_REQUEST['offsetObj'];
	// List the objects that the user has access within the WS
	$listWSObjects = $wslib->list_ws_objects ($wsId, $maxRecordObj, $offsetObj);
	$numObjects = $wslib->count_objects_in_ws ($wsId);
	$smarty->assign('resources',$listWSObjects);
	// Set Back and Next buttons for objects
	if ($offsetObj > 0)
	{
		$offsetObj_prev = (int) $offsetObj- (int) $maxRecordObj;
		$href_prev = "tiki-manage-workspaces.php?editWS=".$wsId."&maxRecordObj=".$maxRecordObj."&offsetObj=".$offsetObj_prev;
	}
	if (((int) $offsetObj + (int) $maxRecordObj) < (int) $numObjects)
	{
		$offsetObj_next = (int) $offsetObj + (int) $maxRecordObj;
		$href_next = "tiki-manage-workspaces.php?editWS=".$wsId."&maxRecordObj=".$maxRecordObj."&offsetObj=".$offsetObj_next;
	}
}
else
{
	require_once 'lib/userslib.php';
	
	// List Workspaces Tab
	if ((!isset($_REQUEST['maxRecords'])) || ($_REQUEST['maxRecords'] < 1))
		$_REQUEST['maxRecords'] = 15;
	if ((!isset($_REQUEST['offset'])) || ($_REQUEST['offset'] < 0))
		$_REQUEST['offset'] = 0;
	
	$maxRecords = $_REQUEST['maxRecords']; 
	$offset = $_REQUEST['offset'];

	if (isset($_REQUEST['sort_mode']))
	    $sort_mode = $_REQUEST['sort_mode'];
	else
	    $sort_mode = 'name_asc';
	
	$listWS_temp = $wslib->list_all_ws($offset, $maxRecords, $sort_mode, "", "", "");
	$listWS = array('data' =>array(), 'cant'=>$listWS_temp['cant']);
	foreach ($listWS_temp["data"] as $res)
	{
		$res['href_edit'] = "tiki-manage-workspaces.php?editWS=".$res["categId"];
		$listWS['data'][] = $res;
	}
	$smarty->assign('listWS', $listWS["data"]);
	
	if ($offset > 0)
	{
		$offset_prev = (int) $offset- (int) $maxRecords;
		$href_prev = "tiki-manage-workspaces.php?maxRecords=".$maxRecords."&offset=".$offset_prev;
	}
	if (((int) $offset + (int) $maxRecords) < (int) $listWS["cant"])
	{
		$offset_next = (int) $offset+ (int) $maxRecords;
		$href_next = "tiki-manage-workspaces.php?maxRecords=".$maxRecords."&offset=".$offset_next;
	}

	if (isset($_REQUEST['submit_mult']) && ($_REQUEST['submit_mult'] == 'remove_workspaces'))
	{
	    var_dump($_REQUEST['checked']);
	    foreach($_REQUEST["checked"] as $deleteWS)
	    {
		var_dump($deleteWS);
	    }
	}
}

$smarty->assign('prev_pageWS',$href_prev);
$smarty->assign('next_pageWS',$href_next);
	
// Add Workspace Tab	
$listGroups = $userlib->get_groups();
$smarty->assign('listGroups', $listGroups);
	
$listParentWS = $wslib->list_all_ws(-1,-1, $sort_mode, null,'','');
$smarty->assign('listParentWS', $listParentWS);
	
$listPerms = $wslib->get_ws_adminperms ();
$smarty->assign('listPerms', $listPerms);
	 
$smarty->assign('mid', 'tiki-manage-workspaces.tpl');
$smarty->display('tiki.tpl');
?>
