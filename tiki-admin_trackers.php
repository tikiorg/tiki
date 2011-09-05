<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
include_once ('lib/groupalert/groupalertlib.php');
$access->check_feature('feature_trackers');
$auto_query_args = array('trackerId');

if (!isset($_REQUEST["trackerId"])) {
	$_REQUEST["trackerId"] = 0;
}
$objectperms = Perms::get( 'tracker', $_REQUEST['trackerId']);
if (!$objectperms->admin_trackers) {
	$access->display_error('', tra('Permission denied').": ". 'tiki_p_admin_trackers', '403');
}
$smarty->assign('permsType', $objectperms->from());

if (!empty($_REQUEST['duplicate']) && !empty($_REQUEST['name']) && !empty($_REQUEST['trackerId'])) {
	$newTrackerId = $trklib->duplicate_tracker($_REQUEST['trackerId'], $_REQUEST['name'], isset($_REQUEST['description']) ? $_REQUEST['description'] : '', $_REQUEST["duplicateDescriptionIsParsed"] ? 'y' : '');
	if (isset($_REQUEST['dupCateg']) && $_REQUEST['dupCateg'] == 'on' && $prefs['feature_categories'] == 'y') {
		global $categlib;
		include_once ('lib/categories/categlib.php');
		$cats = $categlib->get_object_categories('tracker', $_REQUEST['trackerId']);
		$catObjectId = $categlib->add_categorized_object('tracker', $newTrackerId, isset($_REQUEST['description']) ? $_REQUEST['description'] : '', $_REQUEST['name'], "tiki-view_tracker.php?trackerId=$newTrackerId");
		foreach($cats as $cat) {
			$categlib->categorize($catObjectId, $cat);
		}
	}
	if (isset($_REQUEST['dupPerms']) && $_REQUEST['dupPerms'] == 'on') {
		global $userlib;
		include_once ('lib/userslib.php');
		$userlib->copy_object_permissions($_REQUEST['trackerId'], $newTrackerId, 'tracker');
	}
	unset($_REQUEST); // Used to show the list of trackers instead of the new tracker after duplication
	
}
if (!empty($_REQUEST['exportTrackerProfile']) && !empty($_REQUEST['trackerId'])) {
	include_once('lib/profilelib/installlib.php');
	$prof = new Tiki_Profile_InstallHandler_Tracker();
	$res = $prof->_export($_REQUEST['trackerId']);
	header("Content-type: text/yaml");
	header('Content-Disposition: attachment; filename=tracker_'.$_REQUEST['trackerId'].'.yaml');
	header('Expires: 0');
	header('Pragma: public');
	echo $res;
	die;
}

if (isset($_REQUEST['deltodo'])) {
	include_once('lib/todolib.php');
	TodoLib::delTodo($_REQUEST['deltodo']);
}
$cat_type = 'tracker';
$cat_objid = $_REQUEST["trackerId"];
$status_types = $trklib->status_types();
$smarty->assign('status_types', $status_types);

//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
include_once ('lib/userprefs/userprefslib.php');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

