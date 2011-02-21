<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
include_once ('tiki-setup.php');
include_once('lib/reportslib.php');

$access->check_user($user);
$access->check_feature('feature_user_watches');

if ($prefs['feature_user_watches_translations']) {
	$languages = $tikilib->list_languages();
	$smarty->assign_by_ref('languages', $languages);
}

require_once('lib/notifications/notificationlib.php');

$notification_types = $notificationlib->get_global_watch_types(true);
if ($prefs['feature_user_watches_translations'] == 'y') {
	$notification_types['wiki_page_in_lang_created'] = array(
		'label' => tra('A new page is created in a language') ,
		'type' => 'wiki page',
		'url' => '' 
	);
}
if ( $prefs['feature_user_watches_languages'] == 'y') {
	$notification_types['category_changed_in_lang'] = array(
		'label' => tra('Category change in a language') ,
		'type' => '',
		'url' => '' 
	);
}

$smarty->assign('add_options', $notification_types);
if (isset($_POST['langwatch'])) {
	foreach($languages as $lang) if ($_POST['langwatch'] == $lang['value']) {
		$langwatch = $lang;
		break;
	}
} else {
	$langwatch = null;
}

if ($prefs['feature_categories']) {
	include_once ('lib/categories/categlib.php');
	$categories = $categlib->list_categs();
} else {
	$categories = array();
}

if ( isset($_REQUEST['categwatch']) ) {
	$selected_categ = null;
	foreach( $categories as $categ ) {
		if ( $_REQUEST['categwatch'] == $categ['categId'] ) {
			$selected_categ = $categ;
			break;
		}
	}
}

if (isset($_REQUEST['id'])) {
	if ($tiki_p_admin_notifications != 'y' && $user != $tikilib->get_user_notification($_REQUEST['id'])) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("Permission denied"));
		$smarty->display("error.tpl");
		die;
	}
	$access->check_authenticity(tra('Remove the notification email'));
	$error = $tikilib->remove_user_watch_by_id($_REQUEST['id']);
	$smarty->assign('remove_user_watch_error', !$error);
}

if (isset($_REQUEST["add"])) {
	if (isset($_REQUEST['event'])) {
		switch ($_REQUEST['event']) {
			case 'wiki_page_in_lang_created':
				$watch_object = $langwatch['value'];
				$watch_type = 'wiki page';
				$watch_label = tra('Language watch') . ": {$lang['name']}";
				$watch_url = "tiki-user_watches.php";
				break;

			case 'category_changed_in_lang':
				if( $selected_categ && $langwatch ) {
					$watch_object = $selected_categ['categId'];
					$watch_type = $langwatch['value'];
					$watch_label = tr('Category watch: %0, Language: %1', $selected_categ['name'], $langwatch['name'] );
					$watch_url = "tiki-browse_categories.php?lang={$lang['value']}&parentId={$selected_categ['categId']}";
				}
				break;

			default:
				if (!isset($notification_types[$_REQUEST['event']])) {
					$smarty->assign('msg', "Unknown watch type");
					$smarty->display("error.tpl");
					die;					
				}
				$watch_object = '*';
				$watch_type = $notification_types[$_REQUEST['event']]['type'];
				$watch_label = $notification_types[$_REQUEST['event']]['label'];
				$watch_url = $notification_types[$_REQUEST['event']]['url'];
		}
		if (isset($watch_object)) {
			$tikilib->add_user_watch($user, $_REQUEST['event'], $watch_object, $watch_type, $watch_label, $watch_url);
			$_REQUEST['event'] = '';
		}
	} else {
		foreach($_REQUEST['cat_categories'] as $cat) {
			if ($cat > 0) $tikilib->add_user_watch($user, 'new_in_category', $cat, 'category', "tiki-browse_category.php?parentId=$cat");
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
	foreach(array_keys($_REQUEST["watch"]) as $item) {
		$tikilib->remove_user_watch_by_id($item);
	}
}
$notification_types = $notificationlib->get_global_watch_types();
if ($prefs['feature_user_watches_translations'] == 'y') {
	$notification_types['wiki_page_in_lang_created'] = array(
		'label' => tra('A new page is created in a language') ,
		'type' => 'wiki page',
		'url' => '' 
	);
}
if ( $prefs['feature_user_watches_languages'] == 'y') {
	$notification_types['category_changed_in_lang'] = array(
		'label' => tra('Category change in a language') ,
		'type' => '',
		'url' => '' 
	);
}
$rawEvents = $tikilib->get_watches_events();
$event = array();
foreach($rawEvents as $event) {
	if (array_key_exists($event, $notification_types)) {
		$events[$event] = $notification_types[$event]['label'];
	} else {
		$events[$event] = $event; // Fallback to raw event name if no description found
	}
}
$smarty->assign('events', $events);
// if not set event type then all
if (!isset($_REQUEST['event'])) $_REQUEST['event'] = '';
// get all the information for the event
$watches = $tikilib->get_user_watches($user, $_REQUEST['event']);
foreach($watches as $key => $watch) {
	if (array_key_exists($watch['event'], $notification_types)) {
		$watches[$key]['label'] = $notification_types[$watch['event']]['label'];
	}
}
$smarty->assign('watches', $watches);
// this was never needed here, was it ? -- luci
//include_once ('tiki-mytiki_shared.php');
if ($prefs['feature_categories']) {
	$watches = $tikilib->get_user_watches($user, 'new_in_category');
	$nb = count($categories);
	foreach($watches as $watch) {
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
$eok = $userlib->get_user_email($user);
$smarty->assign('email_ok', empty($eok) ? 'n' : 'y');
ask_ticket('user-watches');
$smarty->assign_by_ref('report_preferences', $reportslib->get_report_preferences_by_user($user));
$smarty->assign('mid', 'tiki-user_watches.tpl');
$smarty->display("tiki.tpl");
