<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_site_list()
{
	return array (
		'site_closed' => array(
			'name' => tra('Close site (except for those with permission)'),
			'description' => tra('Close site (except for those with permission)'),
			'type' => 'flag',
			'perspective' => false,
			'tags' => array('basic'),
			'default' => 'n',
		),
		'site_closed_msg' => array(
			'name' => tra('Message to display'),
			'description' => tra('Message to display'),
			'type' => 'text',
			'perspective' => false,
			'dependencies' => array(
				'site_closed',
			),
			'default' => 'Site is closed for maintenance; please come back later.',
			'tags' => array('basic'),
		),
		'site_busy_msg' => array(
			'name' => tra('Message to display'),
			'description' => tra('Message to display'),
			'type' => 'text',
			'perspective' => false,
			'dependencies' => array(
				'use_load_threshold',
			),
			'default' => 'Server is currently too busy; please come back later.',
		),
		'site_crumb_seper' => array(
			'name' => tra('Locations (breadcrumbs)'),
			'description' => tra('Locations (breadcrumbs)'),
			'type' => 'text',
			'size' => '5',
			'default' => '»',
		),
		'site_nav_seper' => array(
			'name' => tra('Choices'),
			'type' => 'text',
			'size' => '5',
			'default' => '|',
		),
		'site_title_location' => array(
			'name' => tra('Site title location'),
			'description' => tra('Location of the site title in the browser title bar relative to the current page\'s descriptor.'),
			'type' => 'list',
			'options' => array(
				'after' => tra('After'),
				'before' => tra('Before'),
				'none' => tra('None'),
			),
			'default' => 'before',
		),
		'site_title_breadcrumb' => array(
			'name' => tra('Browser title display mode'),
			'description' => tra('When breadcrumbs are used, method in which the browser title should be displayed.'),
			'type' => 'list',
			'options' => array(
				'invertfull' => tra('Most specific first'),
				'fulltrail' => tra('Least specific first (site)'),
				'pagetitle' => tra('Current only'),
				'desc' => tra('Description'),
			),
			'default' => 'invertfull',
		),
		'site_favicon' => array(
			'name' => tra('Favicon icon file name'),
			'type' => 'text',
			'size' => '50',
			'default' => 'favicon.png',
			'tags' => array('basic'),
		),
		'site_favicon_type' => array(
			'name' => tra('Favicon MIME type'),
			'type' => 'list',
			'description' => tra('Typical file extensions:<table><tr><td>image/jpeg</td><td><strong>.jpg</strong></td></tr><tr><td>imp/png</td><td><strong>.png</strong></td></tr><tr><td>img/gif</td><td><strong>.gif</strong></td></tr><tr><td>image/vnd.microsoft.icon</td><td><strong>.ico</strong></td></tr></table>'),
			'options' => array(
				'image/jpeg' => tra('image/jpeg'),
				'image/png' => tra('image/png'),
				'image/gif' => tra('image/gif'),
				'image/vnd.microsoft.icon' => tra('image/vnd.microsoft.icon'),
			),
			'default' => 'image/png',
			'tags' => array('basic'),
		),
		'site_terminal_active' => array(
			'name' => tra('Site Terminal'),
			'description' => tra('Allows to direct users to a specific perspective depending on the origin IP address. Can be used inside intranets to use different configurations for users depending on their departements or discriminate people in web contexts. Unspecified IPs will fall back to default behavior, including multi-domain handling. Manually selected perspectives take precedence over this.'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_perspective',
			),
			'default' => 'n',
		),
		'site_terminal_config' => array(
			'name' => tra('Site Terminal Configuration'),
			'description' => tra('Provides the mapping from subnets to perspective.'),
			'type' => 'textarea',
			'perspective' => false,
			'size' => 10,
			'hint' => tra('One per line. Network prefix in CIDR notation (address/mask size), separated by comma with the perspective ID.') . ' ' . tra('Example:') . ' 192.168.12.0/24,12',
			'default' => '',
		),
	);
}
