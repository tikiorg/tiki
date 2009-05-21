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

	if($prefs['feature_friends'] == 'y'){
		simple_set_toggle("feature_community_mouseover_friends");
	}

	simple_set_toggle("feature_community_mouseover_score");
	simple_set_toggle("feature_community_mouseover_country");
	simple_set_toggle("feature_community_mouseover_email");
	simple_set_toggle("feature_community_mouseover_lastlogin");
	simple_set_toggle("feature_community_mouseover_distance");

	if($prefs['feature_friends'] == 'y'){
		simple_set_toggle("feature_community_list_name");
		simple_set_toggle("feature_community_list_score");
		simple_set_toggle("feature_community_list_country");
		simple_set_toggle("feature_community_list_distance");
	}
	simple_set_value("user_list_order");
}


// Users Defaults
if (isset($_REQUEST['users_defaults'])) {
	check_ticket('admin-inc-login');

	// numerical and text values

	$_prefs = array(
		'users_prefs_mess_maxRecords',
		'users_prefs_mess_archiveAfter',
		'users_prefs_minPrio',
		'users_prefs_userbreadCrumb',
		'users_prefs_language',
		'users_prefs_display_timezone',
		'users_prefs_user_information',
		'users_prefs_mailCharset',
		'users_prefs_diff_versions',
		'users_prefs_tasks_maxRecords'
	);

	foreach($_prefs as $pref) {
		simple_set_value($pref);
	}

	// boolean values

	if($prefs['feature_wiki'] == 'y'){
		simple_set_toggle("users_prefs_user_dbl");
	}
	if($prefs['feature_community_mouseover'] == 'y'){
		simple_set_toggle("users_prefs_show_mouseover_user_info");
	}
	if($prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y'){
		simple_set_toggle("users_prefs_allowMsgs");
		simple_set_toggle("users_prefs_mess_sendReadStatus");
	}
	if($prefs['feature_wiki'] == 'y'){
		simple_set_toggle("users_prefs_mytiki_pages");
	}
	if($prefs['feature_blogs'] == 'y'){
		simple_set_toggle("users_prefs_mytiki_blogs");
	}
	if($prefs['feature_galleries'] == 'y'){
		simple_set_toggle("users_prefs_mytiki_gals");
	}
	if($prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y'){
		simple_set_toggle("users_prefs_mytiki_msgs");
	}
	if($prefs['feature_tasks'] == 'y' && $tiki_p_tasks =='y'){
		simple_set_toggle("users_prefs_mytiki_tasks");
	}
	if($prefs['feature_forums'] == 'y'){
		simple_set_toggle("users_prefs_mytiki_forum_topics");
		simple_set_toggle("users_prefs_mytiki_forum_replies");
	}
	if($prefs['feature_trackers'] == 'y'){
		simple_set_toggle("users_prefs_mytiki_items");
	}
	if($prefs['feature_workflow'] == 'y' && $tiki_p_use_workflow == 'y'){
		simple_set_toggle("users_prefs_mytiki_items");
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
