<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-newsreader_news.php,v 1.24 2007-10-12 07:55:29 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
$section = 'newsreader';
require_once ('tiki-setup.php');

include_once ('lib/newsreader/newslib.php');

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_newsreader'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsreader");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_newsreader != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ((!isset($_REQUEST['server'])) || (!isset($_REQUEST['port'])) || (!isset($_REQUEST['group']))) {
	$smarty->assign('msg', tra("Missing information to read news (server,port,username,password,group) required"));

	$smarty->display("error.tpl");
	die;
}
if (!isset($_REQUEST['news_username']))
	$_REQUEST['news_username'] = '';
if (!isset($_REQUEST['password']))
	$_REQUEST['password'] = '';

$smarty->assign('server', $_REQUEST['server']);
$smarty->assign('port', $_REQUEST['port']);
$smarty->assign('news_username', $_REQUEST['news_username']);
$smarty->assign('password', $_REQUEST['password']);
$smarty->assign('group', $_REQUEST['group']);

if (isset($_REQUEST['serverId'])) {
	$smarty->assign('serverId', $_REQUEST['serverId']);
} else {
	$smarty->assign('serverId', 0);
}

if (!$newslib->news_set_server($_REQUEST['server'], $_REQUEST['port'], $_REQUEST['news_username'], $_REQUEST['password'])) {
	$smarty->assign('msg', tra("Cannot connect to"). ':' . $info['server']);

	$smarty->display("error.tpl");
	die;
}

$info = $newslib->news_select_group($_REQUEST['group']);

if (!$info) {
	$smarty->assign('msg', tra("Cannot get messages"));

	$smarty->display("error.tpl");
	die;
}

//Now calculate all the offsets using maxRecords and offset
//then load headers for messages between the first and last message to be displayed
//Assign the headers information to the articles array to be displayed in the template
//calculate next and prev offsets, page number and so...
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);
$cant = $info['last'] - $info['first'] + 1;
$cant_pages = ceil($cant / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($cant > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

// Since the first message is the last one...
$count = 0;
$articles = array();

if (isset($_REQUEST['mark'])) {
	$newslib->news_mark($user, $_REQUEST['serverId'], $_REQUEST['group']);
}

$mark = $newslib->news_get_mark($user, $_REQUEST['serverId'], $_REQUEST['group']);

for ($i = $info['last'] - $offset; $count < $maxRecords && $i >= $info['first']; $i--) {
	$count++;

	$art = $newslib->news_split_headers($i);
	$art['loopid'] = $i;

	if (strtotime($art['Date']) > $mark) {
		$art['status'] = 'new';
	} else {
		$art['status'] = 'old';
	}

	//$art['timestamp']=$tikilib->get_iso8601_datetime($art["Date"]);
	$articles[] = $art;
}

$smarty->assign('articles', $articles);

include_once ('tiki-mytiki_shared.php');

include_once ('tiki-section_options.php');
ask_ticket('newsreader');

$smarty->assign('mid', 'tiki-newsreader_news.tpl');
$smarty->display("tiki.tpl");

?>
