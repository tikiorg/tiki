<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-freetag_translate.php,v 1.1.2.6 2008-02-26 18:47:16 lphuberdeau Exp $

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

if ($tiki_p_freetags_tag != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

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

if ( $cat_objId ) {
	$info = $tikilib->get_page_info( $cat_objId );
} elseif (false&&$tiki_p_admin_freetags != 'y') {
	// Global tag edit only available to admins
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

$smarty->assign( 'type', $cat_type );
$smarty->assign( 'objId', $cat_objId );

$smarty->assign( 'data', $info );

if( isset( $_REQUEST['save'] ) )
{
	// Process save
	if( $_REQUEST->isArray('setlang') )
	{
		foreach( $_REQUEST['setlang'] as $tagId => $lang )
			if( !empty( $lang ) )
				$freetaglib->set_tag_language( $tagId, $lang );
	}

	if( $_REQUEST->isArray('newtag')
		&& $_REQUEST->isArray('rootlang') )
	{
		foreach( $_REQUEST['newtag']->asArray() as $tagGroup => $list )
			if( is_array( $list ) && isset( $_REQUEST['rootlang'][$tagGroup] ) )
				foreach( $list as $lang => $tag )
				{
					$root = $_REQUEST['rootlang'][$tagGroup];
					if( !isset( $root[$lang] ) )
						continue;

					$freetaglib->translate_tag( $root[$lang], $tagGroup, $lang, $tag );
				}
	}

	if( $_REQUEST->isArray('clear') )
	{
		foreach( $_REQUEST['clear'] as $tag )
			$freetaglib->clear_tag_language_from_id( $tag );
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
$used_languages = array();
foreach ($languages as $l)
	$used_languages[$l] = true;
if( isset( $_REQUEST['additional_languages'] ) )
	foreach( $_REQUEST['additional_languages'] as $lang )
		$used_languages[$lang] = true;
$used_languages = array_keys( $used_languages );

$allLanguages = $tikilib->list_languages();
// select roughly readable languages
$t_used_languages = array();
foreach ($allLanguages as $al) {
	foreach ($used_languages as $ul) {
		if (substr($al["value"],0,2) == substr($ul,0,2)) {
			$t_used_languages[] = $al["value"];
			break;
		}
	}
}
$used_languages = $t_used_languages;

$tagList = $freetaglib->get_object_tags_multilingual( $cat_type, $cat_objId, $used_languages );

$rootlangs = array();
foreach( $tagList as $tagGroup ) {
	foreach( $tagGroup as $k => $tag )
	{
		array_merge($used_languages, array($k));
		if( $tag['tagset'] == $tag['tagId'] )
			$rootlangs[$tag['tagset']] = $tag['lang'];
	}
}
	
$smarty->assign( 'tagList', $tagList );
$smarty->assign( 'languageList', $used_languages );
$smarty->assign( 'fullLanguageList', $allLanguages );
$smarty->assign( 'rootlang', $rootlangs );

// Display the template
$smarty->assign('mid', 'tiki-freetag-translate.tpl');
$smarty->display("tiki.tpl");

?>
