<?php
 
include_once('tiki-setup.php');
require_once('lib/workspaces/wslib.php');
 
$value = $user." WorkSpaces";
$smarty->assign('userWS_title', $value);

$listWS = $wslib->list_ws_that_user_have_access ($user, 25, 0);
$smarty->assign('listWS',$listWS);

if ( isset($_REQUEST['showWS']))
{
	$ws_id = $_REQUEST['showWS'];
	$listWSObjects = $wslib->list_ws_objects_for_user ($ws_id, $user);
}
else
	$listWSObjects = array();
$smarty->assign('resources',$listWSObjects);

$smarty->assign('mid', 'tiki-user_ws.tpl');
$smarty->display('tiki.tpl'); 

?>