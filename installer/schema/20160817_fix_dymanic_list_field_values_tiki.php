<?php

/**
 * In r54193 (Tiki 14.x) DynamicList fields behaviour was changed breaking existing data (thanks) wihtout an upgrade
 * script, so belatedly, this is it.
 *
 * Take all DynamicList fields (type=w) and for each item tiki_tracker_item_fields instance change the value
 * from being the label to be the itemId of the linked list
 *
 * @param $installer
 */

function upgrade_20160817_fix_dymanic_list_field_values_tiki($installer)
{
	global $prefs;
	$prefs['trackerfield_dynamiclist'] = 'y';	// needed for the fieldFactory when in the installer

	/** @var \TrackerLib $trklib */
	$trklib = TikiLib::lib('trk');

	$trackerFields = $installer->table('tiki_tracker_fields');
	$trackerItemFields = $installer->table('tiki_tracker_item_fields');

	$fields = $trackerFields->fetchAll($trackerFields->all(), ['type' => $trackerFields->exactly('w')]);

	foreach ($fields as $field) {
		$itemFields = $trackerItemFields->fetchAll(['itemId', 'value'], ['fieldId' => $field['fieldId']]);
		$options = json_decode($field['options'], true);
		$definition = Tracker_Definition::get($options['trackerId']);
		$fieldFactory = $definition->getFieldFactory();


		foreach($itemFields as $itemField) {

			$item_info = $trklib->get_tracker_item($itemField['itemId']);
			$handler = $fieldFactory->getHandler($field, $item_info);

			$trackerIdThere = $handler->getOption('trackerId');
			$listFieldIdThere = $handler->getOption('listFieldIdThere');

			$remoteItemId = $trklib->get_item_id($trackerIdThere, $listFieldIdThere, $itemField['value']);

			if ($remoteItemId) {
				$trackerItemFields->update(
					[
						'value' => $remoteItemId
					],
					[
						'fieldId' => $field['fieldId'],
						'itemId' => $itemField['itemId'],
					]
				);
			}

		}

	}

}

