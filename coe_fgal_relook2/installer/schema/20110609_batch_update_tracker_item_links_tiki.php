<?php

function upgrade_20110609_batch_update_tracker_item_links_tiki($installer)
{
	$fields = $installer->fetchMap("SELECT fieldId, options FROM tiki_tracker_fields WHERE type = 'r'");

	foreach ($fields as $fieldId => $options) {
		$options = explode(',', $options);

		if (isset($options[1])) {
			$remoteFieldId = $options[1];

			$installer->query("
				UPDATE tiki_tracker_item_fields r
					INNER JOIN tiki_tracker_item_fields l ON r.value = l.value AND l.fieldId = ?
					SET r.value = l.itemId
					WHERE r.fieldId = ?
			", array($remoteFieldId, $fieldId));
		}
	}
}

