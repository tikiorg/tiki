<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-view_tracker.php,v 1.73 2004-06-23 22:33:53 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('tiki-setup.php');

include_once('lib/trackers/trackerlib.php');
include_once('lib/notifications/notificationlib.php');

if ($feature_categories == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($feature_trackers != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_trackers");
	$smarty->display("error.tpl");
	die;
}

$_REQUEST["itemId"] = 0;
$smarty->assign('itemId', $_REQUEST["itemId"]);

if (!isset($_REQUEST["trackerId"])) {
	$smarty->assign('msg', tra("No tracker indicated"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$smarty->assign('individual', 'n');

if ($userlib->object_has_one_permission($_REQUEST["trackerId"], 'tracker')) {
	$smarty->assign('individual', 'y');
	if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'trackers');
		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];
			if ($userlib->object_has_permission($user, $_REQUEST["trackerId"], 'tracker', $permName)) {
				$$permName = 'y';
				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';
				$smarty->assign("$permName", 'n');
			}
		}
	}
} elseif ($tiki_p_admin != 'y' && $feature_categories == 'y') {
	$perms_array = $categlib->get_object_categories_perms($user, 'tracker', $_REQUEST['trackerId']);
   	if ($perms_array) {
   		$is_categorized = TRUE;
    	foreach ($perms_array as $perm => $value) {
    		$$perm = $value;
    	}
   	} else {
   		$is_categorized = FALSE;
   	}
	if ($is_categorized && isset($tiki_p_view_categories) && $tiki_p_view_categories != 'y') {
		if (!isset($user)){
			$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
			$smarty->assign('errortitle',tra("Please login"));
		} else {
			$smarty->assign('msg',tra("Permission denied you cannot view this page"));
    	}
	    $smarty->display("error.tpl");
		die;
	}
}

$cookietab = "1";
$defaultvalues = array();

if (isset($_REQUEST['vals']) and is_array($_REQUEST['vals'])) {
	$defaultvalues = $_REQUEST['vals'];
	$cookietab = "2";
} elseif (isset($_REQUEST['new'])) {
	$cookietab = "2";
}
$smarty->assign('defaultvalues', $defaultvalues);

$my = '';
$ours = '';
if (isset($_REQUEST['my'])) {
	if ($tiki_p_admin_trackers == 'y') {
		$my = $_REQUEST['my'];
	} elseif ($user) {
		$my = $user;
	}
} elseif (isset($_REQUEST['ours'])) {
	if ($tiki_p_admin_trackers == 'y') {
		$ours = $_REQUEST['ours'];
	} elseif ($group) {
		$ours = $group;
	}
}

$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
$tracker_info = array_merge($tracker_info,$trklib->get_tracker_options($_REQUEST["trackerId"]));

if ($tiki_p_view_trackers != 'y') {
	if (!$my and isset($tracker_info['writerCanModify']) and $tracker_info['writerCanModify'] == 'y') {
		$my = $user;
	} elseif (!$ours and isset($tracker_info['writergroupCanModify']) and $tracker_info['writergroupCanModify'] == 'y') {
		$ours = $group;
	} elseif ($tiki_p_create_tracker_items != 'y') {
		if (!isset($user)){
			$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
			$smarty->assign('errortitle',tra("Please login"));
		} else {
			$smarty->assign('msg', tra("You dont have permission to use this feature"));
		}
		$smarty->display("error.tpl");
		die;
	}
}
$smarty->assign('my', $my);
$smarty->assign('ours', $ours);

$field_types = $trklib->field_types();
$smarty->assign('field_types', $field_types);

$status_types = array();
$status_raw = $trklib->status_types();

