<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

function module_workspaces_last_image_help() {
	return "List the active workspace image galleries, and the images of the selected gallerie.";
}

function module_workspaces_last_image_title() {
	return "File Galleries";
}

function module_workspaces_last_image_params() {
	$param = array();
	$param["name"]=array();
	$param["name"]["name"]="Image gallerie name";
	$param["name"]["help"]="Image gallerie name, you can use %WSCODE% to paste the active workspace code, Eg. %WSCODE%-FileGal1";
	$param["name"]["defaultValue"]="ImageGal";
	$param["maxImages"]=array();
	$param["maxImages"]["name"]="Max images";
	$param["maxImages"]["help"]="Maximum number of images per page";
	$param["maxImages"]["defaultValue"]="3";
	$param["showBar"]=array();
	$param["showBar"]["name"]="Show list";
	$param["showBar"]["help"]="Show or hide the workspace image galleries list";
	$param["showBar"]["defaultValue"]="y";
	$param["offset"]=array();
	$param["offset"]["name"]="Page offset";
	$param["offset"]["help"]="Page offset";
	$param["offset"]["defaultValue"]="0";
	$param["offset"]["hide"]="y";
	return $param;
}

?>
