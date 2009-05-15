<?php
// $Id$

/* Workspaces GUI Management */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

//if $prefs['feature_workspaces_init'] != 'n'
//{
	$smarty->assign('welcome', tra('Please select one option bellow. Remember this step is very important, so be careful when you select the workspace category container, because you can not step back!'));
//}

ask_ticket('admin-inc-workspaces');
?>