if (isset($_REQUEST['status'])) {
	$sts = preg_split('//', $_REQUEST['status'], -1, PREG_SPLIT_NO_EMPTY);
} elseif (isset($tracker_info["defaultStatus"])) {
	$sts = preg_split('//', $tracker_info["defaultStatus"], -1, PREG_SPLIT_NO_EMPTY);
	$_REQUEST['status'] = $tracker_info["defaultStatus"];
} else {
	$sts = array('o');
	$_REQUEST['status'] = 'o';
}

foreach ($status_raw as $let=>$sta) {
	if ((isset($$sta['perm']) and $$sta['perm'] == 'y') or ($my or $ours)) {
		if (in_array($let,$sts)) {
			$sta['class'] = 'statuson';
			$sta['statuslink'] = str_replace($let,'',implode('',$sts));
		} else {
			$sta['class'] = 'statusoff';
			$sta['statuslink'] = implode('',$sts).$let;
		}
		$status_types["$let"] = $sta;
	}
}
$smarty->assign('status_types', $status_types);

if (count($status_types) == 1) {
	$tracker_info["showStatus"] = 'n';
}

$smarty->assign('tracker_info', $tracker_info);

$fields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '');
$ins_fields = $fields;

$writerfield = '';
$writergroupfield = '';
$mainfield = '';
$mainfieldId = 0;
$orderkey = false;
$listfields = array();

$usecategs = false;
$ins_categs = array();
$textarea_options = false;

