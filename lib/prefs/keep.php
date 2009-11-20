<?php

function prefs_keep_list() {
	return array(

	// Used in templates/tiki-admin-include-wiki.tpl
	'keep_versions' => array(
			'name' => tra('Never delete versions younger than'),
			'type' => '',
			),
	);	
}
