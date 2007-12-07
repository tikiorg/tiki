<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-export_tracker.php,v 1.12.2.1 2007-12-07 05:56:38 mose Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once('tiki-setup.php');

include_once('lib/trackers/trackerlib.php');
include_once('lib/notifications/notificationlib.php');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if ($prefs['feature_trackers'] != 'y') {
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

$maxRecords = '-1';
$offset = 0;
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
} elseif ($tiki_p_admin != 'y' && $prefs['feature_categories'] == 'y') {
	$perms_array = $categlib->get_object_categories_perms($user, 'tracker', $_REQUEST['trackerId']);
   	if ($perms_array) {
   		$is_categorized = TRUE;
    	foreach ($perms_array as $perm => $value) {
    		$$perm = $value;
    	}
   	} else {
   		$is_categorized = FALSE;
   	}
	if ($is_categorized && isset($tiki_p_view_categorized) && $tiki_p_view_categorized != 'y') {
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

$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
if ($t = $trklib->get_tracker_options($_REQUEST["trackerId"]))
	$tracker_info = array_merge($tracker_info,$t);

if ($tiki_p_view_trackers != 'y') {
	if (!isset($user)){
		$smarty->assign('msg',$smarty->fetch('modules/mod-login_box.tpl'));
		$smarty->assign('errortitle',tra("Please login"));
	} else {
		$smarty->assign('msg', tra("You do not have permission to use this feature"));
	}
	$smarty->display("error.tpl");
	die;
}

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

if (count($status_types) == 0) {
	$tracker_info["showStatus"] = 'n';
}

$smarty->assign('tracker_info', $tracker_info);

$xfields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '');

$writerfield = '';
$writergroupfield = '';
$mainfield = '';
$mainfieldId = 0;
$orderkey = false;
$listfields = array();

$usecategs = false;
$ins_categs = array();
$textarea_options = false;

$counter=0;
$temp_max = count($xfields["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$fid = $xfields["data"][$i]["fieldId"];
	
	$ins_id = 'ins_' . $fid;
	$xfields["data"][$i]["ins_id"] = $ins_id;
	$xfields["data"][$i]["id"] = $fid;
	
	$filter_id = 'filter_' . $fid;
	$xfields["data"][$i]["filter_id"] = $filter_id;
	
	if (!isset($mainfield) and $xfields["data"][$i]['isMain'] == 'y') {
		$mainfield = $xfields["data"][$i]["name"];
		$mainfieldId = $fid;
	}

	if (isset($tracker_info['defaultOrderKey']) and $tracker_info['defaultOrderKey'] == $xfields["data"][$i]['fieldId']) {
		$orderkey = true;
	}
	if ( (($tiki_p_admin == 'y' or $tiki_p_admin_trackers == 'y') && (empty($_REQUEST['which']) || $_REQUEST['which'] == 'all'))
		|| (($xfields["data"][$i]['isTblVisible'] == 'y' or $xfields["data"][$i]['isSearchable'] == 'y') && $_REQUEST['which'] == 'list')
		|| ($xfields["data"][$i]['isHidden'] != 'y' && $_REQUEST['which'] == 'item')
		) {
		$listfields[$fid]['type'] = $xfields["data"][$i]["type"];
		$listfields[$fid]['name'] = $xfields["data"][$i]["name"];
		$listfields[$fid]['options'] = $xfields["data"][$i]["options"];
		$listfields[$fid]['options_array'] = split(',',$xfields["data"][$i]["options"]);
		$listfields[$fid]['isMain'] = $xfields["data"][$i]["isMain"];
		$listfields[$fid]['isTblVisible'] = $xfields["data"][$i]["isTblVisible"];
		$listfields[$fid]['isHidden'] = $xfields["data"][$i]["isHidden"];
		$listfields[$fid]['isSearchable'] = $xfields["data"][$i]["isSearchable"];

		if ($listfields[$fid]['type'] == 'e') { //category
		    $parentId = $listfields[$fid]["options"];
		    $listfields[$fid]['categories'] = $categlib->get_child_categories($parentId);
		}

	}

	if ($xfields["data"][$i]['isHidden'] != 'y' or ($tiki_p_admin == 'y' or $tiki_p_admin_trackers == 'y')) {
		$ins_fields["data"][$i] = $xfields["data"][$i];
		$fields["data"][$i] = $xfields["data"][$i];
		if ($fields["data"][$i]["type"] == 'f') { // date and time
			$fields["data"][$i]["value"] = '';
			$ins_fields["data"][$i]["value"] = '';
			if (isset($_REQUEST["$ins_id" . "Day"])) {
				$ins_fields["data"][$i]["value"] = mktime($_REQUEST["$ins_id" . "Hour"], $_REQUEST["$ins_id" . "Minute"],
				0, $_REQUEST["$ins_id" . "Month"], $_REQUEST["$ins_id" . "Day"], $_REQUEST["$ins_id" . "Year"]);
			} else {
				$ins_fields["data"][$i]["value"] = $tikilib->now;
			}
		} elseif ($fields["data"][$i]["type"] == 'e') { // category
			include_once('lib/categories/categlib.php');
			$parentId = $fields["data"][$i]["options"];
			$fields["data"][$i]['categories'] = $categlib->get_child_categories($parentId);
			$categId = "ins_cat_$fid";
			if (isset($_REQUEST[$categId])) {
				if (is_array($_REQUEST[$categId])) {
					foreach ($_REQUEST[$categId] as $c)
						$fields["data"][$i]['cat'][$c] = 'y';
					$ins_categs = array_merge($ins_categs, $_REQUEST[$categId]);
				} else {
					$fields["data"][$i]['cat'][$_REQUEST[$categId]] = 'y';
					$ins_categs[] = $_REQUEST[$categId];
				}
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
			
		} elseif($fields["data"][$i]["type"] == 's') { // rating
			if (isset($_REQUEST["$ins_id"])) {
				$newItemRate = $_REQUEST["$ins_id"];
				$newItemRateField = $fields["data"]["$i"]["fieldId"];
			} else {
				$newItemRate = NULL;
			}
		
		} elseif(  $fields["data"][$i]["type"] == 'y' ) { // country list
			if (isset($_REQUEST["$ins_id"])) {		
				$ins_fields["data"][$i]["value"] = $_REQUEST["$ins_id"];	
			}
			// Get flags here
			$flags = $tikilib->get_flags();
			$fields["data"][$i]['flags'] = $flags;
			$fields["data"][$i]['defaultvalue'] = 'None';
			
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
					if (!empty($prefs['gal_match_regex'])) {
						if (!preg_match('/'.$prefs['gal_match_regex'].'/', $_FILES["$ins_id"]['name'], $reqs)) {
							$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
							$smarty->display("error.tpl");
							die;
						}
					}
					if (!empty($prefs['gal_nmatch_regex'])) {
						if (preg_match('/'.$prefs['gal_nmatch_regex'].'/', $_FILES["$ins_id"]['name'], $reqs)) {
							$smarty->assign('msg', tra('Invalid imagename (using filters for filenames)'));
							$smarty->display("error.tpl");
							die;
						}
					}
					$fp = fopen( $_FILES["$ins_id"]['tmp_name'], 'rb' );
					//$fhash = md5($name = $_FILES["$ins_id"]['name']);
					$data = '';
					while (!feof($fp)) {
						$data .= fread($fp, 8192 * 16);
					}
					fclose ($fp);
					$ins_fields["data"][$i]["value"] = $data;
					$ins_fields["data"][$i]["file_type"] = mime_content_type( $_FILES["$ins_id"]['tmp_name'] );
					
					//$ins_fields["data"][$i]["value"] = $_FILES["$ins_id"]['name'];
					//$ins_fields["data"][$i]["file_type"] = $_FILES["$ins_id"]['type'];
					$ins_fields["data"][$i]["file_size"] = $_FILES["$ins_id"]['size'];
					$ins_fields["data"][$i]["file_name"] = $_FILES["$ins_id"]['name'];
				}
			}
		}
	}
	// store values to have them available when there is 
	// an error in the values typed by an user for a field type.
	if(isset($ins_fields['data'][$i]['value'])) {
		$fields['data'][$counter]['value'] = $ins_fields['data'][$i]['value'];
		$counter++;
	}
}
if (!isset($mainfield) and isset($fields["data"][0]["fieldId"]) and isset($fields["data"][0]["value"])) {
	$mainfield = $fields["data"][0]["value"];
	$mainfieldId = $fields["data"][0]["fieldId"];
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

if (isset($my) and $my and $writerfield) {
	$filterfield = $writerfield;
} elseif (isset($ours) and $ours and $writergroupfield) {
	$filterfield = $writergroupfield;
} else {
	if (isset($_REQUEST["filterfield"])) {
		$filterfield = $_REQUEST["filterfield"];
	} else {
		$filterfield = '';
	}
}
$smarty->assign('filterfield', $filterfield);

if (isset($my) and $my and $writerfield) {
	$exactvalue = $my;
	$filtervalue = '';
	$_REQUEST['status'] = 'opc';
} elseif (isset($ours) and $ours and $writergroupfield) {
	$exactvalue = $ours;
	$filtervalue = '';
	$_REQUEST['status'] = 'opc';
} else {
	if (isset($_REQUEST["filtervalue"]) and is_array($_REQUEST["filtervalue"]) and isset($_REQUEST["filtervalue"]["$filterfield"])) {
		$filtervalue = $_REQUEST["filtervalue"]["$filterfield"];
	} else if (isset($_REQUEST["filtervalue"])) {
		$filtervalue = $_REQUEST["filtervalue"];
	} else {
		$filtervalue = '';
	}
	$exactvalue = '';
}
$smarty->assign('filtervalue', $filtervalue);

if (!isset($_REQUEST["status"]))
	$_REQUEST["status"] = 'o';

$smarty->assign('status', $_REQUEST["status"]);

$items = $trklib->list_items($_REQUEST["trackerId"], $offset, $maxRecords, $sort_mode, $listfields, $filterfield, $filtervalue, $_REQUEST["status"],'',$exactvalue,$numsort);
$smarty->assign_by_ref('items', $items["data"]);
$smarty->assign_by_ref('item_count', $items['cant']);
$smarty->assign_by_ref('listfields', $listfields);

if ($items['data']) {
	foreach ($items['data'] as $f=>$v) {
		$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker.".$_REQUEST["trackerId"].'.'.$items['data'][$f]['itemId'],$user);
	}
}
$data = $smarty->fetch('tiki-export_tracker.tpl');
if (!empty($_REQUEST['encoding']) && $_REQUEST['encoding'] == 'ISO-8859-1') {
	$data = utf8_decode($data);
} else {
	$_REQUEST['encoding'] = "UTF-8";
}
header("Content-type: text/comma-separated-values; charset:".$_REQUEST['encoding']);
header("Content-Disposition: attachment; filename=".tra('tracker')."_".$_REQUEST['trackerId'].".csv");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");
echo $data;

?>