$temp_max = count($fields["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$fid = $fields["data"][$i]["fieldId"];
	
	$ins_id = 'ins_' . $fid;
	$fields["data"][$i]["ins_id"] = $ins_id;
	$fields["data"][$i]["id"] = $fid;
	
	$filter_id = 'filter_' . $fid;
	$fields["data"][$i]["filter_id"] = $filter_id;
	
	if (!isset($mainfield) and $fields["data"][$i]['isMain'] == 'y') {
		$mainfield = $fields["data"][$i]["name"];
		$mainfieldId = $fid;
	}

	if (isset($tracker_info['defaultOrderKey']) and $tracker_info['defaultOrderKey'] == $fields["data"][$i]['fieldId']) {
		$orderkey = true;
	}
	if (($fields["data"][$i]['isTblVisible'] == 'y' or $fields["data"][$i]['isSearchable'] == 'y') and ($fields["data"][$i]['isPublic'] == 'y' or $tiki_p_admin_trackers == 'y')) {
		$listfields[$fid]['type'] = $fields["data"][$i]["type"];
		$listfields[$fid]['name'] = $fields["data"][$i]["name"];
		$listfields[$fid]['options'] = $fields["data"][$i]["options"];
		$listfields[$fid]['options_array'] = split(',',$fields["data"][$i]["options"]);
		$listfields[$fid]['isMain'] = $fields["data"][$i]["isMain"];
		$listfields[$fid]['isTblVisible'] = $fields["data"][$i]["isTblVisible"];
		$listfields[$fid]['isHidden'] = $fields["data"][$i]["isHidden"];
		$listfields[$fid]['isSearchable'] = $fields["data"][$i]["isSearchable"];
	}
	
	if ($fields["data"][$i]["type"] == 'f') { // date and time
		$fields["data"][$i]["value"] = '';
		$ins_fields["data"][$i]["value"] = '';
		if (isset($_REQUEST["$ins_id" . "Day"])) {
			$ins_fields["data"][$i]["value"] = mktime($_REQUEST["$ins_id" . "Hour"], $_REQUEST["$ins_id" . "Minute"],
			0, $_REQUEST["$ins_id" . "Month"], $_REQUEST["$ins_id" . "Day"], $_REQUEST["$ins_id" . "Year"]);
		} else {
			$ins_fields["data"][$i]["value"] = date("U");
		}
	
	} elseif ($fields["data"][$i]["type"] == 'e') { // category
		include_once('lib/categories/categlib.php');
		$k = $fields["data"][$i]["options"];
		$fields["data"][$i]["$k"] = $categlib->get_child_categories($k);
		$categId = "ins_cat_$k";
		if (isset($_REQUEST[$categId]) and is_array($_REQUEST[$categId])) {
			$ins_categs = array_merge($ins_categs,$_REQUEST[$categId]);
		}
		$ins_fields["data"][$i]["value"] = '';

	} elseif ($fields["data"][$i]["type"] == 'u') { // user selection
		if (isset($_REQUEST["$ins_id"]) and $_REQUEST["$ins_id"] and (!$fields["data"][$i]["options"] or $tiki_p_admin_trackers == 'y')) {
			$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
		} else {
			if ($fields["data"][$i]["options"] == 1 and $user) {
				$ins_fields["data"][$i]["value"] = $user;
			} else {
				$ins_fields["data"][$i]["value"] = '';
			}
		}
		if ($fields["data"][$i]["options"] == 1 and !$writerfield) {
			$writerfield = $fid;
		} elseif (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}

	} elseif ($fields["data"][$i]["type"] == 'g') { // group selection
		if (isset($_REQUEST["$ins_id"]) and $_REQUEST["$ins_id"] and (!$fields["data"][$i]["options"] or $tiki_p_admin_trackers == 'y')) {
			$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
		} else {
			if ( $fields["data"][$i]["options"] == 1 and $group) {
				$ins_fields["data"][$i]["value"] = $group;
			} else {
				$ins_fields["data"][$i]["value"] = '';
			}
		}
		if ($fields["data"][$i]["options"] == 1 and !$writergroupfield) {
			$writergroupfield = $fid;
		} elseif (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}

	} elseif ($fields["data"][$i]["type"] == 'c') { // checkbox
		if (isset($_REQUEST["$ins_id"]) && $_REQUEST["$ins_id"] == 'on') {
			$ins_fields["data"][$i]["value"] = 'y';
		} else {
			$ins_fields["data"][$i]["value"] = 'n';
		}
		if (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}

	} elseif ($fields["data"][$i]["type"] == 'a') { // textarea
		if (isset($_REQUEST["$ins_id"])) {
			if (isset($fields["data"][$i]["options_array"][3]) and $fields["data"][$i]["options_array"][3] > 0 and strlen($_REQUEST["$ins_id"]) > $fields["data"][$i]["options_array"][3]) {
				$ins_fields["data"][$i]["value"] = substr($_REQUEST["$ins_id"],0,$fields["data"][$i]["options_array"][3])." (...)";
			} else {
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
			}
		} else {
			$ins_fields["data"][$i]["value"] = '';
		}
		if (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}
		if ($fields["data"][$i]["options_array"][0])	{
			$textarea_options = true;
		} 
		
	} else {
		if (isset($_REQUEST["$ins_id"])) {
			$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];
		} else {
			$ins_fields["data"][$i]["value"] = '';
		}
		if (isset($_REQUEST["$filter_id"])) {
			$fields["data"][$i]["value"] = $_REQUEST["$filter_id"];
		} else {
			$fields["data"][$i]["value"] = '';
		}
		if ($fields["data"][$i]["type"] == 'r')	{ // item link
			if ($tiki_p_admin_trackers == 'y') {
				$stt = 'poc';
			} else {
				$stt = 'o';
			}
			$fields["data"][$i]["list"] = $trklib->get_all_items($fields["data"][$i]["options_array"][0],$fields["data"][$i]["options_array"][1],$stt);
		} elseif ($fields["data"][$i]["type"] == 'i')	{ // image
			if (isset($_FILES["$ins_id"]) && is_uploaded_file($_FILES["$ins_id"]['tmp_name'])) {
				if (!empty($gal_match_regex)) {
					if (!preg_match("/$gal_match_regex/", $_FILES["$ins_id"]['name'], $reqs)) {
						$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
						$smarty->display("error.tpl");
						die;
					}
				}
				if (!empty($gal_nmatch_regex)) {
					if (preg_match("/$gal_nmatch_regex/", $_FILES["$ins_id"]['name'], $reqs)) {
						$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
						$smarty->display("error.tpl");
						die;
					}
				}
				$type = $_FILES["$ins_id"]['type'];
				$size = $_FILES["$ins_id"]['size'];
				$filename = $_FILES["$ins_id"]['name'];
				$ins_fields["data"][$i]["value"] = $_FILES["$ins_id"]['name'];
				$ins_fields["data"][$i]["file_type"] = $_FILES["$ins_id"]['type'];
				$ins_fields["data"][$i]["file_size"] = $_FILES["$ins_id"]['size'];
			}
		}
	}
}
if (!isset($mainfield) and isset($fields["data"][0]["fieldId"]) and isset($fields["data"][0]["value"])) {
	$mainfield = $fields["data"][0]["value"];
	$mainfieldId = $fields["data"][0]["fieldId"];
}
if ($textarea_options) {
	include_once ('lib/quicktags/quicktagslib.php');
	$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','');
	$smarty->assign_by_ref('quicktags', $quicktags["data"]);
}

