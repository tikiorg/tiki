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
	public static function getTypes()
	{
		return array(
			'l' => array(
				'name' => tr('Items List'),
				'description' => tr('Displays a list of field values from another tracker that has a relation with this tracker.'),
				'readonly' => true,
				'params' => array(
					'trackerId' => array(
						'name' => tr('Tracker ID'),
						'description' => tr('Tracker to list items from'),
						'filter' => 'int',
					),
					'fieldIdThere' => array(
						'name' => tr('Link Field ID'),
						'description' => tr('Field ID containing an item link pointing to the item in this tracker or some other value to be matched.'),
						'filter' => 'int',
					),
					'fieldIdHere' => array(
						'name' => tr('Value Field ID'),
						'description' => tr('Field ID matching the value in the link field ID if the field above is not an item link.'),
						'filter' => 'int',
					),
					'displayFieldIdThere' => array(
						'name' => tr('Fields to display'),
						'description' => tr('Display alternate fields instead of the item title'),
						'filter' => 'int',
						'separator' => '|',
					),
					'linkToItems' => array(
						'name' => tr('Display'),
						'description' => tr('How the link to the items should be rendered'),
						'filter' => 'int',
						'options' => array(
							0 => tr('Value'),
							1 => tr('Link'),
						),
					),
					'status' => array(
						'name' => tr('Status Filter'),
						'description' => tr('Limit the available items to a selected set'),
						'filter' => 'alpha',
						'options' => array(
							'opc' => tr('all'),
							'o' => tr('open'),
							'p' => tr('pending'),
							'c' => tr('closed'),
							'op' => tr('open, pending'),
							'pc' => tr('pending, closed'),
						),
					),
				),
			),
		);
	}

	public static function build($type, $trackerDefinition, $fieldInfo, $itemData)
	{
		return new self($fieldInfo, $itemData, $trackerDefinition);
	}

	function getFieldData(array $requestData = array())
	{
		$trackerId = (int) $this->getOption(0);
		$remoteField = (int) $this->getOption(1);
		$displayFields = $this->getOption(3);
		$generateLinks = (bool) $this->getOption(4);
		$status = $this->getOption(5, 'opc');

		$tracker = Tracker_Definition::get($trackerId);
		$technique = 'value';

		if ($tracker && $field = $tracker->getField($remoteField)) {
			if ($field['type'] == 'r') {
				$technique = 'id';
			}
		}

		$trklib = TikiLib::lib('trk');
		if ($technique == 'id') {
			$items = $trklib->get_items_list($trackerId, $remoteField, $this->getItemId(), $status);
		} else {
			$localField = (int) $this->getOption(2);
			$localValue = $this->getData($localField);

			// Skip nulls
			if ($localValue) {
				$items = $trklib->get_items_list($trackerId, $remoteField, $localValue, $status);
			} else {
				$items = array();
			}
		}

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

