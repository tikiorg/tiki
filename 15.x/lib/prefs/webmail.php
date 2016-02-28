<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_webmail_list()
{
	return array(
		'webmail_view_html' => array(
			'name' => tra('Allow viewing HTML emails?'),
			'type' => 'flag',
			'default' => 'y',
		),
		'webmail_max_attachment' => array(
			'name' => tra('Maximum size for each attachment'),
			'type' => 'list',
			'options' => array(
				'500000' => tra('500Kb'),
				'1000000' => tra('1Mb'),
				'1500000' => tra('1.5Mb'),
				'2000000' => tra('2Mb'),
				'2500000' => tra('2.5Mb'),
				'3000000' => tra('3Mb'),
				'100000000' => tra('Unlimited'),
			),
			'default' => 1500000,
		),
		'webmail_quick_flags' => array(
			'name' => tra('Include a flag by all messages to quickly flag/unflag them?'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
