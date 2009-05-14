<?php
// $Id$

/* Workspaces GUI Management */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$smarty->assign('warning', tra('In order to get Workspaces working you must to configure first how do you want '));

ask_ticket('admin-inc-workspaces');
?>
