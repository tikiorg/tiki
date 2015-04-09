<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_permission($params, $content, $smarty, &$repeat)
{
	if ( $repeat ) return;

	// Removing and Modifying a tracker item require a special permissions check
	if ( $params['type'] == 'trackeritem' ) {
		$removePerms = ['remove_tracker_items','remove_tracker_items_pending','remove_tracker_items_closed'];
		$modifyPerms = ['modify_tracker_items','modify_tracker_items_pending','modify_tracker_items_closed'];

		$trklib = TikiLib::lib('trk');
		$itemInfo = $trklib->get_tracker_item($params['object']);

		if (!$itemInfo){
			return ""; //invalid tracker item.
		}

		$itemObject = Tracker_Item::fromInfo($itemInfo);

		if ( in_array($params['name'],$removePerms) ){
			if ($itemObject->canRemove()) {
				return $content;
			}
		} elseif ( in_array($params['name'], $modifyPerms) ) {
			if ( $itemObject->canModify() ) {
				return $content;
			}
		}
	}

	//Standard permissions check
	$context = array();

	if ( isset( $params['type'], $params['object'] ) ) {
		$context['type'] = $params['type'];
		$context['object'] = $params['object'];
	}

	$perms = Perms::get($context);
	$name = $params['name'];

	if ( $perms->$name ) {
		return $content;
	} else {
		return '';
	}
}
