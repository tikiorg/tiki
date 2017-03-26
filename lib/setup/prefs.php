<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// RULE1: $prefs does not contain serialized values. Only the database contains serialized values.
// RULE2: put array('') in default prefs for serialized values

//this script may only be included - so its better to die if called directly.
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
	header("location: index.php");
	exit;
}

//
// This section is being phased out. Please use the instructions at https://dev.tiki.org/Create+a+new+preference instead.
//

// Prefs for which we want to use the site value (they will be prefixed with 'site_')
// ( this is also used in tikilib, not only when reloading prefs )
global $user_overrider_prefs, $prefs;
$user_overrider_prefs = array(
	'language',
	'userbreadCrumb',
	'tikiIndex',
	'wikiHomePage',
	'default_calendars',
	'metatag_robots',
	'theme',
	'theme_option',
);

initialize_prefs();

function get_default_prefs()
{
	static $prefs;
	if ( is_array($prefs) ) {
		return $prefs;
	}

	$cachelib = TikiLib::lib('cache');
	if ( $prefs = $cachelib->getSerialized('tiki_default_preferences_cache') ) {
		return $prefs;
	}

	$prefslib = TikiLib::lib('prefs');
	$prefs = $prefslib->getDefaults();
	$prefs = array_merge(
		$prefs,
		array(
			// tiki and version
			'tiki_release' => '0',
			'tiki_needs_upgrade' => 'n',
			'tiki_version_last_check' => 0,


			'groups_are_emulated' => 'n',


			// wiki
			'backlinks_name_len' => '0',
			'feature_wiki_notepad' => 'n',
			'feature_wiki_feedback_polls' => array(),
			'mailin_respond_email' => 'y',
			'mailin_autocheck' => 'n',
			'mailin_autocheckFreq' => '0',
			'mailin_autocheckLast' => 0,
			'wiki_bot_bar' => 'n',
			'wiki_left_column' => 'y',
			'wiki_right_column' => 'y',
			'wiki_top_bar' => 'y',
			'feature_wiki_watch_structure' => 'n',
			'wiki_validate_plugin' => 'y',
			'wiki_pagealias_tokens' => 'alias',

			// File galleries

			// Root galleries fake preferences. These are automatically overridden by schema upgrade scripts
			//  for installations that pre-date the existence of these root galleries.
			'fgal_root_id' => 1, // Ancestor of "default" type galleries. For old installs, overriden by 20090811_filegals_container_tiki.sql
			'fgal_root_user_id' => 2, // Ancestor of "user" type galleries (feature_use_fgal_for_user_files). For old installs,
																// overriden by 20101126_fgal_add_gallerie_user_tiki.php
			'fgal_root_wiki_attachments_id' => 3, // Ancestor of wiki "attachments" type galleries (feature_use_fgal_for_wiki_attachments).
																						// For old installs, overriden by 20101210_fgal_add_wiki_attachments_tiki.php
			//can probably be removed - doesn't seem to be set or used anywhere
			'fgal_show_checked' => 'y',

			// articles
			'cms_bot_bar' => 'y',
			'cms_left_column' => 'y',
			'cms_right_column' => 'y',
			'cms_top_bar' => 'n',
			'cms_last_viewed_month' => '0',


			// trackers
			't_use_db' => 'y',
			't_use_dir' => '',
			'trackerCreatorGroupName' => ' ',

			// user
			'userlevels' => function_exists('tra') ? array('1'=>tra('Simple'),'2'=>tra('Advanced')) : array('1'=>'Simple','2'=>'Advanced'),
			'userbreadCrumb' => 4,
			'feature_community_friends_permission' => 'n',
			'feature_community_friends_permission_dep' => '2',
			'lowercase_username' => 'n',
			'users_prefs_country' => '',
			'users_prefs_email_is_public' => 'n',
			'users_prefs_homePage' => '',
			'users_prefs_lat' => '0',
			'users_prefs_lon' => '0',
			'users_prefs_mytiki_articles' => 'y',
			'users_prefs_realName' => '',
			'users_prefs_gender' => '',
			'users_prefs_mailCurrentAccount' => '0',

			// freetags
			'freetags_cloud_colors' => '',


			// feed
			'max_rss_mapfiles' => 10,
			'rss_mapfiles' => 'n',
			'title_rss_mapfiles' => '',


			// auth
			'min_user_length' => 1,
			'auth_pear' => 'tiki',
			'auth_ldap_url' => '',
			'auth_pear_host' => 'localhost',
			'auth_pear_port' => '389',
			'auth_ldap_groupnameatr' => '',
			'auth_ldap_groupdescatr' => '',
			'auth_ldap_syncuserattr' => 'uid',
			'auth_ldap_syncgroupattr' => 'cn',


			'auth_phpbb_dbport' => '',
			'auth_phpbb_dbtype' => 'mysql',


			'login_url' => 'tiki-login.php',
			'login_scr' => 'tiki-login_scr.php',
			'register_url' => 'tiki-register.php',
			'error_url' => 'tiki-error.php',

			// intertiki
			'interlist' => array(),
			'known_hosts' => array(),

			// categories
			'category_i18n_unsynced' => array(),

			// look and feel

			'feature_sitenav' => 'n',
			'sitenav' => '{tr}Navigation : {/tr}<a href="tiki-contact.php" accesskey="10" title="">{tr}Contact Us{/tr}</a>',

			// mods
			'feature_mods_provider' => 'n',
			'mods_dir' => 'mods',
			'mods_server' => 'http://mods.tiki.org',


			// toolbars
			// comma delimited items, / delimited rows and | denotes items right justified in toolbar (in reverse order)
			// full list in lib/toolbars/toolbarslib.php Toolbar::getList()
			// cannot contain spaces, commas, forward-slash or pipe chars within tool names
			'toolbar_global' => '
				bold, italic, underline, strike, sub, sup,-, color, -, tikiimage, tikilink, link, unlink, anchor, -,
				undo, redo, -, find, replace, -, removeformat, specialchar, smiley | help, switcheditor, autosave, admintoolbar /
				format, templates, cut, copy, paste, pastetext, pasteword, -, h1, h2, h3, left, center, -,
				blockquote, list, numlist, -, pagebreak, rule, -, table, pastlink, -, source, showblocks, screencapture | fullscreen /
				style, fontname, fontsize, outdent, indent /
			',
			'toolbar_global_comments' => '
				bold, italic, underline, strike , - , link, smiley | help
			',
			'toolbar_sheet' => 'addrow, addrowbefore, addrowmulti, deleterow,-, addcolumn, addcolumnbefore, addcolumnmulti, deletecolumn,-,
								sheetgetrange, sheetrefresh, -, sheetfind | sheetclose, sheetsave, help/
								bold, italic, underline, strike, center, -, color, bgcolor, -, tikilink, nonparsed | fullscreen /',

			// unsorted features
			'anonCanEdit' => 'n',
			'feature_contribution_display_in_comment' => 'y',
			'feature_contribution_mandatory' => 'n',
			'feature_contribution_mandatory_blog' => 'n',
			'feature_contribution_mandatory_comment' => 'n',
			'feature_contribution_mandatory_forum' => 'n',
			'feature_debugger_console' => 'n',
			'feature_events' => 'n',
			'feature_projects' => 'n',
			'feature_ranking' => 'n',
			'feature_top_banner' => 'n',
			'feature_usability' => 'n',
			'minical_reminders' => 0,
			'php_docroot' => 'http://php.net/',
			'shoutbox_autolink' => 'n',
			'show_comzone' => 'n',
			'use_proxy' => 'n',
			'webserverauth' => 'n',

			'feature_intertiki_imported_groups' => '',
			'feature_contributor_wiki' => '',
			'https_login_required' => '',
			'replimaster' => '',
			'javascript_enabled' => 'n',


			// SefUrl
			'feature_sefurl_paths' => array(''), //empty string needed to keep preference from setting unexpectedly

			'feature_bidi' => 'n',
			'feature_lastup' => 'y',

			'terminology_profile_installed' => 'n',
		)
	);

	// Special default values

	$_SESSION['tmpDir'] = class_exists('TikiInit') ? TikiInit::tempdir() : '/tmp';

	$prefs['feature_bidi'] = 'n';
	$prefs['feature_lastup'] = 'y';

	// Be sure we have a default value for user prefs
	foreach ( $prefs as $p => $v ) {
		if ( substr($p, 0, 12) == 'users_prefs_' ) {
			$prefs[substr($p, 12)] = $v;
		}
	}

	$cachelib->cacheItem("tiki_default_preferences_cache", serialize($prefs));
	return $prefs;
}


