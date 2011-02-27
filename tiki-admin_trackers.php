<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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

if (!empty($_REQUEST['show']) && $_REQUEST['show'] == 'mod') {
	$cookietab = '2';
}
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$trklib->remove_tracker($_REQUEST["remove"]);
	$logslib->add_log('admintrackers', 'removed tracker ' . $_REQUEST["remove"]);
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
	if (isset($_REQUEST["showCreated"]) && ($_REQUEST["showCreated"] == 'on' or $_REQUEST["showCreated"] == 'y')) {
		$tracker_options["showCreated"] = 'y';
	} else {
		$tracker_options["showCreated"] = 'n';
	}
	if (isset($_REQUEST['showCreatedFormat'])) {
		$tracker_options['showCreatedFormat'] = $_REQUEST['showCreatedFormat'];
	} else {
		$tracker_options['showCreatedFormat'] = '';
	}
	if (isset($_REQUEST["showCreatedView"]) && ($_REQUEST["showCreatedView"] == 'on' or $_REQUEST["showCreatedView"] == 'y')) {
		$tracker_options["showCreatedView"] = 'y';
	} else {
		$tracker_options["showCreatedView"] = 'n';
	}
	if (isset($_REQUEST["showCreatedBy"]) && ($_REQUEST["showCreatedBy"] == 'on' or $_REQUEST["showCreatedBy"] == 'y')) {
		$tracker_options["showCreatedBy"] = 'y';
	} else {
		$tracker_options["showCreatedBy"] = 'n';
	}
	if (isset($_REQUEST["showStatus"]) && ($_REQUEST["showStatus"] == 'on' or $_REQUEST["showStatus"] == 'y')) {
		$tracker_options["showStatus"] = 'y';
	} else {
		$tracker_options["showStatus"] = 'n';
	}
	if (isset($_REQUEST["showStatusAdminOnly"]) && ($_REQUEST["showStatusAdminOnly"] == 'on' or $_REQUEST["showStatusAdminOnly"] == 'y')) {
		$tracker_options["showStatusAdminOnly"] = 'y';
	} else {
		$tracker_options["showStatusAdminOnly"] = 'n';
	}
	if (isset($_REQUEST["simpleEmail"]) && ($_REQUEST["simpleEmail"] == 'on' or $_REQUEST["simpleEmail"] == 'y')) {
		$tracker_options["simpleEmail"] = 'y';
	} else {
		$tracker_options["simpleEmail"] = 'n';
	}
	if (isset($_REQUEST["outboundEmail"])) {
		$tracker_options["outboundEmail"] = $_REQUEST["outboundEmail"];
	} else {
		$tracker_options["outboundEmail"] = '';
	}
	if (isset($_REQUEST["newItemStatus"]) && ($_REQUEST["newItemStatus"] == 'on' or $_REQUEST["newItemStatus"] == 'y')) {
		$tracker_options["newItemStatus"] = 'y';
	} else {
		$tracker_options["newItemStatus"] = 'n';
	}
	if (isset($_REQUEST["useRatings"]) && ($_REQUEST["useRatings"] == 'on' or $_REQUEST["useRatings"] == 'y')) {
		$tracker_options["useRatings"] = 'y';
		if (isset($_REQUEST["ratingOptions"])) {
			$tracker_options["ratingOptions"] = $_REQUEST["ratingOptions"];
		} else {
			$tracker_options["ratingOptions"] = '-2,-1,0,1,2';
		}
		if (isset($_REQUEST["showRatings"]) && ($_REQUEST["showRatings"] == 'on' or $_REQUEST["showRatings"] == 'y')) {
			$tracker_options["showRatings"] = 'y';
		} else {
			$tracker_options["showRatings"] = 'n';
		}
	} else {
		$tracker_options["useRatings"] = 'n';
		$tracker_options["ratingOptions"] = '';
		$tracker_options["showRatings"] = 'n';
	}
	if (isset($_REQUEST["useComments"]) && ($_REQUEST["useComments"] == 'on' or $_REQUEST["useComments"] == 'y')) {
		$tracker_options["useComments"] = 'y';
		if (isset($_REQUEST["showComments"]) && ($_REQUEST["showComments"] == 'on' or $_REQUEST["showComments"] == 'y')) {
			$tracker_options["showComments"] = 'y';
		} else {
			$tracker_options["showComments"] = 'n';
		}
		if (isset($_REQUEST['showLastComment']) && ($_REQUEST['showLastComment'] == 'on' or $_REQUEST['showLastComment'] == 'y')) {
			$tracker_options['showLastComment'] = 'y';
		} else {
			$tracker_options['showLastComment'] = 'n';
		}
	} else {
		$tracker_options["useComments"] = 'n';
		$tracker_options["showComments"] = 'n';
	}
	if (isset($_REQUEST["useAttachments"]) && ($_REQUEST["useAttachments"] == 'on' or $_REQUEST["useAttachments"] == 'y')) {
		$tracker_options["useAttachments"] = 'y';
		if (isset($_REQUEST["showAttachments"]) && ($_REQUEST["showAttachments"] == 'on' or $_REQUEST["showAttachments"] == 'y')) {
			$tracker_options["showAttachments"] = 'y';
		} else {
			$tracker_options["showAttachments"] = 'n';
		}
	} else {
		$tracker_options["useAttachments"] = 'n';
		$tracker_options["showAttachments"] = 'n';
	}
	if (isset($_REQUEST["showLastModif"]) && ($_REQUEST["showLastModif"] == 'on' or $_REQUEST["showLastModif"] == 'y')) {
		$tracker_options["showLastModif"] = 'y';
	} else {
		$tracker_options["showLastModif"] = 'n';
	}
	if (isset($_REQUEST['showLastModifFormat'])) {
		$tracker_options['showLastModifFormat'] = $_REQUEST['showLastModifFormat'];
	} else {
		$tracker_options['showLastModifFormat'] = '';
	}
	if (isset($_REQUEST["showLastModifView"]) && ($_REQUEST["showLastModifView"] == 'on' or $_REQUEST["showLastModifView"] == 'y')) {
		$tracker_options["showLastModifView"] = 'y';
	} else {
		$tracker_options["showLastModifView"] = 'n';
	}
	if (isset($_REQUEST["showLastModifBy"]) && ($_REQUEST["showLastModifBy"] == 'on' or $_REQUEST["showLastModifBy"] == 'y')) {
		$tracker_options["showLastModifBy"] = 'y';
	} else {
		$tracker_options["showLastModifBy"] = 'n';
	}
	if (isset($_REQUEST["defaultOrderDir"]) && ($_REQUEST["defaultOrderDir"] == 'asc' or $_REQUEST["defaultOrderDir"] == 'desc')) {
		$tracker_options["defaultOrderDir"] = $_REQUEST["defaultOrderDir"];
	} else {
		$tracker_options["defaultOrderDir"] = 'asc';
	}
	if (isset($_REQUEST["newItemStatus"])) {
		$tracker_options["newItemStatus"] = $_REQUEST["newItemStatus"];
	} else {
		$tracker_options["newItemStatus"] = '';
	}
	if (isset($_REQUEST["modItemStatus"])) {
		$tracker_options["modItemStatus"] = $_REQUEST["modItemStatus"];
	} else {
		$tracker_options["modItemStatus"] = '';
	}
	if (isset($_REQUEST["defaultOrderKey"])) {
		$tracker_options["defaultOrderKey"] = $_REQUEST["defaultOrderKey"];
	} else {
		$tracker_options["defaultOrderKey"] = '';
	}
	if (isset($_REQUEST["writerCanModify"]) && ($_REQUEST["writerCanModify"] == 'on' or $_REQUEST["writerCanModify"] == 'y')) {
		$tracker_options["writerCanModify"] = 'y';
	} else {
		$tracker_options["writerCanModify"] = 'n';
	}
	if (isset($_REQUEST['userCanTakeOwnership']) && ($_REQUEST['userCanTakeOwnership'] == 'on' or $_REQUEST['userCanTakeOwnership'] == 'y')) {
		$tracker_options['userCanTakeOwnership'] = 'y';
	} else {
		$tracker_options['userCanTakeOwnership'] = 'n';
	}
	if (isset($_REQUEST["autoCreateCategories"]) && ($_REQUEST["autoCreateCategories"] == 'on' or $_REQUEST["autoCreateCategories"] == 'y')) {
		$tracker_options["autoCreateCategories"] = 'y';
	} else {
		$tracker_options['autoCreateCategories'] = 'n';
	}
	if (isset($_REQUEST['autoCreateGroup']) && ($_REQUEST['autoCreateGroup'] == 'on' or $_REQUEST['autoCreateGroup'] == 'y')) {
		$tracker_options['autoCreateGroup'] = 'y';
	} else {
		$tracker_options['autoCreateGroup'] = 'n';
	}
	if (isset($_REQUEST['autoAssignGroupItem']) && ($_REQUEST['autoAssignGroupItem'] == 'on' or $_REQUEST['autoAssignGroupItem'] == 'y')) {
		$tracker_options['autoAssignGroupItem'] = 'y';
	} else {
		$tracker_options['autoAssignGroupItem'] = 'n';
	}
	if (isset($_REQUEST['autoAssignCreatorGroup']) && ($_REQUEST['autoAssignCreatorGroup'] == 'on' or $_REQUEST['autoAssignCreatorGroup'] == 'y')) {
		$tracker_options['autoAssignCreatorGroup'] = 'y';
	} else {
		$tracker_options['autoAssignCreatorGroup'] = 'n';
	}
	if (isset($_REQUEST['autoAssignCreatorGroupDefault']) && ($_REQUEST['autoAssignCreatorGroupDefault'] == 'on' or $_REQUEST['autoAssignCreatorGroupDefault'] == 'y')) {
		$tracker_options['autoAssignCreatorGroupDefault'] = 'y';
	} else {
		$tracker_options['autoAssignCreatorGroupDefault'] = 'n';
	}
	if (isset($_REQUEST["oneUserItem"]) && ($_REQUEST["oneUserItem"] == 'on' or $_REQUEST["oneUserItem"] == 'y')) {
		$tracker_options["oneUserItem"] = 'y';
	} else {
		$tracker_options["oneUserItem"] = 'n';
	}
	if (isset($_REQUEST["writerGroupCanModify"]) && ($_REQUEST["writerGroupCanModify"] == 'on' or $_REQUEST["writerGroupCanModify"] == 'y')) {
		$tracker_options["writerGroupCanModify"] = 'y';
	} else {
		$tracker_options["writerGroupCanModify"] = 'n';
	}
	if (empty($_REQUEST['autoCreateGroupInc'])) {
		$tracker_options['autoCreateGroupInc'] = 0;
	} else {
		$tracker_options['autoCreateGroupInc'] = $_REQUEST['autoCreateGroupInc'];
	}
	if (empty($_REQUEST['autoCopyGroup'])) {
		$tracker_options['autoCopyGroup'] = 0;
	} else {
		$tracker_options['autoCopyGroup'] = $_REQUEST['autoCopyGroup'];
	}
	if (isset($_REQUEST["defaultStatus"]) && $_REQUEST["defaultStatus"]) {
		if (is_array($_REQUEST["defaultStatus"])) {
			$tracker_options["defaultStatus"] = implode('', $_REQUEST["defaultStatus"]);
		} else {
			$tracker_options["defaultStatus"] = $_REQUEST["defaultStatus"];
		}
	} else {
		$tracker_options["defaultStatus"] = 'o';
	}
	if (isset($_REQUEST['groupforAlert']) && $_REQUEST["groupforAlert"]) {
		$tracker_options['groupforAlert'] = $_REQUEST['groupforAlert'];
	} else {
		$tracker_options['groupforAlert'] = '';
	}
	if (isset($_REQUEST['showeachuser']) && $_REQUEST['showeachuser'] == "on") {
		$tracker_options['showeachuser'] = 'y';
	} else {
		$tracker_options['showeachuser'] = 'n';
	}
	if (isset($_REQUEST['ui'])) {
		if (!is_array($_REQUEST['ui'])) {
			$_REQUEST['ui'] = explode(',', $_REQUEST['ui']);
		}
		$showlist = array();
		$popupinfo = array();
		foreach($_REQUEST['ui'] as $kk => $vv) {
			if ($vv > 0) {
				$showlist[$vv] = $kk;
			}
			if ($vv < 0) {
				$popupinfo[$vv] = $kk;
			}
		}
		ksort($showlist);
		krsort($popupinfo);
		$orderat = implode(',', $showlist);
		if (count($popupinfo)) {
			$orderat.= '|' . implode(',', $popupinfo);
		}
		$tracker_options["orderAttachments"] = $orderat;
	}
	if (isset($_REQUEST["useExplicitNames"]) && ($_REQUEST["useExplicitNames"] == 'on' or $_REQUEST["useExplicitNames"] == 'y')) {
		$tracker_options["useExplicitNames"] = 'y';
	} else {
		$tracker_options["useExplicitNames"] = 'n';
	}
	if (isset($_REQUEST['start']) && $_REQUEST['start'] == 'on') {
		//Convert 12-hour clock hours to 24-hour scale to compute time
		if (!empty($_REQUEST['start_Meridian'])) {
			$_REQUEST['start_Hour'] = date('H', strtotime($_REQUEST['start_Hour'] . ':00 ' . $_REQUEST['start_Meridian']));
		}
		$tracker_options['start'] = TikiLib::make_time($_REQUEST["start_Hour"], $_REQUEST["start_Minute"], 0, $_REQUEST["start_Month"], $_REQUEST["start_Day"], $_REQUEST["start_Year"]);
	}
	if (isset($_REQUEST['end']) && $_REQUEST['end'] == 'on') {
		//Convert 12-hour clock hours to 24-hour scale to compute time
		if (!empty($_REQUEST['end_Meridian'])) {
			$_REQUEST['end_Hour'] = date('H', strtotime($_REQUEST['end_Hour'] . ':00 ' . $_REQUEST['end_Meridian']));
		}
		$tracker_options['end'] = TikiLib::make_time($_REQUEST["end_Hour"], $_REQUEST["end_Minute"], 0, $_REQUEST["end_Month"], $_REQUEST["end_Day"], $_REQUEST["end_Year"]);
	}
	if (isset($_REQUEST['doNotShowEmptyField']) && ($_REQUEST['doNotShowEmptyField'] == 'on' || $_REQUEST['doNotShowEmptyField'] == 'y')) {
		$tracker_options['doNotShowEmptyField'] = 'y';
	} else {
		$tracker_options['doNotShowEmptyField'] = 'n';
	}
	if (isset($_REQUEST['showPopup'])) {
		$tracker_options['showPopup'] = $_REQUEST['showPopup'];
	} else {
		$tracker_options['showPopup'] = '';
	}
	if (isset($_REQUEST['viewItemPretty'])) {
		$tracker_options['viewItemPretty'] = $_REQUEST['viewItemPretty'];
	} else {
		$tracker_options['viewItemPretty'] = '';
	}
	if (isset($_REQUEST['editItemPretty'])) {
		$tracker_options['editItemPretty'] = $_REQUEST['editItemPretty'];
	} else {
		$tracker_options['editItemPretty'] = '';
	}
	if (isset($_REQUEST['descriptionIsParsed']) && ($_REQUEST['descriptionIsParsed'] == 'on' || $_REQUEST['descriptionIsParsed'] == 'y')) {
		$tracker_options['descriptionIsParsed'] = 'y';
	} else {
		$tracker_options['descriptionIsParsed'] = 'n';
	}
	$_REQUEST["trackerId"] = $trklib->replace_tracker($_REQUEST['trackerId'], $_REQUEST['name'], $_REQUEST['description'], $tracker_options, isset($_REQUEST['descriptionIsParsed']) ? 'y' : '');
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
setcookie('tab', $cookietab);
$smarty->assign('cookietab', $cookietab);
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
