<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

global $prefs;

require 'tiki-setup.php';
include_once('lib/multilingual/multilinguallib.php');
include_once('lib/wiki/wikilib.php');
include_once('lib/wiki/renderlib.php');

$access->check_feature(array('feature_multilingual', 'feature_multilingual_one_page'));

if( !isset($_REQUEST['page']) ) {
	header('Location: tiki-index.php');
	die;
}

$pages = array();

$requested = $tikilib->get_page_info( $_REQUEST['page'] );
$page_id = $requested['page_id'];
$pages[] = $requested;
$unordered = array();
$excluded = array();

$page = $_REQUEST['page'];

// If the page doesn't exist then display an error
if(empty($requested)) {
	$likepages = $wikilib->get_like_pages($page);
	// if we have exactly one match, redirect to it 
	if($prefs['feature_wiki_1like_redirection'] == 'y' && count($likepages) == 1  && !$isUserPage) {
		$access->redirect( 'tiki-all_languages.php?page='.urlencode($likepages[0]) );
	}
	$smarty->assign_by_ref('likepages', $likepages);
	$smarty->assign('create', $isUserPage? 'n': 'y');
	$access->display_error( $page, tra('Page cannot be found'), '404' );
}

$preferred_langs = $multilinguallib->preferredLangs();


if (count($preferred_langs) == 1) {
   // If user only has one language, then assume he wants to see all 
   // languages supported by the site (otherwise, why would he have asked
   // for all languages). This has the advantage that users can see multiple
   // languages even if they haven't registered and set their language preferences,
   // or if they haven't logged in. Yet, if they have registered, set language
   // preferences, and logged in, they can limit the displayed languages
   // to only those that they want.  
   $preferred_langs = $prefs['available_languages'];
}

// Sort languages according to user's prefences
foreach( $multilinguallib->getTrads( 'wiki page', $page_id ) as $row )
	if( $row['objId'] != $page_id && in_array($row['lang'], $preferred_langs) )
		$unordered[ $row['lang'] ] = $tikilib->get_page_info_from_id( $row['objId'] );
	elseif( $row['lang'] != $requested['lang'] )
		$excluded[] = $row['lang'];

foreach( $preferred_langs as $lang )
	if( array_key_exists( $lang, $unordered ) )
		$pages[] = $unordered[$lang];

$contents = array();

$show_langs_side_by_side = false;
if (count($pages) >= 2) {
   // If only two languages, its best to show 
   // them side by side for easier comparison
   // (as opposed to one on top of the other).
   // But for more than two languages, side by
   // side is not possible, cause not enough real estate
   $show_langs_side_by_side = true;
}


foreach( array_reverse( $pages ) as $id => $info )
{
	$page = $info['pageName'];
	$section = 'wiki page';

	$renderer = new WikiRenderer( $info, $user );
	$renderer->applyPermissions();

	if( $tiki_p_view == 'y' ) {
		$renderer->runSetups();

		$comments_per_page = $prefs['wiki_comments_per_page'];
		$thread_sort_mode = $prefs['wiki_comments_default_ordering'];
		$comments_vars=Array('page');
		$comments_objectId = 'wiki page:' . $info['pageName'];
		$_REQUEST['page'] = $info['pageName'];
		include('comments.php');

		$contents[] = $smarty->fetch('tiki-show_page.tpl');

		if( $id === count($pages) - 1 )
			$renderer->restoreAll();
	}
}

$contents = array_reverse( $contents );

$smarty->assign( 'side_by_side', $show_langs_side_by_side );
$smarty->assign( 'excluded', $excluded );
$smarty->assign( 'content', $contents);
$smarty->assign( 'mid', 'tiki-all_languages.tpl' );
$smarty->display( 'tiki.tpl' );
