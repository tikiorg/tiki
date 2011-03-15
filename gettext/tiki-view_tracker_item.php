<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'trackers';
require_once ('tiki-setup.php');

$access->check_feature('feature_trackers');

$trklib = TikiLib::lib('trk');
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}
$filegallib = TikiLib::lib('filegal');
$notificationlib = TikiLib::lib('notification');
if ($prefs['feature_groupalert'] == 'y') {
	$groupalertlib = TikiLib::lib('groupalert');
}
$auto_query_args = array(
	'offset',
	'trackerId',
	'reloff',
	'itemId',
	'maxRecords',
	'status',
	'sort_mode',
	'initial',
	'filterfield',
	'filtervalue',
	'exactvalue'
);
$special = false;
if (!isset($_REQUEST['trackerId']) && $prefs['userTracker'] == 'y' && !isset($_REQUEST['user'])) {
	if (isset($_REQUEST['view']) and $_REQUEST['view'] == ' user') {
		if (empty($user)) {
			$smarty->assign('msg', tra("You are not logged in"));
			$smarty->assign('errortype', '402');
			$smarty->display("error.tpl");
			die;
		}
		$utid = $userlib->get_tracker_usergroup($user);
		if (isset($utid['usersTrackerId'])) {
			$_REQUEST['trackerId'] = $utid['usersTrackerId'];
			$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'], $utid['usersFieldId'], $user);
			if ($_REQUEST['itemId'] == NULL) {
				$addit = array();
				$addit[] = array(
					'fieldId' => $utid['usersFieldId'],
					'type' => 'u',
					'value' => $user,
				);
				if ($f = $trklib->get_field_id_from_type($_REQUEST['trackerId'], "u", '1%')) {
					if ($f != $utid['usersFieldId']) {
						$addit[] = array(
							'fieldId' => $f,
							'type' => 'u',
							'value' => $user,
						);
					}
				}
				if ($f = $trklib->get_field_id_from_type($_REQUEST['trackerId'], "g", 1)) {
					$addit[] = array(
						'fieldId' => $f,
						'type' => 'g',
						'value' => $group,
					);
				}
				$_REQUEST['itemId'] = $trklib->replace_item($_REQUEST["trackerId"], 0, array('data' => $addit), 'o');
			}
			$special = 'user';
		}
	} elseif (isset($_REQUEST["usertracker"]) and $tiki_p_admin == 'y') {
		$utid = $userlib->get_tracker_usergroup($_REQUEST['usertracker']);
		if (isset($utid['usersTrackerId'])) {
			$_REQUEST['trackerId'] = $utid['usersTrackerId'];
			$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'], $utid['usersFieldId'], $_REQUEST["usertracker"]);
		}
	}
}
if (!isset($_REQUEST['trackerId']) && $prefs['groupTracker'] == 'y') {
	if (isset($_REQUEST['view']) and $_REQUEST['view'] == ' group') {
		$gtid = $userlib->get_grouptrackerid($group);
		if (isset($gtid['groupTrackerId'])) {
			$_REQUEST["trackerId"] = $gtid['groupTrackerId'];
			$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'], $gtid['groupFieldId'], $group);
			if ($_REQUEST['itemId'] == NULL) {
				$addit = array('data' => array(
					'fieldId' => $gtid['groupFieldId'],
					'type' => 'g',
					'value' => $group,
				));
				$_REQUEST['itemId'] = $trklib->replace_item($_REQUEST["trackerId"], 0, $addit, 'o');
			}
			$special = 'group';
		}
	} elseif (isset($_REQUEST["grouptracker"]) and $tiki_p_admin == 'y') {
		$gtid = $userlib->get_grouptrackerid($_REQUEST["grouptracker"]);
		if (isset($gtid['groupTrackerId'])) {
			$_REQUEST["trackerId"] = $gtid['groupTrackerId'];
			$_REQUEST["itemId"] = $trklib->get_item_id($_REQUEST['trackerId'], $gtid['groupFieldId'], $_REQUEST["grouptracker"]);
		}
	}
}
$smarty->assign_by_ref('special', $special);
//url to a user user tracker tiki-view_tracker_item.php?user=yyyyy&view=+user or tiki-view_tracker_item.php?greoup=yyy&user=yyyyy&view=+user or tiki-view_tracker_item.php?trackerId=xxx&user=yyyyy&view=+user
if ($prefs['userTracker'] == 'y' && isset($_REQUEST['view']) && $_REQUEST['view'] = ' user' && !empty($_REQUEST['user'])) {
	if (empty($_REQUEST['trackerId']) && empty($_REQUEST['group'])) {
		$_REQUEST['group'] = $userlib->get_user_default_group($_REQUEST['user']);
	}
	if (empty($_REQUEST['trackerId']) && !empty($_REQUEST['group'])) {
		$utid = $userlib->get_usertrackerid($_REQUEST['group']);
		if (!empty($utid['usersTrackerId']) && !empty($utid['usersFieldId'])) {
			$_REQUEST['trackerId'] = $utid['usersTrackerId'];
			$fieldId = $utid['usersFieldId'];
		}
	}
	if (!empty($_REQUEST['trackerId']) && empty($fieldId)) {
		$fieldId = $trklib->get_field_id_from_type($_REQUEST['trackerId'], 'u', '1%');
	}
	if (!empty($_REQUEST['trackerId']) && !empty($fieldId)) {
		$_REQUEST['itemId'] = $trklib->get_item_id($_REQUEST['trackerId'], $fieldId, $_REQUEST['user']);
		if (!$_REQUEST['itemId']) {
			$smarty->assign('msg', tra("You don't have a personal tracker item yet. Click here to make one:") .
					'<br /><a href="tiki-view_tracker.php?trackerId=' . $_REQUEST['trackerId'] . '&cookietab=2">' .
					tra('Create tracker item') . '</a>');
			$smarty->display("error.tpl");
			die;
		}
	}
}
if ((!isset($_REQUEST["trackerId"]) || !$_REQUEST["trackerId"]) && isset($_REQUEST["itemId"])) {
	$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
	$_REQUEST['trackerId'] = $item_info['trackerId'];
}
if (!isset($_REQUEST["trackerId"]) || !$_REQUEST["trackerId"]) {
	$smarty->assign('msg', tra("No tracker indicated"));
	$smarty->display("error.tpl");
	die;
}
$xfields = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '');
if (!isset($utid) and !isset($gtid) and (!isset($_REQUEST["itemId"]) or !$_REQUEST["itemId"]) and !isset($_REQUEST["offset"])) {
	$smarty->assign('msg', tra("No item indicated"));
	$smarty->display("error.tpl");
	die;
}
if ($prefs['feature_groupalert'] == 'y') {
	$groupforalert = $groupalertlib->GetGroup('tracker', $_REQUEST['trackerId']);
	if ($groupforalert != "") {
		$showeachuser = $groupalertlib->GetShowEachUser('tracker', $_REQUEST['trackerId'], $groupforalert);
		$listusertoalert = $userlib->get_users(0, -1, 'login_asc', '', '', false, $groupforalert, '');
		$smarty->assign_by_ref('listusertoalert', $listusertoalert['data']);
	}
	$smarty->assign_by_ref('groupforalert', $groupforalert);
	$smarty->assign_by_ref('showeachuser', $showeachuser);
}
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
// ************* previous/next **************
foreach(array(
	'status',
	'filterfield',
	'filtervalue',
	'initial',
	'exactvalue',
	'reloff'
) as $reqfld) {
	$trynam = 'try' . $reqfld;
	if (isset($_REQUEST[$reqfld])) {
		$$trynam = $_REQUEST[$reqfld];
	} else {
		$$trynam = '';
	}
}
if (isset($_REQUEST['filterfield'])) {
	if (is_array($_REQUEST['filtervalue']) and isset($_REQUEST['filtervalue'][$tryfilterfield])) {
		$tryfiltervalue = $_REQUEST['filtervalue'][$tryfilterfield];
	} else {
		$tryfilterfield = preg_split('/\s*:\s*/', $_REQUEST['filterfield']);
		$tryfiltervalue = preg_split('/\s*:\s*/', $_REQUEST['filtervalue']);
		$tryexactvalue = preg_split('/\s*:\s*/', $_REQUEST['exactvalue']);
	}
}
//Management of the field type 'User subscribe' (U)
//when user clic on (un)subscribe
if (isset($_REQUEST['user_subscribe']) || isset($_REQUEST['user_unsubscribe'])) {
	$temp = $userlib->get_user_info($user);
	$id_user = $temp['userId'];
	$id_tiki_user = $temp['userId'];
	$U_itemId = (int) $_REQUEST['U_itemId'];
	$U_fieldId = (int) $_REQUEST['U_fieldId'];
	$U_value = $trklib->get_item_value(null, $U_itemId, (int) $U_fieldId);
	$U_maxsubscriptions = substr($U_value, 0, strpos($U_value, '#'));
	$pattern = "/(\d+)\[(\d+)\]/";
	preg_match_all($pattern, $U_value, $match);
	$users_array2 = array();
	$user_subscription = FALSE;
	foreach($match[1] as $i => $id_user) {
		$temp = $userlib->get_userId_info($id_user);
		if ($id_user == $id_tiki_user) {
			$user_subscription = TRUE;
		} else {
			$users_array2[] = array(
				'id' => $id_user,
				'login' => $temp['login'],
				'friends' => $match[2][$i]
			);
		}
	}
	$match = NULL;
	if (isset($_REQUEST['user_subscribe'])) {
		$users_array2[] = array(
			'id' => $id_tiki_user,
			'login' => $user,
			'friends' => intval($_POST['user_friends'])
		);
	}
	$sql_value = $U_maxsubscriptions . "#";
	$sql_value2 = "";
	foreach($users_array2 as $U) {
		$sql_value2.= $U['id'] . "[" . $U['friends'] . "],";
	}
	$sql_value.= $sql_value2 ? substr($sql_value2, 0, strlen($sql_value2) - 1) : "";
	$xfield = $trklib->list_tracker_fields($_REQUEST["trackerId"], 0, -1, 'position_asc', '', true, array(
		'fieldId' => array(
			$U_fieldId
		)
	));
	$xfield['data'][0]['value'] = $sql_value;
	$trklib->replace_item($_REQUEST['trackerId'], $_REQUEST['itemId'], $xfield);
}
//*********** handle prev/next links *****************
if (isset($_REQUEST['reloff'])) {
	if (isset($_REQUEST['move'])) {
		switch ($_REQUEST['move']) {
			case 'prev':
				$tryreloff+= - 1;
				break;

			case 'next':
				$tryreloff+= 1;
				break;

			default:
				$tryreloff = (int)$_REQUEST['move'];
		}
	}
	$cant = 0;
	$listfields = array();
	if (substr($sort_mode, 0, 2) == 'f_') { //look at the field in case the field needs some processing to find the sort
		list($a, $i, $o) = explode('_', $sort_mode);
		foreach($xfields['data'] as $f) {
			if ($f['fieldId'] == $i) {
				$listfields = array(
					$i => $f
				);
				break;
			}
		}
	}
	if (isset($_REQUEST['cant'])) {
		$cant = $_REQUEST['cant'];
	} else {
		if (is_array($tryfiltervalue)) {
			$tryfiltervalue = array_values($tryfiltervalue);
		}
		$trymove = $trklib->list_items($_REQUEST['trackerId'], $offset + $tryreloff, 1, $sort_mode, $listfields, $tryfilterfield, $tryfiltervalue, $trystatus, $tryinitial, $tryexactvalue);
		if (isset($trymove['data'][0]['itemId'])) {
			// Autodetect itemId if not specified
			if (!isset($_REQUEST['itemId'])) {
				$_REQUEST['itemId'] = $trymove['data'][0]['itemId'];
				unset($item_info);
			}
			$cant = $trymove['cant'];
		}
	}
	$smarty->assign('cant', $cant);
}
//*********** that's all for prev/next *****************
$smarty->assign('itemId', $_REQUEST["itemId"]);
if (!isset($item_info)) {
	$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
	if (empty($item_info)) {
		$smarty->assign('msg', tra("No item indicated"));
		$smarty->display("error.tpl");
		die;
	}
}
$item_info['logs'] = $trklib->get_item_history($item_info, 0, '', 0, 1);
$smarty->assign_by_ref('item_info', $item_info);
$smarty->assign('item', array(
	'itemId' => $_REQUEST['itemId'],
	'trackerId' => $_REQUEST['trackerId']
));
$cat_objid = $_REQUEST['itemId'];
$cat_type = 'trackeritem';
$tracker_info = $trklib->get_tracker($_REQUEST["trackerId"]);
if ($t = $trklib->get_tracker_options($_REQUEST["trackerId"])) {
	$tracker_info = array_merge($tracker_info, $t);
}
if (!isset($tracker_info["writerCanModify"]) or (isset($utid) and ($_REQUEST['trackerId'] != $utid['usersTrackerId']))) {
	$tracker_info["writerCanModify"] = 'n';
}
if (!isset($tracker_info["writerGroupCanModify"]) or (isset($gtid) and ($_REQUEST['trackerId'] != $gtid['groupTrackerId']))) {
	$tracker_info["writerGroupCanModify"] = 'n';
}
$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $tracker_info);
if (!empty($_REQUEST['itemId']) && !$special && $tiki_p_view_trackers != 'y') {
	$g = $trklib-> get_item_group_creator($_REQUEST['trackerId'], $_REQUEST['itemId']);
	if (in_array($g, $tikilib->get_user_groups($user))) {
		$trklib->get_special_group_tracker_perm($tracker_info, true);
	}
}
if ($tiki_p_view_trackers != 'y' and $tracker_info["writerCanModify"] != 'y' and $tracker_info["writerGroupCanModify"] != 'y' && !$special) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

