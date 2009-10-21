<?php
/**
 * tiki-manage-workspaces.php - TikiWiki CMS/GroupWare
 *
 * PHP file that allows to the user manage ws with the gui
 * @author	Benjamin Palacios Gonzalo (mangapower) <mangapowerx@gmail.com>
 * @author	Aldo Borrero Gonzalez (axold) <axold07@gmail.com>
 * @license	http://www.opensource.org/licenses/lgpl-2.1.php
 */

//UNDER HEAVY DEVELOPMENT!!!
// CLEANING SOME THINGS

//Imports 
require_once 'tiki-setup.php';
if (!$wslib) require_once 'lib/workspaces/wslib.php';
require_once 'lib/userslib.php';

//Check security
if (!($tiki_p_admin == 'y' || $tiki_p_admin_users == 'y')) { 
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

//Checking the parameters sent to the url

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

//Remove one WS - TODO: Show a confirmation dialog
if (isset($_REQUEST['deleteWS']))
    $wslib->remove_ws($_REQUEST['deleteWS']);

//Remove multiple WS - TODO: Show a confirmation dialog
if (isset($_REQUEST['submit_mult']) && ($_REQUEST['submit_mult'] == 'remove_workspaces'))
{
    foreach($_REQUEST["checked"] as $deleteWS)
    {
	$wslib->remove_ws($deleteWS);
    }
}

//Displayed rows
if ((!isset($_REQUEST['maxRecords'])) || ($_REQUEST['maxRecords'] < 1))
    $_REQUEST['maxRecords'] = 25;

if ((!isset($_REQUEST['offset'])) || ($_REQUEST['offset'] < 0))
    $_REQUEST['offset'] = 0;
	
$maxRecords = $_REQUEST['maxRecords']; 
$offset = $_REQUEST['offset'];

if (isset($_REQUEST['sort_mode']))
    $sort_mode = $_REQUEST['sort_mode'];
else
    $sort_mode = 'name_asc';

//Display the basic list of Workspaces

//Display ws - TODO: Needs to be more restrictive depending on the user
$listWS_temp = $wslib->list_all_ws($offset, $maxRecords, $sort_mode);
$listWS = array('data' =>$listWS_temp['data'], 'cant'=>$listWS_temp['cant']);
$smarty->assign('listWS', $listWS["data"]);

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
