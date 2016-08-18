<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-tracker_http_request_itemslist.php 48614 2013-11-21 05:48:54Z nkoth $

// TODO - refactor to ajax-services then KILME


/**
 * Handler for (XHR) requests from the trackerfield type itemslist.
 * @see lib/core/Tracker/Field/ItemsList.php for furher details. 
 */

require_once ('tiki-setup.php');
$trklib = TikiLib::lib('trk');
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
header('Cache-Control: no-cache');
header('content-type: application/x-javascript');
Perms::bulk(array( 'type' => 'tracker' ), 'object', $arrayTrackerId);

$json_return = array();

for ($index = 0, $count_arrayTrackerId = count($arrayTrackerId); $index < $count_arrayTrackerId; $index++) {
	$arrayFieldlistMultiple = explode('|',$arrayFieldlist[$index]);
	$tikilib->get_perm_object($arrayTrackerId[$index], 'tracker');
	$filtervalue = utf8_encode(rawurldecode($_GET["filtervalue"]));

	if (!empty($_GET['item'])) { // we want the value of field filterfield for item 
		$filtervalue = $trklib-> get_item_value($arrayTrackerId[$index], $arrayItem[$index], $arrayFilterfield[$index]);
		if (!$filtervalue) {
			$otherField = $trklib->get_tracker_field($arrayFilterfield[$index]);
			if ($otherField['type'] == 'r') {		// filterFieldIdThere is itemlink, so get the filtervalue from what that links to
				$filtervalue = $trklib->get_item_value($otherField['options_array'][0], $arrayItem[$index], $otherField['options_array'][1]);
			} else if ($otherField['type'] == 'u') {
				$exactvalue = $arrayItem[$index]; 
			}
		}
	}

	if ($filtervalue || !empty($exactvalue)) {
		$xfields = $trklib->list_tracker_fields($arrayTrackerId[$index], 0, -1, 'name_asc', '','','',$arrayFieldlistMultiple);
		$listfields = array();
		foreach ($xfields["data"] as $idfi => $val) {
			$fid = $xfields["data"][$idfi]["fieldId"];
			$dfid = $idfi;
			$listfields[$fid]['fieldId'] = $fid;
			$listfields[$fid]['type'] = $xfields["data"][$dfid]["type"];
			$listfields[$fid]['name'] = $xfields["data"][$dfid]["name"];
			$listfields[$fid]['options'] = $xfields["data"][$dfid]["options"];
			$listfields[$fid]['options_array'] = explode(',', $xfields["data"][$dfid]["options"]);
			$listfields[$fid]['isMain'] = $xfields["data"][$dfid]["isMain"];
			$listfields[$fid]['isTblVisible'] = $xfields["data"][$dfid]["isTblVisible"];
			$listfields[$fid]['isHidden'] = $xfields["data"][$dfid]["isHidden"];
			$listfields[$fid]['isSearchable'] = $xfields["data"][$dfid]["isSearchable"];
		}

		if ($filtervalue) {
			//$items = $trklib->list_items($arrayTrackerId[$index], 0, -1, '', $listfields, $arrayFilterfield[$index], $filtervalue, $arrayStatus[$index]);
			$items = $trklib->list_items($arrayTrackerId[$index], 0, -1, '', $listfields, $arrayFilterfield[$index], '', $arrayStatus[$index], '' ,$filtervalue);
		} elseif (!empty($exactvalue)) {
			$items = $trklib->list_items($arrayTrackerId[$index], 0, -1, '', $listfields, $arrayFilterfield[$index], $filtervalue, $arrayStatus[$index], '', $exactvalue);
		}

		foreach ($items['data'] as $item) {
			//$field = $item['field_values'][0];
			$valueField = $item['itemId'];
			$labelField = '';
			$context = array('showlinks' => 'n');
			$prefix = '';
			// if multiple display fields are selected, we do not want them listed as separate entries like multiple items.
			// only items should be separate entries. thus multiple displayfields need to get merged.
			foreach ($item['field_values'] as $field ) {
				if ($field['type'] === 'e' && !empty($field['list'])) {		// for category fields get the label not the value
																			// possibly required for other field types after 11.0?
					$label = $trklib->get_field_handler($field, $item)->renderOutput($context);
					$label = str_replace('<br/>', ',', $label);				// categories can be many so replace html
				} else if ($field['type'] == 'r') {
					$label = $trklib->get_field_handler($field, $item)->renderOutput($context);
				} else {
					$label = $trklib->get_field_handler($field, $item)->renderOutput($context);

				}
				
				$labelField .= $prefix. $label; 
				if (!$prefix) {
					$prefix = ' ';
				}
				
			}
			$json_return[] = array($valueField, $labelField);
		}

	}

}

$access = TikiLib::lib('access');
$access->output_serialized($json_return);