if (!empty($_REQUEST['moveto']) && $tiki_p_admin_trackers == 'y') { // mo to another tracker fields with same name
	$perms = Perms::get('tracker', $_REQUEST['moveto']);
	if ($perms->create_tracker_items) {
		$trklib->move_item($_REQUEST['trackerId'], $_REQUEST['itemId'], $_REQUEST['moveto']);
		header('Location: '.filter_out_sefurl('tiki-view_tracker_item.php?itemId=' . $_REQUEST['itemId']));
		exit;
	} else {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("Permission denied"));
		$smarty->display("error.tpl");
		die;
	}
}

$status_types = $trklib->status_types();
$smarty->assign('status_types', $status_types);
$fields = array();
$ins_fields = array();
$usecategs = false;
$ins_categs = array();
$textarea_options = false;
$cookietab = 1;
$itemUser = $trklib->get_item_creator($_REQUEST['trackerId'], $_REQUEST['itemId']);
$smarty->assign_by_ref('itemUser', $itemUser);
$plugins_loaded = false;
foreach($xfields["data"] as $i => $current_field) {
	$fid = $current_field["fieldId"];

	$ins_id = 'ins_' . $fid;
	$filter_id = 'filter_' . $fid;

	$current_field["ins_id"] = $ins_id;
	$current_field["filter_id"] = $filter_id;
	$xfields['data'][$i] = $current_field;

	$current_field_ins = null;
	$current_field_fields = null;

	if (!isset($mainfield) and $current_field['isMain'] == 'y') {
		$mainfield = $i;
	}
	if ($current_field['type'] == 's' && $current_field['name'] == 'Rating') {
		if ($tiki_p_tracker_view_ratings == 'y') {
			$current_field_ins = $current_field;
			$rateFieldId = $fid;

		}
	} elseif ($current_field['isHidden'] == 'n' or $current_field['isHidden'] == 'p' or $tiki_p_admin_trackers == 'y' or ($current_field['isHidden'] == 'c' && !empty($user) && $user == $itemUser)) {
		$current_field_ins = $current_field;
		$current_field_fields = $current_field;

		$handler = $trklib->get_field_handler($current_field);

		if ($handler) {
			$insert_values = $handler->getInsertValues($_REQUEST);
			$field_values = $handler->getDisplayValues($_REQUEST);

			if ($insert_values) {
				$current_field_ins = array_merge($current_field_ins, $insert_values);
			}

			if ($field_values) {
				$current_field_fields = array_merge($current_field_fields, $field_values);
			}
		} elseif ($current_field_fields["type"] == 'u' and isset($current_field_fields['options_array'][0]) and $user) {
			if (isset($_REQUEST[$ins_id]) and ($current_field_fields['options_array'][0] < 1 or $tiki_p_admin_trackers == 'y')) {
				$current_field_ins["value"] = $_REQUEST[$ins_id];
			} else {
				if ($current_field_fields['options_array'][0] == 2) {
					$current_field_ins["value"] = $user;
				} elseif ($current_field_fields['options_array'][0] == 1) {
					if (isset($tracker_info["writerCanModify"]) and $tracker_info["writerCanModify"] == 'y') {
						$tracker_info["authorfield"] = $fid;
					}
					if (isset($tracker_info['userCanTakeOwnership']) && $tracker_info['userCanTakeOwnership'] == 'y' && empty($current_field_ins['value'])) {
						$current_field_ins['value'] = $user; // the user appropiate the item
					} elseif ($tiki_p_admin_trackers != 'y') {
						unset($current_field_ins['fieldId']);
					}
				} else {
					$current_field_ins["value"] = '';
				}
			}
			if (isset($_REQUEST[$filter_id])) {
				$current_field_fields["value"] = $_REQUEST[$filter_id];
			} else {
				$current_field_fields["value"] = '';
			}
		} elseif ($current_field_fields["type"] == 'I' and isset($current_field_fields['options_array'][0]) and isset($IP)) {
			if (isset($_REQUEST[$ins_id]) and ($current_field_fields['options_array'][0] < 1 or $tiki_p_admin_trackers == 'y')) {
				$current_field_ins["value"] = $_REQUEST[$ins_id];
			} else {
				if ($current_field_fields['options_array'][0] == 2) {
					$current_field_ins["value"] = $IP;
				} elseif ($current_field_fields['options_array'][0] == 1) {
					unset($current_field_ins['fieldId']);
				} else {
					$current_field_ins["value"] = '';
				}
			}
			if (isset($_REQUEST[$filter_id])) {
				$current_field_fields["value"] = $_REQUEST[$filter_id];
			} else {
				$current_field_fields["value"] = '';
			}
		} elseif ($current_field_fields["type"] == 'g' and isset($current_field_fields['options_array'][0]) and $group) {
			if (isset($_REQUEST[$ins_id])) {
				$current_field_ins["value"] = $_REQUEST[$ins_id];
			} else {
				if ($current_field_fields['options_array'][0] == 2) {
					$current_field_ins["value"] = $group;
				} elseif ($current_field_fields['options_array'][0] == 1) {
					if (isset($tracker_info["writerGroupCanModify"]) and $tracker_info["writerGroupCanModify"] == 'y') {
						$tracker_info["authorgroupfield"] = $fid;
					}
					unset($current_field_ins["fieldId"]);
				} else {
					$current_field_ins["value"] = '';
				}
			}
			if (isset($_REQUEST[$filter_id])) {
				$current_field_fields["value"] = $_REQUEST[$filter_id];
			} else {
				$current_field_fields["value"] = '';
			}
		} else {
			if (isset($_REQUEST[$ins_id])) {
				$current_field_ins["value"] = $_REQUEST[$ins_id];
			} else {
				$current_field_ins["value"] = '';
			}
			if ($current_field_ins['type'] == 'D' && !empty($_REQUEST['other_' . $ins_id])) { // drop down with other
				$current_field_ins['value'] = $_REQUEST['other_' . $ins_id];
			}
			if (isset($_REQUEST[$filter_id])) {
				$current_field_fields["value"] = $_REQUEST[$filter_id];
			} else {
				$current_field_fields["value"] = '';
			}
		}
	} elseif ($current_field["type"] == "u" and isset($current_field['options_array'][0]) and $user and $current_field['options_array'][0] == 1 and isset($tracker_info["writerCanModify"]) and $tracker_info["writerCanModify"] == 'y') {
		// even if field is hidden need to pick up user for perm
		$tracker_info["authorfield"] = $fid;
	} elseif ($current_field["type"] == "g" and isset($current_field['options_array'][0]) and $group and $current_field['options_array'][0] == 1 and isset($tracker_info["writerGroupCanModify"]) and $tracker_info["writerGroupCanModify"] == 'y') {
		// even if field hidden need to pick up the group for perm
		$tracker_info["authorgroupfield"] = $fid;
	}

	if (! empty($current_field_ins)) {
		$ins_fields['data'][$i] = $current_field_ins;
	}

	if (! empty($current_field_fields)) {
		$fields['data'][$i] = $current_field_fields;
	}
}

