<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-export_sheet.php,v 1.9 2007-10-12 07:55:27 nyloth Exp $

// Based on tiki-galleries.php
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'sheet';
require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');

if ($prefs['feature_sheet'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_sheets");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y' && !$tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_view_sheet')) {
	$smarty->assign('msg', tra("Access Denied").": feature_sheets");

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('sheetId', $_REQUEST["sheetId"]);

// Individual permissions are checked because we may be trying to edit the gallery

// Init smarty variables to blank values
//$smarty->assign('theme','');

$info = $sheetlib->get_sheet_info( $_REQUEST["sheetId"] );

$encoding = new Encoding ();
$charsetList = $encoding->get_input_supported_encodings();
$smarty->assign_by_ref( "charsets", $charsetList );

$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);

$smarty->assign('page_mode', 'form' );

// Process the insertion or modification of a gallery here

$grid = &new TikiSheet;

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	$smarty->assign('page_mode', 'submit' );

	$sheetId = $_REQUEST['sheetId'];
    $encoding = $_REQUEST['encoding'];

	$handler = &new TikiSheetDatabaseHandler( $sheetId );
	$grid->import( $handler );

	$handler = $_REQUEST['handler'];
	
	if( !in_array( $handler, TikiSheet::getHandlerList() ) )
	{
		$smarty->assign('msg', "Handler is not allowed.");

		$smarty->display("error.tpl");
		die;
	}

	$handler = &new $handler( "php://stdout" , 'UTF-8', $encoding );
	$grid->export( $handler );

	exit;
}
else
{
	$list = array();

	$handlers = TikiSheet::getHandlerList();
	
	foreach( $handlers as $key=>$handler )
	{
		$temp = &new $handler;
		if( !$temp->supports( TIKISHEET_SAVE_DATA | TIKISHEET_SAVE_CALC ) )
			continue;

		$list[$key] = array(
			"name" => $temp->name(),
			"version" => $temp->version(),
			"class" => $handler
		);
	}

	$smarty->assign_by_ref( "handlers", $list );
}

$cat_type = 'sheet';
$cat_objid = $_REQUEST["sheetId"];
include_once ("categorize_list.php");

include_once ('tiki-section_options.php');
ask_ticket('sheet');
// Display the template
$smarty->assign('mid', 'tiki-export-sheets.tpl');
$smarty->display("tiki.tpl");

?>
