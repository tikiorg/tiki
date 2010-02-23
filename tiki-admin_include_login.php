<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_login.php,v 1.61.2.3 2008/03/23 14:12:05 sylvieg Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (isset($_REQUEST["loginprefs"])) {
	check_ticket('admin-inc-login');
	simple_set_toggle('change_password');
	simple_set_value('messu_mailbox_size');
	simple_set_value('messu_archive_size');
	simple_set_value('messu_sent_size');
	simple_set_toggle('eponymousGroups');
	simple_set_toggle('userTracker');
	simple_set_toggle('groupTracker');
	simple_set_toggle('allowRegister');
	simple_set_toggle('validateRegistration');
	simple_set_value('validator_emails');
	simple_set_toggle('webserverauth');
	simple_set_toggle('useRegisterPasscode');
	simple_set_value('registerPasscode');
	simple_set_value('min_username_length');
	simple_set_value('max_username_length');
	simple_set_value('username_pattern');
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
	simple_set_toggle('generate_password');
	simple_set_value('https_login');
	simple_set_toggle('feature_show_stay_in_ssl_mode');
	simple_set_toggle('feature_switch_ssl_mode');
	simple_set_value('http_port');
	simple_set_value('https_port');
	simple_set_value('rememberme');
	simple_set_value('remembertime');
	simple_set_value('cookie_name');
	simple_set_value('cookie_domain');
	simple_set_value('cookie_path');
	simple_set_value('auth_method');
	simple_set_value('highlight_group');
	simple_set_value('user_tracker_infos');
	simple_set_toggle('desactive_login_autocomplete');
	simple_set_toggle('permission_denied_login_box');
	simple_set_value('permission_denied_url');
	simple_set_value('url_after_validation');
	if (isset($_REQUEST['registration_choices'])) {
		$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
		$in = array();
		$out = array();
		foreach($listgroups['data'] as $gr) {
			if ($gr['groupName'] == 'Anonymous') continue;
			if ($gr['registrationChoice'] == 'y' && !in_array($gr['groupName'], $_REQUEST['registration_choices'])) // deselect
			$out[] = $gr['groupName'];
			elseif ($gr['registrationChoice'] != 'y' && in_array($gr['groupName'], $_REQUEST['registration_choices'])) //select
			$in[] = $gr['groupName'];
		}
		if (count($in)) $userlib->set_registrationChoice($in, 'y');
		if (count($out)) $userlib->set_registrationChoice($out, NULL);
	}
	simple_set_toggle('feature_display_my_to_others');
}
if (isset($_REQUEST["auth_ldap"])) {
	check_ticket('admin-inc-login');
	simple_set_toggle('ldap_create_user_tiki');
	simple_set_toggle('ldap_create_user_ldap');
	simple_set_toggle('ldap_skip_admin');
	simple_set_toggle('auth_ldap_permit_tiki_users');
	simple_set_value('auth_ldap_host');
	simple_set_value('auth_ldap_port');
	simple_set_toggle('auth_ldap_debug');
	simple_set_toggle('auth_ldap_ssl');
	simple_set_toggle('auth_ldap_starttls');
	simple_set_value('auth_ldap_type');
	simple_set_value('auth_ldap_scope');
	simple_set_value('auth_ldap_basedn');
	simple_set_value('auth_ldap_userdn');
	simple_set_value('auth_ldap_userattr');
	simple_set_value('auth_ldap_useroc');
	simple_set_value('auth_ldap_nameattr');
	simple_set_value('auth_ldap_emailattr');
	simple_set_value('auth_ldap_countryattr');
	simple_set_toggle('auth_ldap_syncuserattr');
	simple_set_toggle('auth_ldap_syncgroupattr');
	simple_set_value('auth_ldap_groupdn');
	simple_set_value('auth_ldap_groupattr');
	simple_set_value('auth_ldap_groupdescattr');
	simple_set_value('auth_ldap_groupoc');
	simple_set_value('auth_ldap_memberattr');
	simple_set_toggle('auth_ldap_memberisdn');
	simple_set_value('auth_ldap_adminuser');
	simple_set_value('auth_ldap_adminpass');
	simple_set_value('auth_ldap_version');
	simple_set_value('auth_ldap_usergroupattr');
	simple_set_value('auth_ldap_groupgroupattr');
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
$smarty->assign("phpcas_enabled", $phpcas_enabled);
$smarty->assign('gd_lib_found', function_exists('gd_info') ? 'y' : 'n');
// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false, null, true);
$smarty->assign_by_ref('languages', $languages);
$smarty->assign("styles", $tikilib->list_styles());
$listTrackers = $tikilib->list_trackers(0, -1, "name_desc", "");
$smarty->assign("listTrackers", $listTrackers['list']);
$listgroups = $userlib->get_groups(0, -1, 'groupName_desc', '', '', 'n');
$smarty->assign("listgroups", $listgroups['data']);
ask_ticket('admin-inc-login');
