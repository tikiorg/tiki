<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_morelikethis_list()
{
	return array(

		// Used in templates/tiki-admin-include-freetags.tpl
		'morelikethis_algorithm' => array(
			'name' => tra('"More Like This" algorithm'),
            'description' => tra(''),
			'type' => 'list',
			'options' => array(
				'basic' => tra('Basic'),
				'weighted' => tra('Weighted'),
			),
			'default' => 'basic',
		),
		'morelikethis_basic_mincommon' => array(
			'name' => tra('Minimum number of tags in common'),
            'description' => tra(''),
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
			'default' => '2',
		),
	);	
}
