<?php
 
require_once('tiki-setup.php');
require_once('lib/workspaces/wslib.php');

if ( isset($_REQUEST['showWS']))
{
	$ws_id = (int) $_REQUEST['showWS'];
	 
	$value = "WorkSpace's Resources for ".$user;
	$smarty->assign('WS_title', $value);

	$listWSObjects = $wslib->list_ws_objects_for_user ($ws_id, $user);
	
	$smarty->assign('resources',$listWSObjects);

	$smarty->assign('mid', 'tiki-user_ws.tpl');
	$smarty->display('tiki.tpl'); 
}

?>