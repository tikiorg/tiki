<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
				'help' => 'Items List and Item Link Tracker Fields',
				'prefs' => array('trackerfield_itemslist'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'trackerId' => array(
						'name' => tr('Tracker ID'),
						'description' => tr('Tracker to list items from'),
						'filter' => 'int',
					),
					'fieldIdThere' => array(
						'name' => tr('Link Field ID'),
						'description' => tr('Field ID from the other tracker containing an item link pointing to the item in this tracker or some other value to be matched.'),
						'filter' => 'int',
					),
					'fieldIdHere' => array(
						'name' => tr('Value Field ID'),
						'description' => tr('Field ID from this tracker matching the value in the link field ID from the other tracker if the field above is not an item link.'),
						'filter' => 'int',
					),
					'displayFieldIdThere' => array(
						'name' => tr('Fields to display'),
						'description' => tr('Display alternate fields from the other tracker instead of the item title'),
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

	function getFieldData(array $requestData = array())
	{
		$items = $this->getItemIds();
		$list = $this->getItemLabels($items);
		return array(
			'value' => '',
			'items' => $list,
		);
	}

	function renderInput($context = array())
	{
		return tr('Read Only');
	}

	function renderOutput( $context = array() )
	{
		if ($context['list_mode'] === 'csv') {
			return $this->getConfiguration('value');
		} else {
			if (isset($context['search_render']) && $context['search_render'] == 'y') {
				$items = $this->getData($this->getConfiguration('fieldId'));
			} else {
				$items = $this->getItemIds();
			}

			$list = $this->getItemLabels($items);
			return $this->renderTemplate(
				'trackeroutput/itemslist.tpl',
				$context,
				array(
					'links' => (bool) $this->getOption(4),
					'raw' => (bool) $this->getOption(3),
					'itemIds' => implode(',', $items),
					'items' => $list,
					'num' => count($list),
				)
			);
		}
	}

	function getDocumentPart($baseKey, Search_Type_Factory_Interface $typeFactory)
	{
		$items = $this->getItemIds();

		$list = $this->getItemLabels($items);
		$listtext = implode(' ', $list);

		return array(
			$baseKey => $typeFactory->multivalue($items),
			"{$baseKey}_text" => $typeFactory->plaintext($listtext),
		);
	}

	function getProvidedFields($baseKey)
	{
		return array(
			$baseKey,
			"{$baseKey}_text",
		);
	}

	function getGlobalFields($baseKey)
	{
		return array();
	}

	private function getItemIds()
	{
		$trackerId = (int) $this->getOption(0);
		$remoteField = (int) $this->getOption(1);
		$displayFields = $this->getOption(3);
		$status = $this->getOption(5, 'opc');

		$tracker = Tracker_Definition::get($trackerId);
		$technique = 'value';

		if ($tracker && ($field = $tracker->getField($remoteField)) && !$this->getOption(2)) {
			if ($field['type'] == 'r' || $field['type'] == 'q' && $field['options_array'][3] == 'itemId') {
				$technique = 'id';
			}
		}

		$trklib = TikiLib::lib('trk');
		if ($technique == 'id') {
			$items = $trklib->get_items_list($trackerId, $remoteField, $this->getItemId(), $status);
		} else {
			$localField = (int) $this->getOption(2);
			$localValue = $this->getData($localField);
			if (!$localValue) {
				// in some cases e.g. pretty tracker $this->getData($localField) is not reliable as the info is not there
				$localValue = $trklib->get_item_value($trackerId, $this->getItemId(), $localField);
			}
			$localFieldDef = $this->getTrackerDefinition()->getField($localField);
			if ($localFieldDef['type'] == 'r' && isset($localFieldDef['options_array'][0]) && isset($localFieldDef['options_array'][1])) {
				$localValue = $trklib->get_item_value($localFieldDef['options_array'][0], $localValue, $localFieldDef['options_array'][1]);
			}
			// Skip nulls
			if ($localValue) {
				$items = $trklib->get_items_list($trackerId, $remoteField, $localValue, $status);
			} else {
				$items = array();
			}
		}

		return $items;
	}

	private function getItemLabels($items)
	{
		$displayFields = $this->getOption(3);
		$trackerId = (int) $this->getOption(0);
		$status = $this->getOption(5, 'opc');

		$definition = Tracker_Definition::get($trackerId);
		if (! $definition) {
			return array();
		}

		$list = array();
		$trklib = TikiLib::lib('trk');
		foreach ($items as $itemId) {
			if ($displayFields) {
				$list[$itemId] = $trklib->concat_item_from_fieldslist($trackerId, $itemId, $displayFields, $status, ' ');
			} else {
				$list[$itemId] = $trklib->get_isMain_value($trackerId, $itemId);
			}
		}

		return $list;
	}
}

