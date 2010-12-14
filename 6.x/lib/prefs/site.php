<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_site_list() {
	return array (
		'site_closed' => array(
			'name' => tra('Close site (except for those with permission)'),
			'description' => tra('Close site (except for those with permission)'),
			'type' => 'flag',
			'perspective' => false,
		),
		'site_closed_msg' => array(
			'name' => tra('Message to display'),
			'description' => tra('Message to display'),
			'type' => 'text',
			'perspective' => false,
			'dependencies' => array(
				'site_closed',
			),
		),
		'site_busy_msg' => array(
			'name' => tra('Message to display'),
			'description' => tra('Message to display'),
			'type' => 'text',
			'perspective' => false,
			'dependencies' => array(
				'use_load_threshold',
			),
		),
		'site_crumb_seper' => array(
			'name' => tra('Locations (breadcrumbs)'),
			'description' => tra('Locations (breadcrumbs)'),
			'type' => 'text',
			'size' => '5',
		),
		'site_nav_seper' => array(
			'name' => tra('Choices'),
			'type' => 'text',
			'size' => '5',
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
		),
		'site_favicon' => array(
			'name' => tra('Favicon icon file name'),
			'type' => 'text',
			'size' => '15',
		),
		'site_favicon_type' => array(
			'name' => tra('Favicon icon MIME type'),
			'type' => 'list',
			'options' => array(
				'image/png' => tra('image/png'),
				'image/bmp' => tra('image/bmp'),
				'image/x-icon' => tra('image/x-icon'),
			),
		),
	);
}
