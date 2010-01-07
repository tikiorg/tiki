<?php

function prefs_pass_list() {
	return array(
		'pass_chr_num' => array(
			'name' => tra('Require characters and numerals'),
			'type' => 'flag',
		),
		'pass_due' => array(
			'name' => tra('Password expires after'),
			'type' => 'text',
			'size' => 5,
			'filter' => 'digits',
			'shorthint' => tra('days'),
			'hint' => tra('Use "-1" for never'),
		),
	);	
}
