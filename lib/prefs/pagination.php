<?php

function prefs_pagination_list() {
	return array(

	// Used in templates/tiki-admin-include-look.tpl
	'pagination_firstlast' => array(
			'name' => tra("Display 'First' and 'Last' links"),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-look.tpl
	'pagination_hide_if_one_page' => array(
			'name' => tra('Hide pagination when there is only one page'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-look.tpl
	'pagination_icons' => array(
			'name' => tra('Use Icons'),
			'type' => '',
			),
	
	// Used in templates/tiki-admin-include-look.tpl
	'pagination_fastmove_links' => array(
			'name' => tra('Display fast move links (by 10 percent of the total number of pages) '),
			'type' => '',
			),
	
	);	
}
