<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-user_watches.php,v 1.21.2.1 2008-03-13 21:00:48 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

$section = 'mytiki';
include_once ('tiki-setup.php');
if ($prefs['feature_ajax'] == "y") {
require_once ('lib/ajax/ajaxlib.php');
}
if (!$user) {
	$smarty->assign('msg', tra("You must log in to use this feature"));
	$smarty->assign('errortype', '402');
	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_user_watches'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_user_watches");

	$smarty->display("error.tpl");
	die;
}

if( $prefs['feature_user_watches_translations'] ) {
	$languages = $tikilib->list_languages();
	$smarty->assign_by_ref('languages', $languages);
}

$add_options = array();
if( $prefs['feature_articles'] == 'y' )
	$add_options['articles_submitted'] = tra('A user submits an article');
if( $prefs['feature_user_watches_translations'] == 'y' )
	$add_options['wiki_page_in_lang_created'] = tra('A new page is created in a language');

$smarty->assign( 'add_options', $add_options );

if( isset($_POST['langwatch']) )
{
	foreach( $languages as $lang )
		if( $_POST['langwatch'] == $lang['value'] ) {
			$langwatch = $lang;
			break;
		}
}
else
	$langwatch = null;

if (isset($_REQUEST['id'])) {
  $area = 'deluserwatch';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$tikilib->remove_user_watch_by_id($_REQUEST['id']);
  } else {
    key_get($area);
  }
}

if (isset($_REQUEST["add"])) {
	if (isset($_REQUEST['event'])) {
		switch( $_REQUEST['event'] )
		{
		case 'articles_submitted':
			$watch_object = "*";
			$watch_type = 'article';
			$watch_label = '*';
			$watch_url = "tiki-view_articles.php";
			break;
		case 'wiki_page_in_lang_created':
			$watch_object = $langwatch['value'];
			$watch_type = 'wiki page';
			$watch_label = tra('Language watch') . ": {$lang['name']}";
			$watch_url = "tiki-user_watches.php";
			break;
		}

		if( isset( $watch_object ) )
		{
			$tikilib->add_user_watch($user, $_REQUEST['event'], $watch_object, $watch_type, $watch_label, $watch_url);
			$_REQUEST['event'] = '';
		}
	} else {
		foreach ($_REQUEST['cat_categories'] as $cat) {
			if ($cat > 0)
				$tikilib->add_user_watch($user, 'new_in_category', $cat, 'category', "tiki-browse_category.php?parentId=$cat");
			else {
				$tikilib->remove_user_watch($user, 'new_in_category', '*');
				$tikilib->add_user_watch($user, 'new_in_category', '*', 'category', "tiki-browse_category.php");
			}
		}
	}
}

if (isset($_REQUEST["delete"]) && isset($_REQUEST['watch'])) {
  check_ticket('user-watches');
/* CSRL doesn't work if param as passed not in the uri */
/*  $area = 'delwatches';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area); */
	foreach (array_keys($_REQUEST["watch"])as $item) {
		$tikilib->remove_user_watch_by_id($item);
	}
/*  } else {
	key_get($area);
  } */
// 
}

// Get watch events and put them in watch_events
$events = $tikilib->get_watches_events();
$smarty->assign('events', $events);

// if not set event type then all
if (!isset($_REQUEST['event']))
	$_REQUEST['event'] = '';

// get all the information for the event
$watches = $tikilib->get_user_watches($user, $_REQUEST['event']);
$smarty->assign('watches', $watches);
// this was never needed here, was it ? -- luci
//include_once ('tiki-mytiki_shared.php');
if ($prefs['feature_categories']) {
	include_once('lib/categories/categlib.php');
	$watches = $tikilib->get_user_watches($user, 'new_in_category');
	$categories = $categlib->list_categs();
	$nb = count($categories);
	foreach ($watches as $watch) {
		if ($watch['object'] == '*') {
			$smarty->assign('all', 'y');
			break;
		}
		for ($i = 0; $i < $nb; ++$i) {
			if ($watch['object'] == $categories[$i]['categId']) {
				$categories[$i]['incat'] = 'y';
				break;
			}
		}
	}
	$smarty->assign('categories', $categories);
}

if ($prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y') {
  $unread = $tikilib->user_unread_messages($user);
  $smarty->assign('unread', $unread);
}

ask_ticket('user-watches');
if ($prefs['feature_ajax'] == "y") {
function user_watches_ajax() {
    global $ajaxlib, $xajax;
    $ajaxlib->registerTemplate("tiki-user_watches.tpl");
    $ajaxlib->registerTemplate("tiki-my_tiki.tpl");
    $ajaxlib->registerFunction("loadComponent");
    $ajaxlib->processRequests();
}
user_watches_ajax();
$smarty->assign("mootab",'y');
}

$smarty->assign('mid', 'tiki-user_watches.tpl');
$smarty->display("tiki.tpl");

?>
