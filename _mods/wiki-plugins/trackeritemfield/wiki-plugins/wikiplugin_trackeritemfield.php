<?php
// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/trackeritemfield/wiki-plugins/wikiplugin_trackeritemfield.php,v 1.6 2007-07-17 13:03:52 sylvieg Exp $
function wikiplugin_trackeritemfield_help() {
	$help = tra("Displays the value of a tracker item field or the wiki text if the value of the field is set (if itemId not specified, use the itemId of the url or the user tracker).").":\n";
	$help .= "~np~{TRACKERITEMFIELD(itemId=>1, fieldId=>1, test=>1|0)}".tra('Wiki text')."{TRACKERITEMFIELD}~/np~";
	return $help;
}
function wikiplugin_trackeritemfield($data, $params) {
	global $userTracker, $group, $user, $userlib, $tiki_p_admin_trackers;
	global $trklib; include_once('lib/trackers/trackerlib.php');
	static $memoItemId = 0;
	static $memoTrackerId = 0;

	extract ($params, EXTR_SKIP);

	if ((!empty($itemId) && $memoItemId == $itemId) || (empty($itemId) && !empty($memoItemId))) {
		$itemId = $memoItemId;
		if (empty($memoTrackerId)) {
			return false;
		}
		$trackerId = $memoTrackerId;
	} else {
		if (empty($itemId) && !empty($_REQUEST['itemId'])) {
			$itemId = $_REQUEST['itemId'];
		}
		if (empty($itemId) && $userTracker == 'y' && !empty($group) && ($utid = $userlib->get_usertrackerid($group)) && $utid['usersTrackerId']) {
			$trackerId = $utid['usersTrackerId'];
			$itemId = $trklib->get_item_id($trackerId, $utid['usersFieldId'], $user);
			$is_user_tracker = true;
		} else if (empty($trackerId) && !empty($itemId)) {
			$item = $trklib->get_tracker_item($itemId);
			$trackerId = $item['trackerId'];
		}
		if (empty($itemId) || empty($trackerId) || empty($fieldId)) {
			return false;
		}
		$memoItemId = $itemId;
		if ($tiki_p_admin_trackers != 'y' && !$userlib->user_has_perm_on_object($user, $trackerId, 'tracker','tiki_p_view_trackers') && empty($is_user_tracker)) {
			return false;
		}
		$memoTrackerId = $trackerId;
	}

	if (!($field = $trklib->get_tracker_field($fieldId))) {
		return false;
	}
	if ($tiki_p_admin_trackers != 'y' && $field['isHidden'] != 'n') {
		return false;
	}
	if (empty($test))
		$test = false;
	
	if (($val = $trklib->get_item_value($trackerId, $itemId, $fieldId)) !== false) {
		if ($test && empty($val)) {
			return false;
		} elseif ($test) {
			return $data;
		} else {
			return $val;
		}
	} else {
		return false;
	}
}
?>
