<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'sheet';
$tiki_sheet_div_style = '';
require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');
$auto_query_args = array(
	'sheetId',
	'readdate',
	'parse',
	'simple',
	'height',
);
$access->check_feature('feature_sheet');
if (!isset($_REQUEST['sheetId'])) die;

$info = $sheetlib->get_sheet_info($_REQUEST['sheetId']);

if (empty($info)) {
	$smarty->assign('Incorrect parameter');
	$smarty->display('error.tpl');
	die;
}

$objectperms = Perms::get( 'sheet', $_REQUEST['sheetId']);
if ($user && $user == $info['author']) {
	$objectperms->view_sheet = 1;
	$objectperms->edit_sheet = 1;
	$objectperms->tiki_p_view_sheet = 1;
	$objectperms->tiki_p_edit_sheet = 1;
}

if ($tiki_p_admin != 'y' && !$objectperms->view_sheet) {
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('objectperms', $objectperms);

if (isset($_REQUEST['height'])) {
	 $tiki_sheet_div_style .= 'height:'.$_REQUEST['height'].';';
}

if ( $tiki_sheet_div_style) {
	$smarty->assign('tiki_sheet_div_style',  $tiki_sheet_div_style);
}

//here we make sure parse is set so we don't have to keep checking using isset
if (!isset($_REQUEST['parse'])) {
	$_REQUEST['parse'] = 'y';
}

$smarty->assign( 'parse', $_REQUEST['parse'] );

$smarty->assign('sheetId', $_REQUEST["sheetId"]);
$smarty->assign('chart_enabled', (function_exists('imagepng') || function_exists('pdf_new')) ? 'y' : 'n');
$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);

// Process the insertion or modification of a gallery here
if (
		$_REQUEST['parse'] == 'edit' && 
		!$objectperms->edit_sheet && $tiki_p_admin != 'y'
	) {
	$smarty->assign('msg', tra("Permission denied") . ": feature_sheet");
	$smarty->display("error.tpl");
	die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //save
	if (!$objectperms->edit_sheet && $tiki_p_admin != 'y') {
		$smarty->assign('msg', tra('Permission denied'));
		$smarty->display("error.tpl");
		die;
	}
	if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {					// ********* AJAX save request from jQuery.sheet
		$result = $sheetlib->save_sheet( $_REQUEST['s'], $_REQUEST["sheetId"] );
		die($result);
	}
	
} elseif ( $_REQUEST['parse'] == "clone" ) {
	$access->check_permission('tiki_p_edit_sheet');
	//$access->check_authenticity();
	$id = $sheetlib->clone_sheet( $_REQUEST["sheetId"], $_REQUEST['readdate'] );
	if ($id) {
		header("Location: tiki-view_sheets.php?sheetId=".$id);
	}
} elseif ($_REQUEST['parse'] == 'rollback' && !empty($_REQUEST['readdate'])) {
	$access->check_permission('tiki_p_edit_sheet');
	//$access->check_authenticity();
	$id = $sheetlib->rollback_sheet( $_REQUEST["sheetId"], $_REQUEST['readdate'] );
	if ($id) {
		header("Location: tiki-view_sheets.php?sheetId=".$id);
	}
} else {
	if ($_REQUEST['parse'] == 'edit') {
		$access->check_permission('tiki_p_edit_sheet');
	}
	$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
	
	//We make sheet able to look at other date save
	if (isset($_REQUEST['readdate']) && !empty($_REQUEST['readdate'])) {
		$smarty->assign('read_date', $_REQUEST['readdate']);
		$handler->setReadDate($_REQUEST['readdate']);
	}
	
	$grid = new TikiSheet($_REQUEST["sheetId"]);
	$grid->import($handler);

	//ensure that sheet isn't being edited, then parse values if needed
	if ( $grid->parseValues && $_REQUEST['parse'] != 'edit' ) {
		$grid->parseValues = true;
	} else {
		$grid->parseValues = false;
	}
	$smarty->assign('parseValues', $grid->parseValues);
			
	$tableHtml[0] = $grid->getTableHtml( true, $_REQUEST['readdate'] );
		
	if (isset($_REQUEST['sheetonly']) && $_REQUEST['sheetonly'] == 'y') {
		foreach( $tableHtml as $table ) {
			echo $table;
		}
		die;
	}
	
	$smarty->assign('grid_content', $tableHtml);
	$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
	$grid->import($handler);
}


if ($prefs['feature_contribution'] == 'y') {
	$contributionItemId = $_REQUEST['sheetId'];
	include_once ('contribution.php');
}

$sheetlib->setup_jquery_sheet();
$headerlib->add_jq_onready('
	$.sheet.tikiOptions = $.extend($.sheet.tikiOptions, {
		editable: ("'. $_REQUEST['parse'] .'" == "edit" ? true : false)
	});
	
	var tikiSheet = $("div.tiki_sheet").sheet($.sheet.tikiOptions);
	tikiSheet.id = "'.$_REQUEST['sheetId'].'";
	
	$.sheet.manageState(tikiSheet);
	
	$("#edit_button a")
		.click(function() {
			$.sheet.manageState(tikiSheet, true, "edit");
			return false;
		});
						
	$("#save_button a")
		.click( function () {
			$.sheet.saveSheet("tiki-view_sheets.php?sheetId=" + tikiSheet.id, false, function() {
				$.sheet.manageState(tikiSheet, true, "");
			});
			
			return false;
		});
');

$smarty->assign('semUser', '');
if ($prefs['feature_warn_on_edit'] == 'y') {
	if ($tikilib->semaphore_is_set($_REQUEST['sheetId'], $prefs['warn_on_edit_time'] * 60, 'sheet') && ($semUser = $tikilib->get_semaphore_user($_REQUEST['sheetId'], 'sheet')) != $user) {
		$editconflict = 'y';
		$smarty->assign('semUser', $semUser);
	} else {
		$editconflict = 'n';
	}
	if ($_REQUEST['parse'] == 'edit') {
		$_SESSION['edit_lock_sheet' . $_REQUEST['sheetId']] = $tikilib->semaphore_set($_REQUEST['sheetId'], 'sheet');
	} elseif (isset($_SESSION['edit_lock_sheet' . $_REQUEST['sheetId']])) {
		$tikilib->semaphore_unset($_REQUEST['sheetId'], $_SESSION['edit_lock_sheet' . $_REQUEST['sheetId']]);
		unset($_SESSION['edit_lock_sheet' . $_REQUEST['sheetId']]);
	}
} else {
		$editconflict = 'n';
}
$smarty->assign('editconflict', $editconflict);

$cat_type = 'sheet';
$cat_objid = $_REQUEST["sheetId"];
include_once ("categorize_list.php");
include_once ('tiki-section_options.php');
ask_ticket('sheet');

// Display the template
$smarty->assign('mid', 'tiki-view_sheets.tpl');
$smarty->display("tiki.tpl");
