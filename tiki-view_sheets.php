<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-view_sheets.php,v 1.16 2007-10-12 07:55:33 nyloth Exp $

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

if ( !isset($_REQUEST['sheetId']) ) {
	$smarty->assign('msg', tra("A SheetId is required."));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y' && !$tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_view_sheet')) {
	$smarty->assign('msg', tra("Access Denied").": feature_sheets");

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('sheetId', $_REQUEST["sheetId"]);
$smarty->assign('chart_enabled', (function_exists('imagepng') || function_exists('pdf_new')) ? 'y' : 'n');

// Individual permissions are checked because we may be trying to edit the gallery

// Init smarty variables to blank values
//$smarty->assign('theme','');

$info = $sheetlib->get_sheet_info( $_REQUEST["sheetId"] );

if ($tiki_p_admin == 'y' || $tiki_p_admin_sheet == 'y' || ($user && $user == $info['author']) || $tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_edit_sheet'))
	$tiki_p_edit_sheet = 'y';
else
	$tiki_p_edit_sheet = 'n';
$smarty->assign('tiki_p_edit_sheet', $tiki_p_edit_sheet);


$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);

$smarty->assign('page_mode', 'view' );

// Process the insertion or modification of a gallery here

$grid = &new TikiSheet;

if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit' && $tiki_p_edit_sheet != 'y' && $tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y') {
	$smarty->assign('msg', tra("Access Denied").": feature_sheets");
	$smarty->display("error.tpl");
	die;
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	if ($tiki_p_edit_sheet != 'y' && $tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y') {
		$smarty->assign('msg', tra("Access Denied").": feature_sheets");

		$smarty->display("error.tpl");
		die;
	}

	// Load data from the form
	$handler = &new TikiSheetFormHandler;
	if( !$grid->import( $handler ) )
		$grid = &new TikiSheet;

	// Save the changes
	$handler = &new TikiSheetDatabaseHandler( $_REQUEST["sheetId"] );
	$grid->export( $handler );

	// Load the layout settings from the database
	$grid = &new TikiSheet;
	$grid->import( $handler );

	$handler = &new TikiSheetOutputHandler;

	ob_start();
	$grid->export( $handler );
	$smarty->assign( 'grid_content', ob_get_contents() );
	ob_end_clean();
}
else
{
	$handler = &new TikiSheetDatabaseHandler( $_REQUEST["sheetId"] );

	$date = time();
	if( !empty( $_REQUEST[ 'readdate' ] ) )
	{
		$date = $_REQUEST[ 'readdate' ];

		if( !is_numeric( $date ) )
			$date = strtotime( $date );

		if( $date == -1 )
			$date = time();
	}

	$smarty->assign( 'read_date', $date );
	$handler->setReadDate( $date );
	
	$grid->import( $handler );

	if( isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'edit' )
	{
		$handler = &new TikiSheetFormHandler;

		ob_start();
		$grid->export( $handler );
		$smarty->assign( 'init_grid', ob_get_contents() );
		ob_end_clean();

		$smarty->assign('page_mode', 'edit' );
		if ($prefs['feature_contribution'] == 'y') {
			$contributionItemId = $_REQUEST['sheetId'];
			include_once('contribution.php');
		}
	}
	else
	{
		$handler = &new TikiSheetOutputHandler;

		ob_start();
		$grid->export( $handler );
		$smarty->assign( 'grid_content', ob_get_contents() );
		ob_end_clean();
	}
}
if ($prefs['feature_warn_on_edit'] == 'y') {
	if ($tikilib->semaphore_is_set($_REQUEST['sheetId'], $prefs['warn_on_edit_time'] * 60, 'sheet') && ($semUser = $tikilib->get_semaphore_user($_REQUEST['sheetId'], 'sheet')) != $user) {
		$editconflict = 'y';
		$smarty->assign('editconflict', 'y');
		$smarty->assign('semUser', $semUser);
	} else {
		$editconflict = 'n';
	}
	if (isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] == 'edit') {
		$_SESSION['edit_lock_sheet'.$_REQUEST['sheetId']] = $tikilib->semaphore_set($_REQUEST['sheetId'], 'sheet');
	} elseif (isset($_SESSION['edit_lock_sheet'.$_REQUEST['sheetId']])) {
		$tikilib->semaphore_unset($_REQUEST['sheetId'], $_SESSION['edit_lock_sheet'.$_REQUEST['sheetId']]);
		unset($_SESSION['edit_lock_sheet'.$_REQUEST['sheetId']]);
	}
}

$cat_type = 'sheet';
$cat_objid = $_REQUEST["sheetId"];
include_once ("categorize_list.php");

$section = 'sheet';
include_once ('tiki-section_options.php');
ask_ticket('sheet');

// Display the template
$smarty->assign('mid', 'tiki-view-sheets.tpl');
$smarty->display("tiki.tpl");

?>
