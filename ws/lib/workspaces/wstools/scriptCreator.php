<?php
require_once('../../../tiki-setup.php');

/* 
 NOTE: before you run this you should create 5 wiki pages with the names "Wiki1",
 "Wiki2", "Wiki3", "Wiki4" and "Wiki5", and two groups called "G1" and "G2" (This can be
 done if the script is called by ".../lib/workspaces/wstools/scriptCreator.php?action=init).
 The next thing to do is to give to Wiki2 or Wiki3 the 
 tiki_p_view for group G1 or G2. 
 
 To create sample WS and assign sample groups and wiki pages to WS:
 .../lib/workspaces/wstools/scriptCreator.php?action=create
 
 To delete the sample WS:
 .../lib/workspaces/wstools/scriptCreator.php?action=destroy
 
 For better performance, it's recomended to enable Workspaces from Admin Features 
 before running.
 Only for evaluation purposes.
 */


include_once('lib/objectlib.php');
include_once('lib/userslib.php');
include_once('lib/tikilib.php');
include_once('lib/workspaces/wslib.php');



global $prefs, $tikilib;
$wsContainerId = (int) $prefs['ws_container'];
$user = 'Ben';

if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'create'))
{
	// Creating Groups and user
	echo("Creating Groups<br>");
	$userlib->add_group('G1');
	$userlib->add_group('G2');
	if ($userlib->add_user($user, '12345'));
	$userlib->assign_user_to_group($user, 'G1');
	$userlib->assign_user_to_group($user, 'G2');
	$userlib->assign_user_to_group('admin', 'G1');
	$userlib->assign_user_to_group('admin', 'G2');

	echo("Creating WS<br>");
	// Creating new WS
	if  (!($id1 = $wslib->get_ws_id('WS1',0)))
	{
		$groups = array();
		$groups[] = array(
					"groupName" => "G1",
					"groupDescription" => "",
					"noCreateNewGroup" => true,
					"additionalPerms" => array('tiki_p_ws_admingroups') 
					);
		$id1 = $wslib->create_ws ('WS1', $groups, null);
	}
	 if (!($id2 = $wslib->get_ws_id('WS2',0)))
	 {
		$groups = array();
		$groups[] = array(
					"groupName" => "G2",
					"groupDescription" => "",
					"noCreateNewGroup" => true,
					"additionalPerms" => array('tiki_p_ws_adminresources')
					);
		$id2 = $wslib->create_ws ('WS2', $groups, null);
	}
	if  (!($id3 = $wslib->get_ws_id('WS3',0)))
	{
		$groups = array();
		$groups[] = array(
					"groupName" => "G1",
					"groupDescription" => "",
					"noCreateNewGroup" => true,
					"additionalPerms" => array('tiki_p_ws_adminws')
					);
		$groups[] = array(
					"groupName" => "G2",
					"groupDescription" => "",
					"noCreateNewGroup" => true,
					"additionalPerms" => array('tiki_p_ws_view','tiki_p_ws_addresource')
					);
		$id3 = $wslib->create_ws ('WS3', $groups, null);
	}

	// Creating new sub-WS under WS2
	if  (!($id4 = $wslib->get_ws_id('WS21',$id2)))
	{
		$groups = array();
		$groups[] = array(
					"groupName" => "G2",
					"groupDescription" => "",
					"noCreateNewGroup" => true,
					"additionalPerms" => array()
					);
		$id4 = $wslib->create_ws ('WS21', $groups, $id2);
	}
	if  (!($id5 = $wslib->get_ws_id('WS22',$id2)))
	{
		$groups = array();
		$groups[] = array(
					"groupName" => "G2",
					"groupDescription" => "",
					"noCreateNewGroup" => true,
					"additionalPerms" => array('tiki_p_ws_adminws')
					);
		$id5 = $wslib->create_ws ('WS22', $groups, $id2);
	}
		
	// Adding Resources in WS
	echo("Creating Resources<br>");
	$wslib->create_ws_object($id1,'Wiki1','wiki page');
	$wslib->create_ws_object($id2,'Wiki2','wiki page');
	$wslib->add_ws_object($id3,'Wiki2','wiki page');
	$wslib->create_ws_object($id3,'Wiki3','wiki page');
	$wslib->create_ws_object($id3,'FileGallery_WS3','file gallery');
	$wslib->create_ws_object($id3,'ImgGallery_WS3','image gallery');
	$wslib->create_ws_object($id3,'Blog_WS3','blog');
	$wslib->create_ws_object($id4,'Wiki4','wiki page');
	$wslib->create_ws_object($id5,'Wiki5','wiki page');
	$wslib->create_ws_object($id5,'Wiki5','wiki page');
	
	// Adding ObjectPerms in Wiki2 (for G1) and Wiki3 (for G2)
	$userlib->assign_object_permission('G1', 'Wiki2', 'wiki page', 'tiki_p_view');
	$userlib->assign_object_permission('G2', 'Wiki3', 'wiki page', 'tiki_p_view');
}
	
