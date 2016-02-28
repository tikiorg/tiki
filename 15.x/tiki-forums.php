<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'forums';
require_once ('tiki-setup.php');
//get_strings tra('List Forums')
$auto_query_args = array('sort_mode', 'offset', 'find', 'mode');

$access->check_feature('feature_forums');
$access->check_permission('tiki_p_forum_read');

// This shows a list of forums everybody can use this listing
$commentslib = TikiLib::lib('comments');

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

if (isset($_REQUEST['numrows'])) {
	$maxRecords = $_REQUEST['numrows'];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $commentslib->list_forums($offset, $maxRecords, $sort_mode, $find);
Perms::bulk(array( 'type' => 'forum' ), 'object', $channels['data'], 'forumId');

//add tablesorter sorting and filtering
$tsOn = Table_Check::isEnabled(true);
$smarty->assign('tsOn', $tsOn);
$tsAjax = Table_Check::isAjaxCall();
$smarty->assign('tsAjax', $tsAjax);
static $iid = 0;
++$iid;
$ts_tableid = 'forums' . $iid;
$smarty->assign('ts_tableid', $ts_tableid);
//initialize tablesorter
if ($tsOn && !$tsAjax) {
	//set tablesorter code
	Table_Factory::build(
		'TikiForums',
		array(
			'id' => $ts_tableid,
			'total' => $channels["cant"],
		)
	);
	unset($channels);
} else {
	$temp_max = count($channels["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		$forumperms = Perms::get(array( 'type' => 'forum', 'object' => $channels['data'][$i]['forumId'] ));
		$channels["data"][$i]["individual_tiki_p_forum_read"] = $forumperms->forum_read ? 'y' : 'n';
		$channels["data"][$i]["individual_tiki_p_forum_post"] = $forumperms->forum_post ? 'y' : 'n';
		$channels["data"][$i]["individual_tiki_p_forum_post_topic"] = $forumperms->forum_post_topic ? 'y' : 'n';
		$channels["data"][$i]["individual_tiki_p_forum_vote"] = $forumperms->forum_vote ? 'y' : 'n';
		$channels["data"][$i]["individual_tiki_p_admin_forum"] = $forumperms->admin_forum ? 'y' : 'n';
	}

	$smarty->assign_by_ref('channels', $channels["data"]);
	$smarty->assign('cant', $channels["cant"]);
	include_once ('tiki-section_options.php');
}
ask_ticket('forums');
// Display the template
if ($tsAjax) {
	$smarty->display('tiki-forums.tpl');
} else {
	$smarty->assign('mid', 'tiki-forums.tpl');
	$smarty->display("tiki.tpl");
}
