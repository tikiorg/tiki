<?php
 
require_once('tiki-setup.php');
require_once('lib/workspaces/wslib.php');

$title = "WorkSpace Management";
$smarty->assign('headtitle', tra($title));

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
			echo("Falta nombre del WS");
		else if ($groupName == "")
			echo("Falta nombre del grupo nuevo");
	}
	else
	{
		var_dump($adminPerms);
		$perms = array($adminPerms);
		$groups = array();
		$groups[] = array(
					"groupName" => $groupName,
					"groupDescription" => $groupDesc,
					"noCreateNewGroup" => $noCreate,
					"additionalPerms" => $perms
					);
		$wslib->create_ws ($name, $groups, $parentWS, $description);
		
		header("Location: ./tiki-create_ws.php");
	}
	
}
else
{
	require_once 'lib/userslib.php';
	
	$listGroups = $userlib->get_groups();
	$smarty->assign('listGroups', $listGroups);
	
	$listWS = $wslib->list_all_ws(-1,-1,'name_asc',null,'','');
	$smarty->assign('listWS', $listWS);
	
	$listPerms = $wslib->get_ws_adminperms ();
	$smarty->assign('listPerms', $listPerms);
	 
	$smarty->assign('mid', 'tiki-manage-workspaces.tpl');
	$smarty->display('tiki.tpl');
}

?>