// Collect information from the provided fields
$ins_categs = array();
foreach ($ins_fields['data'] as $current_field) {
	if ($current_field['type'] == 'e' && isset($current_field['selected_categories'])) {
		$ins_categs = array_merge($ins_categs, $current_field['selected_categories']);
	}
}

if (isset($tracker_info["authorfield"])) {
	$tracker_info['authorindiv'] = $trklib->get_item_value($_REQUEST["trackerId"], $_REQUEST["itemId"], $tracker_info["authorfield"]);
	if (($user && $tracker_info['authorindiv'] == $user) or ($user && $tracker_info['userCanTakeOwnership'] == 'y' && empty($tracker_info['authorindiv']))) {
		$tiki_p_modify_tracker_items = 'y';
		$smarty->assign("tiki_p_modify_tracker_items", "y");
		$tiki_p_modify_tracker_items_pending = 'y';
		$smarty->assign('tiki_p_modify_tracker_items_pending', 'y');
		$tiki_p_modify_tracker_items_closed = 'y';
		$smarty->assign('tiki_p_modify_tracker_items_closed', 'y');
		$tiki_p_attach_trackers = 'y';
		$smarty->assign("tiki_p_attach_trackers", "y");
		$tiki_p_comment_trackers = 'y';
		$smarty->assign("tiki_p_comment_trackers", "y");
		$tiki_p_view_trackers = 'y';
		$smarty->assign("tiki_p_view_trackers", "y");
	}
}
if ($tiki_p_view_trackers != 'y' && !$special) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
if (!isset($mainfield)) {
	$mainfield = 0;
}
if ($tiki_p_admin_trackers == 'y' || ($tiki_p_remove_tracker_items == 'y' && $item_info['status'] != 'p' && $item_info['status'] != 'c') || ($tiki_p_remove_tracker_items_pending == 'y' && $item_info['status'] == 'p') || ($tiki_p_remove_tracker_items_closed == 'y' && $item_info['status'] == 'c')) {
	if (isset($_REQUEST["remove"])) {
		check_ticket('view-trackers-items');
		$trklib->remove_tracker_item($_REQUEST["remove"]);
	}
}
if (($tiki_p_modify_tracker_items == 'y' && $item_info['status'] != 'p' && $item_info['status'] != 'c') || ($tiki_p_modify_tracker_items_pending == 'y' && $item_info['status'] == 'p') || ($tiki_p_modify_tracker_items_closed == 'y' && $item_info['status'] == 'c') || $special) {
	if (isset($_REQUEST["save"]) || isset($_REQUEST["save_return"])) {
		$captchalib = TikiLib::lib('captcha');
		if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
			$smarty->assign('msg', $captchalib->getErrors());
			$smarty->assign('errortype', 'no_redirect_login');
			$smarty->display("error.tpl");
			die;
		}
		// Check field values for each type and presence of mandatory ones
		$mandatory_missing = array();
		$err_fields = array();
		$categorized_fields = array();
		while (list($postVar, $postVal) = each($_REQUEST)) {
			if (preg_match("/^ins_cat_([0-9]+)/", $postVar, $m)) {
				$categorized_fields[] = $m[1];
			}
		}
		$field_errors = $trklib->check_field_values($ins_fields, $categorized_fields, $_REQUEST['trackerId'], empty($_REQUEST['itemId'])?'':$_REQUEST['itemId']);
		$smarty->assign('err_mandatory', $field_errors['err_mandatory']);
		$smarty->assign('err_value', $field_errors['err_value']);
		// values are OK, then lets save the item
		if (count($field_errors['err_mandatory']) == 0 && count($field_errors['err_value']) == 0) {
			$smarty->assign('input_err', '0'); // no warning to display
			if ($prefs['feature_groupalert'] == 'y') {
				$groupalertlib->Notify(isset($_REQUEST['listtoalert']) ? $_REQUEST['listtoalert'] : '', "tiki-view_tracker_item.php?itemId=" . $_REQUEST["itemId"]);
			}
			check_ticket('view-trackers-items');
			if (!isset($_REQUEST["edstatus"]) or ($tracker_info["showStatus"] != 'y' and $tiki_p_admin_trackers != 'y')) {
				$_REQUEST["edstatus"] = $tracker_info["modItemStatus"];
			}
			$trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $ins_fields, $_REQUEST["edstatus"], $ins_categs);
			if (isset($rateFieldId) && isset($_REQUEST["ins_$rateFieldId"])) {
				$trklib->replace_rating($_REQUEST["trackerId"], $_REQUEST["itemId"], $rateFieldId, $user, $_REQUEST["ins_$rateFieldId"]);
			}
			$mainvalue = $ins_fields["data"][$mainfield]["value"];
			$_REQUEST['show'] = 'view';
			foreach($fields["data"] as $i => $array) {
				if (isset($fields["data"][$i])) {
					$fid = $fields["data"][$i]["fieldId"];
					$ins_id = 'ins_' . $fid;
					$ins_fields["data"][$i]["value"] = '';
				}
			}
			$item_info = $trklib->get_tracker_item($_REQUEST["itemId"]);
			$smarty->assign('item_info', $item_info);
			$trklib->categorized_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $mainvalue, $ins_categs);
		} else {
			$error = $ins_fields;
			$cookietab = "2";
			if ($tracker_info['useAttachments'] == 'y') {
				++$cookietab;
			}
			if ($tracker_info['useComments'] == 'y') {
				++$cookietab;
			}
			$smarty->assign('input_err', '1'); // warning to display
			// can't go back if there are errors
			if (isset($_REQUEST['save_return'])) {
				$_REQUEST['save'] = 'save';
				unset($_REQUEST['save_return']);
			}
		}
		if (isset($_REQUEST['from'])) {
			header('Location: tiki-index.php?page=' . urlencode($_REQUEST['from']));
			exit;
		}
	}
}
// remove image from an image field
if (isset($_REQUEST["removeImage"])) {
	$img_field = array(
		'data' => array()
	);
	$img_field['data'][] = array(
		'fieldId' => $_REQUEST["fieldId"],
		'type' => 'i',
		'name' => $_REQUEST["fieldName"],
		'value' => 'blank'
	);
	$trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $img_field);
	$_REQUEST['show'] = "mod";
}
// ************* return to list ***************************
if (isset($_REQUEST["returntracker"]) || isset($_REQUEST["save_return"])) {
	require_once ('lib/smarty_tiki/block.self_link.php');
	header('Location: ' . smarty_block_self_link(array(
		'_script' => 'tiki-view_tracker.php',
		'_tag' => 'n',
		'_urlencode' => 'n',
		'itemId' => 'NULL'
	) , '', $smarty));
	die;
}
// ********************************************************
if (isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' and $tiki_p_tracker_vote_ratings == 'y') {
	if ($user and $tiki_p_tracker_vote_ratings == 'y' and isset($rateFieldId) and isset($_REQUEST['ins_' . $rateFieldId])) {
		$trklib->replace_rating($_REQUEST['trackerId'], $_REQUEST['itemId'], $rateFieldId, $user, $_REQUEST['ins_' . $rateFieldId]);
		header('Location: tiki-view_tracker_item.php?trackerId=' . $_REQUEST['trackerId'] . '&itemId=' . $_REQUEST['itemId']);
		die;
	}
}
if ($_REQUEST["itemId"]) {
	$info = $trklib->get_tracker_item($_REQUEST["itemId"]);
	if (!isset($info['trackerId'])) {
		$info['trackerId'] = $_REQUEST['trackerId'];
	}
	if ((isset($info['status']) and $info['status'] == 'p' && $tiki_p_view_trackers_pending != 'y') || (isset($info['status']) and $info['status'] == 'c' && $tiki_p_view_trackers_closed != 'y') || ($tiki_p_admin_trackers != 'y' && $tiki_p_view_trackers != 'y' && (!isset($utid) || $_REQUEST['trackerId'] != $utid['usersTrackerId']) && (!isset($gtid) || $_REQUEST['trackerId'] != $utid['groupTrackerId']) && ($tracker_info['writerCanModify'] != 'y' || $user != $itemUser) && !$special)) {
		$itemGroup = $trklib->get_item_group_creator($_REQUEST['trackerId'], $_REQUEST['itemId']);
		if (empty($itemGroup) || !$userlib->user_is_in_group($user, $itemGroup)) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('Permission denied'));
			$smarty->display('error.tpl');
			die;
		}
	}
	$last = array();
	$lst = '';
	$tracker_item_main_value = '';
	foreach($xfields["data"] as $i => $current_field) {
		$current_field_fields = null;
		$current_field_ins = array();

		$handler = $trklib->get_field_handler($current_field, $info);

		if ($current_field['isHidden'] == 'n' or $current_field['isHidden'] == 'p' or $tiki_p_admin_trackers == 'y' or ($current_field['type'] == 's' and $xfields[$i]['name'] == 'Rating' and $tiki_p_tracker_view_ratings == 'y') or ($current_field['isHidden'] == 'c' && !empty($user) && $user == $itemUser)) {
			$current_field_fields = $current_field;

			if ($handler) {
				$insert_values = $handler->getInsertValues($_REQUEST);
				$field_values = $handler->getDisplayValues($_REQUEST);

				if ($insert_values) {
					$current_field_ins = array_merge($current_field_ins, $insert_values);
				}

				if ($field_values) {
					$current_field_fields = array_merge($current_field_fields, $field_values);
				}
			} else {

				if ($current_field_fields['type'] == 'f' || $current_field_fields['type'] == 'j') {
					$dateFields[] = $current_field_fields['fieldId'];
				}
				if ($current_field_fields["type"] != 'h') {
					$fid = $current_field_fields["fieldId"];
					$current_field_ins["id"] = $fid;
					if ($current_field_fields["type"] == 'c') {
						if (!isset($info["$fid"])) $info["$fid"] = 'n';
					} else {
						if (!isset($info["$fid"])) $info["$fid"] = '';
					}
					if ($current_field_fields["type"] == 'u') {
						if (isset($current_field_fields['options_array'][0]) && $current_field_fields['options_array'][0] == 2 and !$info["$fid"]) {
							$current_field_ins["defvalue"] = $user;
						}
						$current_field_ins["value"] = $info["$fid"];
					} elseif ($current_field_fields["type"] == 'G') {
						if (!empty($info["$fid"])) {
							$current_field_ins["value"] = $info["$fid"];
							$first_comma = strpos($info["$fid"], ',');
							$second_comma = strpos($info["$fid"], ',', $first_comma + 1);
							if ($second_comma === false) {
								$second_comma = strlen($info["$fid"]);
								$current_field_ins["value"].= ",11";
							}
							$current_field_ins["x"] = substr($current_field_ins["value"], 0, $first_comma);
							$current_field_ins["y"] = substr($current_field_ins["value"], $first_comma + 1, $second_comma - $first_comma - 1);
							$current_field_ins["z"] = substr($current_field_ins["value"], $second_comma + 1);
						} else {
							$current_field_ins["value"] = null;
							$current_field_ins["x"] = null;
							$current_field_ins["y"] = null;
						}
						if (empty($current_field_ins["z"])) {
							$current_field_ins["z"] = 1;
						}
					} elseif ($current_field_fields["type"] == 'U') {
						$current_field_ins["value"] = $info["$fid"];
						$temp = $userlib->get_user_info($user);
						$id_user = $temp['userId'];
						$id_tiki_user = $temp['userId'];
						$pattern = "/(\d+)\[(\d+)\]/";
						preg_match_all($pattern, $current_field_ins["value"], $match);
						$users_array = array();
						$current_field_ins["user_subscription"] = FALSE;
						$U_nb_users = 0;
						$current_field_ins["user_nb_friends"] = 0;
						foreach($match[1] as $j => $id_user) {
							$temp = $userlib->get_userId_info($id_user);
							array_push($users_array, array(
								'id' => $id_user,
								'login' => $temp['login'],
								'friends' => $match[2][$j]
							));
							$U_nb_users+= $match[2][$j] + 1;
							if ($id_user == $id_tiki_user) {
								$current_field_ins["user_subscription"] = TRUE;
								$current_field_ins["user_nb_friends"] = $match[2][$j];
							}
						}
						$current_field_ins["users_array"] = array();
						$current_field_ins["users_array"] = $users_array;
						$U_maxsubscriptions = substr($info["$fid"], 0, strpos($info["$fid"], '#'));
						$current_field_ins["maxsubscriptions"] = $U_maxsubscriptions;
						$U_liste = NULL;
						$U_othersubscriptions = $current_field_ins["user_nb_friends"];
						if (!$current_field_ins["user_subscription"]) {
							$U_othersubscriptions--;
						}
						if ($U_maxsubscriptions) {
							for ($j = 0; $j <= $U_maxsubscriptions - $U_nb_users + $U_othersubscriptions; $j++) {
								$U_liste[$j] = $j;
							}
						}
						$smarty->assign("U_liste", $U_liste);
					} elseif ($current_field_fields["type"] == 'C') {
						$calc = preg_replace('/#([0-9]+)/', '$info[\1]', $current_field_fields['options_array'][0]);
						eval('$computed = ' . $calc . ';');
						$current_field_ins["value"] = $computed;
						$info[$current_field_fields['fieldId']] = $computed; // in case a computed field use this one
						$infoComputed = $trklib->get_computed_info($current_field_fields['options_array'][0], $_REQUEST['trackerId'], $fields['data']);
						if (!empty($infoComputed)) {
							$current_field_ins = array_merge($infoComputed, $current_field_ins);
						}
					} elseif ($current_field_fields["type"] == 'g') {
						if (isset($current_field_fields['options_array'][0]) && $current_field_fields['options_array'][0] == 2 and !$info["$fid"]) {
							$current_field_ins["defvalue"] = $group;
						}
						$current_field_ins["value"] = $info["$fid"];
					} elseif ($current_field_fields['type'] == 'usergroups' && !empty($itemUser)) {
						$current_field_ins['value'] = array_diff($tikilib->get_user_groups($itemUser), array('Registered', 'Anonymous'));
					} elseif ($current_field_fields['type'] == 'p' && !empty($itemUser)) {
						if ($current_field_fields['options_array'][0] == 'email') {
							$current_field_ins['value'] = $userlib->get_user_email($itemUser);
						} else {
							$current_field_ins['value'] = $userlib->get_user_preference($itemUser, $current_field_fields['options_array'][0]);
						}
					} elseif ($current_field_fields['type'] == 'N' && !empty($itemUser)) {
						$current_field_ins['value'] = $trklib->in_group_value($current_field_fields, $itemUser);
					} elseif ($current_field_fields['type'] == 'F') {
						$current_field_ins["value"] = $info["$fid"];
						$freetaglib = TikiLib::lib('freetag');
						$current_field_ins["freetags"] = $freetaglib->_parse_tag($info["$fid"]);
						$current_field_ins["tag_suggestion"] = $freetaglib->get_tag_suggestion($current_field_ins["freetags"],$prefs['freetags_browse_amount_tags_suggestion']);
					} else {
						$current_field_ins["value"] = $info["$fid"];
					}
					if ($current_field_fields['type'] == 'M') {
						global $filegallib, $prefs;
						if ($prefs['URLAppend'] == '') {
							list($val1, $val2) = explode('=', $current_field_ins["value"]);
						} else {
							$val2 = $current_field_ins["value"];
						}
						$res = $filegallib->get_file_info($val2);
						if ($res["filetype"] == "video/x-flv") {
							$ModeVideo = 'y';
						} else {
							$ModeVideo = 'n';
						};
						$smarty->assign('ModeVideo', $ModeVideo);
					}
					if ($current_field_fields['type'] == 'i' && !empty($current_field_ins['options_array'][2]) && !empty($current_field_ins['value'])) {
						$imagegallib = TikiLib::lib('imagegal');

						if ($imagegallib->readimagefromfile($current_field_ins['value'])) {
							$imagegallib->getimageinfo();
							if (!isset($current_field_ins['options_array'][3])) {
								$current_field_ins['options_array'][3] = 0;
							}
							$t = $imagegallib->ratio($imagegallib->xsize, $imagegallib->ysize, $current_field_ins['options_array'][2], $current_field_ins['options_array'][3]);
							$current_field_ins['options_array'][2] = $t * $imagegallib->xsize;
							$current_field_ins['options_array'][3] = $t * $imagegallib->ysize;
						}
					}
					if (isset($current_field_ins["value"])) {
						$last[$fid] = $current_field_ins["value"];
					}
				}
				if ($current_field_fields['type'] == 'A') {
					if (!empty($current_field_ins['value'])) {
						$current_field_ins['info'] = $trklib->get_item_attachment($current_field_ins['value']);
					}
				} elseif (($current_field_fields['type'] == 's' && $current_field_fields['name'] == 'Rating') || $current_field_fields['type'] == '*') {
					$current_field_fields['value'] = $info[$fid];
					if ($current_field_fields['type'] == '*' && $tiki_p_tracker_vote_ratings == 'y' && !empty($_REQUEST['vote']) && !empty($_REQUEST['itemId']) && isset($_REQUEST['ins_'.$current_field_fields['fieldId']])) {
						$trklib->replace_star($_REQUEST['ins_'.$current_field_fields['fieldId']], $_REQUEST['trackerId'], $_REQUEST['itemId'], $current_field_ins, $user, true);
					} else {
						$trklib->update_star_field($_REQUEST['trackerId'], $_REQUEST['itemId'], $current_field_ins);
						if (!empty($current_field_ins['numVotes'])) {
							$smarty->assign('itemHasVotes', 'y');
						}
					}
				}
			}

			if ($current_field_fields['isMain'] == 'y') {
				$tracker_item_main_value .= (empty($tracker_item_main_value) ? '' : ' ') . $current_field_ins['value']; 
			}
		}

		if ($current_field_fields) {
			$fields["data"][$i] = $current_field_fields;
		}

		if (! empty($current_field_ins)) {
			$ins_fields['data'][$i] = array_merge($ins_fields['data'][$i], $current_field_ins);
		}
	}
	if (!empty($tracker_item_main_value)) {
		$smarty->assign('tracker_item_main_value', $tracker_item_main_value);
	}
}
//restore types values if there is an error
if (isset($error)) {
	foreach($ins_fields["data"] as $i => $current_field) {
		if (isset($error["data"][$i]["value"])) {
			$ins_fields["data"][$i]["value"] = $error["data"][$i]["value"];
		}
	}
}
// dynamic list process
$id_fields = array();
foreach($xfields['data'] as $sid => $onefield) {
	$id_fields[$xfields['data'][$sid]['fieldId']] = $sid;
}
foreach($ins_fields['data'] as $sid => $onefield) {
	if ($ins_fields['data'][$sid]['type'] == 'w') {
		$trklib->prepare_dynamic_items_list($ins_fields['data'][$sid], $ins_fields['data']);
	}
}

