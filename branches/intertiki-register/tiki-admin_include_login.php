<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_login.php,v 1.61.2.3 2008/03/23 14:12:05 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.

// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["loginprefs"])) {
	check_ticket('admin-inc-login');
	simple_set_toggle('change_theme');
	simple_set_toggle('change_language');
	simple_set_toggle('change_password');
	simple_set_value('messu_mailbox_size');
	simple_set_value('messu_archive_size');
	simple_set_value('messu_sent_size');
	simple_set_toggle('eponymousGroups');
	simple_set_toggle('userTracker');
	simple_set_toggle('groupTracker');
	simple_set_toggle('allowRegister');
	simple_set_toggle('validateRegistration');
	simple_set_toggle('webserverauth');
	simple_set_toggle('useRegisterPasscode');
	simple_set_value('registerPasscode');
	simple_set_value('min_username_length');
	simple_set_value('max_username_length');
	simple_set_value('min_pass_length');
	simple_set_value('pass_due');
	simple_set_value('email_due');
	simple_set_value('unsuccessful_logins');
	simple_set_toggle('validateUsers');
	simple_set_toggle('validateEmail');
	simple_set_toggle('login_is_email');
	simple_set_toggle('rnd_num_reg');
	simple_set_toggle('pass_chr_num');
	simple_set_toggle('lowercase_username');
	simple_set_toggle('feature_challenge');
	simple_set_toggle('feature_clear_passwords');
	simple_set_toggle('forgotPass');
	simple_set_value('feature_crypt_passwords');
	simple_set_value('https_login');
	simple_set_toggle('feature_show_stay_in_ssl_mode');
	simple_set_toggle('feature_switch_ssl_mode');
	simple_set_value('feature_crypt_passwords');
	simple_set_value('http_port');
	simple_set_value('https_port');
	simple_set_value('rememberme');
	simple_set_value('remembertime');
	simple_set_value('cookie_name');
	simple_set_value('cookie_domain');
	simple_set_value('cookie_path');
	simple_set_value('auth_method');
	simple_set_toggle('feature_ticketlib');
	simple_set_toggle('feature_ticketlib2');
	simple_set_value('highlight_group');
	simple_set_value('user_tracker_infos');
	simple_set_toggle('desactive_login_autocomplete');
	simple_set_value('available_languages');
	simple_set_value('available_styles');


	if (isset($_REQUEST['registration_choices'])) {
		$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
		$in = array();
		$out = array();
		foreach ($listgroups['data'] as $gr) {
			if ($gr['groupName'] == 'Anonymous')
				continue;
			if ($gr['registrationChoice'] == 'y' && !in_array($gr['groupName'], $_REQUEST['registration_choices'])) // deselect
				$out[] = $gr['groupName'];
			elseif ($gr['registrationChoice'] != 'y' && in_array($gr['groupName'], $_REQUEST['registration_choices'])) //select
				$in[] = $gr['groupName'];
		}
		if (count($in))
			$userlib->set_registrationChoice($in, 'y');
		if (count($out))
			$userlib->set_registrationChoice($out, NULL);
	}
	simple_set_toggle('feature_display_my_to_others');
}

if (isset($_REQUEST["auth_pear"])) {
	check_ticket('admin-inc-login');
	simple_set_toggle('auth_create_user_tiki');
	simple_set_toggle('auth_create_user_auth');
	simple_set_toggle('auth_skip_admin');
	simple_set_value('auth_ldap_url');
	simple_set_value('auth_pear_host');
	simple_set_value('auth_pear_port');
	simple_set_value('auth_ldap_scope');
	simple_set_value('auth_ldap_basedn');
	simple_set_value('auth_ldap_userdn');
	simple_set_value('auth_ldap_userattr');
	simple_set_value('auth_ldap_url');
	simple_set_value('auth_ldap_useroc');
	simple_set_value('auth_ldap_groupdn');
	simple_set_value('auth_ldap_groupattr');
	simple_set_value('auth_ldap_groupoc');
	simple_set_value('auth_ldap_memberattr');
	simple_set_toggle('auth_ldap_memberisdn');
	simple_set_value('auth_ldap_adminuser');
	simple_set_value('auth_ldap_adminpass');
	simple_set_value('auth_ldap_version');
	simple_set_value('auth_ldap_nameattr');
}

if (isset($_REQUEST["auth_pam"])) {
	check_ticket('admin-inc-login');
	simple_set_toggle('pam_create_user_tiki');
	simple_set_toggle('pam_skip_admin');
	simple_set_value('pam_service');
}

if ($phpcas_enabled == 'y') {
	if (isset($_REQUEST['auth_cas'])) {
		check_ticket('admin-inc-login');
		simple_set_toggle('cas_create_user_tiki');
		simple_set_toggle('cas_skip_admin');
		simple_set_value('cas_version');
		simple_set_value('cas_hostname');
		simple_set_value('cas_port');
		simple_set_value('cas_path');
	}
}

if (isset($_REQUEST['auth_shib'])) {
	check_ticket('admin-inc-login');
  simple_set_toggle('shib_create_user_tiki');
  simple_set_toggle('shib_skip_admin');
  simple_set_value('shib_affiliation');
  simple_set_toggle('shib_usegroup');
  simple_set_value('shib_group');
}

// Users Defaults
if (isset($_REQUEST['users_defaults'])) {
	check_ticket('admin-inc-login');

	// numerical and text values
	$_prefs = array(
		'users_prefs_theme',
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

$smarty->assign("phpcas_enabled", $phpcas_enabled);

// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false,null,true);
$smarty->assign_by_ref('languages', $languages);

$smarty->assign("styles", $tikilib->list_styles());

$listTrackers = $tikilib->list_trackers(0,-1,"name_desc","");
$smarty->assign("listTrackers",$listTrackers['list']);

$listgroups = $userlib->get_groups(0, -1, 'groupName_desc', '', '', 'n');
$smarty->assign("listgroups", $listgroups['data']);

// Users Defaults
$mailCharsets = array('utf-8', 'iso-8859-1');
$smarty->assign_by_ref('mailCharsets', $mailCharsets);

ask_ticket('admin-inc-login');
?>
