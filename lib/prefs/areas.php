<?php

function prefs_areas_list() {
	return array(
		'areas_root' => array(
			'name' => tra('Areas root category id'),
			'description' => tra('Id of category whose children are bound to a perspective by areas.'),
			'type' => 'text',
			'size' => '10',
			'filter' => 'digits',
			'default' => 0,	
			'help' => 'Areas',
			'dependencies' => array(
				'feature_areas',
			),
		),
	);

}

