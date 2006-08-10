<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

function module_workspaces_viewpage_help() {
	return "Ayuda del modulo";
}

function module_workspaces_viewpage_title() {
	return "View page";
}

function module_workspaces_viewpage_params() {
	$param = array();
	$param["name"]=array();
	$param["name"]["name"]="Page name";
	$param["name"]["help"]="WikiPage name, you can use %WSCODE% to paste the active workspace code, Eg. %WSCODE%-Home";
	$param["name"]["defaultValue"]="HomePage";
	$param["showBar"]=array();
	$param["showBar"]["name"]="Show bar";
	$param["showBar"]["help"]="Show or hide the navigation bar";
	$param["showBar"]["defaultValue"]="y";
	return $param;
}

?>
