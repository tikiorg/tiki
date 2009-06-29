<?php
// $Id$

/* Workspaces GUI Management */
require_once ('tiki-setup.php');
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

//include_once ('lib/workspaces/wsGuiParser.php');

//We load the wsGuiParser for controlling the major actions the user 
//can make in this page
//$wsGui = new wsGuiParser();

//So we starting checking if the user first time using Workspaces
//$wsGui->checkIfNewToWS();

ask_ticket('admin-inc-workspaces');

