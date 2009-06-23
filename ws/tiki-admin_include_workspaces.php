<?php
// $Id$

/* Workspaces GUI Management */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

//We check if the user first time using Workspaces
if (isset($_REQUEST['save']))
{
    //The user wants to create its own workspaces category
    if (isset($_REQUEST['selected_radio']) && ($_REQUEST['selected_radio'] == 'selected_new_container'))
    {
	$workspacesContainer = $_REQUEST['new_container_name'];
	echo $workspacesContainer;
	$smarty->assign('warning', tra('You chosed an invalid workspaces category name. Please make sure you have typed with a valid set of characters'));
	
    }
    else
    {
    }
}

ask_ticket('admin-inc-workspaces');
?>

