<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// RULE1: $prefs does not contain serialized values. Only the database contains serialized values.
// RULE2: put array('') in default prefs for serialized values

//this script may only be included - so its better to die if called directly.
if ( basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__) ) {
  header("location: index.php");
  exit;
}

$user_overrider_prefs = array('language', 'style', 'style_option', 'userbreadCrumb', 'tikiIndex', 'wikiHomePage',
								'default_calendars', 'metatag_robots', 'themegenerator_theme');
initialize_prefs();

function get_default_prefs() {
	static $prefs;
	if( is_array($prefs) )
		return $prefs;

	global $cachelib; require_once 'lib/cache/cachelib.php';
	if( $prefs = $cachelib->getSerialized("tiki_default_preferences_cache") ) {
		return $prefs;
	}

	$prefslib = TikiLib::lib('prefs');
	$prefs = $prefslib->getDefaults();
	$prefs = array_merge($prefs, array(
		// tiki and version
		'tiki_release' => '0',
		'tiki_needs_upgrade' => 'n',
		'tiki_version_last_check' => 0,
		'lastUpdatePrefs' => 1,


		'groups_are_emulated' => 'n',


		// wiki
		'backlinks_name_len' => '0',
		'feature_wiki_notepad' => 'n',
		'feature_wiki_feedback_polls' => array(),
		'feature_wiki_pagealias' => 'y',
		'mailin_autocheck' => 'n',
		'mailin_autocheckFreq' => '0',
		'mailin_autocheckLast' => 0,
		'wiki_bot_bar' => 'n',
		'wiki_left_column' => 'y',
		'wiki_page_separator' => '...page...',
		'wiki_right_column' => 'y',
		'wiki_top_bar' => 'y',
		'feature_wiki_watch_structure' => 'n',
		'wiki_validate_plugin' => 'y',

		// webservices
		'webservice_consume_defaultcache' => 300, // 5 min

		// filegals
		'fgal_root_id' => 1,
		'fgal_root_user_id' => 2,
		'fgal_root_wiki_attachments_id' => 3,
		'fgal_enable_auto_indexing' => 'y',
		'fgal_asynchronous_indexing' => 'y',
		'fgal_sort_mode' => '',
		'fgal_list_id' => 'o',
		'fgal_list_type' => 'y',
		'fgal_list_name' => 'a',
		'fgal_list_description' => 'o',
		'fgal_list_size' => 'y',
		'fgal_list_created' => 'o',
		'fgal_list_lastModif' => 'y',
		'fgal_list_creator' => 'o',
		'fgal_list_author' => 'o',
		'fgal_list_last_user' => 'o',
		'fgal_list_comment' => 'o',
		'fgal_list_files' => 'o',
		'fgal_list_hits' => 'o',
		'fgal_list_lastDownload' => 'n',
		'fgal_list_lockedby' => 'a',
		'fgal_list_deleteAfter' => 'n',
		'fgal_list_share' => 'n',
		'fgal_show_path' => 'y',
		'fgal_show_explorer' => 'y',
		'fgal_show_slideshow' => 'n',
		'fgal_default_view' => 'list',
		'fgal_list_backlinks' => 'n',
		'fgal_list_id_admin' => 'y',
		'fgal_list_type_admin' => 'y',
		'fgal_list_name_admin' => 'n',
		'fgal_list_description_admin' => 'o',
		'fgal_list_size_admin' => 'y',
		'fgal_list_created_admin' => 'o',
		'fgal_list_lastModif_admin' => 'y',
		'fgal_list_creator_admin' => 'o',
		'fgal_list_author_admin' => 'o',
		'fgal_list_last_user_admin' => 'o',
		'fgal_list_comment_admin' => 'o',
		'fgal_list_files_admin' => 'o',
		'fgal_list_hits_admin' => 'o',
		'fgal_list_lastDownload_admin' => 'n',
		'fgal_list_lockedby_admin' => 'n',
		'fgal_list_backlinks_admin' => 'y',
		'fgal_show_checked' => 'y',

		// imagegals
		'feature_gal_batch' => 'n',
		'feature_gal_slideshow' => 'n',
		'gal_use_db' => 'y',
		'gal_use_lib' => 'imagick',
		'gal_match_regex' => '',
		'gal_nmatch_regex' => '',
		'gal_use_dir' => '',
		'gal_batch_dir' => '',
		'feature_gal_rankings' => 'n',
		'feature_image_galleries_comments' => 'n',
		'image_galleries_comments_default_order' => 'points_desc',
		'image_galleries_comments_per_page' => 10,
		'gal_list_name' => 'y',
		'gal_list_parent' => 'n',
		'gal_list_description' => 'y',
		'gal_list_created' => 'n',
		'gal_list_lastmodif' => 'y',
		'gal_list_user' => 'n',
		'gal_list_imgs' => 'y',
		'gal_list_visits' => 'y',
		'preset_galleries_info' =>'n',
		'gal_image_mouseover' => 'n',

		// articles
		'cms_bot_bar' => 'y',
		'cms_left_column' => 'y',
		'cms_right_column' => 'y',
		'cms_top_bar' => 'n',


		// trackers
		't_use_db' => 'y',
		't_use_dir' => '',
		'trackerCreatorGroupName' => ' ',

		// user
		'userlevels' => function_exists('tra') ? array('1'=>tra('Simple'),'2'=>tra('Advanced')) : array('1'=>'Simple','2'=>'Advanced'),
		'userbreadCrumb' => 4,
		'uf_use_db' => 'y',
		'uf_use_dir' => '',
		'userfiles_quota' => 30,
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


		// calendar
		'feature_default_calendars' => 'n',
		'default_calendars' => array(),

		// feed
		'max_rss_mapfiles' => 10,
		'rss_mapfiles' => 'n',
		'title_rss_mapfiles' => '',

	
		// auth
		'min_user_length' => 1,
		'auth_pear' => 'tiki',
		'auth_ldap_url' => '',
		'auth_pear_host' => "localhost",
		'auth_pear_port' => "389",
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
		'feature_intertiki_server' => 'n',
		'feature_intertiki_slavemode' => 'n',
		'interlist' => array(),
		'feature_intertiki_mymaster' => '',
		'feature_intertiki_import_preferences' => 'n',
		'feature_intertiki_import_groups' => 'n',
		'known_hosts' => array(),
		'tiki_key' => '',
		'intertiki_logfile' => '',
		'intertiki_errfile' => '',
		'feature_intertiki_sharedcookie' => 'n',

		// categories
		'category_i18n_unsynced' => array(),
		'expanded_category_jail' => '',
		'expanded_category_jail_key' => '',
		'ws_container' => 0,

		// html header
		'head_extra_js' => array(),

		// look and feel

		'feature_sitenav' => 'n',
		'sitenav' => '{tr}Navigation : {/tr}<a href="tiki-contact.php" accesskey="10" title="">{tr}Contact Us{/tr}</a>',

		// layout
		'feature_theme_generator' => 'n',

		// mods
		'feature_mods_provider' => 'n',
		'mods_dir' => 'mods',
		'mods_server' => 'http://mods.tiki.org',


		// toolbars
		// comma delimited items, / delimited rows and | denotes items right justified in toolbar (in reverse order)
		// full list in lib/toolbars/toolbarslib.php Toolbar::getList()
		// cannot contain spaces, commas, forward-slash or pipe chars
		'toolbar_global' => '
			bold,italic,underline,strike, sub, sup,-,color,-,wikiplugin_img,tikiimage,wikiplugin_file,tikilink,link, unlink, anchor,-,
			undo, redo,-,find,replace,-, removeformat,specialchar,smiley|help,switcheditor,autosave/
			templates, cut, copy, paste, pastetext, pasteword,-,h1,h2,h3, left,center,-,
			blockquote,list,numlist,wikiplugin_mouseover,wikiplugin_module,wikiplugin_html, outdent, indent,-,
			pagebreak,rule,-,table,-,wikiplugin_code, source, showblocks,nonparsed|fullscreen/
			format,style,-,fontname,fontsize/
		',
		'toolbar_global_comments' => '
			bold, italic, underline, strike , - , link, smiley | help
		',
		'toolbar_sheet' => 'addrow,addrowbefore,addrowmulti,deleterow,-,addcolumn,addcolumnbefore,addcolumnmulti,deletecolumn,-,
							sheetgetrange,sheetrefresh,-,sheetfind|sheetclose,sheetsave,help/
							bold,italic,underline,strike,center,-,color,bgcolor,-,tikilink,nonparsed|fullscreen/',

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
		'ip_can_be_checked' => 'n',
		'shoutbox_autolink' => 'n',
		'show_comzone' => 'n',
		'use_proxy' => 'n',
		'webserverauth' => 'n',
	
		'case_patched' => 'n',

		'feature_intertiki_imported_groups' => '',
		'feature_contributor_wiki' => '',
		'https_login_required' => '',
		'maxRowsGalleries' => '',
		'replimaster' => '',
		'rowImagesGalleries' => '',
		'scaleSizeGalleries' => '',
		'thumbSizeXGalleries' => '',
		'thumbSizeYGalleries' => '',
		'javascript_enabled' => 'n',


		// SefUrl
		'feature_sefurl_paths' => array(''), //empty string needed to keep preference from setting unexpectedly

		'feature_bidi' => 'n',
		'feature_lastup' => 'y',

		'terminology_profile_installed' => 'n',
	));

	// Special default values

	global $tikidomain;
	if ( is_file('styles/'.$tikidomain.'/'.$prefs['site_favicon']) )
		$prefs['site_favicon'] = 'styles/'.$tikidomain.'/'.$prefs['site_favicon'];
	elseif ( ! is_file($prefs['site_favicon']) )
		$prefs['site_favicon'] = false;

	$_SESSION['tmpDir'] = class_exists('TikiInit') ? TikiInit::tempdir() : '/tmp';

	$prefs['feature_bidi'] = 'n';
	$prefs['feature_lastup'] = 'y';

	// Be sure we have a default value for user prefs
	foreach ( $prefs as $p => $v ) {
		if ( substr($p, 0, 12) == 'users_prefs_' ) {
			$prefs[substr($p, 12)] = $v;
		}
	}

	$cachelib->cacheItem("tiki_default_preferences_cache",serialize($prefs));
	return $prefs;
}