if ($tiki_p_admin_trackers == 'y' and isset($_REQUEST["remove"])) {
  $area = 'deltrackeritem';
  if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
    key_check($area);
		$trklib->remove_tracker_item($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}


$smarty->assign('mail_msg', '');
$smarty->assign('email_mon', '');

if ($user) {
	if (isset($_REQUEST["monitor"])) {
		check_ticket('view-trackers');
		$user_email = $userlib->get_user_email($user);
		$emails = $notificationlib->get_mail_events('tracker_modified', $_REQUEST["trackerId"]);
		if (in_array($user_email, $emails)) {
			$notificationlib->remove_mail_event('tracker_modified', $_REQUEST["trackerId"], $user_email);
			$mail_msg = tra('Your email address has been removed from the list of addresses monitoring this tracker');
		} else {
			$notificationlib->add_mail_event('tracker_modified', $_REQUEST["trackerId"], $user_email);
			$mail_msg = tra('Your email address has been added to the list of addresses monitoring this tracker');
		}
		$smarty->assign('mail_msg', $mail_msg);
	}
	$user_email = $userlib->get_user_email($user);
	$emails = $notificationlib->get_mail_events('tracker_modified', $_REQUEST["trackerId"]);
	if (in_array($user_email, $emails)) {
		$smarty->assign('email_mon', tra('Cancel monitoring'));
	} else {
		$smarty->assign('email_mon', tra('Monitor'));
	}
}

if (isset($_REQUEST["save"])) {
	if ($tiki_p_create_tracker_items == 'y') {
		check_ticket('view-trackers');
		if (!isset($_REQUEST["status"]) or ($tracker_info["showStatus"] != 'y' and $tiki_p_admin_trackers != 'y')) {
			$_REQUEST["status"] = '';
		}
		$itemid = $trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $ins_fields, $_REQUEST['status']);
		$cookietab = "1";
		$smarty->assign('itemId', '');
		
		if (count($ins_categs)) {
			$cat_type = "tracker ".$_REQUEST["trackerId"];
			$cat_objid = $_REQUEST["itemId"];
			$cat_desc = "";
			$cat_name = $mainfield;
			$cat_href = "tiki-view_tracker_item.php?trackerId=".$_REQUEST["trackerId"]."&amp;itemId=".$_REQUEST["itemId"];
			$categlib->uncategorize_object($cat_type, $cat_objid);
			foreach ($ins_categs as $cats) {
				$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
				if (!$catObjectId) {
					$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
				}
				$categlib->categorize($catObjectId, $cats);
			}
		}
		if(isset($_REQUEST["viewitem"])) {
			header("location: tiki-view_tracker_item.php?trackerId=".$_REQUEST["trackerId"]."&itemId=".$itemid);
			die;
		}
	}
}

$smarty->assign_by_ref('fields', $fields["data"]);

