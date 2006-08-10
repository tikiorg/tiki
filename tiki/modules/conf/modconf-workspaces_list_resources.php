<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

function module_workspaces_list_resources_help() {
	return "This module allow to list a type of resources (blogs,wikipages,file galleries....), on the active workspace.";
}

function module_workspaces_list_resources_title() {
	return "Workspace resources";
}

function module_workspaces_list_resources_params() {
	$param = array();

	$param["type"]=array();
	$param["type"]["name"]="Resources type";
	$param["type"]["help"]="List the resources of the selected type (blog, calendar, faq, file gallery, forum, image gallery, quiz,structure, sheet, survey, tracker, wiki page).";
	$param["type"]["defaultValue"]="blog";
	$param["showDesc"]=array();
	$param["showDesc"]["name"]="Show description";
	$param["showDesc"]["help"]="Show (y) or hide (n) the resource description column.";
	$param["showDesc"]["defaultValue"]="y";
	$param["showType"]=array();
	$param["showType"]["name"]="Show type";
	$param["showType"]["help"]="Show (y) or hide (n) the resource type column.";
	$param["showType"]["defaultValue"]="n";
	$param["showCreationDate"]=array();
	$param["showCreationDate"]["name"]="Show date";
	$param["showCreationDate"]["help"]="Show (y) or hide (n) the creation date column.";
	$param["showCreationDate"]["defaultValue"]="y";
	$param["showButtons"]=array();
	$param["showButtons"]["name"]="Show buttons";
	$param["showButtons"]["help"]="Show (y) or hide (n) the resource buttons (admin,permissions and delete).";
	$param["showButtons"]["defaultValue"]="n";
	return $param;
}

?>
