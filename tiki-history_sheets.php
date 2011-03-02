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
	'idx_0',
	'idx_1'
);
$access->check_feature('feature_sheet');

$info = $sheetlib->get_sheet_info( $_REQUEST['sheetId'] );
if (empty($info)) {
	$smarty->assign('Incorrect parameter');
	$smarty->display('error.tpl');
	die;
}	

$objectperms = Perms::get( 'sheet', $_REQUEST['sheetId'] );
if ($tiki_p_admin != 'y' && !$objectperms->view_sheet && !($user && $info['author'] == $user)) {
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display('error.tpl');
	die;
}
$smarty->assign('objectperms', $objectperms);
$smarty->assign('sheetId', $_REQUEST["sheetId"]);
$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);
$smarty->assign('page_mode', 'view' );

$history = $sheetlib->sheet_history( $_REQUEST['sheetId'] );
$smarty->assign_by_ref( 'history', $history );

$sheetIndexes = array();
if ( isset($_REQUEST['idx_0']) ) {
	$sheetIndexes[0] = $_REQUEST['idx_0'];	
} else {
	$sheetIndexes[0] = 1; //this sets defalut for initial page load
}
if ( isset($_REQUEST['idx_1']) ) {
	$sheetIndexes[1] = $_REQUEST['idx_1'];
} else {
	$sheetIndexes[1] = 0; //this sets defalut for initial page load
}

$smarty->assign_by_ref( 'sheetIndexes', $sheetIndexes );
$smarty->assign( 'ver_cant' , count($history) );
$smarty->assign( 'grid_content', $sheetlib->diff_sheets_as_html($_REQUEST["sheetId"], array($history[$sheetIndexes[0]]['stamp'], $history[$sheetIndexes[1]]['stamp'])) );

$cookietab = 1;

$sheetlib->setup_jquery_sheet();
$headerlib->add_jq_onready("
	$.sheet.tikiOptions = $.extend($.sheet.tikiOptions, {
		editable: false,
		fnPaneScroll: $.sheet.paneScrollLocker,
		fnSwitchSheet: $.sheet.switchSheetLocker
	});
	
	$('div.tiki_sheet').each(function() {
		$(this).sheet($.sheet.tikiOptions);
	});
	
	$.sheet.setValuesForCompareSheet('$sheetIndexes[0]', $('input.compareSheet1'), '$sheetIndexes[1]', $('input.compareSheet2'));
	
	$('#go_fullscreen').toggle(function() {
		$.sheet.dualFullScreenHelper($('#tiki_sheet_container').parent());
	}, function() {
		$.sheet.dualFullScreenHelper($('#tiki_sheet_container').parent(), true);
	});
", 500);

if ( $tiki_sheet_div_style) {
	$smarty->assign('tiki_sheet_div_style',  $tiki_sheet_div_style);
}

include_once ('tiki-section_options.php');
ask_ticket('sheet');

$smarty->assign('lock', true);

// Display the template
$smarty->assign('mid', 'tiki-history_sheets.tpl');
$smarty->display("tiki.tpl");
