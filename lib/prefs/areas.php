<?php

function prefs_areas_list() {
	return array(
		'areas_root' => array(
			'name' => tra('Areas-Category root id'),
			'description' => tra('Id of category whose children are used by areas/perspective binder.'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'default' => 1,	
			'help' => 'Areas',
			'dependencies' => array(
				'feature_areas',
			),
		),
	);

}

