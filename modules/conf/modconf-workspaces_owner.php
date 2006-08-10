<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

function module_workspaces_owner_help() {
	return "This module show the owner of the current workspace and list the workspaces of the owner user.";
}

function module_workspaces_owner_title() {
	return "Workspace owner";
}

function module_workspaces_owner_params() {
	$param = array();

	$param["showName"]=array();
	$param["showName"]["name"]="Showname";
	$param["showName"]["help"]="Show (y) or hide (n) the owner name.";
	$param["showName"]["defaultValue"]="y";
	
	$param["showWorkspaces"]=array();
	$param["showWorkspaces"]["name"]="Show workspaces";
	$param["showWorkspaces"]["help"]="Show (y) or hide (n) the workspace list.";
	$param["showWorkspaces"]["defaultValue"]="y";
	
	return $param;
}

?>
