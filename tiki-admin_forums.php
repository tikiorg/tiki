<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_forums.php,v 1.48.2.6 2007-12-18 14:08:59 nkoth Exp $ 

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'forums';
require_once ('tiki-setup.php');

$smarty->assign('headtitle',tra('Admin Forums'));

if (!isset($_REQUEST["forumId"])) {
	$_REQUEST["forumId"] = 0;
}

if ($prefs['feature_forums'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_forums');
	$smarty->display('error.tpl');
	die;
}


$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["forumId"], 'forum')) {
	$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'forums');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $_REQUEST["forumId"], 'forum', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}

if ($tiki_p_admin_forum != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('You do not have permission to use this feature'));
	$smarty->display('error.tpl');
	die;
}

include_once ("lib/commentslib.php");
$commentslib = new Comments($dbTiki);

if (isset($_REQUEST["remove"])) {
  $area = 'delforum';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$commentslib->remove_forum($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}
if (isset($_REQUEST['batchaction']) && $_REQUEST['batchaction'] = 'delsel_x' && isset($_REQUEST['checked'])) {
	check_ticket('admin-forums');
	foreach($_REQUEST['checked'] as $id) {
		$commentslib->remove_forum($id);
	}
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-forums');

	$_REQUEST['useMail'] = isset($_REQUEST['useMail']) ? 'y' : 'n';
	$useMail = $_REQUEST['useMail'];
	$_REQUEST['usePruneUnreplied'] = isset($_REQUEST['usePruneUnreplied']) ? 'y' : 'n';
	$usePruneUnreplied = $_REQUEST['usePruneUnreplied'];
	$_REQUEST['controlFlood'] = isset($_REQUEST['controlFlood']) ? 'y' : 'n';
	$controlFlood = $_REQUEST['controlFlood'];
	$_REQUEST['usePruneOld'] = isset($_REQUEST['usePruneOld']) ? 'y' : 'n';
	$usePruneOld = $_REQUEST['usePruneOld'];
	$_REQUEST['vote_threads'] = isset($_REQUEST['vote_threads']) ? 'y' : 'n';
	$_REQUEST['outbound_mails_for_inbound_mails'] = isset($_REQUEST['outbound_mails_for_inbound_mails']) ? 'y' : 'n';
	$_REQUEST['outbound_mails_reply_link'] = isset($_REQUEST['outbound_mails_reply_link']) ? 'y' : 'n';
	$_REQUEST['topics_list_reads'] = isset($_REQUEST['topics_list_reads']) ? 'y' : 'n';
	$_REQUEST['topics_list_replies'] = isset($_REQUEST['topics_list_replies']) ? 'y' : 'n';
	$_REQUEST['show_description'] = isset($_REQUEST['show_description']) ? 'y' : 'n';
	$_REQUEST['is_flat'] = isset($_REQUEST['is_flat']) ? 'y' : 'n';

	$_REQUEST['topic_summary'] = isset($_REQUEST['topic_summary']) ? 'y' : 'n';
	$_REQUEST['topic_smileys'] = isset($_REQUEST['topic_smileys']) ? 'y' : 'n';
	$_REQUEST['ui_avatar'] = isset($_REQUEST['ui_avatar']) ? 'y' : 'n';
	$_REQUEST['ui_flag'] = isset($_REQUEST['ui_flag']) ? 'y' : 'n';
	$_REQUEST['ui_email'] = isset($_REQUEST['ui_email']) ? 'y' : 'n';
	$_REQUEST['ui_posts'] = isset($_REQUEST['ui_posts']) ? 'y' : 'n';
	$_REQUEST['ui_level'] = isset($_REQUEST['ui_level']) ? 'y' : 'n';
	$_REQUEST['ui_online'] = isset($_REQUEST['ui_online']) ? 'y' : 'n';

	$_REQUEST['topics_list_pts'] = isset($_REQUEST['topics_list_pts']) ? 'y' : 'n';
	$_REQUEST['topics_list_lastpost'] = isset($_REQUEST['topics_list_lastpost']) ? 'y' : 'n';
	$_REQUEST['topics_list_author'] = isset($_REQUEST['topics_list_author']) ? 'y' : 'n';
	if (empty($_REQUEST['threadOrdering'])) $_REQUEST['threadOrdering'] = '';
	if (empty($_REQUEST['threadStyle'])) $_REQUEST['threadStyle'] = '';
	if (empty($_REQUEST['commentsPerPage'])) $_REQUEST['commentsPerPage'] = '';

	if ($_REQUEST["section"] == '__new__')
		$_REQUEST["section"] = $_REQUEST["new_section"];
        // Check for last character being a / or a \
        if (substr($_REQUEST["att_store_dir"], -1) != "\\" && substr($_REQUEST["att_store_dir"], -1) != "/" && $_REQUEST["att_store_dir"] != "") {
                $_REQUEST["att_store_dir"] .= "/";
        }


	$fid = $commentslib->replace_forum($_REQUEST["forumId"], $_REQUEST["name"], $_REQUEST["description"],
		$controlFlood, $_REQUEST["floodInterval"], $_REQUEST["moderator"], $_REQUEST["mail"], $useMail,
		$usePruneUnreplied, $_REQUEST["pruneUnrepliedAge"],
		$usePruneOld, $_REQUEST["pruneMaxAge"], $_REQUEST["topicsPerPage"], $_REQUEST["topicOrdering"], $_REQUEST["threadOrdering"],
		$_REQUEST["section"], $_REQUEST['topics_list_reads'], $_REQUEST['topics_list_replies'], $_REQUEST['topics_list_pts'],
		$_REQUEST['topics_list_lastpost'], $_REQUEST['topics_list_author'],
		$_REQUEST['vote_threads'],
		$_REQUEST['show_description'], $_REQUEST['inbound_pop_server'],
		110, $_REQUEST['inbound_pop_user'], $_REQUEST['inbound_pop_password'],
		trim($_REQUEST['outbound_address']),
		$_REQUEST['outbound_mails_for_inbound_mails'],
		$_REQUEST['outbound_mails_reply_link'],
		$_REQUEST['outbound_from'],
		$_REQUEST['topic_smileys'], $_REQUEST['topic_summary'], $_REQUEST['ui_avatar'], $_REQUEST['ui_flag'], $_REQUEST['ui_posts'],
		$_REQUEST['ui_level'], $_REQUEST['ui_email'], $_REQUEST['ui_online'], $_REQUEST['approval_type'],
		$_REQUEST['moderator_group'], $_REQUEST['forum_password'], $_REQUEST['forum_use_password'], $_REQUEST['att'],
		$_REQUEST['att_store'], $_REQUEST['att_store_dir'], $_REQUEST['att_max_size'],$_REQUEST['forum_last_n'],
		$_REQUEST['commentsPerPage'], $_REQUEST['threadStyle'], $_REQUEST['is_flat']);

	$cat_type = 'forum';
	$cat_objid = $fid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["name"];
	$cat_href = "tiki-view_forum.php?forumId=" . $cat_objid;
	include_once ("categorize.php");

	$_REQUEST["forumId"] = $fid;
}

if (!empty($_REQUEST['duplicate']) && !empty($_REQUEST['name']) && !empty($_REQUEST['forumId'])) {
	$newForumId = $commentslib->duplicate_forum($_REQUEST['forumId'], $_REQUEST['name'], isset($_REQUEST['description'])?$_REQUEST['description']: '' );
	if (isset($_REQUEST['dupCateg']) && $_REQUEST['dupCateg'] == 'on' && $prefs['feature_categories'] == 'y') {
		global $categlib; include_once('lib/categories/categlib.php');
		$cats = $categlib->get_object_categories('forum', $_REQUEST['forumId']);
		$catObjectId = $categlib->add_categorized_object('forum', $newForumId, isset($_REQUEST['description'])?$_REQUEST['description']: '', $_REQUEST['name'], "tiki-view_forum.php?forumId=$newForumId");
		foreach($cats as $cat) {
			$categlib->categorize($catObjectId, $cat);
		}
	}
	if (isset($_REQUEST['dupPerms']) && $_REQUEST['dupPerms'] == 'on') {
		global $userlib; include_once('lib/userslib.php');
		$userlib->copy_object_permissions($_REQUEST['forumId'], $newForumId, 'forum');
	}
	$_REQUEST['forumId'] = $newForumId;
}

if ($_REQUEST["forumId"]) {
	$info = $commentslib->get_forum($_REQUEST["forumId"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["description"] = '';
	$info["controlFlood"] = 'n';
	$info["floodInterval"] = 120;
	$info["moderator"] = 'admin';
	$info["section"] = '';
	$info["mail"] = '';
	$info["topicsPerPage"] = 10;
	$info["useMail"] = 'n';
	$info["topicOrdering"] = 'lastPost_desc';
	$info["threadOrdering"] = '';
	$info["threadStyle"] = '';
	$info["commentsPerPage"] = '';
	$info["usePruneUnreplied"] = 'n';
	$info["pruneUnrepliedAge"] = 60 * 60 * 24 * 30;
	$info["usePruneOld"] = 'n';
	$info["pruneMaxAge"] = 60 * 60 * 24 * 30;
	$info["topics_list_replies"] = 'y';
	$info["show_description"] = 'n';

	$info["outbound_address"] = '';
	$info["outbound_mails_for_inbound_mails"] = 'n';
	$info["outbound_mails_reply_link"] = 'n';
	$info["outbound_from"] = '';
	$info["inbound_pop_server"] = '';
	$info["inbound_pop_port"] = 110;
	$info["inbound_pop_user"] = '';
	$info["inbound_pop_password"] = '';
	$info["topic_summary"] = 'n';
	$info["topic_smileys"] = 'n';
	$info["ui_avatar"] = 'y';
	$info["ui_flag"] = 'y';
	$info["ui_posts"] = 'n';
	$info['ui_level'] = 'n';
	$info["ui_email"] = 'n';
	$info["ui_online"] = 'n';
	$info["approval_type"] = 'all_posted';
	$info["moderator_group"] = '';
	$info['forum_password'] = '';
	$info['forum_use_password'] = 'n';
	$info['att'] = 'att_no';
	$info['att_store'] = 'db';
	$info['att_store_dir'] = '';
	$info['att_max_size'] = 1000000;

	$info["topics_list_reads"] = 'y';
	$info["topics_list_pts"] = 'n';
	$info["topics_list_lastpost"] = 'y';
	$info["topics_list_author"] = 'y';
	$info["vote_threads"] = 'n';
	$info["forum_last_n"] = 0;
	$info["is_flat"] = 'n';
}

$smarty->assign('forumId', $_REQUEST["forumId"]);

foreach ($info as $key => $value) {
	if ($key == "section") /* conflict with section management */
		$smarty->assign("forumSection", $value);
	else
		$smarty->assign($key, $value);
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_asc';
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

$max = count($channels["data"]);
for ($i = 0; $i < $max; $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["forumId"], 'forum')) {
		$channels["data"][$i]["individual"] = 'y';

		if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $channels["data"][$i]["forumId"], 'forum', 'tiki_p_admin_forum')) {
			$channels["data"][$i]["individual_tiki_p_admin_forum"] = 'y';
		}
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}

$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign_by_ref('cant', $channels["cant"]);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
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

$cat_type = 'forum';
$cat_objid = $_REQUEST["forumId"];
$categories = array();
include_once ("categorize_list.php");

if (!empty($_REQUEST['dup_mode'])) {
	if ($offset == 0 && ($maxRecords == -1 || $channels['cant'] <= $maxRecords)) {
		$smarty->assign_by_ref('allForums', $channels['data']);
	} else {
		$allForums = $commentslib->list_forums(0, -1,'name_asc');
		$smarty->assign_by_ref('allForums', $allForums['data']);
	}
	$smarty->assign_by_ref('dup_mode', $_REQUEST['dup_mode']);
}
$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);

$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);

$maxAttachSize = ini_get('upload_max_filesize');
if (preg_match('/^([\d\.]+)([gmk])?$/i', $maxAttachSize, $matches) && !empty($matches[2])) {
		$maxAttachSize = $matches[1];
		switch (strtolower($matches[0][strlen($matches[0]) - 1])) {
		case 'g': $maxAttachSize *= 1024;
		case 'm': $maxAttachSize *= 1024;
		case 'k': $maxAttachSize *= 1024;
	}
}
$smarty->assign_by_ref('maxAttachSize', $maxAttachSize);

$sections = $tikilib->get_forum_sections();
$smarty->assign_by_ref('sections', $sections);

include_once ('tiki-section_options.php');

ask_ticket('admin-forums');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_forums.tpl');
$smarty->display("tiki.tpl");

?>
