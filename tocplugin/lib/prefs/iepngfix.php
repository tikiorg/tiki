<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_iepngfix_list()
{
	return array(
		'iepngfix_selectors' => array(
			'name' => tra('CSS selectors to be fixed'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '30',
			'hint' => tra('Separate multiple elements with a comma (,)'),
			'default' => '.sitelogo a img',
		),
		'iepngfix_elements' => array(
			'name' => tra('HTML DOM Elements to be fixed'),
            'description' => tra(''),
			'type' => 'text',
			'size' => '30',
			'hint' => tra('Separate multiple elements with a comma (,)'),
			'default' => '',
		),
	);	
}
