<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_community.php,v 1.5 2007-03-06 19:29:45 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["userfeatures"])) {
	check_ticket('admin-inc-community');
	simple_set_toggle("feature_community_gender");
	simple_set_toggle("feature_community_mouseover");
	simple_set_toggle("feature_community_mouseover_name");
	simple_set_toggle("feature_community_mouseover_gender");
	simple_set_toggle("feature_community_mouseover_picture");
	simple_set_toggle("feature_community_mouseover_friends");
	simple_set_toggle("feature_community_mouseover_score");
	simple_set_toggle("feature_community_mouseover_country");
	simple_set_toggle("feature_community_mouseover_email");
	simple_set_toggle("feature_community_mouseover_lastlogin");
	simple_set_toggle("feature_community_mouseover_distance");
	simple_set_toggle("feature_community_list_name");
	simple_set_toggle("feature_community_list_score");
	simple_set_toggle("feature_community_list_country");
	simple_set_toggle("feature_community_list_distance");
	simple_set_value("user_list_order");
}


// Users Defaults
if (isset($_REQUEST['users_defaults'])) {
	check_ticket('admin-inc-login');

	// numerical and text values
	$_prefs = array(
		'users_prefs_userbreadCrumb',
		'users_prefs_language',
		'users_prefs_display_timezone',
		'users_prefs_user_information',
		'users_prefs_mailCharset',
		'users_prefs_mess_maxRecords',
		'users_prefs_minPrio',
		'users_prefs_user_dbl',
		'users_prefs_diff_versions',
		'users_prefs_mess_archiveAfter',
		'users_prefs_tasks_maxRecords'
	);
	foreach($_prefs as $pref) {
		simple_set_value($pref);
	}

	// boolean values
	$_prefs = array(
		'users_prefs_show_mouseover_user_info',
		'users_prefs_allowMsgs',
		'users_prefs_mytiki_pages',
		'users_prefs_mytiki_blogs',
		'users_prefs_mytiki_articles',		
		'users_prefs_mytiki_gals',
		'users_prefs_mytiki_msgs',
		'users_prefs_mytiki_tasks',
		'users_prefs_mytiki_items',
		'users_prefs_mytiki_workflow',
		'users_prefs_mytiki_forum_topics',
		'users_prefs_mytiki_forum_replies',
		'users_prefs_mess_sendReadStatus'
	);
	foreach($_prefs as $pref) {
		simple_set_toggle($pref);
	}
}


// Users Defaults
$mailCharsets = array('utf-8', 'iso-8859-1');
$smarty->assign_by_ref('mailCharsets', $mailCharsets);

// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false,null,true);
$smarty->assign_by_ref('languages', $languages);


ask_ticket('admin-inc-community');
