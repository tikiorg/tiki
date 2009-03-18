<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
* Based on:
* */
// $Header: /cvsroot/tikiwiki/_mods/features/aulawiki/tiki-workspaces_objectpermissions.php,v 1.3 2007-03-03 08:44:56 jreyesg Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("tiki-setup.php");
require_once ('lib/workspaces/workspacelib.php');

$workspacesLib = new WorkspaceLib($dbTiki);
$workspace = $workspacesLib->get_current_workspace();
$wsUserLib = new WorkspaceUserLib($dbTiki);

if (!isset($_REQUEST["referer"])) {
	if (isset($_SERVER['HTTP_REFERER'])) {
		$_REQUEST["referer"] = $_SERVER['HTTP_REFERER'];
	}
}

if (isset($_REQUEST["referer"])) {
	$smarty->assign('referer', $_REQUEST["referer"]);
}

if (!isset(
	$_REQUEST["objectName"]) || !isset($_REQUEST["objectType"]) || !isset($_REQUEST["resourceIdName"]) || !isset($_REQUEST["permType"])) {
	$smarty->assign('msg', tra("Not enough information to display this page"));

	$smarty->display("error.tpl");
	die;
}

$_REQUEST["resourceIdName"] = urldecode($_REQUEST["resourceIdName"]);
$_REQUEST["objectType"] = urldecode($_REQUEST["objectType"]);
$_REQUEST["permType"] = urldecode($_REQUEST["permType"]);

$smarty->assign('objectName', $_REQUEST["objectName"]);
$smarty->assign('resourceIdName', $_REQUEST["resourceIdName"]);
$smarty->assign('objectType', $_REQUEST["objectType"]);
$smarty->assign('permType', $_REQUEST["permType"]);

global $userlib;
$admin_perm_name = "tiki_p_";
if($_REQUEST["objectType"]=="blog"){
	$admin_perm_name = "tiki_p_blog_admin";
}elseif($_REQUEST["objectType"]=="image gallery"){
	$admin_perm_name = "tiki_p_admin_galleries";
}elseif($_REQUEST["objectType"]=="file gallery"){
	$admin_perm_name = "tiki_p_admin_file_galleries";
}elseif($_REQUEST["objectType"]=="assignments"){
	$admin_perm_name = "aulawiki_p_admin_assignments";
}elseif($_REQUEST["objectType"]=="forum" ){
	$admin_perm_name = "tiki_p_admin_forum";
}elseif($_REQUEST["objectType"]=="calendar" ){
	$admin_perm_name = "tiki_p_admin_calendar";
}elseif($_REQUEST["objectType"]=="quiz"){
	$admin_perm_name = "tiki_p_admin_quizzes";
}elseif($_REQUEST["objectType"]=="wiki page"){
	$admin_perm_name = "tiki_p_admin_wiki";
}elseif($_REQUEST["objectType"]=="sheet"){
	$admin_perm_name = "tiki_p_admin_sheet";
##### pingus start  
//    'tiki_p_admin_workspace' has no ending "s" too
}elseif($_REQUEST["objectType"]=="workspace"){
	$admin_perm_name = "tiki_p_admin_workspace";
##### pingus end
}else{
	$admin_perm_name = "tiki_p_admin_".$_REQUEST["objectType"]."s";
} 

global $user;
##### pingus start: 
//  if user has tiki_p_admin_workspace on this ws, he can assign perms to all object of this ws too
//  like a real 'tiki_p_admin_workspace' enabled user/group can do 
//  block partially pasted from tiki-workspaces_desktop.php
if ($tiki_p_admin != 'y' && $tiki_p_admin_workspace!='y') {  # not admin nor his group can admin workspaces
        # nor has tiki_p_admin_workspace nor tiki_p_create_ws_resour from perms of this workspace object 
#	if (!$userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "tiki_p_admin_workspace")
        if (!$workspacesLib->user_can_admin_workspace_or_upper($user,$workspace)
	    && ($_REQUEST["objectType"]=="workspace"
                && $userlib->object_has_permission($user, $workspace["workspaceId"], 'workspace', "tiki_p_create_workspace_resour"))) {  
                  $smarty->assign('msg', tra("Permission denied you cannot view this page"));
                  $smarty->display("error.tpl");
                  die;
	}
}
##### pingus end

// Process the form to assign a new permission to this page
if (isset($_REQUEST["assign"])) {
	check_ticket('object-perms');
	$userlib->assign_object_permission($_REQUEST["group"], $_REQUEST["resourceIdName"], $_REQUEST["objectType"], $_REQUEST["perm"]);
	$smarty->assign('groupName', $_REQUEST["group"]);
}

// Process the form to remove a permission from the page
if (isset($_REQUEST["action"])) {
	check_ticket('object-perms');
	if ($_REQUEST["action"] == 'remove') {
		$userlib->remove_object_permission($_REQUEST["group"], $_REQUEST["resourceIdName"], $_REQUEST["objectType"], $_REQUEST["perm"]);
	}
}

// Now we have to get the individual page permissions if any
$page_perms = $userlib->get_object_permissions($_REQUEST["resourceIdName"], $_REQUEST["objectType"]);
$smarty->assign_by_ref('page_perms', $page_perms);

// Get a list of groups
######## pingus test
##$groups = $userlib->get_groups(0, -1, 'groupName_desc', '',"WSGRP".$workspace["code"], 'n');

#$groups = array_merge($groups1, $workspacesLib->get_child_workspaces_groups(0,-1,'name_desc',$workspace["workspaceId"],"WSGRP".$workspace["code"]));
if ($topmost_workspace_Iadmin=$workspacesLib->get_topmost_workspace_Iadmin($user,$workspace)){
	$groups = $workspacesLib->get_child_workspaces_groups($topmost_workspace_Iadmin,"WSGRP".$workspace["code"]);
	}
#	$groups = $workspacesLib->get_child_workspaces_groups($workspace["workspaceId"],"WSGRP".$workspace["code"]);
$groups[]="Anonymous";
$groups[]="Registered";

$smarty->assign_by_ref('groups', $groups);

// Get a list of permissions
$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', $_REQUEST["permType"]);
$smarty->assign_by_ref('perms', $perms["data"]);

ask_ticket('object-perms');

$smarty->assign('mid', 'tiki-workspaces_objectpermissions.tpl');
$smarty->display("tiki.tpl");

?>
