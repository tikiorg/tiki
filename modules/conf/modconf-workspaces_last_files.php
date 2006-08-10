<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

function module_workspaces_last_files_help() {
	return "List the active workspace file galleries, and the files of the selected gallerie.";
}

function module_workspaces_last_files_title() {
	return "File Galleries";
}

function module_workspaces_last_files_params() {
	$param = array();
	$param["name"]=array();
	$param["name"]["name"]="File gallerie name";
	$param["name"]["help"]="File gallerie name, you can use %WSCODE% to paste the active workspace code, Eg. %WSCODE%-FileGal1";
	$param["name"]["defaultValue"]="FileGal";
	$param["maxFiles"]=array();
	$param["maxFiles"]["name"]="Max files";
	$param["maxFiles"]["help"]="Maximum number of files per page";
	$param["maxFiles"]["defaultValue"]="3";
	$param["showBar"]=array();
	$param["showBar"]["name"]="Show list";
	$param["showBar"]["help"]="Show or hide the workspace file galleries list";
	$param["showBar"]["defaultValue"]="y";
	$param["offset"]=array();
	$param["offset"]["name"]="Page offset";
	$param["offset"]["help"]="Page offset";
	$param["offset"]["defaultValue"]="0";
	$param["offset"]["hide"]="y";
	return $param;
}

?>
