<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_captcha_list()
{
    return array (
		'captcha_wordLen' => array(
			'name' => tra('Word length of the CAPTCHA image'),
            'description' => tra('Word length of the CAPTCHA image.').' '.tra('Default:'). '6',
			'type' => 'text',
			'default' => 6,
		),
		'captcha_width' => array(
			'name' => tra('Width of the CAPTCHA image in pixels'),
            'description' => tra('Width of the CAPTCHA image in pixels.').' '.tra('Default:'). '180',
			'type' => 'text',
			'default' => 180,
		),
		'captcha_noise' => array(
			'name' => tra('Level of noise of the CAPTCHA image'),
            'description' => tra('Level of noise of the CAPTCHA image.').' '.tra('Choose a smaller number for less noise and easier reading.').' '.tra('Default:'). '100',
            'type' => 'text',
            'default' => 100,
		),
		'captcha_questions_active' => array(
			'name' => tra('CAPTCHA Questions'),
			'description' => tra('Requires anonymous visitors to enter the answer to a question .'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_antibot',
			),
			'default' => 'n',
		),
		'captcha_questions' => array(
			'name' => tra('CAPTCHA Questions and answers'),
			'description' => tra('Add some simple questions that only humans should be able to answer, in the format: "Question?: Answer" with one per line'),
			'hint' => tra('One question per line with a colon separating the question and answer'),
			'type' => 'textarea',
			'size' => 6,
			'dependencies' => array(
				'captcha_questions_active',
			),
			'default' => '',
		),
	);
}
