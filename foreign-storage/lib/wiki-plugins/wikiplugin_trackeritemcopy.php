<?php

function wikiplugin_trackeritemcopy_info() {
	return array(
		'name' => tra('Copy Tracker Item'),
		'documentation' => tra('PluginTrackerItemCopy'),
		'description' => tra('Will not work with category or certain special fields, copies only data from specified fields'),
		'prefs' => array('wikiplugin_trackeritemcopy', 'feature_trackers'),
		'validate' => 'all',
		'filter' => 'wikicontent',
		'params' => array(
			'trackerId' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Tracker from which to copy item'),
				'filter' => 'text',
				'default' => '',
			),
			'copyFieldIds' => array(
				'required' => true,
				'name' => tra('Field IDs to copy'),
				'description' => tra('Field IDs to copy old value of, separated by :'),
				'filter' => 'text',
				'default' => '',
				'separator' => ':',
			),
			'updateFieldIds' => array(
				'required' => false,
				'name' => tra('Field IDs to update values with'),
				'description' => tra('Field IDs to update with new values specified, separated by :'),
				'filter' => 'text',
				'default' => '',
				'separator' => ':',
			), 
			'updateFieldValues' => array(
				'required' => false,
				'name' => tra('New Values'),
				'description' => tra('New Values to replace for the field IDs specified, separated by :, -randomstring- will generate random string; and f_xx to use value of field xx of itemId'),
				'filter' => 'text',
				'default' => '',
				'separator' => ':',
			),
			'itemId' => array(
				'required' => false,
				'name' => tra('Item ID'),
				'description' => tra('ID of item to make copy of, otherwise input is asked for'),
				'filter' => 'text',
				'default' => '',
			),
			'copies_on_load' => array(
				'required' => false,
				'name' => tra('Make this number of copies on load'),
				'description' => tra('Set the number of copies to make on load of plugin automatically'),
				'filter' => 'int',
				'default' => ''
			),
			'return_array' => array(
				'required' => false,
				'name' => tra('Returns array non-interactively'),
				'advanced' => true,
				'description' => tra('If y, returns array of new information instead of displaying results to screen, used in non-interactive mode'),
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_trackeritemcopy( $data, $params ) {
	global $smarty;
	if (!isset($params["trackerId"]) || !isset($params["copyFieldIds"])) {
		return tra('Missing mandatory parameters');
	} else {
		$trackerId = $params["trackerId"];
		$copyFieldIds = $params["copyFieldIds"];
	}
	$smarty->assign('itemIdSet', 'n');
	$itemId = 0;
	if (isset($params["itemId"])) {
		$itemId = $params["itemId"];
		$smarty->assign('itemIdSet', 'y');
	} elseif (isset($_POST["itemIdToCopy"])) {
		$itemId = $_POST["itemIdToCopy"];
	} 

	if (isset($params["updateFieldIds"]) || isset($params["updateFieldValues"])) {
		$updateFieldIds = $params["updateFieldIds"];
		$updateFieldValues = $params["updateFieldValues"];
		if (count($updateFieldIds) != count($updateFieldValues)) {
			return tra('Number of update fields do not match new values');
		}
		$copyFieldIds = array_diff($copyFieldIds, $updateFieldIds);
	}
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $itemId && isset($_POST['copytrackeritem']) && isset($_POST['numberofcopies'])) {
		$copies = (int) $_POST['numberofcopies'];
	} elseif (isset($params['copies_on_load'])) {
		$copies = (int) $params['copies_on_load']; 
	} else {
		$copies = 0;
	}
	if ($copies) {
		global $trklib; require_once("lib/trackers/trackerlib.php");
		$tracker_fields_info = $trklib->list_tracker_fields($trackerId);
		$fieldTypes = array();
		foreach($tracker_fields_info['data'] as $t) {
			$fieldTypes[$t['fieldId']] = $t['type'];
			$fieldOptionsArray[$t['fieldId']] = $t['options_array'];
		}
		$ins_fields["data"] = array();
		for($i = 0; $i < count($updateFieldIds); $i++) {
			$ins_fields["data"][] = array('options_array' => $fieldOptionsArray[$updateFieldIds[$i]], 'type' => $fieldTypes[$updateFieldIds[$i]], 'fieldId' => $updateFieldIds[$i], 'value' => $updateFieldValues[$i]);
		}
		// CUSTOM: this part is totally custom to store admin notes (how to generalize?)
		if (!empty($_POST['admin_notes_for_copy'])) {
			$ins_fields["data"][] = array('type' => 'a', 'fieldId' => 118, 'value' => $_POST['admin_notes_for_copy']);
		}
		// end totally CUSTOM part
		$newitems = array();
		for ($i = 0; $i < $copies; $i++) {
			// Check for -randomstring- and f_xx
			$ins_fields_final["data"] = array();
			foreach($ins_fields["data"] as $h) {
				if ($h["value"] == '-randomstring-') {
					$h["value"] = $trklib->genPass();
				} else if (substr($h["value"], 0, 2) == 'f_') {
					$sourceFieldId = (int) trim(substr($h["value"], 2));
					$h["value"] = $trklib->get_item_value($trackerId, $itemId, $sourceFieldId);
				}
				$ins_fields_final["data"][] = $h; 
			}
			$newitemsdata[] = $ins_fields_final["data"];
			$newitems[] = $trklib->replace_item($trackerId, 0, $ins_fields_final);		
		}
		foreach ($newitems as $n) {
			$trklib->copy_item($itemId, $n, null, $copyFieldIds);
			$newitemslist .= '  ' . $n;
		}
		$smarty->assign('newitemslist', $newitemslist);
		if ($params['return_array'] == 'y') {
			$return_array['data'] = $newitemsdata;
			$return_array['items'] = $newitems;
			return $return_array;
		}
	}
	return $smarty->fetch('wiki-plugins/wikiplugin_trackeritemcopy.tpl');
} 
			
