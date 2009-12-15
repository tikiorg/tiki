<?php
// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/trackeritemfield/wiki-plugins/wikiplugin_trackeritemfield.php,v 1.6 2007/07/17 13:03:52 sylvieg Exp $
function wikiplugin_trackeritemfield_help() {
	$help = tra("Displays the value of a tracker item field or the wiki text if the value of the field is set or has a value(if itemId not specified, use the itemId of the url or the user tracker).").":\n";
	$help .= "~np~{TRACKERITEMFIELD(trackerId=1, itemId=1, fieldId=1, fields=1:2, status=o|p|c|op|oc|pc|opc, test=1|0, value=x)}".tra('Wiki text')."{ELSE}".tra('Wiki text')."{TRACKERITEMFIELD}~/np~";
	return $help;
}

function wikiplugin_trackeritemfield_info() {
	return array(
		'name' => tra('Tracker Item Field'),
		'documentation' => 'PluginTrackerItemField',
		'description' => tra("Displays the value of a tracker item field or the wiki text if the value of the field is set or has a value(if itemId not specified, use the itemId of the url or the user tracker)."),
		'prefs' => array( 'wikiplugin_trackeritemfield', 'feature_trackers' ),
		'body' => tra('Wiki text containing an {ELSE} marker.'),
		'params' => array(
			'trackerId' => array(
				'required' => false,
				'name' => tra('Tracker ID'),
				'description' => tra('Numeric value.'),
			),
			'itemId' => array(
				'required' => false,
				'name' => tra('Item ID'),
				'description' => tra('Numeric value.'),
			),
			'fieldId' => array(
				'required' => false,
				'name' => tra('Field ID'),
				'description' => tra('Numeric value.'),
			),
			'fields' => array(
				'required' => false,
				'name' => tra('Fields'),
				'description' => tra('Colon separated list of field IDs.'),
			),
			'status' => array(
				'required' => false,
				'name' => tra('Status'),
				'description' => tra('o|p|c|op|oc|pc|opc'),
			),
			'test' => array(
				'required' => false,
				'name' => tra('Test'),
				'description' => tra('0|1'),
			),
			'value' => array(
				'required' => true,
				'name' => tra('Value'),
				'description' => tra('Value to compare against.'),
			),
		),
	);
}