if (isset($_REQUEST["save"])) {
	if (isset($_REQUEST['import']) and isset($_REQUEST['rawmeat'])) {
		$raw = $tikilib->read_raw($_REQUEST['rawmeat']);
		foreach($raw['tracker'] as $it => $da) {
			$_REQUEST["$it"] = $da;
		}
	}
	check_ticket('admin-trackers');
	if (!empty($_REQUEST['todo_event']) && !empty($_REQUEST['todo_after']) ) {
		include_once('lib/todolib.php');
		if (!function_exists('compute_select_duration')) include('lib/smarty_tiki/function.html_select_duration.php');
		$todoId = TodoLib::addTodo($todo_after = compute_select_duration($_REQUEST, 'todo_after'), $_REQUEST['todo_event'], 'tracker', $_REQUEST['trackerId'], array('status'=>$_REQUEST['todo_from']), array('status'=>$_REQUEST['todo_to']));
		if (!empty($_REQUEST['todo_notif'])) {
			$todo_notif = compute_select_duration($_REQUEST, 'todo_notif');
			$todo_detail = array('mail'=>'creator', 'before'=>$todo_notif);
			if (!empty($_REQUEST['todo_subject'])) $todo_detail['subject'] = $_REQUEST['todo_subject'];
			if (!empty($_REQUEST['todo_body'])) $todo_detail['body'] = $_REQUEST['todo_body'];
			TodoLib::addTodo($todo_after - $todo_notif, $_REQUEST['todo_event'], 'todo', $todoId, '', $todo_detail);
		}
	}
	$groupalertlib->AddGroup('tracker', $_REQUEST['trackerId'], !empty($_REQUEST['groupforAlert'])?$_REQUEST['groupforAlert']:'', !empty($_REQUEST['showeachuser']) ? $_REQUEST['showeachuser'] : 'n');
	$logslib->add_log('admintrackers', 'changed or created tracker ' . $_REQUEST["name"]);
	$cat_desc = $_REQUEST["description"];
	$cat_name = $_REQUEST["name"];
	$cat_href = "tiki-view_tracker.php?trackerId=" . $_REQUEST["trackerId"];
	$cat_objid = $_REQUEST["trackerId"];
	include_once ("categorize.php");

	$cookietab = 1;
}
$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$info = array();
$fields = array(
	'data' => array()
);
$info["name"] = '';
$info["description"] = '';
$info['descriptionIsParsed'] = '';
$info["showCreated"] = '';
$info['showCreatedFormat'] = '';
$info["showCreatedView"] = '';
$info["showCreatedBy"] = '';
$info["useExplicitNames"] = '';
$info['doNotShowEmptyField'] = '';
$info['showPopup'] = '';
$info['viewItemPretty'] = '';
$info['editItemPretty'] = '';
$info["showStatus"] = '';
$info["showStatusAdminOnly"] = '';
$info["simpleEmail"] = '';
$info["outboundEmail"] = '';
$info["newItemStatus"] = '';
$info["showLastModif"] = '';
$info["showLastModifFormat"] = '';
$info["showLastModifView"] = '';
$info["showLastModifBy"] = '';
$info["useRatings"] = '';
$info["ratingOptions"] = '';
$info["showRatings"] = '';
$info["useComments"] = '';
$info["showComments"] = '';
$info['showLastComment'] = '';
$info["useAttachments"] = '';
$info["showAttachments"] = '';
$info["defaultOrderKey"] = '';
$info["defaultOrderDir"] = 'asc';
$info["newItemStatus"] = 'o';
$info["modItemStatus"] = '';
$info["writerCanModify"] = '';
$info['userCanTakeOwnership'] = '';
$info['oneUserItem'] = '';
$info["writerGroupCanModify"] = '';
$info["defaultStatus"] = 'o';
$info["defaultStatusList"] = array();
$info["orderAttachments"] = 'name,created,filesize,hits,desc';
$info["groupforAlertList"] = array();
$info["groupforAlert"] = $groupalertlib->GetGroup('tracker', $_REQUEST["trackerId"]);
$info["showeachuser"] = $groupalertlib->GetShowEachUser('tracker', $_REQUEST["trackerId"], $info["groupforAlert"]);
$info['start'] = 0;
$info['end'] = 0;
$info['autoCreateCategories'] = '';
$info['autoCreateGroup'] = '';
$info['autoCreateGroupInc'] = 0;
$info['autoAssignGroupItem'] = '';
if ($_REQUEST["trackerId"]) {
	$info = array_merge($info, $trklib->get_tracker($_REQUEST["trackerId"]));
	$info = array_merge($info, $trklib->get_tracker_options($_REQUEST["trackerId"]));
	require_once 'lib/todolib.php';
	$info['todos'] = $todolib->listTodoObject('tracker', $_REQUEST['trackerId']);
	$fields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '');
	$smarty->assign('action', '');
	include_once ('lib/wiki-plugins/wikiplugin_trackerfilter.php');
	$formats = '';
	$filters = wikiplugin_trackerFilter_get_filters($_REQUEST['trackerId'], '', $formats);
	$smarty->assign_by_ref('filters', $filters);
	
	$smarty->assign('recordsMax', $info['items']);
	$smarty->assign('recordsOffset', 1);
	
}
$dstatus = preg_split('//', $info['defaultStatus'], -1, PREG_SPLIT_NO_EMPTY);
foreach($dstatus as $ds) {
	$info["defaultStatusList"][$ds] = true;
}
$all_groupIds = $userlib->list_all_groupIds();
if (is_array($all_groupIds)) {
	foreach($all_groupIds as $g) {
		$groupforAlertList[$g['groupName']] = ($g['groupName'] == $info["groupforAlert"]) ? 'selected' : '';
	}
}
$smarty->assign('fields', $fields['data']);
$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('descriptionIsParsed', $info["descriptionIsParsed"]);
$smarty->assign('showCreated', $info["showCreated"]);
$smarty->assign('showCreatedFormat', $info['showCreatedFormat']);
$smarty->assign('showCreatedView', $info["showCreatedView"]);
$smarty->assign('showCreatedBy', $info["showCreatedBy"]);
$smarty->assign('useExplicitNames', $info["useExplicitNames"]);
$smarty->assign('doNotShowEmptyField', $info['doNotShowEmptyField']);
$smarty->assign('showPopup', $info['showPopup']);
$smarty->assign('showStatus', $info["showStatus"]);
$smarty->assign('showStatusAdminOnly', $info["showStatusAdminOnly"]);
$smarty->assign('simpleEmail', $info["simpleEmail"]);
$smarty->assign('outboundEmail', $info["outboundEmail"]);
$smarty->assign('newItemStatus', $info["newItemStatus"]);
$smarty->assign('showLastModif', $info["showLastModif"]);
$smarty->assign('showLastModifFormat', $info["showLastModifFormat"]);
$smarty->assign('showLastModifView', $info["showLastModifView"]);
$smarty->assign('showLastModifBy', $info["showLastModifBy"]);
$smarty->assign('useRatings', $info["useRatings"]);
$smarty->assign('ratingOptions', $info["ratingOptions"]);
$smarty->assign('showRatings', $info["showRatings"]);
$smarty->assign('useComments', $info["useComments"]);
$smarty->assign('showComments', $info["showComments"]);
$smarty->assign('showLastComment', $info['showLastComment']);
$smarty->assign('useAttachments', $info["useAttachments"]);
$smarty->assign('showAttachments', $info["showAttachments"]);
$smarty->assign('defaultOrderKey', $info["defaultOrderKey"]);
$smarty->assign('defaultOrderDir', $info["defaultOrderDir"]);
$smarty->assign('newItemStatus', $info["newItemStatus"]);
$smarty->assign('modItemStatus', $info["modItemStatus"]);
$smarty->assign('writerCanModify', $info["writerCanModify"]);
$smarty->assign('userCanTakeOwnership', $info['userCanTakeOwnership']);
$smarty->assign('oneUserItem', $info["oneUserItem"]);
$smarty->assign('writerGroupCanModify', $info["writerGroupCanModify"]);
$smarty->assign('defaultStatus', $info["defaultStatus"]);
$smarty->assign('defaultStatusList', $info["defaultStatusList"]);
$smarty->assign('autoCreateCategories', $info["autoCreateCategories"]);
$smarty->assign_by_ref('groupforAlertList', $groupforAlertList);
$smarty->assign('groupforAlert', $info["groupforAlert"]);
$smarty->assign('showeachuser', $info["showeachuser"]);
$smarty->assign_by_ref('all_groupIds', $all_groupIds);
$smarty->assign_by_ref('info', $info);
$outatt = array();
$info["orderPopup"] = '';
if (strstr($info["orderAttachments"], '|')) {
	$part = explode('|', $info["orderAttachments"]);
	$info["orderAttachments"] = $part[0];
	$info["orderPopup"] = $part[1];
}
$i = 1;
foreach(preg_split('/,/', $info["orderAttachments"]) as $it) {
	$outatt["$it"] = $i;
	$i++;
}
$i = - 1;
foreach(preg_split('/,/', $info["orderPopup"]) as $it) {
	$outatt["$it"] = $i;
	$i--;
}
$smarty->assign('ui', $outatt);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
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
$channels = $trklib->list_trackers($offset, $maxRecords, $sort_mode, $find);
if ($offset != 0 || $maxRecords < $channels['cant'] || $sort_mode != '' || $find != '') {
	$trackers = $trklib->list_trackers();
	$smarty->assign_by_ref('trackers', $trackers['data']); // for duplicate
	
} else {
	$smarty->assign_by_ref('trackers', $channels['data']);
}
$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["trackerId"], 'tracker')) {
		$channels["data"][$i]["individual"] = 'y';
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}
$urlquery['find'] = $find;
$urlquery['sort_mode'] = $sort_mode;
$smarty->assign_by_ref('urlquery', $urlquery);
$smarty->assign_by_ref('cant', $channels['cant']);
$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign('uses_tabs', 'y');
// block for categorization
include_once ("categorize_list.php");
ask_ticket('admin-trackers');
global $wikilib;
include_once ('lib/wiki/wikilib.php');
$plugins = $wikilib->list_plugins(true, 'trackerDescription');
$smarty->assign_by_ref('plugins', $plugins);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_trackers.tpl');
$smarty->display("tiki.tpl");
