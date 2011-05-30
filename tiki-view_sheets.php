<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
	'file',
	'fileId'
);
$access->check_feature('feature_sheet');
$access->check_feature('feature_jquery_ui');

$info = $sheetlib->get_sheet_info($_REQUEST['sheetId']);

if (empty($info) && !isset( $_REQUEST['file']) && !isset($_REQUEST['fileId'])) {
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

if (!$sheetlib->user_can_view( $_REQUEST['sheetId'] )) {
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

// Start permissions
if ( $_REQUEST['parse'] == 'edit' && !$sheetlib->user_can_edit( $_REQUEST['sheetId'] ) ) {
	$smarty->assign('msg', tra("Permission denied") . ": feature_sheet");
	$smarty->display("error.tpl");
	die;
}

//check to see if we are to do something other than view a file, which is not allowed
if ( $_REQUEST['parse'] != 'y' && ( isset($_REQUEST['file']) || isset($_REQUEST['fileId']) ) ) {
	$smarty->assign('msg', tra("Files are read only at this time"));
	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_contribution'] == 'y') {
	$contributionItemId = $_REQUEST['sheetId'];
	include_once ('contribution.php');
}
//End permissions

//Save
if (isset($_REQUEST['s']) && !empty($_REQUEST['s']) ) { //save
	if ( $_REQUEST['sheetId'] ) {
		$result = $sheetlib->save_sheet( $_REQUEST['s'], $_REQUEST["sheetId"] );
		
	} elseif ( $_REQUEST['file'] ) {
		//$result = $sheetlib->save_sheet( $_REQUEST['s'], null, $_REQUEST['file'], $_REQUEST['type'] );
	}
	die($result);
//Clone
} elseif ( $_REQUEST['parse'] == "clone" ) {
	if ( !$sheetlib->user_can_edit( $_REQUEST['sheetId'] ) ) {
		$smarty->assign('msg', tra("Permission denied"));
		die;
	}
	$access->check_authenticity(tra("Are you sure you want to clone this spreadsheet?"));
	$id = $sheetlib->clone_sheet( $_REQUEST["sheetId"], $_REQUEST['readdate'] );
	if ($id) {
		header("Location: tiki-view_sheets.php?sheetId=".$id);
	} else {
		$smarty->assign('msg', tra("Clone Error"));
	}

//Rollback
} elseif ($_REQUEST['parse'] == 'rollback' && !empty($_REQUEST['readdate'])) {
	if ( !$sheetlib->user_can_edit( $_REQUEST['sheetId'] ) ) {
		$smarty->assign('msg', tra("Permission denied"));
		die;
	}
	$access->check_authenticity(tra("Are you sure you want to rollback this spreadsheet?"));
	$id = $sheetlib->rollback_sheet( $_REQUEST["sheetId"], $_REQUEST['readdate'] );
	if ($id) {
		header("Location: tiki-view_sheets.php?sheetId=".$id);
	} else {
		$smarty->assign('msg', tra("Rollback Error"));
	}
}

//Edit & View
if ( isset($_REQUEST['file']) ) {
	//File sheets
	$handler = new TikiSheetCSVHandler( $_REQUEST['file'] );
	$grid = new TikiSheet();
	$grid->import( $handler );
	$tableHtml[0] = $grid->getTableHtml( true , null, false );
	$smarty->assign('notEditable', 'true');
	
	if ($handler->truncated) $smarty->assign('msg', tra('Spreadsheet truncated'));
} elseif ( isset($_REQUEST['fileId']) ) {
	include_once('lib/filegals/filegallib.php');
	$access->check_feature('feature_file_galleries');
	$handler = new TikiSheetFileGalleryCSVHandler($_REQUEST['fileId']);
	
	$grid = new TikiSheet();
	$grid->import( $handler );
	$tableHtml[0] = $grid->getTableHtml( true , null, false );
	$smarty->assign('notEditable', 'true');
	
	if ($handler->truncated) $smarty->assign('msg', tra('Spreadsheet truncated'));
} else {
	//Database sheet
	$handler = new TikiSheetDatabaseHandler( $_REQUEST["sheetId"] );
	//We make sheet able to look at other date save
	if (isset($_REQUEST['readdate']) && !empty($_REQUEST['readdate'])) {
		$smarty->assign('read_date', $_REQUEST['readdate']);
		$handler->setReadDate($_REQUEST['readdate']);
	}
	
	$grid = new TikiSheet( $_REQUEST["sheetId"] );
	$grid->import( $handler );
		
	//ensure that sheet isn't being edited, then parse values if needed
	if ( $grid->parseValues && $_REQUEST['parse'] != 'edit' ) {
		$grid->parseValues = true;
	} else {
		$grid->parseValues = false;
	}
	
	$smarty->assign('parseValues', $grid->parseValues);
			
	$tableHtml[0] = $grid->getTableHtml( true, $_REQUEST['readdate'] );
}

	
if (isset($_REQUEST['sheetonly']) && $_REQUEST['sheetonly'] == 'y') {
	foreach( $tableHtml as $table ) {
		echo $table;
	}
	die;
}

$smarty->assign('grid_content', $tableHtml);
$smarty->assign('menu', $smarty->fetch('tiki-view_sheets_menu.tpl'));

$sheetlib->setup_jquery_sheet();
$headerlib->add_jq_onready('
	$.sheet.tikiOptions = $.extend($.sheet.tikiOptions, {
		editable: ("'. $_REQUEST['parse'] .'" == "edit" ? true : false),
		menu: $("#sheetMenu").html()
	});
	
	$.sheet.tikiSheet = $("div.tiki_sheet").sheet($.sheet.tikiOptions);
	
	$.sheet.tikiSheet.id = "'.$_REQUEST['sheetId'].'";
	$.sheet.tikiSheet.file = "'.$_REQUEST['file'].'";
	
	$.sheet.link.setupUI($.sheet.tikiSheet);
	$.sheet.manageState($.sheet.tikiSheet);
	
	$("#edit_button a")
		.click(function() {
			$.sheet.manageState($.sheet.tikiSheet, true, "edit");
			return false;
		});
						
	$("#save_button a")
		.click( function () {
			$("#saveState").hide();
			
			$.sheet.saveSheet($.sheet.tikiSheet, function() {
				$.sheet.manageState($.sheet.tikiSheet, true, "");
			});
			
			return false;
		});
	
	$("#cancel_button")
		.click(function() {
			$("#saveState").hide();
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
