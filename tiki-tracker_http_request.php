<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// TODO - refactor to ajax-services then KILME

// If you make changes here check also Tracker/Field/DynamicList.php
/**
 * Handler for (XHR) requests from the trackerfield type dynamic itemslist. This handler does not save or update anything.
 * It only updates the listfield when the $filterFieldValueHere changes, which happens if it is for example as selection change on a linkfield or category field.
 * 
 * @see lib/core/Tracker/Field/DynamicList.php for furher details.
 */


require_once ('tiki-setup.php');
$trklib = TikiLib::lib('trk');
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}
if ($prefs['feature_trackers'] != 'y') {
	die;
}
$access = TikiLib::lib('access');

$filterFieldIdHere =  isset($_GET["filterFieldIdHere"]) ? $_GET["filterFieldIdHere"] : null;
$trackerIdThere =  isset($_GET["trackerIdThere"]) ? $_GET["trackerIdThere"] : null;
$filterFieldValueHere =  isset($_GET["filterFieldValueHere"]) ? $_GET["filterFieldValueHere"] : null;
$filterFieldIdThere = isset($_GET["filterFieldIdThere"]) ? $_GET["filterFieldIdThere"] : null;
$listFieldIdThere = isset($_GET["listFieldIdThere"]) ? $_GET["listFieldIdThere"] : null;
$statusThere = isset($_GET["statusThere"]) ? $_GET["statusThere"] : null;
// needed when multiple fields are bound to the same selection i.e $filterFieldValueHere
$insertId = isset($_GET["insertId"]) ? $_GET["insertId"] : null;
// needed when the default should be passed back to the frontend
$originalValue = isset($_GET["originalValue"]) ? $_GET["originalValue"] : null;

header('Cache-Control: no-cache');
header('content-type: application/x-javascript');

$json_return = array();
// prepare response, we need to pass back some request data:
//  $insertId: their could be more than one dynamic list field bound to the same $filterFieldValueHere. Need to know Dynamic Item List field we refer to.
// $originalValue: to select the orginalValue (the value currently used for selecting the element), on changes of $filterFieldValueHere  
$json_return['request'] = array(
	'insertId' => $insertId,
	'originalValue' => $originalValue
);
$json_return['response'] = array();

// if we do not have something to compare with we return empty result
if (empty($filterFieldValueHere)) {
	$access->output_serialized($json_return);
	return;
}

// start pre processing
$filterFieldHere = $trklib->get_tracker_field($filterFieldIdHere);
$filterFieldThere = $trklib->get_tracker_field($filterFieldIdThere);
// set defaults - normalize $filterValueHere to $finalFilterValueHere, $filterFieldIdThere to $finalFilterFieldIdThere
$finalFilterFieldIdThere = $filterFieldIdThere;
$finalFilterValueHere = $filterFieldValueHere;

// check which combination of filterFieldTypeHere and filterFieldTypeThere we have and how to deal with it.
// case 'xyz' - xyz tested means: this combinination has been tested and works for both, trackerlistview and item templateview.
switch ($filterFieldHere['type']) {
	case 'e': // category tested
		// only allow category with category. disallow any other category combination.
		if ($filterFieldThere['type'] != 'e') {
			$access->output_serialized($json_return);
			return;
		}
	break;
	
	case 'r': // r = itemlink - disallow itemlink/category, allow itemlink/itemlink, itemlink/simplefield types like text
		switch ($filterFieldThere['type']) {
			case 'r': // r = itemlink tested 
			break;
			
			case 't': // textfield tested
			default:
				$handler = $trklib->get_field_handler($filterFieldHere);
				$optTrackerId = $handler->getOption('trackerId');
				$optFieldId = $handler->getOption('fieldId');
				$finalFilterValueHere = $trklib->get_item_value($optTrackerId, $filterFieldValueHere, $optFieldId);
			break;
		}
	break;

	default:
		// if both field types are unknown but match, we assume that it could work
		if ($filterFieldHere['type'] != $filterFieldThere['type']) {
			$access->output_serialized($json_return);
			return;
		}
	break;
}

// start main processing - $trackerIdThere is not used at all in get_items_list()
$remoteItemIds = $trklib->get_items_list($trackerIdThere, $finalFilterFieldIdThere, $finalFilterValueHere, $statusThere);
$listFieldThere = $trklib->get_tracker_field($listFieldIdThere);
// special handling for itemList field. We would get always the same values on each iteration so we do it only one time.
$itemListFirstRun = true;
foreach ($remoteItemIds as $remoteItemId) {

	$itemInfo = $trklib->get_tracker_item($remoteItemId);

	// @TODO although it should be checked by the fieldhandler, verify that permissions on itemId level are respected. i.e restricted by a category field etc.
	// however: something like this does not work: $permObject = $tikilib->get_perm_object($remoteItemId, 'trackeritem');
	$hasPermission = true;
	if (!$hasPermission) {
		continue;
	}
	
	$listFieldThere = array_merge($listFieldThere, array('value' => $itemInfo[$listFieldIdThere]));
	$handler = $trklib->get_field_handler($listFieldThere, $itemInfo);
	// do not inherit showlinks settings from remote items.
	$context = array('showlinks' => 'n');
	
	// permissions are ok, now get the values depending on the fieldtype of $listFieldIdThere
	switch ($listFieldThere['type']) {

		case 'l': // itemlist tested
			// itemlink can have multiple matches and we would the same matches on each iteration.
			// so we only process one iteration and take the fields as selections out of it.
			if (!$itemListFirstRun) {
				break;
			}

			$valueFields = $handler->getFieldData();
			if (is_array($valueFields)) {
				// return each item of that list - requires match in DynamicList.php renderInnerOutput()
				// note: we save the itemId of the item selected out of the list, not the value. Allows to keep the links consistant on changing values.
				foreach ( $valueFields['items'] as $valueField => $labelField) {
					$json_return['response'][] = array($valueField, $labelField);
				}
			}
			$itemListFirstRun = false;
		break;

		case 'r': // itemlink tested
			$valueField = $handler->getFieldData();
			$labelField = $handler->renderOutput($context);
			$json_return['response'][] = array($valueField['value'], $labelField);
		break;
			
		case 'e': // category tested
			// array selected_categories etc.
			$valueField = $handler->getFieldData();
			// for some reason, need to apply the values back, oterwise renderOutput does not return a value - bug?
			$listFieldThere = array_merge($listFieldThere, $valueField);
			$handler = $trklib->get_field_handler($listFieldThere, $itemInfo);
			$labelField = $handler->renderOutput($context);
			// we return all categories per itemId, without html, comma separated
			$labelField = str_replace('<br/>', ', ', $labelField);
			$json_return['response'][] = array($remoteItemId, $labelField);
		break;
		
		// other fieldtypes
		case 't': // textfield tested
		default:		
			$labelField = $handler->renderOutput($context);
			$json_return['response'][] = array($remoteItemId, $labelField);
		break;
	} // switch

} // foreach
	
$access->output_serialized($json_return);
return;