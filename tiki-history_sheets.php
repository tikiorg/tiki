<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'sheet';
require_once ('tiki-setup.php');
require_once ('lib/sheet/grid.php');
$access->check_feature('feature_sheet');

if ($tiki_p_admin != 'y' && $tiki_p_admin_sheet != 'y' && !$tikilib->user_has_perm_on_object($user, $_REQUEST['sheetId'], 'sheet', 'tiki_p_view_sheet')) {
	$smarty->assign('msg', tra("Access Denied").": feature_sheet");

	$smarty->display("error.tpl");
	die;
}

$smarty->assign('sheetId', $_REQUEST["sheetId"]);

// Individual permissions are checked because we may be trying to edit the gallery

// Init smarty variables to blank values
//$smarty->assign('theme','');

$info = $sheetlib->get_sheet_info( $_REQUEST["sheetId"] );

$smarty->assign('title', $info['title']);
$smarty->assign('description', $info['description']);

$smarty->assign('page_mode', 'view' );

$result = $tikilib->query( "SELECT DISTINCT `begin`, `user` FROM `tiki_sheet_values` WHERE `sheetId` = ? ORDER BY begin DESC", array( $_REQUEST['sheetId'] ) );
$data = array();
while( $row = $result->fetchRow() )
	$data[] = array( "stamp" =>$row['begin'], "string" => $tikilib->date_format( "%Y-%m-%d %H:%M:%S", $row['begin'] ), "user" => $row['user'] );

$smarty->assign_by_ref( 'history', $data );

include_once ('tiki-section_options.php');
ask_ticket('sheet');

// Display the template
$smarty->assign('mid', 'tiki-history-sheets.tpl');
$smarty->display("tiki.tpl");