function initialize_prefs() {
	// Initialize prefs for which we want to use the site value (they will be prefixed with 'site_')
	// ( this is also used in tikilib, not only when reloading prefs )
	
	global $prefs, $tikiroot, $tikilib, $user_overrider_prefs;
		
	// Check if prefs needs to be reloaded
	if (isset($_SESSION['s_prefs'])) {

		// lastUpdatePrefs pref is retrived in tiki-setup_base
		$lastUpdatePrefs = isset($prefs['lastUpdatePrefs']) ? $prefs['lastUpdatePrefs'] : 1;

		// Reload if there was an update of some prefs
		if ( empty($_SESSION['s_prefs']['lastReadingPrefs']) || $lastUpdatePrefs > $_SESSION['s_prefs']['lastReadingPrefs'] ) {
			$_SESSION['need_reload_prefs'] = true;
		} else {
			$_SESSION['need_reload_prefs'] = false;
		}

		// Reload if the virtual host or tikiroot has changed
		if (!isset($_SESSION['lastPrefsSite'])) $_SESSION['lastPrefsSite'] = '';
		//   (this is needed when using the same php sessions for more than one tiki)
		if ( $_SESSION['lastPrefsSite'] != $_SERVER['SERVER_NAME'].'|'.$tikiroot ) {
			$_SESSION['lastPrefsSite'] = $_SERVER['SERVER_NAME'].'|'.$tikiroot;
			$_SESSION['need_reload_prefs'] = true;
		}

	} else {
		$_SESSION['need_reload_prefs'] = true;
	}

	$defaults = get_default_prefs();
	// Set default prefs only if needed
	if ( ! $_SESSION['need_reload_prefs'] ) {
		$modified = $_SESSION['s_prefs'];
	} else {

		// Find which preferences need to be serialized/unserialized, based on the default values (those with arrays as values)
		if ( ! isset($_SESSION['serialized_prefs']) ) {
			$_SESSION['serialized_prefs'] = array();
			foreach ( $defaults as $p => $v )
			if ( is_array($v) ) $_SESSION['serialized_prefs'][] = $p;
		}

		// Override default prefs with values specified in database
		$modified = isset($tikilib) ? $tikilib->get_db_preferences() : "";

		// Unserialize serialized preferences
		if ( isset($_SESSION['serialized_prefs']) && is_array($_SESSION['serialized_prefs']) ) {
			foreach ( $_SESSION['serialized_prefs'] as $p ) {
				if ( isset($modified[$p]) && ! is_array($modified[$p]) ) $modified[$p] = unserialize($modified[$p]);
			}
		}

		// Keep some useful sites values available before overriding with user prefs
		// (they could be used in templates, so we need to set them even for Anonymous)
		foreach ( $user_overrider_prefs as $uop ) {
			$modified['site_'.$uop] = isset($modified[$uop])?$modified[$uop]:$defaults[$uop];
		}

		// Assign prefs to the session
		$_SESSION['s_prefs'] = $modified;
	}

	// Disabled by default so it has to be modified
	global $in_installer, $section;	// but not if called during installer
	if( isset($modified['feature_perspective']) && $modified['feature_perspective'] == 'y' && empty($in_installer) ) {
		if( ! isset( $section ) || $section != 'admin' ) {
			require_once 'lib/perspectivelib.php';
			if( $persp = $perspectivelib->get_current_perspective( $modified ) ) {
				$changes = $perspectivelib->get_preferences( $persp );
				$modified = array_merge( $modified, $changes );
			}
		}
	}

	$prefs = empty($modified) ? $defaults : array_merge( $defaults, $modified );
	global $systemConfiguration;
	$prefs = array_merge($prefs, $systemConfiguration->preference->toArray());
}

