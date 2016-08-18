<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_dailyreports_list()
{
	return array(
		'dailyreports_enabled_for_new_users' => array(
			'name' => tr('Enable daily reports for new users'),
            'description' => tra('Determines if daily reports will be automatically enabled for new users. Default is "no".'),
			'type' => 'flag',
			'default' => 'n',
			'help' => 'Daily+Reports',
			'tags' => array('basic','tiki reporting feature'),
		),
	);
}
