<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-import_sheet.php,v 1.10 2007-10-12 07:55:28 nyloth Exp $

// Based on tiki-galleries.php
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');

if ($prefs['feature_sheet'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_sheets");

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('sheetId', $_REQUEST["sheetId"]);

// Individual permissions are checked because we may be trying to edit the gallery

// Init smarty variables to blank values
//$smarty->assign('theme','');

$info = $sheetlib->get_sheet_info( $_REQUEST["sheetId"] );

if ($tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y' && !($user && $info['author'] == $user) && !$tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_edit_sheet')) {
	$smarty->assign('msg', tra("Access Denied").": feature_sheets");

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);

$smarty->assign('page_mode', 'form' );

// Process the insertion or modification of a gallery here

$grid = &new TikiSheet;

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	$smarty->assign('page_mode', 'submit' );

	$sheetId = $_REQUEST['sheetId'];
	$handler = $_REQUEST['handler'];
    $encoding = $_REQUEST['encoding'];
	
	// Instanciate the handler
	switch( $handler )
	{
	case 'TikiSheetWikiTableHandler': // Well known, special handlers
		$handler = &new $handler( $_POST['page'] );
		break;
	default: // All file based handlers registered
		if( !in_array( $handler, TikiSheet::getHandlerList() ) )
		{
			$smarty->assign('msg', "Handler is not allowed.");
			$smarty->display("error.tpl");
			die;
		}
        
       	$handler = &new $handler( $_FILES['file']['tmp_name'] , $encoding, 'UTF-8');
	}

	if( !$grid->import( $handler ) )
	{
		$smarty->assign('msg', "Impossible to import the file.");

		$smarty->display("error.tpl");
		die;
	}

	$handler = &new TikiSheetDatabaseHandler( $sheetId );
	$grid->export( $handler );

	ob_start();
	$handler = &new TikiSheetOutputHandler;
	$grid->export( $handler );
	$smarty->assign( "grid_content", ob_get_contents() );
	ob_end_clean();
}
else
{   
	$list = array();
    $encoding = new Encoding ();
    $charsetList = $encoding->get_input_supported_encodings();

	$handlers = TikiSheet::getHandlerList();
	
	foreach( $handlers as $key=>$handler )
	{
		$temp = &new $handler;
		if( !$temp->supports( TIKISHEET_LOAD_DATA | TIKISHEET_LOAD_CALC ) )
			continue;

		$list[$key] = array(
			"name" => $temp->name(),
			"version" => $temp->version(),
			"class" => $handler
		);
	}

	$smarty->assign_by_ref( "handlers", $list );
    $smarty->assign_by_ref( "charsets", $charsetList );
}

$cat_type = 'sheet';
$cat_objid = $_REQUEST["sheetId"];
include_once ("categorize_list.php");

$section = 'sheet';
include_once ('tiki-section_options.php');

ask_ticket('sheet');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-import-sheets.tpl');
$smarty->display("tiki.tpl");

?>
