<?php

function prefs_iepngfix_list() {
	return array(
		'iepngfix_selectors' => array(
			'name' => 'CSS selectors to be fixed',
			'type' => 'text',
			'size' => '30',
			'hint' => tra('Separate multiple elements with a comma (,)'),
		),
		'iepngfix_elements' => array(
			'name' => 'HTMLDomElements to be fixed',
			'type' => 'text',
			'size' => '30',
			'hint' => tra('Separate multiple elements with a comma (,)'),
		),
	);	
}
