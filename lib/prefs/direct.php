<?php

function prefs_direct_list() {
	return array(

	// Used in templates/tiki-admin-include-look.tpl
	'direct_pagination' => array(
			'name' => tra('Use direct pagination links'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-look.tpl
	'direct_pagination_max_middle_links' => array(
			'name' => '',
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-look.tpl
	'direct_pagination_max_ending_links' => array(
			'name' => '',
			'type' => '',
			),
	);	
}
