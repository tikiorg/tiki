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
