<?php
// $Id$

/* Workspaces GUI Management */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));
require_once ('lib/workspaces/wsController.php');
require_once 'lib/workspaces/wslib.php';

$ws_gui = new ws_gui_controller();

$ws_gui->check_if_new_to_ws();

global $userlib; require_once 'lib/userslib.php';
//var_dump($groups = $userlib->get_groups());
$smarty->assign('groups', $userlib->get_groups());

if ( isset($_REQUEST['wsoptions']) )
{
    if ( (isset($_REQUEST['wsdevtools'])) && ($_REQUEST['wsdevtools'] == 'create') )
	header("Location: ./lib/workspaces/wstools/scriptCreator.php?action=init&redirect=yes");
    else if ( ($_REQUEST['wsdevtools'] == 'clearcache') )
	header("Location: ./lib/workspaces/wstools/scriptCreator.php?action=clearcache&redirect=yes");
    else
	header("Location: ./lib/workspaces/wstools/scriptCreator.php?action=destroy&redirect=yes");
}
else if ( isset($_REQUEST['wscreate']) )
{
    //var_dump($groupName = $_REQUEST['groupname']);
    $wslib->add_ws($_REQUEST['wsname'],1, $_REQUEST['groupname']);
}

//$ws_gui->list_ws_resources();

ask_ticket('admin-inc-workspaces');
