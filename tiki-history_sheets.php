<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'sheet';
$tiki_sheet_div_style = 'height: 400px ! important;';
require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');
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

$smarty->assign('sheetId', $_REQUEST["sheetId"]);

// Individual permissions are checked because we may be trying to edit the gallery

// Init smarty variables to blank values
//$smarty->assign('theme','');


$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);

$smarty->assign('page_mode', 'view' );

//display the history picker if no sheets are defined
if (empty($_REQUEST['readdate'])) {
	$result = $tikilib->query( "SELECT DISTINCT `begin`, `user` FROM `tiki_sheet_values` WHERE `sheetId` = ? ORDER BY begin DESC", array( $_REQUEST['sheetId'] ) );
	$data = array();
	while( $row = $result->fetchRow() )
		$data[] = array( "stamp" =>$row['begin'], "string" => $tikilib->date_format( "%Y-%m-%d %H:%M:%S", $row['begin'] ), "user" => $row['user'] );
	
	$smarty->assign_by_ref( 'history', $data );
	
	include_once ('tiki-section_options.php');
	ask_ticket('sheet');
} else {
	// Process the insertion or modification of a gallery here
	$grid = new TikiSheet($_REQUEST["sheetId"]);
	$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
	$dates = array(time());
	
	$i = 0;
	$readdates = explode("|", $_REQUEST['readdate'] );
	foreach ( $readdates as $dateStr ) {
		if ( $dateStr ) {
			$dates[$i] = $dateStr;
			if (!is_numeric($dates[$i])) $dates[$i] = strtotime($dates[$i]);
			if ($dates[$i] == - 1) $dates[$i] = time();
		}
		$i++;
	}
	
	$tableHtml = array();
	$i = 0;
	
	foreach ( $dates as $date ) {
		$smarty->assign('read_date', $date);
		$handler->setReadDate($date);
		$grid->import($handler);
		$tableHtml[$i] = $grid->getTableHtml( true, $date );
		$i++;
	}
	
	$smarty->assign('grid_content', $tableHtml);
	$handler = new TikiSheetDatabaseHandler($_REQUEST["sheetId"]);
	$grid->import($handler);
	
	$headerlib->add_jq_onready(
		'if (typeof ajaxLoadingShow == "function") {
			ajaxLoadingShow("role_main");
		}
		setTimeout (function () { $("div.tiki_sheet").tiki("sheet", "",{editable:false});}, 500);
		instanceCount = ' . count($readdates) . ';
		$("#tiki_sheet_container").one("mousedown", function() {
			lockSheetTogether();		
		});
		'
	, 500);
	
	if ( $tiki_sheet_div_style) {
		$smarty->assign('tiki_sheet_div_style',  $tiki_sheet_div_style);
	}
}

// Display the template
$smarty->assign('mid', 'tiki-history-sheets.tpl');
$smarty->display("tiki.tpl");
