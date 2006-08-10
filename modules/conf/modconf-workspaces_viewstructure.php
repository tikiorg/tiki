<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
function module_workspaces_viewstructure_help() {
	return "This module list the pages of a structure";
}

function module_workspaces_viewstructure_title() {
	return "Structure";
}

function module_workspaces_viewstructure_params() {
	$param = array();

	$param["name"]=array();
	$param["name"]["name"]="Structure name";
	$param["name"]["help"]="Main WikiPage name of a structure, you can use %WSCODE% to paste the active workspace code, Eg. %WSCODE%-Structure1.";
	$param["name"]["defaultValue"]="%WSCODE%-Structure1";
	$param["showControls"]=array();
	$param["showControls"]["name"]="Show controls";
	$param["showControls"]["help"]="Show (y) or hide (n) the move and delete page controls.";
	$param["showControls"]["defaultValue"]="y";
	return $param;
}

?>
