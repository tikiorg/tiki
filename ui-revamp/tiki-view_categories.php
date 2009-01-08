<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-objectpermissions.php,v 1.25.2.2 2008-03-11 15:17:54 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once ("tiki-setup.php");

if (!isset(
	$_REQUEST['objectName']) || empty($_REQUEST['objectType']) || empty($_REQUEST['objectId'])) {
	$smarty->assign('msg', tra("Not enough information to display this page"));

	$smarty->display("error.tpl");
	die;
}

$perms = array(
	'tiki_p_view_categories',
	'tiki_p_edit_categories',
	'tiki_p_admin_categories',
);

foreach( $perms as $p ) {
	$$p = $tikilib->user_has_perm_on_object($user, $_REQUEST['objectId'], $_REQUEST['objectType'], $p ) ? 'y' : 'n';
	$smarty->assign( $p, $$p );
}

if( $prefs['feature_categories'] != 'y' 
 || ! $tikilib->user_has_perm_on_object($user, $_REQUEST['objectId'], $_REQUEST['objectType'], 'tiki_p_view_categories' ) ) {
	$smarty->assign('msg', tra("Impossible to view categories."));
	$smarty->display("error.tpl");
	die;
}

require_once('TikiPageControls.php');

$smarty->assign('notable', 'y');
$smarty->assign('showcateg', 'y');

$cat_type = $_REQUEST['objectType'];
$cat_objid = $_REQUEST['objectId'];
$cat_name = $_REQUEST['objectName'];
$cat_desc = '';
$cat_href = TikiPageControls_Link::build( $cat_type, $cat_objid );

//$smarty->assign('tiki_p_edit_categories','n');

if( $_SERVER['REQUEST_METHOD'] == 'POST' && $tiki_p_edit_categories == 'y' ) {
	include_once('categorize.php');
}

include_once('categorize_list.php');

if( $controls = TikiPageControls::factory( $_REQUEST['objectType'], $_REQUEST['objectId'], $_REQUEST['objectName'] ) ) {
	$controls->setMode('category');
	$controls->build();
	$smarty->assign('object_page_controls', $controls);
}

$smarty->assign( 'objectId', $cat_objid );
$smarty->assign( 'objectName', $cat_name );
$smarty->assign( 'objectType', $cat_type );

$smarty->assign('mid','tiki-view_categories.tpl');
$smarty->display("tiki.tpl");

?>
