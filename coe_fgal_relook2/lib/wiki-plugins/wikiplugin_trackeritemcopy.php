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
				'description' => tra('Tracker from which to copy item, joined tracker ids separated by :'),
				'filter' => 'text',
				'default' => '',
				'separator' => array(':'),
			),
			'linkFieldIds' => array(
				'required' => true,
				'name' => tra(''),
				'description' => tra('Fields links that are related to this tracker that you would like to join on, separated by :'),
				'filter' => 'text',
				'default' => '',
				'separator' => array(':'),
			),
			'copyFieldIds' => array(
				'required' => true,
				'name' => tra('Field IDs to copy'),
				'description' => tra('Field IDs to copy old value of, separated by :, joined fields separated by |'),
				'filter' => 'text',
				'default' => '',
				'separator' => array('|', ':'),
			),
			'updateFieldIds' => array(
				'required' => false,
				'name' => tra('Field IDs to update values with'),
				'description' => tra('Field IDs to update with new values specified, separated by :, joined fields separated by |'),
				'filter' => 'text',
				'default' => '',
				'separator' => array('|', ':'),
			), 
			'updateFieldValues' => array(
				'required' => false,
				'name' => tra('New Values'),
				'description' => tra('New Values to replace for the field IDs specified, separated by :, joined fields separated by |, -randomstring- will generate random string; and f_xx to use value of field xx of itemId'),
				'filter' => 'text',
				'default' => '',
				'separator' => array('|', ':'),
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
	global $trklib; require_once("lib/trackers/trackerlib.php");
	global $trkqrylib; require_once("lib/trackers/trackerquerylib.php");
	
	if (!isset($params["trackerId"]) || !isset($params["copyFieldIds"])) {
		return tra('Missing mandatory parameters');
	} else {
		$trackerId = $params["trackerId"];
		if (is_array($trackerId) == false) $trackerId = array($trackerId);
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

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		function items_copy($trackerId, $updateFieldIds, $updateFieldValues, $copyFieldIds, $itemIds, $linkFieldId, $itemLinkId, $copies) {
			global $trklib, $trkqrylib, $_POST;
			
			if(is_array($itemIds) == false) $itemIds = array($itemIds);
			
			foreach ($itemIds as $itemId) {
				$tracker_fields_info = $trklib->list_tracker_fields($trackerId);
				
				$fieldTypes = array();
				$fieldOptionsArray = array();
				
				foreach($tracker_fields_info['data'] as $t) {
					$fieldTypes[$t['fieldId']] = $t['type'];
					$fieldOptionsArray[$t['fieldId']] = $t['options_array'];
				}
				
				$ins_fields["data"] = array();
				
				if (isset($linkFieldId) && isset($itemLinkId)) {
					$updateFieldIds[] = $linkFieldId;
					$updateFieldValues[] = $itemLinkId;
				}
				
				//print_r(array($trackerId, $updateFieldIds, $updateFieldValues, $copyFieldIds, $itemIds, $linkFieldId, $itemLinkId, $copies));
				
				for($i = 0; $i < count($updateFieldIds); $i++) {
					$ins_fields["data"][] = array(
						'options_array' => $fieldOptionsArray[$updateFieldIds[$i]], 
						'type' => $fieldTypes[$updateFieldIds[$i]], 
						'fieldId' => $updateFieldIds[$i], 
						'value' => $updateFieldValues[$i]
					);
				}
				
				// CUSTOM: this part is totally custom to store admin notes (how to generalize?)
				if (!empty($_POST['admin_notes_for_copy'])) {
					$ins_fields["data"][] = array(
						'type' => 'a',
						'fieldId' => 118,
						'value' => $_POST['admin_notes_for_copy']
					);
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
			}
			
			return array(
				"items" => $newitems,
				"data" => $newitemsdata,
				"list" => $newitemslist
			);
		}
		
		$return_array = array();
		$itemIds = array();
		
		foreach($trackerId as $key => $trackerIdLeft) {
			//ensure that the fields are set and usable
			if (isset($params["updateFieldIds"]) || isset($params["updateFieldValues"])) {
				$updateFieldIds = $params["updateFieldIds"];
				$updateFieldValues = $params["updateFieldValues"];
				
				foreach($updateFieldIds as $key => $updateFieldId) {
					if (count($updateFieldIds[$key]) != count($updateFieldValues[$key])) {
						return tra('Number of update fields do not match new values');
					}
				}
				
				$copyFieldIds[$key] = array_diff($copyFieldIds[$key], $updateFieldIds);
			}
			
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && $itemId && isset($_POST['copytrackeritem']) && isset($_POST['numberofcopies'])) {
				$copies = (int) $_POST['numberofcopies'];
			} elseif (isset($params['copies_on_load'])) {
				$copies = (int) $params['copies_on_load']; 
			} else {
				$copies = 0;
			}
			
			if ($copies > 0) {
				
				if ($key > 0) {
					$qry = $trkqrylib->tracker_query($trackerIdLeft, $start, $end, null, array($itemId), $search, $params["linkFieldIds"][0]);
					$itemIds = array();
					foreach($qry as $linkedItemIds => $item) {
						$itemIds[] = $linkedItemIds;
					}
				}
				
				$return_array[] = items_copy(
					$trackerId[$key], 
					$updateFieldIds[$key], 
					$updateFieldValues[$key], 
					$copyFieldIds[$key], 
					(
						$key == 0 ? $itemId : $itemIds
					),
					(
						$key == 0 ? null : $params["linkFieldIds"][$key - 1]
					),
					(
						$key == 0 ? null : $return_array[0]['items'][0]
					),
					$copies
				);
			}

		}
		
		$smarty->assign('newitemslist', $return_array['list']);
		
		if ($params['return_array'] == 'y') {
			if (count($return_array) == 1) { //backward compatible
				return $return_array[0];
			} else {
				return $return_array;
			}
		}
	
	}
	
	return $smarty->fetch('wiki-plugins/wikiplugin_trackeritemcopy.tpl');
}
