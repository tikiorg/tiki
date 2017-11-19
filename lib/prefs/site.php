<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_site_list()
{
	global $prefs;

	$available_layouts = TikiLib::lib('css')->list_user_selectable_layouts(isset($prefs['site_theme']) ? $prefs['site_theme'] : '', isset($prefs['theme_option']) ? $prefs['theme_option'] : '');
	$available_admin_layouts = TikiLib::lib('css')->list_user_selectable_layouts(isset($prefs['site_theme_admin']) ? $prefs['site_theme_admin'] : '', isset($prefs['theme_option_admin']) ? $prefs['theme_option_admin'] : '');

	return  [
		'site_closed' => [
			'name' => tra('Close site'),
			'description' => tra('Use this setting to “close” your Tiki (such as for maintenance). Users attempting to access your site will see only a login box. Only users with specific permission will be allowed to log in.
Use the Message to display to specify the message that visitors will see when attempting to access your site.'),
			'type' => 'flag',
			'help' => 'Site-Access#Close_site',
			'perspective' => false,
			'tags' => ['basic'],
			'default' => 'n',
		],
		'site_closed_msg' => [
			'name' => tra('Message'),
			'type' => 'text',
			'perspective' => false,
			'dependencies' => [
				'site_closed',
			],
			'default' => tra('Site is closed for maintenance; please come back later.'),
			'tags' => ['basic'],
		],
		'site_busy_msg' => [
			'name' => tra('Message'),
			'type' => 'text',
			'perspective' => false,
			'dependencies' => [
				'use_load_threshold',
			],
			'default' => tra('Server is currently too busy; please come back later.'),
		],
		'site_crumb_seper' => [
			'name' => tra('Locations (breadcrumbs)'),
			'type' => 'text',
			'hint' => tr('Examples:  » / >  : -> →'),
			'size' => '5',
			'default' => '»',
		],
		'site_nav_seper' => [
			'name' => tra('Choices'),
			'type' => 'text',
			'hint' => tr('Examples: | / ¦  :'),
			'size' => '5',
			'default' => '|',
		],
		'site_title_location' => [
			'name' => tra('Browser title position'),
			'description' => tra('Position of the browser title in the full browser bar relative to the current page\'s descriptor.'),
			'type' => 'list',
			'options' => [
				'after' => tra('After current page\'s descriptor'),
				'before' => tra('Before current page\'s descriptor'),
				'none' => tra('No browser title, only current page\'s descriptor'),
				'only' => tra('Only browser title, no current page\'s descriptor'),
			],
			'tags' => ['basic'],
			'default' => 'before',
		],
		'site_title_breadcrumb' => [
			'name' => tra('Browser title display mode'),
			'description' => tra('When breadcrumbs are used, method to display the browser title.'),
			'type' => 'list',
			'options' => [
				'invertfull' => tra('Most-specific first'),
				'fulltrail' => tra('Least-specific first (site)'),
				'pagetitle' => tra('Current only'),
				'desc' => tra('Description'),
			],
			'tags' => ['advanced'],
			'default' => 'invertfull',
		],
		'site_favicon_enable' => [
			'name' => tr('Favicons'),
			'description' => tra('You may drop custom favicons into /themes/yourtheme/favicons, or use the default tiki icons.'),
			'type' => 'flag',
			'default' => 'y',
			'help' => 'Favicon',
		],
		'site_terminal_active' => [
			'name' => tra('Site terminal'),
			'description' => tra('Allows users to be directed to a specific perspective depending on the origin IP address. Can be used inside intranets to use different configurations for users depending on their departements or discriminate people in web contexts. Unspecified IPs will fall back to default behavior, including multi-domain handling. Manually selected perspectives take precedence over this.'),
			'type' => 'flag',
			'dependencies' => [
				'feature_perspective',
			],
			'default' => 'n',
		],
		'site_terminal_config' => [
			'name' => tra('Site terminal configuration'),
			'description' => tra('Provides the mapping from subnets to perspective.'),
			'type' => 'textarea',
			'perspective' => false,
			'size' => 10,
			'hint' => tra('One per line. Network prefix in CIDR notation (address/mask size), separated by comma with the perspective ID.') . ' ' . tra('Example:') . ' 192.168.12.0/24,12',
			'default' => '',
		],
		'site_google_analytics_account' => [
			'name' => tr('Google Analytics account number'),
			'description' => tra('The account number for the site. The account number from Google is something like UA-XXXXXXX-YY.'),
			'type' => 'text',
			'size' => 15,
			'default' => '',
			'hint' => ' Enter only XXXXXXX-YY (without the UA)',
			'dependencies' => [
				'wikiplugin_googleanalytics',
			],
		],
		'site_google_analytics_gtag' => [
			'name' => tr('Google Global Site Tag Mode'),
			'description' => tra('Use the newer Google Global Site Tag (gtag.js) as opposed to the previous ga.js.'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => [
				'site_google_analytics_account',
			],
		],
		'site_google_credentials' => [
			'name' => tra('Google authentication credentials file'),
			'description' => tr('Path to your Google Service Account credentials JSON file.'),
			'type' => 'text',
			'size' => 30,
			'default' => '',
			'warning' => 'Must be kept private and not accessible on the internet directly',
		],
		'site_layout' => [
			'name' => tr('Site layout'),
			'description' => tr('Changes the template for the overall site layout'),
			'type' => 'list',
			'default' => 'basic',
			'help' => 'Site Layout',
			'tags' => ['advanced'],
			'options' => $available_layouts,
		],
		'site_layout_admin' => [
			'name' => tr('Admin layout'),
			'description' => tr('Changes the layout templates for admin pages'),
			'type' => 'list',
			'default' => 'basic',
			'tags' => ['advanced'],
			'options' => $available_admin_layouts,
		],
		'site_layout_per_object' => [
			'name' => tr('Enable layout per page, etc.'),
			'description' => tr('Specify an alternate layout for a particular wiki page, etc.'),
			'tags' => ['experimental'],
			'type' => 'flag',
			'default' => 'n',
		],
		'site_piwik_analytics_server_url' => [
			'name' => tr('Piwik server URL'),
			'description' => tr('The url to your Piwik server') . '<br />'
					. tr('In your Piwik, your selected site (Site Id) MUST have view permission for anonymous OR you can insert in your Piwik server url a token authentification parameter.'),
			'type' => 'text',
			'filter' => 'url',
			'size' => 30,
			'default' => '',
			'hint' => 'http(s)://yourpiwik.tld/index.php(?token_auth=yourtokencode)',
		],
		'site_piwik_site_id' => [
			'name' => tra('Site Id'),
			'description' => tr('The ID of your website in Piwik.'),
			'type' => 'text',
			'size' => '5',
			'default' => '',
			'dependencies' => [
				'site_piwik_analytics_server_url',
			],
		],
		'site_piwik_code' => [
			'name' => tra('Piwik JavaScript tracking code'),
			'description' => tra("Code placed on every page of your website before the </body> tag"),
			'type' => 'textarea',
			'size' => '6',
			'filter' => 'rawhtml_unsafe',
			'default' => '',
			'dependencies' => [
				'site_piwik_analytics_server_url',
			],
		],
	];
}
