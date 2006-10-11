<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

function module_workspaces_resources_help() {
	return "This module allow to manage the active workspace resources (blogs,wikipages,file galleries....).";
}

function module_workspaces_resources_title() {
	return "Workspace resources";
}

function module_workspaces_resources_params() {
	$param = array();

	$param["showBar"]=array();
	$param["showBar"]["name"]="Show buttons bar";
	$param["showBar"]["help"]="Show or hide the buttons bar";
	$param["showBar"]["defaultValue"]="y";
	return $param;
}

?>
