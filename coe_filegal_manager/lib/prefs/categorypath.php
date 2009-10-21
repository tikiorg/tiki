<?php

function prefs_categorypath_list() {
	return array(

	// Used in templates/tiki-admin-include-category.tpl
	'categorypath_excluded' => array(
			'name' => tra('Exclude these categories'),
			'type' => 'text',
			'size' => '15',
			),
		);
}
