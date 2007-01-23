<?php
// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/trackeritemfield/wiki-plugins/wikiplugin_trackeritemfield.php,v 1.1 2007-01-23 21:10:37 sylvieg Exp $
function wikiplugin_trackeritemfield_help() {
	$help = tra("Displays the value of an tracker item field or the wiki text id test is true (if itemID not specified, the user tracker).").":\n";
	$help .= "~np~{TRACKERITEMFIELD(itemId=>1, fieldId=>1, test=>1|0)}".tra('Wiki text')."{TRACKERITEMFIELD}~/np~";
	return $help;
}
function wikiplugin_trackeritemfield($data, $params) {
	global $userTracker, $group, $user, $userlib, $tiki_p_admin_trackers;
	global $trklib; include_once('lib/trackers/trackerlib.php');

	extract ($params, EXTR_SKIP);
	if (empty($itemId) && $userTracker == 'y' && !empty($group) && ($utid = $userlib->get_usertrackerid($group)) && $utid['usersTrackerId']) {
		$trackerId = $utid['usersTrackerId'];
		$itemId = $trklib->get_item_id($trackerId, $utid['usersFieldId'], $user);
	} else if (empty($trackerId) && !empty($itemId)) {
		$item = $trklib->get_tracker_item($itemId);
		$trackerId = $item['trackerId'];
	}

	if (empty($itemId) || empty($trackerId) || empty($fieldId)) {
		return '';
	}
	if ($tiki_p_admin_trackers != 'y' && !$userlib->user_has_perm_on_object($user, $trackerId, 'tracker','tiki_p_view_trackers')) {
		return '';
	}

	if (!($field = $trklib->get_tracker_field($fieldId))) {
		return '';
	}
	if ($tiki_p_admin_trackers != 'y' && $field['options']['isHidden'] != 'n') {
		return '';
	}
	if (empty($test))
		$test = false;
	
	if (($val = $trklib->get_item_value($trackerId, $itemId, $fieldId)) !== false && !empty($val)) {
		return ($test?$data: $val);
	} else {
		return '';
	}
}
?>