function initialize_prefs($force = false)
{
	global $prefs, $user_overrider_prefs, $in_installer, $section, $systemConfiguration;


	if (! $force && (defined('TIKI_IN_INSTALLER') || defined('TIKI_IN_TEST'))) {
		$prefs = get_default_prefs();
		return;
	}
	$cachelib = TikiLib::lib('cache');

	if ($cachelib->isCached('global_preferences')) {
		$prefs = $cachelib->getSerialized('global_preferences');
		// note there is a small chance in high concurrency environments that cache file may be cleared
		// in the interim leading to blank $prefs
	}

	if (empty($prefs) || !$cachelib->isCached('global_preferences')) {
		$defaults = get_default_prefs();

		// Find which preferences need to be serialized/unserialized, based on the default
		//  values (those with arrays as values) and preferences with special serializations
		$serializedPreferences = array();
		$prefslib = TikiLib::lib('prefs');
		foreach ( $defaults as $preference => $value ) {
			if ( is_array($value) || in_array($preference, array('category_defaults', 'memcache_servers'))) {
				$serializedPreferences[] = $preference;
			}
		}

		$tikilib = TikiLib::lib("tiki");
		if (method_exists($tikilib, "getModifiedPreferences")) {
			$modified = TikiLib::lib("tiki")->getModifiedPreferences();
		} else {
			$modified = array();
		}

		// Unserialize serialized preferences
		foreach ( $serializedPreferences as $serializedPreference ) {
			if ( !empty($modified[$serializedPreference]) && ! is_array($modified[$serializedPreference]) ) {
				$modified[$serializedPreference] = unserialize($modified[$serializedPreference]);
			}
		}

		// Keep some useful sites values available before overriding with user prefs
		// (they could be used in templates, so we need to set them even for Anonymous)
		foreach ( $user_overrider_prefs as $uop ) {
			if (isset($modified[$uop])) {
				$modified['site_'.$uop] = $modified[$uop];
			} elseif (isset($defaults[$uop])) {
				$modified['site_'.$uop] = $defaults[$uop];
			}
		}

		$prefs = $modified + $defaults;
		$cachelib->cacheItem('global_preferences', serialize($prefs));
	}

	if ( $prefs['feature_perspective'] == 'y') {
		if ( ! isset( $section ) || $section != 'admin' ) {
			$perspectivelib = TikiLib::lib('perspective');
			if ( $persp = $perspectivelib->get_current_perspective($prefs) ) {
				$perspectivePreferences = $perspectivelib->get_preferences($persp);
				$prefs = $perspectivePreferences + $prefs;
			}
		}
	}

	// Override preferences with system-configured preferences.
	$system = $systemConfiguration->preference->toArray();
	// Also include the site_ versions
	foreach ( $user_overrider_prefs as $uop ) {
		if (isset($system[$uop])) {
			$system['site_' . $uop] = $system[$uop];
		}
	}
	$prefs = array_merge($prefs, $system);

	if ( !defined('TIKI_PREFS_DEFINED') ) define('TIKI_PREFS_DEFINED', 1);
}

/**
 * Detect the engine used in the current schema.
 * Assumes that all tables use the same table engine
 * @return string identifying the current engine, or an empty string if not installed
 */
function getCurrentEngine()
{
	return TikiLib::lib("tiki")->getCurrentEngine();
}

/**
 * Determine if MySQL fulltext search is supported by the current DB engine
 * Assumes that all tables use the same table engine
 * @return true if it is supported, otherwise false
 */
function isMySQLFulltextSearchSupported()
{
	return TikiLib::lib("tiki")->isMySQLFulltextSearchSupported();
}
