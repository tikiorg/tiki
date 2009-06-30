<?php
// $Id$

/* Workspaces GUI Management */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));
require_once ('lib/workspaces/wsController.php');

$wsGui = new wsGuiController();

//if ($wsGui->check_if_new_to_ws())
//    $smarty->assign("welcome", true);
    
//$wsGui->prueba();

ask_ticket('admin-inc-workspaces');
