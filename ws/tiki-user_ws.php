<?php
 
require_once('tiki-setup.php');
require_once('lib/workspaces/wslib.php');

if ( isset($_REQUEST['showWS']))
{
	$ws_id = $_REQUEST['showWS'];
	$ws_name = $_REQUEST['nameWS'];
	
	// Set title
	$title = "Objects for ".$user." in '".$ws_name."'";
	$smarty->assign('headtitle', tra($title));
	$smarty->assign('WS_title', $title);

	// List the objects that the user has access within the WS
	$listWSObjects = $wslib->list_ws_objects_for_user ($ws_id, $user);
	$smarty->assign('resources',$listWSObjects);

	$smarty->assign('mid', 'tiki-user_ws.tpl');
	$smarty->display('tiki.tpl'); 
}

?>