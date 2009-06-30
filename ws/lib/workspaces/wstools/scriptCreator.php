<?php
require_once('../../../tiki-setup.php');

include_once('lib/workspaces/wslib.php');

/* 
 NOTE: before you run this you should create 5 wiki pages with the names "Wiki1",
 "Wiki2", "Wiki3", "Wiki4" and "Wiki5", and two groups called "G1" and "G2".
 By the way, for better performance, you should enable Workspaces from Admin Features.
 */

$ws = new wslib();

global $prefs;
$wsContainerId = (int) $prefs['ws_container'];

if ($wsContainerId == NULL)
{
	//Initializing WorkSpace Container
	global $tikilib; 
	$currentTime = (string) time();
	$hash = md5($currentTime);
	$wsContainerId = $ws->init_ws($hash);
	$tikilib->set_preference('ws_container', $wsContainerId);
	echo($wsContainerId);
	echo(")");
}

else
{
	if (!$ws->get_ws_id('WS1',$wsContainerId)) 
	{

		//Creating new WS
		$id1 = $ws->add_ws('WS1',$wsContainerId);
		$id2 = $ws->add_ws('WS2',$wsContainerId);
		$id3 = $ws->add_ws('WS3',$wsContainerId);
	
		//Creating new sub-WS under WS2
		$id4 = $ws->add_ws('WS21',$id2);
		$id5 = $ws->add_ws('WS22',$id2);
		
		//Adding G1 in WS1 and WS3
		$ws->add_ws_group($id1,'G1');
		$ws->add_ws_group($id3,'G1');
		
		//Adding G2 in WS2, WS3, WS21 and WS22
		$ws->add_ws_group($id2,'G2');
		$ws->add_ws_group($id3,'G2');
		$ws->add_ws_group($id4,'G2');
		$ws->add_ws_group($id5,'G2');
		
		//Adding Resources in WS
		$ws->add_ws_object($id1,'Wiki1','wiki_page');
		$ws->add_ws_object($id2,'Wiki2','wiki_page');
		$ws->add_ws_object($id3,'Wiki2','wiki_page');
		$ws->add_ws_object($id3,'Wiki3','wiki_page');
		$ws->add_ws_object($id4,'Wiki4','wiki_page');
		$ws->add_ws_object($id5,'Wiki5','wiki_page');
		
		echo("WS have been created and added! :)");
	
	}
	else
	{
	
		//Getting existing WS id
		$id1= $ws->get_ws_id('WS1',$wsContainerId);
		$id2= $ws->get_ws_id('WS2',$wsContainerId);
		$id3= $ws->get_ws_id('WS3',$wsContainerId);
		$id4= $ws->get_ws_id('WS21',$id2);
		$id5= $ws->get_ws_id('WS22',$id2);
		
		//Removing WS
		$ws->remove_ws($id1);
		$ws->remove_ws($id2);
		$ws->remove_ws($id3);
		$ws->remove_ws($id4);
		$ws->remove_ws($id5);
		echo("WS have been deleted");
		
	}
}
