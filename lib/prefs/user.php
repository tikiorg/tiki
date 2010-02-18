<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_user_list() {
	return array(
		'user_show_realnames' => array(
			'name' => tra('Show user\'s real name instead of login (when possible)'),
			'description' => tra('Show user\'s real name instead of login (when possible)'),
			'help' => 'User+Preferences',
			'type' => 'flag',
		),
		'user_tracker_infos' => array(
			'name' => tra('Display UserTracker information on the user information page'),
			'description' => tra('Display UserTracker information on the user information page'),
			'help' => 'User+Tracker',
			'type' => 'text',
			'size' => '50',
			'dependencies' => array(
				'userTracker',
			),
		),
		'user_assigned_modules' => array(
			'name' => tra('Users can configure modules'),
			'help' => 'Users+Configure+Modules',
			'type' => 'flag',
		),	
		'user_flip_modules' => array(
			'name' => tra('Users can shade modules'),
			'help' => 'Users+Shade+Modules',
			'type' => 'list',
			'options' => array(
				'y' => tra('Always'),
				'module' => tra('Module decides'),
				'n' => tra('Never'),
			),
		),
		'user_store_file_gallery_picture' => array(
			'name' => tra('Store full-size copy of avatar in file gallery'),
			'help' => 'User+Preferences',
			'type' => 'flag',
		),
		'user_picture_gallery_id' => array(
			'name' => tra('File gallery to store full-size copy of avatar in'),
			'description' => tra('Enter the gallery id here. Please create a dedicated gallery that is admin-only for security, or make sure gallery permissions are set so that only admins can edit.'),
			'help' => 'User+Preferences',
			'type' => 'text',
			'filter' => 'digits',
			'size' => '3',
		),
		'user_who_viewed_my_stuff' => array(
			'name' => tra('Display who viewed my stuff on the user information page'),
			'description' => tra('You will need to activate tracking of views for various items in the action log for this to work'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_actionlog',
			),
		),
		'user_who_viewed_my_stuff_days' => array(
			'name' => tra('Number of days to consider who viewed my stuff'),
			'description' => tra('Number of days before current time to consider when showing who viewed my stuff'),
			'type' => 'text',
			'filter' => 'digit',
			'size' => '4',
		),
	);
}
