<?php
 
require_once('tiki-setup.php');
require_once('lib/workspaces/wslib.php');

// Get maxRecord and offset
if ( !isset($_REQUEST['maxRecord']) || ($_REQUEST['maxRecord'] < 1) || (!is_int($_REQUEST['maxRecord'])) )
    $_REQUEST['maxRecord'] = 10;
if ( !isset($_REQUEST['offset']) || $_REQUEST['offset'] < 0 || (!is_int($_REQUEST['maxRecord'])) )
    $_REQUEST['offset'] = 0;
$maxRecord = $_REQUEST['maxRecord'];
$offset = $_REQUEST['offset'];

// List the objects that the user has access within the WS
$listWS = $wslib->list_ws_that_user_have_access ($user, $maxRecord, $offset);
$smarty->assign('listWS',$listWS);
$numWSUser = $wslib->count_ws ($user);

if ($offset > 0)
{
	$offset_prev = (int) $offset - (int) $maxRecord;
	$href_prev = "tiki-my-workspaces.php?maxRecord=".$maxRecord."&offset=".$offset_prev;
}
if (((int) $offset + (int) $maxRecord) <= (int) $numWSUser)
{
	$offset_next = (int) $offset + (int) $maxRecord;
	$href_next = "tiki-my-workspaces.php?maxRecord=".$maxRecord."&offset=".$offset_next;
}

$smarty->assign('prev_page',$href_prev);
$smarty->assign('next_page',$href_next);

$smarty->assign('mid', 'tiki-my-workspaces.tpl');
$smarty->display('tiki.tpl'); 
?>
