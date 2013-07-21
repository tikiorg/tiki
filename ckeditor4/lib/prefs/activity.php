<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_activity_list()
{
	return array(
		'activity_custom_events' => array(
			'name' => tr('Custom Activities'),
			'description' => tr('Allows to define custom behaviors on top of internal events.'),
			'help' => 'Custom+Activity',
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
