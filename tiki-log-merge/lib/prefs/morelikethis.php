<?php

function prefs_morelikethis_list() {
	return array(

		// Used in templates/tiki-admin-include-freetags.tpl
		'morelikethis_algorithm' => array(
			'name' => tra('"More Like This" algorithm'),
			'type' => 'list',
			'options' => array(
				'basic' => tra('Basic'),
				'weighted' => tra('Weighted'),
			),
		),
		'morelikethis_basic_mincommon' => array(
			'name' => tra('Minimum amount of tags in common'),
			'type' => 'list',
			'options' => array(
				'1' => tra('1'),
				'2' => tra('2'),
				'3' => tra('3'),
				'4' => tra('4'),
				'5' => tra('5'),
				'6' => tra('6'),
				'7' => tra('7'),
				'8' => tra('8'),
				'9' => tra('9'),
				'10' => tra('10'),
			),
		),
	);	
}
