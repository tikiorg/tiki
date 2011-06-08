<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for ItemsList
 * 
 * Letter key: ~l~
 *
 */
class Tracker_Field_ItemsList extends Tracker_Field_Abstract
{
	function getFieldData(array $requestData = array())
	{
		$trackerId = (int) $this->getOption(0);
		$remoteField = (int) $this->getOption(1);
		$displayFields = $this->getOption(3);
		$generateLinks = (bool) $this->getOption(4);
		$status = $this->getOption(5, 'opc');

		$trklib = TikiLib::lib('trk');
		$items = $trklib->get_items_list($trackerId, $remoteField, $this->getItemId(), $status);

		$list = array();
		foreach ($items as $itemId) {
			if ($displayFields) {
				$list[$itemId] = $trklib->concat_item_from_fieldslist($trackerId, $itemId, $displayFields, $status, ' ');
			} else {
				$list[$itemId] = $trklib->get_isMain_value($trackerId, $itemId);
			}
		}
		
		return array(
			'value' => '',
			'itemIds' => implode(',', $items),
			'items' => $list,
			'links' => $generateLinks,
		);
	}
	
	function renderInput($context = array())
	{
		return tr('Read Only');
	}

	function renderOutput( $context = array() ) {
		if ($context['list_mode'] === 'csv') {
			return $this->getConfiguration('value');
		} else {
			return $this->renderTemplate('trackeroutput/itemslist.tpl', $context);
		}
	}
}

