<?php
 
require_once('tiki-setup.php');
require_once('lib/workspaces/wslib.php');

if ( isset($_REQUEST['showWS']))
{
	$ws_id = $_REQUEST['showWS'];
	$ws_name = $wslib->get_ws_name($ws_id);
	
	// Set title
	$title = "Objects for ".$user." in '".$ws_name."'";
	$smarty->assign('headtitle', tra($title));
	$smarty->assign('WS_title', $title);

	// Get maxRecord and offset
	if ( !isset($_REQUEST['maxRecord']))
		$_REQUEST['maxRecord'] = 10;
	if ( !isset($_REQUEST['offset']))
		$_REQUEST['offset'] = 0;
	$maxRecord = $_REQUEST['maxRecord'];
	$offset = $_REQUEST['offset'];

	// List the objects that the user has access within the WS
	$listWSObjects = $wslib->list_ws_objects_for_user ($ws_id, $user, $maxRecord, $offset);
	$numObjects = $wslib->count_objects_in_ws ($ws_id);
	$smarty->assign('resources',$listWSObjects);
	
	if ($offset > 0)
	{
		$offset_prev = (int) $offset- (int) $maxRecord;
		$href_prev = "tiki-user_ws.php?showWS=".$ws_id."&nameWS=".$ws_name."&maxRecord=".$maxRecord."&offset=".$offset_prev;
	}
	if (((int) $offset + (int) $maxRecord) <= (int) $numObjects)
	{
		$offset_next = (int) $offset+ (int) $maxRecord;
		$href_next = "tiki-user_ws.php?showWS=".$ws_id."&nameWS=".$ws_name."&maxRecord=".$maxRecord."&offset=".$offset_next;
	}

	$smarty->assign('prev_page',$href_prev);
	$smarty->assign('next_page',$href_next);

	$smarty->assign('mid', 'tiki-user_ws.tpl');
	$smarty->display('tiki.tpl'); 
}

?>