if (!isset($_REQUEST["sort_mode"])) {
	if ($orderkey) {
		$sort_mode = 'f_'.$tracker_info['defaultOrderKey'];
		if (isset($tracker_info['defaultOrderDir'])) {
			$sort_mode.= "_".$tracker_info['defaultOrderDir'];
		} else {
			$sort_mode.= "_asc";
		}
	} else {
		$sort_mode = '';
	}
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$sorts = split('_',$sort_mode);
if (is_array($sorts) and isset($sorts[1]) and isset($listfields["{$sorts[1]}"]['type']) and $listfields["{$sorts[1]}"]['type'] == 'n') {
	$numsort = true;
} else {
	$numsort = false;
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["initial"])) {
	$initial = $_REQUEST["initial"];
} else {
	$initial = '';
}
$smarty->assign('initial', $initial);
$smarty->assign('initials', split(' ','a b c d e f g h i j k l m n o p q r s t u v w x y z'));

if ($my and $writerfield) {
	$filterfield = $writerfield;
} elseif ($ours and $writergroupfield) {
	$filterfield = $writergroupfield;
} else {
	if (isset($_REQUEST["filterfield"])) {
		$filterfield = $_REQUEST["filterfield"];
	} else {
		$filterfield = '';
	}
}
$smarty->assign('filterfield', $filterfield);

if ($my and $writerfield) {
	$exactvalue = $my;
	$filtervalue = '';
	$_REQUEST['status'] = 'opc';
} elseif ($ours and $writergroupfield) {
	$exactvalue = $ours;
	$filtervalue = '';
	$_REQUEST['status'] = 'opc';
} else {
	if (isset($_REQUEST["filtervalue"]) and isset($_REQUEST["filtervalue"]["$filterfield"])) {
		$filtervalue = $_REQUEST["filtervalue"]["$filterfield"];
	} else {
		$filtervalue = '';
	}
	$exactvalue = '';
}
$smarty->assign('filtervalue', $filtervalue);


if (!isset($_REQUEST["status"]))
	$_REQUEST["status"] = 'o';

$smarty->assign('status', $_REQUEST["status"]);

$items = $trklib->list_items($_REQUEST["trackerId"], $offset, $maxRecords, $sort_mode, $listfields, $filterfield, $filtervalue, $_REQUEST["status"],$initial,$exactvalue,$numsort);
//var_dump($items);die();
$urlquery['status'] = $_REQUEST["status"];
$urlquery['initial'] = $initial;
$urlquery['trackerId'] = $_REQUEST["trackerId"];
$urlquery['sort_mode'] = $sort_mode;
$urlquery['exactvalue'] = $exactvalue;
$urlquery['filterfield'] = $filterfield;
$urlquery["filtervalue[".$filterfield."]"] = $filtervalue;
$smarty->assign_by_ref('urlquery', $urlquery);
$cant = $items["cant"];
include "tiki-pagination.php";

$smarty->assign_by_ref('items', $items["data"]);
$smarty->assign_by_ref('listfields', $listfields);


$users = $userlib->list_all_users();
$groups = $userlib->list_all_groups();
$smarty->assign('users', $users);
$smarty->assign('groups', $groups);

$section = 'trackers';
include_once('tiki-section_options.php');

$smarty->assign('uses_tabs', 'y');
if ($feature_jscalendar) {
	$smarty->assign('uses_jscalendar', 'y');
}
$smarty->assign('show_filters', 'n');
foreach ($fields['data'] as $it) {
	if ($it['isSearchable'] == 'y' and $it['isTblVisible'] == 'y'){
		$smarty->assign('show_filters', 'y');
		break;
	}
}
setcookie('tab',$cookietab);
$smarty->assign('cookietab',$cookietab);

ask_ticket('view-trackers');

// Display the template
$smarty->assign('mid', 'tiki-view_tracker.tpl');
$smarty->display("tiki.tpl");

?>