function wikiplugin_trackeritemfield($data, $params) {
	global $userTracker, $group, $user, $userlib, $tiki_p_admin_trackers, $prefs, $smarty;
	global $trklib; include_once('lib/trackers/trackerlib.php');
	static $memoItemId = 0;
	static $memoTrackerId = 0;
	static $memoStatus = 0;
	static $memoUserTracker = false;

	extract ($params, EXTR_SKIP);

	if (empty($itemId) && !empty($trackerId) && ($tracker_info = $trklib->get_tracker($trackerId))) {
		if ($t = $trklib->get_tracker_options($trackerId)) {
			$tracker_info = array_merge($tracker_info, $t);
		}
		$itemId = $trklib->get_user_item($trackerId, $tracker_info);
		$memoUserTracker = true;
	}

	if ((!empty($itemId) && $memoItemId == $itemId) || (empty($itemId) && !empty($memoItemId))) {
		$itemId = $memoItemId;
		if (empty($memoTrackerId)) {
			return tra('Incorrect param');
		}
		$trackerId = $memoTrackerId;
	} else {
		if (empty($itemId) && !empty($_REQUEST['itemId'])) {
			$itemId = $_REQUEST['itemId'];
		}
		if (!empty($trackerId) && !empty($_REQUEST['view_user'])) {
			$itemId = $trklib->get_user_item($trackerId, $tracker_info, $_REQUEST['view_user']);
		}
		if (empty($trackerId) && empty($itemId) && ((isset($userTracker) && $userTracker == 'y') || (isset($prefs) && $prefs['userTracker'] == 'y')) && !empty($group) && ($utid = $userlib->get_tracker_usergroup($user)) && $utid['usersTrackerId']) {
			$trackerId = $utid['usersTrackerId'];
			$itemId = $trklib->get_item_id($trackerId, $utid['usersFieldId'], $user);
			$memoUserTracker = true;
		} else if (empty($trackerId) && !empty($itemId)) {
			$item = $trklib->get_tracker_item($itemId);
			$trackerId = $item['trackerId'];
		}

		if (empty($itemId) && empty($test) && empty($status)) {// need an item
			return tra('Incorrect param').': itemId';
		}
		if (empty($trackerId)) {
			return tra('Incorrect param').': trackerId';
		}

		$memoItemId = $itemId;
		if (!empty($status) && !$trklib->valid_status($status)) {
			return tra('Incorrect param').': status';
		}

		$info = $trklib->get_item_info($itemId);
		$memoStatus = $info['status'];
		//$perm = (isset($status) && $status == 'c')? 'tiki_p_view_trackers_closed':((isset($status) && $status == 'p')?'tiki_p_view_trackers_pending':'tiki_p_view_trackers');
		//if ((!empty($fieldId)|| isset($fields)) && !$memoUserTracker && $tiki_p_admin_trackers != 'y' && !$userlib->user_has_perm_on_object($user, $trackerId, 'tracker', $perm) && empty($is_user_tracker)) {
		//	return false;
		//}
		$memoTrackerId = $trackerId;
	}
	if (!isset($data)) {
		$data = $dataelse = '';
	} elseif (!empty($data) && strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data,'{ELSE}')+6);
		$data = substr($data, 0, strpos($data,'{ELSE}'));
	} else {
			$dataelse = '';
	}
	if (!empty($status)) {
		if (!strstr($status, $memoStatus)) {
			return $dataelse;
		}
	}
	if (isset($fields)) {
		$all_fields = $trklib->list_tracker_fields($trackerId, 0, -1);
		$all_fields = $all_fields['data'];
		if (!empty($fields)) {
			$fields = split(':', $fields);
			foreach ($all_fields as $i=>$fopt) {
				if (!in_array($fopt['fieldId'], $fields)) {
					unset($all_fields[$i]);
				}
			}
			if (empty($all_fields)) {
				return tra('Incorrect param');
			}
		}
		$field_values = $trklib->get_item_fields($trackerId, $itemId, $all_fields, $itemUser);
		foreach ($field_values as $field_value) {
			if (($field_value['type'] == 'p' && $field_value['options_array'][0] == 'password') || ($field_value['isHidden'] != 'n' && $field_value['isHidden'] != 'c'))
				continue;
			if (!empty($field_value['visibleBy']) && !in_array($default_group, $field_value['visibleBy']))
				continue;

			if (empty($field_value['value'])) {
//echo "MISSING:".$field_value['fieldId'];
				return $dataelse;
			}
		}
	} elseif (!empty($fieldId)) {

		if (!($field = $trklib->get_tracker_field($fieldId))) {
			return tra('Incorrect param').': fieldId';
		}
		if ($tiki_p_admin_trackers != 'y' && $field['isHidden'] != 'n') {
			return tra('Incorrect param').': fieldId';
		}
		if (empty($test))
			$test = false;

		if (($val = $trklib->get_item_value($trackerId, $itemId, $fieldId)) !== false) {
			if ($field['type'] == 'c' && !empty($value)) {
				if (strtolower($value) == 'on')
					$value = 'y';
				if (strtolower($val) == 'on')
					$val = 'y';
			}
			if ($test && empty($val)) {
				return $dataelse;
			} elseif ($test && !empty($value) && $value == $val) {
				return $data;
			} elseif ($test && !empty($value) && $value != $val) {
				return $dataelse;
			} elseif ($test) { 
				return $data;
			} else {
				$field['value'] = $val;
				$field['itemId'] = $itemId;
				$smarty->assign('field_value', $field);
				$smarty->assign('list_mode', 'n');
				$smarty->assign('showlinks', 'n');
				return $smarty->fetch('tracker_item_field_value.tpl');
			}
		} else {
			return tra('Incorrect param').': fieldId';
		}
	}
	return $data;
}
?>
