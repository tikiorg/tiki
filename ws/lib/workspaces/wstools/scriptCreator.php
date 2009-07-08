<?php
require_once('../../../tiki-setup.php');

/* 
 NOTE: before you run this you should create 5 wiki pages with the names "Wiki1",
 "Wiki2", "Wiki3", "Wiki4" and "Wiki5", and two groups called "G1" and "G2" (This can be
 done if the script is called by ".../lib/workspaces/wstools/scriptCreator.php?action=init).
 
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

if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'init') && ($wsContainerId))
{
	if (!$objectlib->get_object_id('wiki page','Wiki1'))
	{
		$objectlib->add_object('wiki page','Wiki1');
		$tikilib->create_page('Wiki1', 0, '', time(), '');
	}
	if (!$objectlib->get_object_id('wiki page','Wiki2'))
	{
		$objectlib->add_object('wiki page','Wiki2');
		$tikilib->create_page('Wiki2', 0, '', time(), '');
	}
	if (!$objectlib->get_object_id('wiki page','Wiki3'))
	{
		$objectlib->add_object('wiki page','Wiki3');
		$tikilib->create_page('Wiki3', 0, '', time(), '');
	}
	if (!$objectlib->get_object_id('wiki page','Wiki4'))
	{
		$objectlib->add_object('wiki page','Wiki4');
		$tikilib->create_page('Wiki4', 0, '', time(), '');
	}
	if (!$objectlib->get_object_id('wiki page','Wiki5'))
	{
		$objectlib->add_object('wiki page','Wiki5');
		$tikilib->create_page('Wiki5', 0, '', time(), '');
	}
}

if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'create'))
{
	//Creating new WS
	if  (!$wslib->get_ws_id('WS1',$wsContainerId))
		$id1 = $wslib->add_ws('WS1',$wsContainerId,'G1',array('tiki_p_ws_admingroups','tiki_p_ws_adminresources'));
	 if (!$wslib->get_ws_id('WS2',$wsContainerId))
		$wslib->add_ws('WS2',$wsContainerId,'G2',array('tiki_p_ws_adminperms'));
	if  (!$wslib->get_ws_id('WS3',$wsContainerId))
	    $id3 = $wslib->add_ws('WS3',$wsContainerId,'G1',array('tiki_p_ws_adminws'));

	$id2 = $wslib->get_ws_id('WS2',$wsContainerId);

	//Creating new sub-WS under WS2
	if  (!$wslib->get_ws_id('WS21',$id2))
		$id4 = $wslib->add_ws('WS21',$id2,'G2');
	if  (!$wslib->get_ws_id('WS22',$id2))
		$id5 = $wslib->add_ws('WS22',$id2,'G2',array('tiki_p_ws_adminws'));
	
	// Giving access to G2 in WS3
	$wslib->set_permissions_for_group_in_ws($id3,'G2',array('tiki_p_ws_view','tiki_p_ws_addresource'));
	
	//Adding Resources in WS
	$wslib->add_ws_object($id1,'Wiki1','wiki_page');
	$wslib->add_ws_object($id2,'Wiki2','wiki_page');
	$wslib->add_ws_object($id3,'Wiki2','wiki_page');
	$wslib->add_ws_object($id3,'Wiki3','wiki_page');
	$wslib->add_ws_object($id4,'Wiki4','wiki_page');
	$wslib->add_ws_object($id5,'Wiki5','wiki_page');
}
	
if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'destroy'))
{
	//Getting existing WS id
	$id1= $wslib->get_ws_id('WS1',$wsContainerId);
	$id2= $wslib->get_ws_id('WS2',$wsContainerId);
	$id3= $wslib->get_ws_id('WS3',$wsContainerId);
	$id4= $wslib->get_ws_id('WS21',$id2);
	$id5= $wslib->get_ws_id('WS22',$id2);
	
	//Removing WS
	$wslib->remove_ws($id1);
	$wslib->remove_ws($id2);
	$wslib->remove_ws($id3);
	$wslib->remove_ws($id4);
	$wslib->remove_ws($id5);
}

if ( isset($_REQUEST['action'])  &&  ($_REQUEST['action'] == 'test'))
{
	$listWS = $wslib->list_all_ws(-1,-1,'name_asc',null,'wiki page','Wiki1');
	echo ("\n<br>");
	foreach ($listWS["data"] as $key)
	{
		echo ($key["categId"]);
		echo ("	");
		echo ($key["name"]);
		echo ("\n<br>");
	}/*
	$wslib->list_ws_that_can_be_accessed_by_group ('G1') ;
	echo ("\n<br>");
	var_dump($wslib->get_ws_id ('WS3',1));
	*/
	echo ("\n<br>");
	echo ("\n<br>");
	echo ("\n<br>");
	var_dump($listGroupWS = $wslib->list_ws_that_can_be_accessed_by_group ('G2'));
	echo ("\n<br>");
	echo ("\n<br>");
	$id = $wslib->get_ws_id('WS3',$wsContainerId);
	var_dump($listWSGroups = $wslib->list_groups_that_can_access_in_ws($id));
	echo ("\n<br>");
	echo ("\n<br>");
	var_dump($wslib->list_ws_that_user_have_access('Ben'));
}

if (isset($_REQUEST['redirect']) && ($_REQUEST['redirect'] == 'yes'))
	header("Location: ./../../../tiki-admin.php?page=workspaces");
