<?php
// $Id$

/* Workspaces GUI Management */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

//We check if the user first time using Workspaces
if (($prefs['new_to_ws'] == 'y') && (isset($_REQUEST['save']) && (isset($_REQUEST['selected_radio']))))
{
    //The user has selected the default option
    if ($_REQUEST['selected_radio'] == 'selected_workspace_name'){
    }
    //The user wants to create its own workspaces category
    else if ($_REQUEST['selected_radio'] == 'selected_new_container')
    {
	$workspacesContainer = $_REQUEST['new_container_name'];
	$smarty->assign('warning', tra('You chosed an invalid workspaces category name. Please make sure you have typed with a valid set of characters'));
    }
    //The user has selected one category for holding the workspaces
    else
    {
    }
}

ask_ticket('admin-inc-workspaces');
?>

