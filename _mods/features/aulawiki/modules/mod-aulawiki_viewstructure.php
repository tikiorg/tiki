<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

include_once ('lib/aulawiki/workspacelib.php');
include_once ('lib/aulawiki/printlib.php');
include_once "lib/structures/structlib.php";

global $dbTiki;
global $userlib;
$workspacesLib = new WorkspaceLib($dbTiki);

$workspace = $workspacesLib->get_current_workspace();

if (!isset ($workspace)) {
	$smarty->assign('msg', tra("Workspace not selected"));
	$smarty->display("error.tpl");
	die;
}

$page = $workspace["code"]."-WStructure";

if (isset ($module_params["name"]) && $module_params["name"] != "") {
	$page = $workspace["code"]."-".$module_params["name"];
}

$structlib = new StructLib($dbTiki);
$structureId = $structlib->get_struct_ref_if_head($page);

//TODO: Check perms
/*	
if (isset($_REQUEST["move_node"]) && isset($_REQUEST["page_ref_id"])) {
		
		if ($_REQUEST["move_node"] == '1') {
			$structlib->promote_node($_REQUEST["page_ref_id"]);
		} elseif ($_REQUEST["move_node"] == '2') {
			$structlib->move_before_previous_node($_REQUEST["page_ref_id"]);
		}	elseif ($_REQUEST["move_node"] == '3') {
			$structlib->move_after_next_node($_REQUEST["page_ref_id"]);
		} elseif ($_REQUEST["move_node"] == '4') {
			$structlib->demote_node($_REQUEST["page_ref_id"]);
		}
	}
	*/
if (isset ($structureId) && $structureId != "") {
	$printlib = new PrintLib($dbTiki);
	$subtree = $printlib->s_print_structure($structureId);
	$smarty->assign_by_ref('subtree', $subtree);
	$smarty->assign('structureId', $structureId);
} else {
	$smarty->assign('subtree', null);
}
?>