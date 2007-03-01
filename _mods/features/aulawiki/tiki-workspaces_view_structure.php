<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once ('tiki-setup.php');

include_once ('lib/workspaces/printlib.php');
include_once ("lib/ziplib.php");

global $structlib;

include_once "lib/structures/structlib.php";
if (isset($_REQUEST["move_node"])) {
	
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
if (isset($_REQUEST['print'])) {
	//check_ticket('admin-structures');
	global $dbTiki;
	$printlib = new PrintLib($dbTiki);
	$subtree = $printlib->s_print_structure($_REQUEST['print']);
	$smarty->assign_by_ref('subtree', $subtree);
	$smarty->assign('structureId', $_REQUEST['print']);
	$smarty->assign('print_page','y');
	$smarty->assign('show_page_bar', 'n');
	$smarty->assign('mid','tiki-workspaces_print.tpl');
	$smarty->display('tiki.tpl');
	
	die;
	
}

?>