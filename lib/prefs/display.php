<?php

function prefs_display_list() {
	return array(
		'display_field_order' => array(
			'name' => tra('Fields display order'),
			'type' => 'list',
			'options' => array(
				'DMY' => tra('Day') . ' ' . tra('Month') . ' ' . tra('Year'),
				'DYM' => tra('Day') . ' ' . tra('Year') . ' ' . tra('Month'),
				'MDY' => tra('Month') . ' ' . tra('Day') . ' ' . tra('Year'),
				'MYD' => tra('Month') . ' ' . tra('Year') . ' ' . tra('Day'),
				'YDM' => tra('Year')  . ' ' . tra('Day') . ' ' . tra('Month'),
				'YMD' => tra('Year')  . ' ' . tra('Month') . ' ' . tra('Day'),
			),
		),
		// Used in templates/tiki-admin-include-general.tpl
		'display_timezone' => array(
			'name' => '',
			'type' => '',
		),
	
	);
}
