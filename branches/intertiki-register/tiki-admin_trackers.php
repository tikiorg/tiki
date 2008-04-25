<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_trackers.php,v 1.60.2.6 2008-02-27 15:18:36 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/trackers/trackerlib.php');

if ($prefs['feature_trackers'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_trackers");
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin_trackers != 'y') {
	$smarty->assign('msg', tra("You don't have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST["trackerId"])) {
	$_REQUEST["trackerId"] = 0;
}

$smarty->assign('individual', 'n');
if ($userlib->object_has_one_permission($_REQUEST["trackerId"], 'tracker')) {
	$smarty->assign('individual', 'y');
}

if (!empty($_REQUEST['duplicate']) && !empty($_REQUEST['name']) && !empty($_REQUEST['trackerId'])) {
  $newTrackerId = $trklib->duplicate_tracker($_REQUEST['trackerId'], $_REQUEST['name'], isset($_REQUEST['description'])?$_REQUEST['description']: '' );
	if (isset($_REQUEST['dupCateg']) && $_REQUEST['dupCateg'] == 'on' && $prefs['feature_categories'] == 'y') {
		global $categlib; include_once('lib/categories/categlib.php');
		$cats = $categlib->get_object_categories('tracker', $_REQUEST['trackerId']);
		$catObjectId = $categlib->add_categorized_object('tracker', $newTrackerId, isset($_REQUEST['description'])?$_REQUEST['description']: '', $_REQUEST['name'], "tiki-view_tracker.php?trackerId=$newTrackerId");
		foreach($cats as $cat) {
			$categlib->categorize($catObjectId, $cat);
		}
	}
	if (isset($_REQUEST['dupPerms']) && $_REQUEST['dupPerms'] == 'on') {
		global $userlib; include_once('lib/userslib.php');
		$userlib->copy_object_permissions($_REQUEST['trackerId'], $newTrackerId, 'tracker');
	}  
	unset($_REQUEST); // Used to show the list of trackers instead of the new tracker after duplication
}

if (!empty($_REQUEST['show']) && $_REQUEST['show'] == 'mod') {
	$cookietab = '2';
} else {
	$cookietab = '1';
}

if (isset($_REQUEST["remove"])) {
  $area = 'deltracker';
  if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$trklib->remove_tracker($_REQUEST["remove"]);
		$logslib->add_log('admintrackers','removed tracker '.$_REQUEST["remove"]);
	} else {
		key_get($area);
	}
}

$cat_type = 'tracker';
$cat_objid = $_REQUEST["trackerId"];

if (isset($_REQUEST["save"])) {

	if (isset($_REQUEST['import']) and isset($_REQUEST['rawmeat'])) {
		$raw = $tikilib->read_raw($_REQUEST['rawmeat']);
		foreach ($raw['tracker'] as $it=>$da) {
			$_REQUEST["$it"] = $da;
		}
	}

	check_ticket('admin-trackers');
	if (isset($_REQUEST["showCreated"]) 
		&& ($_REQUEST["showCreated"] == 'on' 
			or $_REQUEST["showCreated"] == 'y')) {
		$tracker_options["showCreated"] = 'y';
	} else {
		$tracker_options["showCreated"] = 'n';
	}
	if (isset($_REQUEST['showCreatedFormat'])) {
		$tracker_options['showCreatedFormat'] = $_REQUEST['showCreatedFormat'];
	} else {
		$tracker_options['showCreatedFormat'] = '';
	}
	if (isset($_REQUEST["showCreatedView"]) 
		&& ($_REQUEST["showCreatedView"] == 'on' 
			or $_REQUEST["showCreatedView"] == 'y')) {
		$tracker_options["showCreatedView"] = 'y';
	} else {
		$tracker_options["showCreatedView"] = 'n';
	}

	if (isset($_REQUEST["showStatus"]) 
		&& ($_REQUEST["showStatus"] == 'on' 
			or $_REQUEST["showStatus"] == 'y')) {
		$tracker_options["showStatus"] = 'y';
	} else {
		$tracker_options["showStatus"] = 'n';
	}

	if (isset($_REQUEST["showStatusAdminOnly"]) 
		&& ($_REQUEST["showStatusAdminOnly"] == 'on' 
			or $_REQUEST["showStatusAdminOnly"] == 'y')) {
		$tracker_options["showStatusAdminOnly"] = 'y';
	} else {
		$tracker_options["showStatusAdminOnly"] = 'n';
	}
	
	if (isset($_REQUEST["simpleEmail"]) 
		&& ($_REQUEST["simpleEmail"] == 'on' 
			or $_REQUEST["simpleEmail"] == 'y')) {
		$tracker_options["simpleEmail"] = 'y';
	} else {
		$tracker_options["simpleEmail"] = 'n';
	}
	
	if( isset($_REQUEST["outboundEmail"]) )
	{
		$tracker_options["outboundEmail"] = $_REQUEST["outboundEmail"];
	} else {
		$tracker_options["outboundEmail"] = '';
	}
	
	if (isset($_REQUEST["newItemStatus"]) 
		&& ($_REQUEST["newItemStatus"] == 'on'
			or $_REQUEST["newItemStatus"] == 'y')) {
		$tracker_options["newItemStatus"] = 'y';
	} else {
		$tracker_options["newItemStatus"] = 'n';
	}
	
	if (isset($_REQUEST["useRatings"]) 
		&& ($_REQUEST["useRatings"] == 'on'
			or $_REQUEST["useRatings"] == 'y')) {
		$tracker_options["useRatings"] = 'y';
		if (isset($_REQUEST["ratingOptions"])) {
			$tracker_options["ratingOptions"] = $_REQUEST["ratingOptions"];
		} else {
			$tracker_options["ratingOptions"] = '-2,-1,0,1,2';
		}
		if (isset($_REQUEST["showRatings"]) 
			&& ($_REQUEST["showRatings"] == 'on'
				or $_REQUEST["showRatings"] == 'y')) {
			$tracker_options["showRatings"] = 'y';
		} else {
			$tracker_options["showRatings"] = 'n';
		}
	} else {
		$tracker_options["useRatings"] = 'n';
		$tracker_options["ratingOptions"] = '';
		$tracker_options["showRatings"] = 'n';
	}

	if (isset($_REQUEST["useComments"]) 
		&& ($_REQUEST["useComments"] == 'on'
			or $_REQUEST["useComments"] == 'y')) {
		$tracker_options["useComments"] = 'y';
		if (isset($_REQUEST["showComments"]) 
			&& ($_REQUEST["showComments"] == 'on'
				or $_REQUEST["showComments"] == 'y')) {
			$tracker_options["showComments"] = 'y';
		} else {
			$tracker_options["showComments"] = 'n';
		}
	} else {
		$tracker_options["useComments"] = 'n';
		$tracker_options["showComments"] = 'n';
	}

	if (isset($_REQUEST["useAttachments"]) 
		&& ($_REQUEST["useAttachments"] == 'on'
			or $_REQUEST["useAttachments"] == 'y')) {
		$tracker_options["useAttachments"] = 'y';
		if (isset($_REQUEST["showAttachments"]) 
			&& ($_REQUEST["showAttachments"] == 'on'
				or $_REQUEST["showAttachments"] == 'y')) {
			$tracker_options["showAttachments"] = 'y';
		} else {
			$tracker_options["showAttachments"] = 'n';
		}
	} else {
		$tracker_options["useAttachments"] = 'n';
		$tracker_options["showAttachments"] = 'n';
	}


	if (isset($_REQUEST["showLastModif"]) 
		&& ($_REQUEST["showLastModif"] == 'on'
			or $_REQUEST["showLastModif"] == 'y')) {
		$tracker_options["showLastModif"] = 'y';
	} else {
		$tracker_options["showLastModif"] = 'n';
	}
	if (isset($_REQUEST['showLastModifFormat'])) {
		$tracker_options['showLastModifFormat'] = $_REQUEST['showLastModifFormat'];
	} else {
		$tracker_options['showLastModifFormat'] = '';
	}
	if (isset($_REQUEST["showLastModifView"]) 
		&& ($_REQUEST["showLastModifView"] == 'on'
			or $_REQUEST["showLastModifView"] == 'y')) {
		$tracker_options["showLastModifView"] = 'y';
	} else {
		$tracker_options["showLastModifView"] = 'n';
	}


	if (isset($_REQUEST["defaultOrderDir"])
		&& ($_REQUEST["defaultOrderDir"] == 'asc' 
			or $_REQUEST["defaultOrderDir"] == 'desc')) {
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


	if (isset($_REQUEST["writerCanModify"]) 
		&& ($_REQUEST["writerCanModify"] == 'on'
			or $_REQUEST["writerCanModify"] == 'y')) {
		$tracker_options["writerCanModify"] = 'y';
	} else {
		$tracker_options["writerCanModify"] = 'n';
	}
	
	if (isset($_REQUEST["autoCreateCategories"]) 
		&& ($_REQUEST["autoCreateCategories"] == 'on'
			or $_REQUEST["autoCreateCategories"] == 'y')) {
		$tracker_options["autoCreateCategories"] = 'y';
	} else {
		$tracker_options["autoCreateCategories"] = 'n';
	}

	if (isset($_REQUEST["oneUserItem"]) 
		&& ($_REQUEST["oneUserItem"] == 'on'
			or $_REQUEST["oneUserItem"] == 'y')) {
		$tracker_options["oneUserItem"] = 'y';
	} else {
		$tracker_options["oneUserItem"] = 'n';
	}

	if (isset($_REQUEST["writerGroupCanModify"]) 
		&& ($_REQUEST["writerGroupCanModify"] == 'on'
			or $_REQUEST["writerGroupCanModify"] == 'y')) {
		$tracker_options["writerGroupCanModify"] = 'y';
	} else {
		$tracker_options["writerGroupCanModify"] = 'n';
	}

	if (isset($_REQUEST["defaultStatus"]) 
		&& $_REQUEST["defaultStatus"]) { 
		if (is_array($_REQUEST["defaultStatus"])) {
			$tracker_options["defaultStatus"] = implode('',$_REQUEST["defaultStatus"]);
		} else {
			$tracker_options["defaultStatus"] = $_REQUEST["defaultStatus"];
		}
	} else {
		$tracker_options["defaultStatus"] = 'o';
	}

	if (isset($_REQUEST['ui'])) {
		if (!is_array($_REQUEST['ui'])) {
			$_REQUEST['ui'] = split(',',$_REQUEST['ui']);
		}
		$showlist = array();
		$popupinfo = array();
		foreach ($_REQUEST['ui'] as $kk=>$vv) {
			if ($vv > 0) { $showlist[$vv] = $kk; }
			if ($vv < 0) { $popupinfo[$vv] = $kk; }
		}
		ksort($showlist);
		krsort($popupinfo);
		$orderat = implode(',',$showlist);
		if (count($popupinfo)) {
			$orderat.= '|'.implode(',',$popupinfo);
		}
		$tracker_options["orderAttachments"] = $orderat;
	}
	
	
	if(isset($_REQUEST["useExplicitNames"]) 
		&& ($_REQUEST["useExplicitNames"] == 'on' 
			or $_REQUEST["useExplicitNames"] == 'y')) {
		$tracker_options["useExplicitNames"] = 'y';
	}
	else {
		$tracker_options["useExplicitNames"] = 'n';
	}
	if (isset($_REQUEST['start']) && $_REQUEST['start'] == 'on') {
		$tracker_options['start'] = TikiLib::make_time(
			$_REQUEST["start_Hour"],
			$_REQUEST["start_Minute"],
			0,
			$_REQUEST["start_Month"],
			$_REQUEST["start_Day"],
			$_REQUEST["start_Year"]
		);
	}
	if (isset($_REQUEST['end']) && $_REQUEST['end'] == 'on') {
		$tracker_options['end'] = TikiLib::make_time(
			$_REQUEST["end_Hour"],
			$_REQUEST["end_Minute"],
			0,
			$_REQUEST["end_Month"],
			$_REQUEST["end_Day"],
			$_REQUEST["end_Year"]
		);
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
	
	$_REQUEST["trackerId"] = $trklib->replace_tracker($_REQUEST["trackerId"], $_REQUEST["name"], $_REQUEST["description"], $tracker_options);
	$logslib->add_log('admintrackers','changed or created tracker '.$_REQUEST["name"]);

	$cat_desc = $_REQUEST["description"];
	$cat_name = $_REQUEST["name"];
	$cat_href = "tiki-view_tracker.php?trackerId=".$_REQUEST["trackerId"];
	$cat_objid = $_REQUEST["trackerId"];
	include_once("categorize.php");
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$status_types = $trklib->status_types();
$smarty->assign('status_types', $status_types);

$info = array();
$fields = array('data'=>array());
$info["name"] = '';
$info["description"] = '';
$info["showCreated"] = '';
$info['showCreatedFormat'] = '';
$info["showCreatedView"] = '';
$info["useExplicitNames"] = '';
$info['doNotShowEmptyField'] = '';
$info['showPopup'] = '';
$info["showStatus"] = '';
$info["showStatusAdminOnly"] = '';
$info["simpleEmail"] = '';
$info["outboundEmail"] = '';
$info["newItemStatus"] = '';
$info["showLastModif"] = '';
$info["showLastModifFormat"] = '';
$info["showLastModifView"] = '';
$info["useRatings"] = '';
$info["ratingOptions"] = '';
$info["showRatings"] = '';
$info["useComments"] = '';
$info["showComments"] = '';
$info["useAttachments"] = '';
$info["showAttachments"] = '';
$info["defaultOrderKey"] = '';
$info["defaultOrderDir"] = 'asc';
$info["newItemStatus"] = 'o';
$info["modItemStatus"] = '';
$info["writerCanModify"] = '';
$info['oneUserItem'] = '';
$info["writerGroupCanModify"] = '';
$info["defaultStatus"] = 'o';
$info["defaultStatusList"] = array();
$info["orderAttachments"] = 'name,created,filesize,hits,desc';
$info['start']= 0;
$info['end'] = 0;
$info['autoCreateCategories']='';

if ($_REQUEST["trackerId"]) {
	$info = array_merge($info,$tikilib->get_tracker($_REQUEST["trackerId"]));
	$info = array_merge($info,$trklib->get_tracker_options($_REQUEST["trackerId"]));
	$cookietab = '2';
	$fields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '');
	$smarty->assign('action', '');
	include_once('lib/wiki-plugins/wikiplugin_trackerfilter.php');
	$filters = wikiplugin_trackerFilter_get_filters($_REQUEST['trackerId']);
	$smarty->assign_by_ref('filters', $filters);
}
$dstatus = preg_split('//', $info['defaultStatus'], -1, PREG_SPLIT_NO_EMPTY);
foreach ($dstatus as $ds) {
	$info["defaultStatusList"][$ds] = true;
}

$smarty->assign('fields', $fields['data']);
$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('showCreated', $info["showCreated"]);
$smarty->assign('showCreatedFormat', $info['showCreatedFormat']);
$smarty->assign('showCreatedView', $info["showCreatedView"]);
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
$smarty->assign('useRatings', $info["useRatings"]);
$smarty->assign('ratingOptions', $info["ratingOptions"]);
$smarty->assign('showRatings', $info["showRatings"]);
$smarty->assign('useComments', $info["useComments"]);
$smarty->assign('showComments', $info["showComments"]);
$smarty->assign('useAttachments', $info["useAttachments"]);
$smarty->assign('showAttachments', $info["showAttachments"]);
$smarty->assign('defaultOrderKey', $info["defaultOrderKey"]);
$smarty->assign('defaultOrderDir', $info["defaultOrderDir"]);
$smarty->assign('newItemStatus', $info["newItemStatus"]);
$smarty->assign('modItemStatus', $info["modItemStatus"]);
$smarty->assign('writerCanModify', $info["writerCanModify"]);
$smarty->assign('oneUserItem', $info["oneUserItem"]);
$smarty->assign('writerGroupCanModify', $info["writerGroupCanModify"]);
$smarty->assign('defaultStatus', $info["defaultStatus"]);
$smarty->assign('defaultStatusList', $info["defaultStatusList"]);
$smarty->assign('autoCreateCategories', $info["autoCreateCategories"]);
$smarty->assign_by_ref('info', $info);

$outatt = array();
$info["orderPopup"] = '';
if (strstr($info["orderAttachments"],'|')) {
	$part = split("\|",$info["orderAttachments"]);
	$info["orderAttachments"] = $part[0];
	$info["orderPopup"] = $part[1];
}
$i = 1;
foreach (split(',',$info["orderAttachments"]) as $it) {
	$outatt["$it"] = $i;
	$i++;
}
$i = -1;
foreach (split(',',$info["orderPopup"]) as $it) {
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
if ($offset != 0 || $maxRecords < $channels['cant'] || $sort_mode != ''|| $find != '') {
	$trackers = $trklib->list_trackers();
	$smarty->assign_by_ref('trackers', $trackers['data']);// for duplicate
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
$cant = $channels["cant"];
include "tiki-pagination.php";

$smarty->assign_by_ref('channels', $channels["data"]);

setcookie('tab',$cookietab);
$smarty->assign('cookietab',$cookietab);
$smarty->assign('uses_tabs', 'y');

// block for categorization
include_once ("categorize_list.php");

ask_ticket('admin-trackers');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-admin_trackers.tpl');
$smarty->display("tiki.tpl");

?>
