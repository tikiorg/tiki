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
include_once('lib/workspaces/wslib.php');

global $prefs;
$wsContainerId = (int) $prefs['ws_container'];

if (isset($_REQUEST['action']))
{
    if (($_REQUEST['action'] == 'init'))
    {
	if (!$objectlib->get_object_id('wiki page','Wiki1'))
		$objectlib->add_object('wiki page','Wiki1');
	if (!$objectlib->get_object_id('wiki page','Wiki2'))
		$objectlib->add_object('wiki page','Wiki2');
	if (!$objectlib->get_object_id('wiki page','Wiki3'))
		$objectlib->add_object('wiki page','Wiki3');
	if (!$objectlib->get_object_id('wiki page','Wiki4'))
		$objectlib->add_object('wiki page','Wiki4');
	if (!$objectlib->get_object_id('wiki page','Wiki5'))
		$objectlib->add_object('wiki page','Wiki5');

	if ($userlib->add_group('G1'));
	if ($userlib->add_group('G2'));

    }
    
    if (($_REQUEST['action'] == 'create'))
    {
    	//Creating new WS
	if  (!$wslib->get_ws_id('WS1',$wsContainerId))
    	    $id1 = $wslib->add_ws('WS1',$wsContainerId);
       	if (!$wslib->get_ws_id('WS2',$wsContainerId))
	    $wslib->add_ws('WS2',$wsContainerId);
	if  (!$wslib->get_ws_id('WS3',$wsContainerId))
	    $id3 = $wslib->add_ws('WS3',$wsContainerId);

	$id2 = $wslib->get_ws_id('WS2',$wsContainerId);

	//Creating new sub-WS under WS2
	if  (!$wslib->get_ws_id('WS21',$id2))
		$id4 = $wslib->add_ws('WS21',$id2);
	if  (!$wslib->get_ws_id('WS22',$id2))
		$id5 = $wslib->add_ws('WS22',$id2);
	
	//Adding G1 in WS1 and WS3
	$wslib->add_ws_group($id1,'G1');
	$wslib->add_ws_group($id3,'G1');
	
	//Adding G2 in WS2, WS3, WS21 and WS22
	$wslib->add_ws_group($id2,'G2');
	$wslib->add_ws_group($id3,'G2');
	$wslib->add_ws_group($id4,'G2');
	$wslib->add_ws_group($id5,'G2');
	
	//Adding Resources in WS
	$wslib->add_ws_object($id1,'Wiki1','wiki_page');
	$wslib->add_ws_object($id2,'Wiki2','wiki_page');
	$wslib->add_ws_object($id3,'Wiki2','wiki_page');
	$wslib->add_ws_object($id3,'Wiki3','wiki_page');
	$wslib->add_ws_object($id4,'Wiki4','wiki_page');
	$wslib->add_ws_object($id5,'Wiki5','wiki_page');

    }
	
    if ($_REQUEST['action'] == 'destroy')
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

    if ($_REQUEST['action'] == 'clearcache')
	$cachelib->empty_full_cache();
    
    if ($_REQUEST['action'] == 'test')
    {
    }
}

if (isset($_REQUEST['redirect']) && ($_REQUEST['redirect'] == 'yes'))
	    header("Location: ./../../../tiki-admin.php?page=workspaces");
