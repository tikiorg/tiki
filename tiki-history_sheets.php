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
	'idx_0',
	'idx_1'
);
$access->check_feature('feature_sheet');

$sheetlib->setupJQuerySheet();
$sheetlib->setupJQuerySheetHistory();

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

$result = $tikilib->query( "SELECT DISTINCT `begin`, `user` FROM `tiki_sheet_values` WHERE `sheetId` = ? ORDER BY begin DESC", array( $_REQUEST['sheetId'] ) );
$history = array();

$i = 0;
while( $row = $result->fetchRow() ) {
	$history[$i] = array(
		"stamp" => $row['begin'], 
		"string" => $tikilib->date_format( "%Y-%m-%d %H:%M:%S", $row['begin'] ), 
		"user" => $row['user'],
		"index" => $i
	);
	$i++;
}

$smarty->assign_by_ref( 'history', $history );

$sheetIndexes = array();
if ( isset($_REQUEST['idx_0']) ) $sheetIndexes[0] = $_REQUEST['idx_0'];
if ( isset($_REQUEST['idx_1']) ) $sheetIndexes[1] = $_REQUEST['idx_1'];

//display the history picker if no sheets are defined
if ( count($sheetIndexes) > 1 ) {
	$dates = array();
	$datesFormatted = array();
	
	$smarty->assign_by_ref( 'sheetIndexes', $sheetIndexes );
	
	$j = 0;
	foreach( $sheetIndexes as $i ) {
		$dates[$j] = $history[(int)$i]['stamp'];
		$datesFormatted[$j] = date("F j, Y, g:i a", strftime($dates[$j]));
		$j++;
	}
	
	// for revision info
	$smarty->assign( 'datesFormatted' , $datesFormatted );
	
	// for pagination
	$smarty->assign( 'ver_cant' , count($history) );
	//$paginate = (isset($_REQUEST['paginate']) && $_REQUEST['paginate'] == 'on');
	//$smarty->assign('paginate', $paginate);
	$smarty->assign( 'grid_content', diffSheetsAsHTML($_REQUEST["sheetId"], $dates) );
	
	$headerlib->add_jq_onready("
		$.sheet.tikiOptions = $.extend($.sheet.tikiOptions, {
			editable: false,
			fnPaneScroll: $.sheet.paneScrollLocker,
			fnSwitchSheet: $.sheet.switchSheetLocker
		});
		
		$('div.tiki_sheet').each(function() {
			$(this).sheet($.sheet.tikiOptions);
		});
		
		setValuesForCompareSheet('$sheetIndexes[0]','$sheetIndexes[1]');
	", 500);
	
	if ( $tiki_sheet_div_style) {
		$smarty->assign('tiki_sheet_div_style',  $tiki_sheet_div_style);
	}
}

include_once ('tiki-section_options.php');
ask_ticket('sheet');

$smarty->assign('lock', true);

// Display the template
$smarty->assign('mid', 'tiki-history_sheets.tpl');
$smarty->display("tiki.tpl");