if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'destroy') && ($wsContainerId))
{
	// Getting existing WS id
	$id1= $wslib->get_ws_id('WS1',0);
	$id2= $wslib->get_ws_id('WS2',0);
	$id3= $wslib->get_ws_id('WS3',0);
	$id5= $wslib->get_ws_id('WS22',$id2);
	
	// Removing WS
	echo("Delete all created WS, with it groups and resources");
	$wslib->remove_ws($id1);
	$wslib->remove_ws($id5);
	$wslib->remove_ws($id3);
	$wslib->remove_ws($id2);
}

if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'apocalipsis') && ($wsContainerId))
{
	$wslib->remove_all_ws();
	echo("WS have been slaughtered. You're the worst person in the world!!!  :-(");
}

if ( isset($_REQUEST['action'])  &&  ($_REQUEST['action'] == 'test') && ($wsContainerId))
{
	echo("Show WS container <br>");
	$ws_id = $wslib->get_ws_container();
	echo($ws_id."<br>");
	echo("<br>");
	
	$listWS = $wslib->list_all_ws(-1,-1,'name_asc',null,'','');
	echo ("List all WS stored in Tiki");
	echo ("\n<br>");
	foreach ($listWS["data"] as $key)
	{
		echo ($key["categId"]);
		echo ("		");
		echo ($key["name"]);
		echo ("\n<br>");
	}
		
	echo ("\n<br>");
	echo ("List all groups that have access to WS3");
	echo ("\n<br>");
	$id3 = $wslib->get_ws_id('WS3',0);
	$listWSGroups = $wslib->list_groups_that_can_access_in_ws($id3);
	foreach ($listWSGroups as $key)
	{
		echo ($key["groupName"]);
		echo ("\n<br>");
	}
	
	echo ("\n<br>");
	echo ("List all WS that group G2 have access");
	echo ("\n<br>");
	$listGroupWS = $wslib->list_ws_that_can_be_accessed_by_group ('G2');
	ksort($listGroupWS);
	foreach ($listGroupWS as $key)
	{
		echo ($key["categId"]);
		echo ("		");
		echo ($key["name"]);
		echo ("\n<br>");
	}
	
	echo ("\n<br>");
	echo ("List all WS that the cool user named Ben have access");
	echo ("\n<br>");
	$listUserWS = $wslib->list_ws_that_user_have_access($user);
	ksort($listUserWS);
	foreach ($listUserWS as $key)
	{
		echo ($key["categId"]);
		echo ("		");
		echo ($key["name"]);
		echo ("\n<br>");
	}
	
	echo ("\n<br>");
	echo ("List all objects stored in WS3");
	echo ("\n<br>");
	$listWSObjects = $wslib->list_ws_objects($id3);
	foreach ($listWSObjects as $key)
	{
		echo ($key["objectId"]);
		echo ("     ");
		echo ($key["type"]);
		echo ("     ");
		echo ($key["name"]);
		echo ("\n<br>");
	}
	
	echo ("\n<br>");
	echo ("List all objects that the cool user named Ben have access from WS3");
	echo ("\n<br>");
	$listWSObjectsUser = $wslib->list_ws_objects_for_user ($id3,$user);
	foreach ($listWSObjectsUser as $key)
	{
		if ($key["userCanView"] == "y")
		{
			echo ($key["objectId"]);
			echo ("     ");
			echo ($key["type"]);
			echo ("     ");
			echo ($key["name"]);
			echo ("<br>");
		}
	}
	echo ("\n<br>");
	
	$wslib->add_ws_object($id3, 'Wiki5', 'wiki page');
	echo ("Wiki5 have been added in WS3\n<br>");
	$wslib->create_ws_object($id3, 'Wiki6', 'wiki page');
	echo ("Wiki6 have been added in WS3\n<br>");
	$userlib->assign_object_permission('G1', 'Wiki5', 'wiki page', 'tiki_p_view');
	$userlib->assign_object_permission('G2', 'Wiki5', 'wiki page', 'tiki_p_view');
	$userlib->assign_object_permission('G1', 'Wiki6', 'wiki page', 'tiki_p_view');
	echo ("\n<br>");
	
	echo ("List all objects that the cool user named Ben have access from WS3");
	echo ("\n<br>");
	$listWSObjectsUser = $wslib->list_ws_objects_for_user ($id3,$user);
	foreach ($listWSObjectsUser as $key)
	{
		if ($key["userCanView"] == "y")
		{
			echo ($key["objectId"]);
			echo ("     ");
			echo ($key["type"]);
			echo ("     ");
			echo ($key["name"]);
			echo ("<br>");
		}
	}
	echo ("\n<br>");
	
	$objectId1 = $objectlib->get_object_id('wiki page', 'Wiki5');
	$objectId2 = $objectlib->get_object_id('wiki page', 'Wiki6');
	$wslib->remove_object_from_ws($id3,$objectId1,'Wiki5','wiki page');
	echo ("Wiki5 have been removed from WS3 and only the perms related to the uniques groups in WS3 have been deleted\n<br>");
	$wslib->remove_object_from_ws($id3,$objectId2,'Wiki6','wiki page');
	echo ("Wiki6 have been removed from WS3 and all it permissions have been deleted\n<br>\n<br>");
	
	echo ("List all objects that the cool user named Ben have access from WS3");
	echo ("\n<br>");
	$listWSObjectsUser = $wslib->list_ws_objects_for_user ($id3,$user);
	foreach ($listWSObjectsUser as $key)
	{
		if ($key["userCanView"] == "y")
		{
			echo ($key["objectId"]);
			echo ("     ");
			echo ($key["type"]);
			echo ("     ");
			echo ($key["name"]);
			echo ("
<br>");
		}
	}
	echo ("\n<br>");
	
	$id1 = $wslib->get_ws_id('WS1',0);
	if ($userlib->add_group('G3'));
	$wslib->set_permissions_for_group_in_ws($id1,'G2',array('tiki_p_ws_view'));
	$wslib->set_permissions_for_group_in_ws($id1,'G3',array('tiki_p_ws_view'));
	$wslib->add_ws_object($id1,'Wiki2','wiki page');
	$userlib->assign_object_permission('G2', 'Wiki1', 'wiki page', 'tiki_p_view');
	$userlib->assign_object_permission('G3', 'Wiki1', 'wiki page', 'tiki_p_view');
	echo ("G2 have been added in WS1 and can view Wiki1\n<br>");
	echo ("G3 have been added in WS1 and can view Wiki1\n<br>");
	
	echo ("\n<br>");
	echo ("List all groups that have access to WS1");
	echo ("\n<br>");
	$listWSGroups = $wslib->list_groups_that_can_access_in_ws($id1);
	foreach ($listWSGroups as $key)
	{
		echo ($key["groupName"]);
		echo ("\n<br>");
	}
	
	echo ("\n<br>");
	
	$wslib->remove_group_from_ws($id1,'G2');
	$wslib->remove_group_from_ws($id1,'G3');
	echo ("G2 have been removed in WS1 and can't view Wiki1 anymore, but still exist in Tiki\n<br>");
	echo ("G3 have been removed in WS1 and have been deleted from Tiki\n<br>\n<br>");
	
	
	echo ("List all groups that have access to WS1");
	echo ("\n<br>");
	$listWSGroups = $wslib->list_groups_that_can_access_in_ws($id1);
	foreach ($listWSGroups as $key)
	{
		echo ($key["groupName"]);
		echo ("\n<br>");
	}
	echo ("\n<br>");
	
	echo("Testing new function: set_permissions_for_group_in_object");
	
	$group1 = array();
	$group1["groupName"] = 'G1';
	$group1["permList"] = array('tiki_p_view', 'tiki_p_edit');
	
	$group2 = array();
	$group2["groupName"] = 'G2';
	$group2["permList"] = array('tiki_p_view', 'tiki_p_edit');
	
	$groupSet = array();
	$groupSet[] = $group1;
	$groupSet[] = $group2;
	
	$itemId = 'Wiki4';
	$type = 'wiki page';
	 
	$wslib->set_permissions_for_groups_in_object ($itemId, $type, $groupSet);
	
	$id2 = $wslib->get_ws_id('WS2',0);
	$id4 = $wslib->get_ws_id('WS21',$id2);
	echo ("\n<br>");
	echo ("List all objects that the cool user named Ben have access from WS21");
	echo ("\n<br>");
	$listWSObjectsUser = $wslib->list_ws_objects_for_user ($id4,$user);
	foreach ($listWSObjectsUser as $key)
	{
		if ($key["userCanView"] == "y")
		{
			echo ($key["objectId"]);
			echo ("     ");
			echo ($key["type"]);
			echo ("     ");
			echo ($key["name"]);
			echo ("\n<br>");
		}
	}
	echo ("\n<br>");
	
	
}

if ( isset($_REQUEST['action'])  &&  ($_REQUEST['action'] == 'mensaje') && ($wsContainerId))
	echo("...");

if (isset($_REQUEST['redirect']) && ($_REQUEST['redirect'] == 'yes'))
	header("Location: ./../../../tiki-admin.php?page=workspaces");
