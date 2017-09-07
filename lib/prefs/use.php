<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_use_list() 
{
	return array (
		'use_load_threshold' => array(
			'name' => tra('Close site when server load is above the threshold'),
			'description' => tra('Use this option to “close” your Tiki when the server load exceeds a specific threshold. Only users with specific permission will be allowed to log in. Use Maximum average server load threshold in the last minute to define the maximum server load. Use the Message to display to specify the message that visitors will see when attempting to access your site.'),
			'type' => 'flag',
			'help' => 'Site-Access#Close_site',
			'perspective' => false,
			'default' => 'n',
		),
		'use_proxy' => array(
			'name' => tra('Use proxy'),
			'description' => tra('Specify if Tiki requires a proxy to access the internet. If enabled, you can specify your proxy Host name (either with or without the http:// prefix), Port settings, Username, and Password.'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'n',
		),

		// FIXME: These 2 have misleading names. Actions will use context menus even if (only) one is disabled. Do we really need to have 2 preferences for this? Surely if we have context menus they should have both icons and text. Chealer 2017-07-03
		'use_context_menu_icon' => array(
			'name' => tra('Use context menus for actions (icons)'),
			'type' => 'flag',
			'hint' => tr('Currently used in File Galleries only.'),
			'default' => 'y',
		),
		'use_context_menu_text' => array(
			'name' => tra('Use context menus for actions (text)'),
			'type' => 'flag',
			'hint' => tr('Currently used in File Galleries only.'),
			'default' => 'y',
		),
	);
}
