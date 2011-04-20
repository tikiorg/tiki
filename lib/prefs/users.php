<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_users_list($partial = false) {

	// retrieve language list for users_prefs_language preference
	global $tikilib, $prefs;

	$languages = array();

	$list_languages = array('' => tra('Default'));

	if (! $partial) {
		$languages = $tikilib->list_languages(false, null, true);

		foreach ($languages as $one_lang) {
			if ( in_array($one_lang['value'], $prefs['available_languages']) ) {
				// only availables languages are proposed to users
				$list_languages[ $one_lang['value'] ] = $one_lang['name'];
			}
		}
	}
	
	return array(
		'users_prefs_language' => array(
			'name' => tra('Language'),
			'type' => 'list',
			'options' => $list_languages,
			'dependencies' => array(
				'change_language',
			),
		),
		'users_serve_avatar_static' => array(
			'name' => tra('Serve avatar images statically'),
			'description' => tra('When enabled, feature checks and permission checks will be skipped.'),
			'type' => 'flag',
			'perspective' => false,
			'default' => 'y',
		),
		'users_prefs_display_timezone' => array(
			'name' => tra('Displayed time zone'),
			'type' => 'radio',
			'options' => array(
				'Site' => tra('Always the site default time zone.'),
				'Local' => tra('Use time zone set from user preferences, or the automatically detected time zone for anonymous (if browser allows). Site default is used as fallback.'),
			),
			'default' => 'Local',
		),
		'users_prefs_userbreadCrumb' => array(
			'name' => tra('Number of visited pages to remember'),
			'type' => 'list',
			'options' => array(
				'1' => tra('1'),
				'2' => tra('2'),
				'3' => tra('3'),
				'4' => tra('4'),
				'5' => tra('5'),
				'10' => tra('10'),
			),
			'default' => '4',
		),
		'users_prefs_user_information' => array(
			'name' => tra('User information'),
			'type' => 'list',
			'options' => array(
				'private' => tra('Private'),
				'public' => tra('Public'),
			),
			'default' => 'private',
		),
		'users_prefs_user_dbl' => array(
			'name' => tra('Use double-click to edit pages'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wiki',
			),
			'default' => 'n',
		),
		'users_prefs_display_12hr_clock' => array(
			'name' => tra('Use 12-hour clock for time selectors'),
			'type' => 'flag',
			'description' => tra('Use the 12-hour clock (with AM and PM) for time selectors used in some edit windows 
									to set the time for publishing new or edited blog posts, articles, etc.'),
			'default' => 'n',
		),
		'users_prefs_diff_versions' => array(
			'name' => tra('Use new diff any version interface'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wiki',
			),
			'default' => 'n',
		),
		'users_prefs_show_mouseover_user_info' => array(
			'name' => tra("Show user's info on mouseover"),
			'type' => 'flag',
			'dependencies' => array(
				'feature_community_mouseover',
			),
			'default' => 'n',
		),
		'users_prefs_tasks_maxRecords' => array(
			'name' => tra('Tasks per page'),
			'type' => 'list',
			'options' => array(
				'2' => tra('2'),
				'5' => tra('5'),
				'10' => tra('10'),
				'20' => tra('20'),
				'30' => tra('30'),
				'40' => tra('40'),
				'50' => tra('50'),
			),
			'dependencies' => array(
				'feature_tasks',
			),
			'help' => 'Tasks',
			'default' => '10',
		),
		'users_prefs_mess_maxRecords' => array(
			'name' => tra('Messages per page'),
			'type' => 'list',
			'dependencies' => array(
				'feature_messages',
			),
			'options' => array(
				'2' => tra('2'),
				'5' => tra('5'),
				'10' => tra('10'),
				'20' => tra('20'),
				'30' => tra('30'),
				'40' => tra('40'),
				'50' => tra('50'),
			),
			'default' => '10',
		),
		'users_prefs_allowMsgs' => array(
			'name' => tra('Allow messages from other users'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_messages',
			),
			'default' => 'y',
		),
		'users_prefs_mess_sendReadStatus' => array(
			'name' => tra('Notify sender when reading mail'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_messages',
			),
			'default' => 'n',
		),
		'users_prefs_minPrio' => array(
			'name' => tra('Send me an email for messages with priority equal or greater than'),
			'type' => 'list',
			'dependencies' => array(
				'feature_messages',
			),
			'options' => array(
				'1' => tra('1'),
				'2' => tra('2'),
				'3' => tra('3'),
				'4' => tra('4'),
				'5' => tra('5'),
				'6' => tra('None'),
			),
			'default' => '3',
		),
		'users_prefs_mess_archiveAfter' => array(
			'name' => tra('Auto-archive read messages after'),
			'type' => 'list',
			'dependencies' => array(
				'feature_messages',
			),
			'options' => array(
				'0' => tra('Never'),
				'1' => tra('1'),
				'2' => tra('2'),
				'5' => tra('5'),
				'10' => tra('10'),
				'20' => tra('20'),
				'30' => tra('30'),
				'40' => tra('40'),
				'50' => tra('50'),
				'60' => tra('60'),
			),
			'shorthint' => tra('days'),
			'default' => '0',
		),
		'users_prefs_mytiki_pages' => array(
			'name' => tra('My pages'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_wiki',
			),
			'default' => 'y',
		),
		'users_prefs_mytiki_blogs' => array(
			'name' => tra('My blogs'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_blogs',
			),
			'default' => 'y',
		),
		'users_prefs_mytiki_gals' => array(
			'name' => tra('My galleries'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_galleries',
			),
			'default' => 'y',
		),
		'users_prefs_mytiki_msgs' => array(
			'name' => tra('My messages'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_messages',
			),
			'default' => 'y',
		),
		'users_prefs_mytiki_tasks' => array(
			'name' => tra('My tasks'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_tasks',
			),
			'default' => 'y',
		),
		'users_prefs_mytiki_forum_topics' => array(
			'name' => tra('My forum topics'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
			'default' => 'y',
		),
		'users_prefs_mytiki_forum_replies' => array(
			'name' => tra('My forum replies'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
			'default' => 'y',
		),
		'users_prefs_mytiki_items' => array(
			'name' => tra('My items'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_trackers',
			),
			'default' => 'y',
		),
		'users_prefs_mailCharset' => array(
			'name' => tra('Character set for mail'),
			'type' => 'list',
			'options' => array(
				'' => 'default',
				'utf-8' => 'utf-8',
				'iso-8859-1' => 'iso-8859-1',
			),
			'default' => 'utf-8',
		),
	);
}
