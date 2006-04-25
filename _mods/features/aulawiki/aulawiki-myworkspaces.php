<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

require_once('tiki-setup.php');
require_once('lib/aulawiki/workspacelib.php');

$workspacesLib = new WorkspaceLib($dbTiki);



global $tikilib;
global $userlib;
global $user;

$grupos = $userlib->get_user_groups($user);
		
$smarty->assign_by_ref('user', $user);
$smarty->assign_by_ref('groups', $grupos);
//print_r($grupos);


$activeWorkspaces = $workspacesLib->list_active_workspaces();
//print_r($activeWorkspaces);
$workspaceCodes = array();
foreach ($activeWorkspaces as $key => $workspace) {
	$workspaceCodes[$workspace["code"]] = $workspace;
}
//print_r($workspaceCodes);
// Buscamos los grupos a los el pertenece el usuario, 
// que estan asociados a una asignatura
$userWorkspaces = array();

foreach ($grupos as $key => $group) {
	if (substr($group,0,5)=="WSGRP"){
		$pos = strpos ($group, "-");
		if ($pos === false) {
    		// not a role group
    		$pos=strlen($group);
		}
		$wscode = substr($group,5,$pos-5);
		$userWorkspaces[]=$workspaceCodes[$wscode];
	}
} 

$smarty->assign_by_ref('userWorkspaces',$userWorkspaces);
$smarty->assign('mid','aulawiki-myworkspaces.tpl');
$smarty->display('tiki.tpl');
?>