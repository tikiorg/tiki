<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-freetag_translate.php,v 1.1.2.2 2008-02-18 20:23:48 lphuberdeau Exp $

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
	if( isset( $_REQUEST['setlang'] )
		&& is_array( $_REQUEST['setlang'] ) )
	{
		foreach( $_REQUEST['setlang'] as $tagId => $lang )
			if( !empty( $lang ) )
				$freetaglib->set_tag_language( $tagId, $lang );
	}

	if( isset( $_REQUEST['newtag'] )
		&& isset( $_REQUEST['rootlang'] )
		&& is_array( $_REQUEST['newtag'] )
		&& is_array( $_REQUEST['rootlang'] ) )
	{
		foreach( $_REQUEST['newtag'] as $tagGroup => $list )
			if( is_array( $list ) && array_key_exists( $tagGroup, $_REQUEST['rootlang'] ) )
				foreach( $list as $lang => $tag )
				{
					$root = $_REQUEST['rootlang'][$tagGroup];
					if( !array_key_exists( $lang, $root ) )
						continue;

					$freetaglib->translate_tag( $root[$lang], $tagGroup, $lang, $tag );
				}
	}
}
else
{
	// Form reload
	if( isset( $_REQUEST['newtag'] ) )
		$smarty->assign( 'newtags', $_REQUEST['newtag'] );
	if( isset( $_REQUEST['setlang'] ) )
		$smarty->assign( 'setlang', $_REQUEST['setlang'] );
}

$languages = $multilinguallib->preferedLangs();

$tagList = $freetaglib->get_object_tags_multilingual( $cat_type, $cat_objId, $languages );

$used_languages = array();
$rootlangs = array();
foreach( $tagList as $tagGroup )
	foreach( $tagGroup as $k => $tag )
	{
		$used_languages[$k] = true;
		if( $tag['tagset'] == $tag['tagId'] )
			$rootlangs[$tag['tagset']] = $tag['lang'];
	}
if( array_key_exists( 'additional_languages', $_REQUEST )
	&& is_array( $_REQUEST['additional_languages'] ) )
	foreach( $_REQUEST['additional_languages'] as $lang )
		$used_languages[$lang] = true;
$used_languages = array_keys( $used_languages );

function remove_existing_languages( $item ) {
	global $used_languages;
	return !in_array( $item['value'], $used_languages );
}

$allLanguages = $tikilib->list_languages();
$allLanguages = array_filter( $allLanguages, 'remove_existing_languages' );

$smarty->assign( 'tagList', $tagList );
$smarty->assign( 'languageList', $used_languages );
$smarty->assign( 'fullLanguageList', $allLanguages );
$smarty->assign( 'rootlang', $rootlangs );

// Display the template
$smarty->assign('mid', 'tiki-freetag-translate.tpl');
$smarty->display("tiki.tpl");

?>
