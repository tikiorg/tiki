<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// TODO - refactor to ajax-services then KILME

require_once ('tiki-setup.php');
include_once ('lib/trackers/trackerlib.php');
if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once ('lib/categories/categlib.php');
	}
}
if ($prefs['feature_trackers'] != 'y') {
	die;
}
$arrayTrackerId = explode(',', $_GET["trackerIdList"]);
$arrayMandatory = explode(',', $_GET["mandatory"]);
if (isset($_GET['selected'])) $arraySelected = explode(',', utf8_encode(rawurldecode($_GET["selected"])));
$arrayFieldlist = explode(',', $_GET["fieldlist"]);
$arrayFilterfield = explode(',', $_GET["filterfield"]);
$arrayStatus = explode(',', $_GET["status"]);
$arrayItem = explode(',', $_GET['item']);
$sort_mode = 'f_' . $arrayFieldlist[0] . '_asc';
header('Cache-Control: no-cache');
header('content-type: application/x-javascript');
Perms::bulk(array( 'type' => 'tracker' ), 'object', $arrayTrackerId);

$json_return = array();

for ($index = 0, $count_arrayTrackerId = count($arrayTrackerId); $index < $count_arrayTrackerId; $index++) {
	$tikilib->get_perm_object($arrayTrackerId[$index], 'tracker');

	if (!isset($_GET['selected'])) {
		$selected = '';
		$filtervalue = utf8_encode(rawurldecode($_GET["filtervalue"]));
	} elseif (isset($_GET["filtervalue"])) {
		$selected = $arraySelected[$index];
		$filtervalue = $_GET["filtervalue"];
	}
	if (!empty($_GET['item'])) { // we want the value of field filterfield for item 
		$filtervalue = $trklib-> get_item_value($arrayTrackerId[$index], $arrayItem[$index], $arrayFilterfield[$index]);

		if (!$filtervalue) {
			$otherField = $trklib->get_tracker_field($arrayFilterfield[$index]);
			if ($otherField['type'] == 'r') {		// filterFieldIdThere is itemlink, so get the filtervalue from what that links to
				$filtervalue = $trklib-> get_item_value($otherField['options_array'][0], $arrayItem[$index], $otherField['options_array'][1]);
			} else if ($otherField['type'] == 'u') {
				$exactvalue = $arrayItem[$index]; 
			}
		}
	}

	if ($filtervalue || !empty($exactvalue)) {
		$xfields = $trklib->list_tracker_fields($arrayTrackerId[$index], 0, -1, 'name_asc', '');
		foreach ($xfields["data"] as $idfi => $val) {
			if ($xfields["data"][$idfi]["fieldId"] == $arrayFieldlist[$index]) {
				$fid = $xfields["data"][$idfi]["fieldId"];
				$dfid = $idfi;
				break;
			}
		}
		$listfields = array();
		$listfields[$fid]['fieldId'] = $fid;
		$listfields[$fid]['type'] = $xfields["data"][$dfid]["type"];
		$listfields[$fid]['name'] = $xfields["data"][$dfid]["name"];
		$listfields[$fid]['options'] = $xfields["data"][$dfid]["options"];
		$listfields[$fid]['options_array'] = explode(',', $xfields["data"][$dfid]["options"]);
		$listfields[$fid]['isMain'] = $xfields["data"][$dfid]["isMain"];
		$listfields[$fid]['isTblVisible'] = $xfields["data"][$dfid]["isTblVisible"];
		$listfields[$fid]['isHidden'] = $xfields["data"][$dfid]["isHidden"];
		$listfields[$fid]['isSearchable'] = $xfields["data"][$dfid]["isSearchable"];

		if ($filtervalue) {
			$items = $trklib->list_items($arrayTrackerId[$index], 0, -1, $sort_mode, $listfields, $arrayFilterfield[$index], $filtervalue, $arrayStatus[$index]);
		} elseif (!empty($exactvalue)) {
			$items = $trklib->list_items($arrayTrackerId[$index], 0, -1, $sort_mode, $listfields, $arrayFilterfield[$index], $filtervalue, $arrayStatus[$index], '', $exactvalue);
		}

		if ($arrayMandatory[$index] != 'y') {
			$json_return[] = array('', '');
		}

		foreach ($items['data'] as $item) {
			$field = $item['field_values'][0];
			if ($field['type'] === 'e' && !empty($field['list'])) {		// for category fields get the label not the value
																		// possibly required for other field types after 11.0?
				$label = $trklib->get_field_handler($field, $item)->renderOutput();
				$label = str_replace('<br/>', ',', $label);				// categories can be many so replace html
				$json_return[] = array($field['value'], $label);
			} else if ($field['type'] == 'r') {
				$label = $trklib->get_field_handler($field, $item)->renderOutput();
				$json_return[] = array($field['value'], $label);
			} else {
				$json_return[] = array($field['value'], $field['value']);
			}
		}
	}
}
global $access; include_once 'lib/tikiaccesslib.php';
$access->output_serialized($json_return);

