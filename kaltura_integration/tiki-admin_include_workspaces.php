<?php

/* Check access */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

/* Rest of imports */
global $userlib; require_once 'lib/userslib.php';
global $wslib; require_once 'lib/workspaces/wslib.php';

$wslib->init_ws();

//var_dump($groups = $userlib->get_groups());
$smarty->assign('groups', $userlib->get_groups());

if ( isset($_REQUEST['wsoptions']) )
{
    if ( (isset($_REQUEST['wsdevtools'])) && ($_REQUEST['wsdevtools'] == 'create') )
	header("Location: ./lib/workspaces/wstools/scriptCreator.php?action=create&redirect=yes");
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
