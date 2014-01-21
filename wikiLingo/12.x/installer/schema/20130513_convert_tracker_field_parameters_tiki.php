<?php

function upgrade_20130513_convert_tracker_field_parameters_tiki($installer)
{
	// Using an old version of the definition could be critical here, so making sure
	// a fresh one is used
	$cachelib = TikiLib::lib('cache');
	$oldCache = $cachelib->replaceImplementation(new CacheLibNoCache);

	$fields = $installer->fetchAll('SELECT fieldId, type, options FROM tiki_tracker_fields');
	$factory = new Tracker_Field_Factory;
	$table = $installer->table('tiki_tracker_fields');

	foreach ($fields as $field) {
		$info = $factory->getFieldInfo($field['type']);
		$options = Tracker_Options::fromString($field['options'], $info);

		$table->update(
			array(
				'options' => $options->serialize(),
			),
			array(
				'fieldId' => $field['fieldId']
			)
		);
	}

	$cachelib->replaceImplementation($oldCache);
}

