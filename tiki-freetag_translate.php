<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_feature(array('feature_freetags','freetags_multilingual','feature_multilingual'));
$access->check_permission('tiki_p_freetags_tag');

if( !isset($_REQUEST['type']))
	$_REQUEST['type'] = 'wiki page';
if( !isset($_REQUEST['objId']))
	$_REQUEST['objId'] = '';

$cat_type = $_REQUEST['type'];
$cat_objId = $_REQUEST['objId'];

if ($cat_type != 'wiki page' && $cat_type != 'article') {
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

	if( isset( $_REQUEST['clear'] ) && is_array( $_REQUEST['clear'] ) )
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

$freetags_per_page = $prefs['maxRecords'];
$offset = intval($_REQUEST['offset']);
if ($offset < 0) {
	$offset = 0;
}
$smarty->assign('freetags_offset', $offset);
$smarty->assign('freetags_per_page', $freetags_per_page);

$languages = $multilinguallib->preferredLangs();
$used_languages = array();
foreach ($languages as $l)
	$used_languages[$l] = true;
if( array_key_exists( 'additional_languages', $_REQUEST )
	&& is_array( $_REQUEST['additional_languages'] ) )
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

$tagList = $freetaglib->get_object_tags_multilingual( $cat_type, $cat_objId, $used_languages, $offset, $freetags_per_page );

$rootlangs = array();
foreach( $tagList as $tagGroup ) {
	foreach( $tagGroup as $k => $tag )
	{
		array_merge($used_languages, array($k));
		if( $tag['tagset'] == $tag['tagId'] )
			$rootlangs[$tag['tagset']] = $tag['lang'];
	}
}

$baseArgs = array(
	'type' => $cat_type,
	'objId' => $cat_objId,
	'additional_languages' => $used_languages,
);

$prev = http_build_query( array_merge( $baseArgs, array( 'offset' => $offset - $freetags_per_page ) ), '', '&' );
$next = http_build_query( array_merge( $baseArgs, array( 'offset' => $offset + $freetags_per_page ) ), '', '&' );

$smarty->assign( 'next', 'tiki-freetag_translate.php?' . $next );
if( $offset ) {
	$smarty->assign( 'previous', 'tiki-freetag_translate.php?' . $prev );
} else {
	$smarty->assign( 'previous', '' );
}
	
$smarty->assign( 'tagList', $tagList );
$smarty->assign( 'languageList', $used_languages );
$smarty->assign( 'fullLanguageList', $allLanguages );
$smarty->assign( 'rootlang', $rootlangs );

// Display the template
$smarty->assign('mid', 'tiki-freetag-translate.tpl');
$smarty->display("tiki.tpl");
