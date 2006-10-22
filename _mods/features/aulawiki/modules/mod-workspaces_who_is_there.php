<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
require_once ('lib/workspaces/userlib.php');
include_once ('lib/workspaces/workspacelib.php');

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('msg', tra("Workspace not selected"));
	$smarty->display("error.tpl");
	die;
}

$wsUserLib = new WorkspaceUserLib($dbTiki);
$on_users = array ();
$off_users = array ();

$workspace_users = array ();

$workspace_users = $wsUserLib->get_includegrp_users("WSGRP".$workspace["code"]);
$online_users = $tikilib->get_online_users();
foreach ($workspace_users as $key => $wsuser) {
	$online = false;
	foreach ($online_users as $key2 => $on_user) {
		if ($wsuser["login"] == $on_user["user"]) {
			$on_users[$wsuser["login"]] = $wsuser;
			$online = true;
		}
	}
	if (!$online) {
		$off_users[$wsuser["login"]] = $wsuser;
	}
}

$smarty->assign('online_users', $on_users);
$smarty->assign('offline_users', $off_users);
?>