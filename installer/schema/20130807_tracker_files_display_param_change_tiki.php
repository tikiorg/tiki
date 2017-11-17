<?php

// change Files fields param names and values
// displayImages to displayMode, values 0 to '', 1 to 'img' (cope with 'y' as well)
// imageParams to displayParams and imageParamsForLists to displayParamsForLists


function upgrade_20130807_tracker_files_display_param_change_tiki($installer)
{

	$fields = $installer->fetchAll('SELECT fieldId, type, options FROM tiki_tracker_fields WHERE `type` = \'FG\'');
	$table = $installer->table('tiki_tracker_fields');

	foreach ($fields as $field) {
		// using direct access to the data as param names have changes
		$options = json_decode($field['options'], true);

		if (isset($options['displayImages'])) {
			$options['displayMode'] = $options['displayImages'];
			unset($options['displayImages']);
		}
		if (isset($options['imageParams'])) {
			$options['displayParams'] = $options['imageParams'];
			unset($options['imageParams']);
		}
		if (isset($options['imageParamsForLists'])) {
			$options['displayParamsForLists'] = $options['imageParamsForLists'];
			unset($options['imageParamsForLists']);
		}
		if (empty($options['displayMode'])) {
			$options['displayMode'] = '';
			unset($options['displayImages']);
		} else {
			$options['displayMode'] = 'img';	// only one supperted so far, used to be 1
		}

		$table->update(
			[
				'options' => json_encode($options),
			],
			[
				'fieldId' => $field['fieldId']
			]
		);
	}
}
