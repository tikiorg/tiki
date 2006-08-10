<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

function module_workspaces_user_groups_help() {
	return "This module allow to admin the workspace groups and users";
}

function module_workspaces_user_groups_title() {
	return "Users and groups administration";
}

function module_workspaces_user_groups_params() {
	$param = array();
	$param["showButtons"]=array();
	$param["showButtons"]["name"]="Show buttons";
	$param["showButtons"]["help"]="Show (y) or hide (n) the user/groups administration buttons.";
	$param["showButtons"]["defaultValue"]="y";
	$param["activeGroup"]=array();
	$param["activeGroup"]["name"]="Active group";
	$param["activeGroup"]["help"]="Current selected group";
	$param["activeGroup"]["defaultValue"]="";
	$param["activeGroup"]["hide"]="y";
	$param["activeParentGroup"]=array();
	$param["activeParentGroup"]["name"]="Parent group";
	$param["activeParentGroup"]["help"]="Selected parent group";
	$param["activeParentGroup"]["defaultValue"]="-1";
	$param["activeParentGroup"]["hide"]="y";
	return $param;
}

?>