// Pull realname for user.
$info["createdByReal"] = $tikilib->get_user_preference($info["createdBy"], 'realName', '');
$info["lastModifByReal"] = $tikilib->get_user_preference($info["lastModifBy"], 'realName', '');

$smarty->assign('id_fields', $id_fields);
$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$smarty->assign('tracker_info', $tracker_info);
$smarty->assign_by_ref('info', $info);
$smarty->assign_by_ref('fields', $fields["data"]);
$smarty->assign_by_ref('ins_fields', $ins_fields["data"]);
$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);
$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);
if ($prefs['feature_user_watches'] == 'y' and $tiki_p_watch_trackers == 'y') {
	if ($user and isset($_REQUEST['watch'])) {
		check_ticket('view-trackers');
		if ($_REQUEST['watch'] == 'add') {
			$tikilib->add_user_watch($user, 'tracker_item_modified', $_REQUEST["itemId"], 'tracker ' . $_REQUEST["trackerId"], $tracker_info['name'], "tiki-view_tracker_item.php?trackerId=" . $_REQUEST["trackerId"] . "&amp;itemId=" . $_REQUEST["itemId"]);
		} else {
			$remove_watch_tracker_type = 'tracker ' . $_REQUEST['trackerId'];
			$tikilib->remove_user_watch($user, 'tracker_item_modified', $_REQUEST["itemId"], $remove_watch_tracker_type);
		}
	}
	$smarty->assign('user_watching_tracker', 'n');
	$it = $tikilib->user_watches($user, 'tracker_item_modified', $_REQUEST['itemId'], 'tracker ' . $_REQUEST["trackerId"]);
	if ($user and $tikilib->user_watches($user, 'tracker_item_modified', $_REQUEST['itemId'], 'tracker ' . $_REQUEST["trackerId"])) {
		$smarty->assign('user_watching_tracker', 'y');
	}
	// Check, if the user is watching this trackers' item by a category.
	if ($prefs['feature_categories'] == 'y') {
		$watching_categories_temp = $categlib->get_watching_categories($_REQUEST['trackerId'], 'tracker', $user);
		$smarty->assign('category_watched', 'n');
		if (count($watching_categories_temp) > 0) {
			$smarty->assign('category_watched', 'y');
			$watching_categories = array();
			foreach($watching_categories_temp as $wct) {
				$watching_categories[] = array(
					"categId" => $wct,
					"name" => $categlib->get_category_name($wct)
				);
			}
			$smarty->assign('watching_categories', $watching_categories);
		}
	}
}
if ($tracker_info["useComments"] == 'y') {
	if ($tiki_p_admin_trackers == 'y' and isset($_REQUEST["remove_comment"])) {
		$access->check_authenticity();
		$trklib->remove_item_comment($_REQUEST["remove_comment"]);
	}
	if (isset($_REQUEST["commentId"])) {
		$comment_info = $trklib->get_item_comment($_REQUEST["commentId"]);
		$smarty->assign('comment_title', $comment_info["title"]);
		$smarty->assign('comment_data', $comment_info["data"]);
		$cookietab = 2;
	} else {
		$_REQUEST["commentId"] = 0;
		$smarty->assign('comment_title', '');
		$smarty->assign('comment_data', '');
	}
	$smarty->assign('commentId', $_REQUEST["commentId"]);
	if ($_REQUEST["commentId"] && $tiki_p_admin_trackers != 'y') {
		$_REQUEST["commentId"] = 0;
	}
	if ($tiki_p_comment_tracker_items == 'y') {
		if (isset($_REQUEST["save_comment"])) {
			check_ticket('view-trackers-items');
			if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
				$smarty->assign('msg', $captchalib->getErrors());
				$smarty->assign('errortype', 'no_redirect_login');
				$smarty->display("error.tpl");
				die;
			}
			$trklib->replace_item_comment($_REQUEST["commentId"], $_REQUEST["itemId"], $_REQUEST["comment_title"], $_REQUEST["comment_data"], $user, $tracker_info);
			$smarty->assign('comment_title', '');
			$smarty->assign('comment_data', '');
			$smarty->assign('commentId', 0);
		}
	}
	$comments = $trklib->list_item_comments($_REQUEST["itemId"], 0, -1, 'posted_desc', '');
	$smarty->assign_by_ref('comments', $comments["data"]);
	$smarty->assign_by_ref('commentCount', $comments["cant"]);
}
if ($tracker_info["useAttachments"] == 'y') {
	if (isset($_REQUEST["removeattach"])) {
		check_ticket('view-trackers-items');
		$owner = $trklib->get_item_attachment_owner($_REQUEST["removeattach"]);
		if (($user && ($owner == $user)) || ($tiki_p_admin_trackers == 'y')) {
			$access->check_authenticity();
			$trklib->remove_item_attachment($_REQUEST["removeattach"]);
		}
		$_REQUEST["show"] = "att";
	}
	if (isset($_REQUEST["editattach"])) {
		$att = $trklib->get_item_attachment($_REQUEST["editattach"]);
		$smarty->assign("attach_comment", $att['comment']);
		$smarty->assign("attach_version", $att['version']);
		$smarty->assign("attach_longdesc", $att['longdesc']);
		$smarty->assign("attach_file", $att["filename"]);
		$smarty->assign("attId", $att["attId"]);
		$_REQUEST["show"] = "att";
	}
	if (isset($_REQUEST['attach']) && $tiki_p_attach_trackers == 'y' && isset($_FILES['userfile1'])) {
		// Process an attachment here
		if (is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
			$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
			$data = '';
			$fhash = '';
			if ($prefs['t_use_db'] == 'n') {
				$fhash = md5($_FILES['userfile1']['name'] . $tikilib->now);
				$fw = fopen($prefs['t_use_dir'] . $fhash, "wb");
				if (!$fw) {
					$smarty->assign('msg', tra('Cannot write to this file:') . $fhash);
					$smarty->display("error.tpl");
					die;
				}
			}
			while (!feof($fp)) {
				if ($prefs['t_use_db'] == 'y') {
					$data.= fread($fp, 8192 * 16);
				} else {
					$data = fread($fp, 8192 * 16);
					fwrite($fw, $data);
				}
			}
			fclose($fp);
			if ($prefs['t_use_db'] == 'n') {
				fclose($fw);
				$data = '';
			}
			$size = $_FILES['userfile1']['size'];
			$name = $_FILES['userfile1']['name'];
			$type = $_FILES['userfile1']['type'];
		} else {
			$smarty->assign('msg', $_FILES['userfile1']['name'] . ': ' . tra('Upload was not successful') . ': ' . $tikilib->uploaded_file_error($_FILES['userfile1']['error']));
			$smarty->display("error.tpl");
			die;
		}
		$trklib->replace_item_attachment($_REQUEST["attId"], $name, $type, $size, $data, $_REQUEST["attach_comment"], $user, $fhash, $_REQUEST["attach_version"], $_REQUEST["attach_longdesc"], $_REQUEST['trackerId'], $_REQUEST['itemId'], $tracker_info);
		$_REQUEST["attId"] = 0;
		$_REQUEST['show'] = "att";
	}
	// If anything below here is changed, please change lib/wiki-plugins/wikiplugin_attach.php as well.
	$attextra = 'n';
	if (strstr($tracker_info["orderAttachments"], '|')) {
		$attextra = 'y';
	}
	$attfields = explode(',', strtok($tracker_info["orderAttachments"], '|'));
	$atts = $trklib->list_item_attachments($_REQUEST["itemId"], 0, -1, 'comment_asc', '');
	$smarty->assign('atts', $atts["data"]);
	$smarty->assign('attCount', $atts["cant"]);
	$smarty->assign('attfields', $attfields);
	$smarty->assign('attextra', $attextra);
}
if (isset($_REQUEST['moveto']) && empty($_REQUEST['moveto'])) {
	$trackers = $trklib->list_trackers();
	$smarty->assign_by_ref('trackers', $trackers['data']);
	$_REQUEST['show'] = 'mod';
}
if (isset($_REQUEST['show'])) {
	if ($_REQUEST['show'] == 'view') {
		$cookietab = 1;
	} elseif ($tracker_info["useComments"] == 'y' and $_REQUEST['show'] == 'com') {
		$cookietab = 2;
	} elseif ($_REQUEST['show'] == "mod") {
		$cookietab = 2;
		if ($tracker_info["useAttachments"] == 'y') $cookietab++;
		if ($tracker_info["useComments"] == 'y' && $tiki_p_tracker_view_comments == 'y') $cookietab++;
	} elseif ($_REQUEST['show'] == "att") {
		$cookietab = 2;
		if ($tracker_info["useComments"] == 'y' && $tiki_p_tracker_view_comments == 'y') $cookietab = 3;
	}
}
if (isset($_REQUEST['from'])) {
	$from = $_REQUEST['from'];
} else {
	$from = false;
}
$smarty->assign('from', $from);
if (isset($_REQUEST['status'])) $smarty->assign_by_ref('status', $_REQUEST['status']);
include_once ('tiki-section_options.php');
$smarty->assign('uses_tabs', 'y');
ask_ticket('view-trackers-items');
$logslib = TikiLib::lib('logs');
$logslib->add_action('Viewed', $_REQUEST['itemId'], 'trackeritem');

// Generate validation js
if ($prefs['feature_jquery'] == 'y' && $prefs['feature_jquery_validation'] == 'y') {
	$validatorslib = TikiLib::lib('validators');
	$validationjs = $validatorslib->generateTrackerValidateJS( $fields['data'] );
	$smarty->assign('validationjs', $validationjs);
}

// Display the template
$smarty->assign('mid', 'tiki-view_tracker_item.tpl');

if (isset($_REQUEST['print'])) {
	$smarty->display('tiki-print.tpl');
	$smarty->assign('print', 'y');
} else {
	$smarty->display('tiki.tpl');
}
