<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_user_list($partial = false)
{
	
	global $prefs;

	$fieldFormat = '{title} ({tracker_name})';
	return array(
		'user_show_realnames' => array(
			'name' => tra('Show user\'s real name instead of username (log-in name), when possible'),
			'description' => tra('Show user\'s real name instead of username (log-in name), when possible'),
			'help' => 'User+Preferences',
			'type' => 'flag',
			'default' => 'n',
		),
		'user_unique_email' => array(
			'name' => tra('User e-mails must be unique'),
			'description' => tra('User e-mails must be unique'),
			'help' => 'User+Preferences',
			'type' => 'flag',
			'default' => 'n',
		),
		'user_tracker_infos' => array(
			'name' => tra('Display user tracker information on the user information page'),
			'description' => tra('Display user tracker information on the user information page'),
			'help' => 'User+Tracker',
			'hint' => tra('Input the user tracker ID then field IDs to be shown, all separated by commas. Example: 1,1,2,3,4 (user tracker ID 1 followed by field IDs 1-4)'),
			'type' => 'text',
			'size' => '50',
			'dependencies' => array(
				'userTracker',
			),
			'default' => '',
			'profile_reference' => 'prefs_user_tracker_references',
		),
		'user_assigned_modules' => array(
			'name' => tra('Users can configure modules'),
			'help' => 'Users+Configure+Modules',
			'tags' => array('experimental'),	// This feature seems broken and will mess the display of the adventurous user. See https://dev.tiki.org/item5871
			'type' => 'flag',
			'default' => 'n',
		),	
		'user_flip_modules' => array(
			'name' => tra('Users can open and close the modules'),
			'help' => 'Users+Shade+Modules',
			'type' => 'list',
			'description' => tra('Allows users to open and close modules using the icon in the module header.'),
			'options' => array(
				'y' => tra('Always'),
				'module' => tra('Module decides'),
				'n' => tra('Never'),
			),
			'default' => 'module',
		),
		'user_store_file_gallery_picture' => array(
			'name' => tra('Store full-size copy of profile picture in file gallery'),
			'help' => 'User+Preferences',
			'keywords' => 'avatar',
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => ['user_picture_gallery_id',],
		),
		'user_small_avatar_size' => array(
			'name' => tra('Size of the small profile picture stored for users'),
			'help' => 'User+Preferences',
			'type' => 'text',
			'filter' => 'digits',
			'default' => '45',
		),
		'user_small_avatar_square_crop' => array(
			'name' => tra('Crop the profile picture thumbnail to a square'),
			'help' => 'User+Preferences',
			'type' => 'flag',
			'default' => 'n',
		),
		'user_picture_gallery_id' => array(
			'name' => tra('File gallery in which to store full-size profile picture'),
			'description' => tra('Enter the gallery ID here. Please create a dedicated gallery that is admin-only for security, or make sure gallery permissions are set so that only admins can edit.'),
			'help' => 'User+Preferences',
			'keywords' => 'avatar',
			'type' => 'text',
			'filter' => 'digits',
			'size' => '3',
			'default' => 0,
			'profile_reference' => 'file_gallery',
			'dependencies' => ['feature_file_galleries',],
		),
		'user_default_picture_id' => array(
			'name' => tra('File ID of default profile picture'),
			'deacription' => tra('File ID of image to use in file gallery as the profile picture if user has no profile picture in file galleries'),
			'keywords' => 'avatar',
			'help' => 'User+Preferences',
			'type' => 'text',
			'filter' => 'digits',
			'size' => '5',
			'default' => 0,
			'dependencies' => array('user_store_file_gallery_picture'),
			'profile_reference' => 'file',
		),
		'user_who_viewed_my_stuff' => array(
			'name' => tra('Display who has viewed "my items" on the user information page'),
			'description' => tra('This requires activation of tracking of views for various items in the action log'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_actionlog',
			),
			'default' => 'n',
		),
		'user_who_viewed_my_stuff_days' => array(
			'name' => tra('Number of days to consider in displaying "who viewed my items"'),
			'description' => tra('Number of days before the current day to consider when displaying "who viewed my items"'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => '4',
			'default' => 90,
		),
		'user_who_viewed_my_stuff_show_others' => array(
			'name' => tra('Show to others "who viewed my items" on the user information page'),
			'description' => tra('Show to others "who viewed my items" on the user information page. Admins can always see this information.'),
			'type' => 'flag',
			'dependencies' => array(
				'user_who_viewed_my_stuff',
			),
			'default' => 'n',
		),
		'user_list_order' => array(
			'name' => tra('Sort order'),
			'type' => 'list',
			'options' => $partial ? array() : UserListOrder(),
			'default' => 'score_desc',
		),
		'user_register_prettytracker' => array(
			'name' => tra('Use pretty trackers for registration form'),
			'help' => 'User+Tracker',
			'description' => tra('Use pretty trackers for registration form'),
			'type' => 'flag',
			'dependencies' => array(
				'userTracker',
			),
			'default' => 'n',
		),
		'user_register_prettytracker_tpl' => array(
			'name' => tra('Registration pretty tracker template'),
			'description' => tra('Use wiki page name or template file with .tpl extension'),
			'type' => 'text',
			'size' => '20',
			'dependencies' => array(
				'user_register_pretty_tracker',
			),
			'default' => ''
		),
		'user_register_prettytracker_output' => array(
			'name' => tra('Output the registration results'),
			'help' => 'User+Tracker',
			'description' => tra('Use a wiki page as template to output the registration results to'),
			'type' => 'flag',
			'default' => 'n',
			'dependencies' => array(
				'userTracker',
			),
			'profile_reference' => 'wiki_page',
		),
		'user_register_prettytracker_outputwiki' => array(
			'name' => tra('Output registration pretty tracker template'),
			'description' => tra('Wiki page only'),
			'type' => 'text',
			'size' => '20',
			'default' => '',
			'dependencies' => array(
				'user_register_prettytracker_output',
			),
			'profile_reference' => 'wiki_page',
		),
		'user_register_prettytracker_outputtowiki' => array(
			'name' => tra('Page name field ID'),
			'description' => tra("User tracker's field ID whose value is used as output page name"),
			'type' => 'text',
			'size' => '20',
			'default' => '',
			'dependencies' => array(
				'user_register_prettytracker_output',
			),
			'profile_reference' => 'tracker_field',
			'format' => $fieldFormat,
		),
		'user_trackersync_trackers' => array(
			'name' => tra('User tracker IDs to sync prefs from'),
			'description' => tra('Enter the IDs separated by commas of trackers to sync user prefs from'),
			'type' => 'text',
			'size' => '10',
			'dependencies' => array(
				'userTracker',
			),
			'default' => '',
			'separator' => ',',
			'profile_reference' => 'tracker',
		),
		'user_trackersync_realname' => array(
			'name' => tra('Tracker field IDs to sync the "real name" pref from'),
			'description' => tra('Enter the IDs separated by commas in priority of being chosen, each item can concatenate multiple fields using +, e.g. 2+3,4'),
			'type' => 'text',
			'size' => '10',
			'dependencies' => array(
				'userTracker',
				'user_trackersync_trackers',
			),
			'default' => '',
		),
		'user_trackersync_geo' => array(
			'name' => tra('Synchronize long/lat/zoom to location field'),
			'description' => tra('Synchronize user geolocation prefs to main location field'),
			'type' => 'flag',
			'dependencies' => array(
				'userTracker',
				'user_trackersync_trackers',
			),
			'default' => 'n',
		),
		'user_trackersync_lang' => array(
			'name' => tra('Change user system language when changing user tracker item language'),
			'type' => 'flag',
			'dependencies' => array(
				'userTracker',
				'user_trackersync_trackers',
			),
			'default' => 'n',
		),
		'user_tracker_auto_assign_item_field' => array(
			'name' => tra('Assign a user tracker item when registering if email equals this field'),
			'type' => 'text',
			'filter' => 'digits',
			'dependencies' => array(
				'userTracker',
			),
			'default' => '',
			'profile_reference' => 'tracker_field',
			'format' => $fieldFormat,
		),
		'user_selector_threshold' => array(
			'name' => tra('Maximum number of users to show in drop-down lists'),
			'description' => tra('Prevents out-of-memory and performance issues when the user list is very large, by using a jQuery autocomplete text input box.'),
			'type' => 'text',
			'size' => '5',
			'dependencies' => array('feature_jquery_autocomplete'),
			'default' => 50,
		),
		'user_selector_realnames_tracker' => array(
			'name' => tra('Show user\'s real name instead of log-in name in the autocomplete selector in trackers'),
			'description' => tra('Use user\'s real name instead of log-in name in the autocomplete selector in trackers'),
			'type' => 'flag',
			'dependencies' => array('feature_jquery_autocomplete', 'user_show_realnames', 'feature_trackers'),
			'default' => 'n',
		),
		'user_selector_realnames_messu' => array(
			'name' => tra('Show user\'s real name instead of log-in name in the autocomplete selector in the messaging feature'),
			'description' => tra('Use user\'s real name instead of log-in name in the autocomplete selector in the messaging feature'),
			'type' => 'flag',
			'dependencies' => array('feature_jquery_autocomplete', 'user_show_realnames', 'feature_messages'),
			'default' => 'n',
		),
		'user_favorites' => array(
			'name' => tra('User Favorites'),
			'description' => tra('Allows users to flag content as their favorite.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'user_likes' => array(
			'name' => tra('User Likes'),
			'description' => tra('Allows for users to "like" content.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'user_must_choose_group' => array(
			'name' => tra('Users must choose a group at registration'),
			'description' => tra('Users cannot register without choosing one of the groups indicated above.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'user_in_search_result' => array(
			'name' => tr('Users available in search results'),
			'description' => tr('Users available within search results. Content related to the user will be included in the index.'),
			'type' => 'list',
			'dependencies' => array('feature_search'),
			'options' => array(
				'none' => tr('None'),
				'all' => tr('All'),
				'public' => tr('Public'),
			),
			'default' => 'none',
		),
		'user_use_gravatar' => array(
			'name' => tr('Use Gravatar for user profile pictures'),
			'description' => tr('Always request the Gravatar image for the user profile picture.'),
			'hint' => tr('See [http://gravatar.com/|Gravatar].'),
			'type' => 'flag',
			'default' => 'n',
		),
		'user_multilike_config' => array(
			'name' => tr('Configuration for multilike'),
			'description' => tr('Separate configurations by a blank line. E.g. relation_prefix=tiki.multilike values=1,3,5 labels=Good,Great,Excellent)'),
			'type' => 'textarea',
			'size' => 5,
			'default' => ''
		),
	);
}

/**
 * UserListOrder computes the value list for user_list_order preference
 * 
 * @access public
 * @return array : list of values
 */
function UserListOrder()
{
	global $prefs;
	$options = array();

	if ($prefs['feature_community_list_score'] == 'y') {
		$options['score_asc'] = tra('Score ascending');
		$options['score_desc'] = tra('Score descending');
	}
	
	if ($prefs['feature_community_list_name'] == 'y') {
		$options['pref:realname_asc'] = tra('Name ascending');
		$options['pref:realname_desc'] = tra('Name descending');
	}

	$options['login_asc'] = tra('Login ascending');
	$options['login_desc'] = tra('Login descending');

	return $options;
}

function prefs_user_tracker_references(Tiki_Profile_Writer $writer, $values)
{
	$values = array_filter(explode(',', $values));
	$tracker = array_shift($values);

	$values = $writer->getReference('tracker_field', $values);
	array_unshift($values, $writer->getReference('tracker', $tracker));

	return implode(',', $values);
}

