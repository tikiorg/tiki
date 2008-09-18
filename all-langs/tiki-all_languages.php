<?php

require 'tiki-setup.php';
ini_set('display_errors', 'on');
error_reporting(E_ALL);
include_once('lib/multilingual/multilinguallib.php');
include_once('lib/wiki/wikilib.php');
include_once('lib/wiki/renderlib.php');

if ($prefs['feature_multilingual'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_multilingual");
	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_multilingual_one_page'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_multilingual_one_page");
	$smarty->display("error.tpl");
	die;
}

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

$prefered = $multilinguallib->preferedLangs();

// Sort languages according to user's prefences
foreach( $multilinguallib->getTrads( 'wiki page', $page_id ) as $row )
	if( $row['objId'] != $page_id && in_array($row['lang'], $prefered) )
		$unordered[ $row['lang'] ] = $tikilib->get_page_info_from_id( $row['objId'] );
	elseif( $row['lang'] != $requested['lang'] )
		$excluded[] = $row['lang'];

foreach( $prefered as $lang )
	if( array_key_exists( $lang, $unordered ) )
		$pages[] = $unordered[$lang];

$contents = array();

foreach( array_reverse( $pages ) as $id => $info )
{
	$page = $info['pageName'];
	$section = 'wiki page';

	$renderer = new WikiRenderer( $info, $user );
	$renderer->applyPermissions();
	$renderer->runSetups();

	$smarty->assign( 'hide_page_header', $id !== count($pages) - 1 );

    $comments_per_page = $prefs['wiki_comments_per_page'];
    $thread_sort_mode = $prefs['wiki_comments_default_ordering'];
    $comments_vars=Array('page');
	$comments_objectId = 'wiki page:' . $info['pageName'];
    include('comments.php');

	$contents[] = $smarty->fetch('tiki-show_page.tpl');

	if( $id === count($pages) - 1 )
		$renderer->restoreAll();
}

$smarty->assign( 'excluded', $excluded );
$smarty->assign( 'content', array_reverse( $contents ) );
$smarty->assign( 'mid', 'tiki-all_languages.tpl' );
$smarty->display( 'tiki.tpl' );

?>
