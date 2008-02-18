<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-freetag_translate.php,v 1.1.2.1 2008-02-18 19:03:06 lphuberdeau Exp $

// Based on tiki-galleries.php
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($prefs['feature_freetags'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_freetags");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['freetags_multilingual'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": freetags_multilingual");

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_multilingual'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_multilingual");

	$smarty->display("error.tpl");
	die;
}

if( !isset($_REQUEST['type']))
	$_REQUEST['type'] = 'wiki page';
if( !isset($_REQUEST['objId']))
	$_REQUEST['objId'] = '';

$cat_type = $_REQUEST['type'];
$cat_objId = $_REQUEST['objId'];

if ($cat_type != 'wiki page') {
	$smarty->assign('msg', tra("Not supported yet."));

	$smarty->display("error.tpl");
	die;
}

include_once "lib/freetag/freetaglib.php";
include_once "lib/multilingual/multilinguallib.php";

$info = $tikilib->get_page_info( $cat_objId );

if( empty( $info ) ) {
	$smarty->assign('msg', tra("Object not found").": " . $cat_objId);

	$smarty->display("error.tpl");
	die;
}

$smarty->assign( 'type', $cat_type );
$smarty->assign( 'objId', $cat_objId );

$smarty->assign( 'data', $info );

if( isset( $_REQUEST['save'] ) )
{
	// Process save
}
elseif( isset( $_REQUEST['newtag'] ) )
{
	// Form reload
	$smarty->assign( 'newtags', $_REQUEST['newtag'] );
}

function remove_existing_languages( $item ) {
	global $languages;
	return !in_array( $item['value'], $languages );
}

$languages = $multilinguallib->preferedLangs();
$allLanguages = $tikilib->list_languages();
$allLanguages = array_filter( $allLanguages, 'remove_existing_languages' );

$tagList = $freetaglib->get_object_tags_multilingual( $cat_type, $cat_objId, $languages );

$used_languages = array();
foreach( $tagList as $tagGroup )
	foreach( $tagGroup as $k => $tag )
		$used_languages[$k] = true;
if( array_key_exists( 'additional_languages', $_REQUEST )
	&& is_array( $_REQUEST['additional_languages'] ) )
	foreach( $_REQUEST['additional_languages'] as $lang )
		$used_languages[$lang] = true;
$used_languages = array_keys( $used_languages );

$smarty->assign( 'tagList', $tagList );
$smarty->assign( 'languageList', $used_languages );
$smarty->assign( 'fullLanguageList', $allLanguages );

// Display the template
$smarty->assign('mid', 'tiki-freetag-translate.tpl');
$smarty->display("tiki.tpl");

?>
