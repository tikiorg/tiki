<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-forums.php,v 1.18.2.2 2008-01-28 16:44:10 pkdille Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'forums';
require_once ('tiki-setup.php');

$auto_query_args = array('sort_mode', 'offset', 'find', 'mode');

$smarty->assign('headtitle',tra('Forums'));

if ($prefs['feature_forums'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_forums");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_forum_read != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied. You cannot view this section"));

	$smarty->display("error.tpl");
	die;
}

// This shows a list of forums everybody can use this listing
include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = $prefs['forums_ordering'];
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $commentslib->list_forums($offset, $maxRecords, $sort_mode, $find);
Perms::bulk( array( 'type' => 'forum' ), 'object', $channels['data'], 'forumId' );

$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$forumperms = Perms::get( array( 'type' => 'forum', 'object' => $channels['data'][$i]['forumId'] ) );
	$channels["data"][$i]["individual_tiki_p_forum_read"] = $forumperms->forum_read ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_forum_post"] = $forumperms->forum_post ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_forum_post_topic"] = $forumperms->forum_post_topic ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_forum_vote"] = $forumperms->forum_vote ? 'y' : 'n';
	$channels["data"][$i]["individual_tiki_p_admin_forum"] = $forumperms->admin_forum ? 'y' : 'n';
}

$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign('cant',$channels["cant"]);
include_once ('tiki-section_options.php');

if ($prefs['feature_mobile'] =='y' && isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'mobile') {
	include_once ("lib/hawhaw/hawtikilib.php");

	HAWTIKI_forums($channels["data"], $tiki_p_forum_read, $offset, $maxRecords, $channels["cant"]);
}

ask_ticket('forums');

// Display the template
$smarty->assign('mid', 'tiki-forums.tpl');
$smarty->display("tiki.tpl");
