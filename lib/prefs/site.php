<?php

function prefs_site_list() {
	return array (
		'site_closed' => array(
			'name' => tra('Close site (except for those with permission)'),
			'description' => tra('Close site (except for those with permission)'),
			'type' => 'flag',
		),
		'site_closed_msg' => array(
			'name' => tra('Message to display'),
			'description' => tra('Message to display'),
			'type' => 'text',
			'dependencies' => array(
				'site_closed',
			),
		),
		'site_busy_msg' => array(
			'name' => tra('Message to display'),
			'description' => tra('Message to display'),
			'type' => 'text',
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
	);
}
