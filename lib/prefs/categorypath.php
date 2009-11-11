<?php

function prefs_categorypath_list() {
	return array(

	'categorypath_excluded' => array(
			'name' => tra('Exclude these categories'),
			'hint' => tra('Separate category IDs with a comma (,)'),
			'type' => 'text',
			'size' => '15',
			),
		);
}
