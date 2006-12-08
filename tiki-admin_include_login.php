<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_login.php,v 1.46 2006-12-08 10:49:35 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.

// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["loginprefs"])) {
	check_ticket('admin-inc-login');
    if (isset($_REQUEST["change_theme"]) && $_REQUEST["change_theme"] == "on") {
	$tikilib->set_preference("change_theme", 'y');
    } else {
	$tikilib->set_preference("change_theme", 'n');
    }

    if (isset($_REQUEST["change_language"]) && $_REQUEST["change_language"] == "on") {
	$tikilib->set_preference("change_language", 'y');
    } else {
	$tikilib->set_preference("change_language", 'n');
    }

    if (isset($_REQUEST["change_password"]) && $_REQUEST["change_password"] == "on") {
	$tikilib->set_preference("change_password", 'y');
    } else {
	$tikilib->set_preference("change_password", 'n');
    }

    if (isset($_REQUEST["change_language"]) && isset($_REQUEST["available_languages"])) {
	$tikilib->set_preference("available_languages", serialize($_REQUEST["available_languages"]));
    } else {
	$tikilib->set_preference("available_languages", serialize(array()));
    }

    if (isset($_REQUEST["available_styles"])) {
	$tikilib->set_preference("available_styles", serialize($_REQUEST["available_styles"]));
    } else {
	$tikilib->set_preference("available_styles", serialize(array()));
    }

    if (isset($_REQUEST["messu_mailbox_size"])) {
	    $tikilib->set_preference("messu_mailbox_size", $_REQUEST["messu_mailbox_size"]);
		$smarty->assign('messu_mailbox_size', $_REQUEST["messu_mailbox_size"]);
    } else {
	    $tikilib->set_preference("messu_mailbox_size", '0');
		$smarty->assign('messu_mailbox_size', 0);
    }

    if (isset($_REQUEST["messu_archive_size"])) {
	    $tikilib->set_preference("messu_archive_size", $_REQUEST["messu_archive_size"]);
		$smarty->assign('messu_archive_size', $_REQUEST["messu_archive_size"]);
    } else {
	    $tikilib->set_preference("messu_archive_size", '200');
		$smarty->assign('messu_archive_size', 200);
    }

    if (isset($_REQUEST["messu_sent_size"])) {
	    $tikilib->set_preference("messu_sent_size", $_REQUEST["messu_sent_size"]);
		$smarty->assign('messu_sent_size', $_REQUEST["messu_sent_size"]);
    } else {
	    $tikilib->set_preference("messu_sent_size", '200');
		$smarty->assign('messu_sent_size', 200);
    }

    if (isset($_REQUEST["eponymousGroups"]) &&
	    $_REQUEST["eponymousGroups"] == "on")
    {
	$tikilib->set_preference("eponymousGroups", 'y');

	$smarty->assign('eponymousGroups', 'y');
    } else {
	$tikilib->set_preference("eponymousGroups", 'n');

	$smarty->assign('eponymousGroups', 'n');
    }
		
	if (isset($_REQUEST["userTracker"]) && $_REQUEST["userTracker"] == "on") {
		$tikilib->set_preference("userTracker", 'y');
	} else {
		$tikilib->set_preference("userTracker", 'n');
	}

	if (isset($_REQUEST["groupTracker"]) && $_REQUEST["groupTracker"] == "on") {
		$tikilib->set_preference("groupTracker", 'y');
	} else {
		$tikilib->set_preference("groupTracker", 'n');
	}

    if (isset($_REQUEST["allowRegister"]) && $_REQUEST["allowRegister"] == "on") {
	$tikilib->set_preference("allowRegister", 'y');

	$smarty->assign('allowRegister', 'y');
    } else {
	$tikilib->set_preference("allowRegister", 'n');

	$smarty->assign('allowRegister', 'n');
    }

	if (isset($_REQUEST["validateRegistration"]) && $_REQUEST["validateRegistration"] == "on") {
		$tikilib->set_preference("validateRegistration", 'y');
		$smarty->assign('validateRegistration', 'y');
	} else {
		$tikilib->set_preference("validateRegistration", 'n');
		$smarty->assign('validateRegistration', 'n');
	}

    if (isset($_REQUEST["webserverauth"]) && $_REQUEST["webserverauth"] == "on") {
	$tikilib->set_preference("webserverauth", 'y');

	$smarty->assign('webserverauth', 'y');
    } else {
	$tikilib->set_preference("webserverauth", 'n');

	$smarty->assign('webserverauth', 'n');
    }

    if (isset($_REQUEST["useRegisterPasscode"]) && $_REQUEST["useRegisterPasscode"] == "on") {
	$tikilib->set_preference("useRegisterPasscode", 'y');

	$smarty->assign('useRegisterPasscode', 'y');
    } else {
	$tikilib->set_preference("useRegisterPasscode", 'n');

	$smarty->assign('useRegisterPasscode', 'n');
    }

    $tikilib->set_preference("registerPasscode", $_REQUEST["registerPasscode"]);
    $smarty->assign('registerPasscode', $_REQUEST["registerPasscode"]);

    $tikilib->set_preference("min_username_length", $_REQUEST["min_username_length"]);
    $smarty->assign('min_username_length', $_REQUEST["min_username_length"]);

    $tikilib->set_preference("max_username_length", $_REQUEST["max_username_length"]);
    $smarty->assign('max_username_length', $_REQUEST["max_username_length"]);

    $tikilib->set_preference("min_pass_length", $_REQUEST["min_pass_length"]);
    $smarty->assign('min_pass_length', $_REQUEST["min_pass_length"]);

    $tikilib->set_preference("pass_due", $_REQUEST["pass_due"]);
    $smarty->assign('pass_due', $_REQUEST["pass_due"]);

    if (isset($_REQUEST["validateUsers"]) && $_REQUEST["validateUsers"] == "on") {
	$tikilib->set_preference("validateUsers", 'y');

	$smarty->assign('validateUsers', 'y');
    } else {
	$tikilib->set_preference("validateUsers", 'n');

	$smarty->assign('validateUsers', 'n');
    }

    if (isset($_REQUEST["validateEmail"]) && $_REQUEST["validateEmail"] == "on") {
        $tikilib->set_preference("validateEmail", 'y');

        $smarty->assign('validateEmail', 'y');
    } else {
        $tikilib->set_preference("validateEmail", 'n');

        $smarty->assign('validateEmail', 'n');
    }

    if (isset($_REQUEST["allowmsg_is_optional"]) && $_REQUEST["allowmsg_is_optional"] == "on") {
        $tikilib->set_preference("allowmsg_is_optional", 'y');
        $smarty->assign('allowmsg_is_optional', 'y');
    } else {
        $tikilib->set_preference("allowmsg_is_optional", 'n');
        $smarty->assign('allowmsg_is_optional', 'n');
    }

    if (isset($_REQUEST["allowmsg_by_default"]) && $_REQUEST["allowmsg_by_default"] == "on") {
        $tikilib->set_preference("allowmsg_by_default", 'y');
        $smarty->assign('allowmsg_by_default', 'y');
    } else {
        $tikilib->set_preference("allowmsg_by_default", 'n');
        $smarty->assign('allowmsg_by_default', 'n');
    }


    if (isset($_REQUEST["rnd_num_reg"]) && $_REQUEST["rnd_num_reg"] == "on") {
	$tikilib->set_preference("rnd_num_reg", 'y');

	$smarty->assign('rnd_num_reg', 'y');
    } else {
	$tikilib->set_preference("rnd_num_reg", 'n');

	$smarty->assign('rnd_num_reg', 'n');
    }

    if (isset($_REQUEST["pass_chr_num"]) && $_REQUEST["pass_chr_num"] == "on") {
	$tikilib->set_preference("pass_chr_num", 'y');

	$smarty->assign('pass_chr_num', 'y');
    } else {
	$tikilib->set_preference("pass_chr_num", 'n');

	$smarty->assign('pass_chr_num', 'n');
    }

    if (isset($_REQUEST["lowercase_username"]) && $_REQUEST["lowercase_username"] == "on") {
	$tikilib->set_preference("lowercase_username", 'y');

	$smarty->assign('lowercase_username', 'y');
    } else {
	$tikilib->set_preference("lowercase_username", 'n');

	$smarty->assign('lowercase_username', 'n');
    }

    if (isset($_REQUEST["feature_challenge"]) && $_REQUEST["feature_challenge"] == "on") {
	$tikilib->set_preference("feature_challenge", 'y');

	$smarty->assign('feature_challenge', 'y');
    } else {
	$tikilib->set_preference("feature_challenge", 'n');

	$smarty->assign('feature_challenge', 'n');
    }

    if (isset($_REQUEST["feature_clear_passwords"]) && $_REQUEST["feature_clear_passwords"] == "on") {
	$tikilib->set_preference("feature_clear_passwords", 'y');

	$smarty->assign('feature_clear_passwords', 'y');
    } else {
	$tikilib->set_preference("feature_clear_passwords", 'n');

	$smarty->assign('feature_clear_passwords', 'n');
    }

    if (isset($_REQUEST["feature_crypt_passwords"])) {
	$tikilib->set_preference('feature_crypt_passwords', $_REQUEST['feature_crypt_passwords']);

	$smarty->assign('feature_crypt_passwords', $_REQUEST['feature_crypt_passwords']);
    }

    if (isset($_REQUEST["forgotPass"]) && $_REQUEST["forgotPass"] == "on") {
	$tikilib->set_preference("forgotPass", 'y');

	$smarty->assign('forgotPass', 'y');
    } else {
	$tikilib->set_preference("forgotPass", 'n');

	$smarty->assign('forgotPass', 'n');
    }

    /* # not implemented
       $b = isset($_REQUEST['http_basic_auth']) && $_REQUEST['http_basic_auth'] == 'on';
       $tikilib->set_preference('http_basic_auth', $b); 
       $smarty->assign('http_basic_auth', $b);
     */

    $b = (isset($_REQUEST['https_login']) && $_REQUEST['https_login'] == 'on') ? 'y' : 'n';
    $tikilib->set_preference('https_login', $b);
    $tikilib->set_preference('useUrlIndex', 'n');
    $smarty->assign('https_login', $b);
    $smarty->assign('useUrlIndex', 'n');

    $b = (isset($_REQUEST['https_login_required']) && $_REQUEST['https_login_required'] == 'on') ? 'y' : 'n';
    $tikilib->set_preference('https_login_required', $b);
    $tikilib->set_preference('useUrlIndex', 'n');
    $smarty->assign('https_login_required', $b);
    $smarty->assign('useUrlIndex', 'n');

    $v = isset($_REQUEST['http_domain']) ? $_REQUEST['http_domain'] : '';
    $tikilib->set_preference('http_domain', $v);
    $smarty->assign('http_domain', $v);

    $v = isset($_REQUEST['http_port']) ? $_REQUEST['http_port'] : 80;
    $tikilib->set_preference('http_port', $v);
    $smarty->assign('http_port', $v);

    $v = isset($_REQUEST['http_prefix']) ? $_REQUEST['http_prefix'] : '/';
    $tikilib->set_preference('http_prefix', $v);
    $smarty->assign('http_prefix', $v);

    $v = isset($_REQUEST['https_domain']) ? $_REQUEST['https_domain'] : '';
    $tikilib->set_preference('https_domain', $v);
    $smarty->assign('https_domain', $v);

    $v = isset($_REQUEST['https_port']) ? $_REQUEST['https_port'] : 443;
    $tikilib->set_preference('https_port', $v);
    $smarty->assign('https_port', $v);

    $v = isset($_REQUEST['https_prefix']) ? $_REQUEST['https_prefix'] : '/';
    $tikilib->set_preference('https_prefix', $v);
    $smarty->assign('https_prefix', $v);
    $tikilib->set_preference('rememberme', $_REQUEST['rememberme']);
    $tikilib->set_preference('remembertime', $_REQUEST['remembertime']);
    $smarty->assign('rememberme', $_REQUEST['rememberme']);
    $smarty->assign('remembertime', $_REQUEST['remembertime']);
		
		$v = isset($_REQUEST['cookie_name']) ? $_REQUEST['cookie_name'] : $_SERVER['SERVER_NAME'];
    $tikilib->set_preference('cookie_name', $v);
    $smarty->assign('cookie_name', $v);

		$v = isset($_REQUEST['cookie_domain']) ? $_REQUEST['cookie_domain'] : $_SERVER['SERVER_NAME'];
    $tikilib->set_preference('cookie_domain', $v);
    $smarty->assign('cookie_domain', $v);

		$v = isset($_REQUEST['cookie_path']) ? $_REQUEST['cookie_path'] : '/';
    $tikilib->set_preference('cookie_path', $v);
    $smarty->assign('cookie_path', $v);

    if (isset($_REQUEST["auth_method"])) {
	$tikilib->set_preference('auth_method', $_REQUEST['auth_method']);

	$smarty->assign('auth_method', $_REQUEST['auth_method']);
    }

	$b = (isset($_REQUEST['feature_ticketlib']) && $_REQUEST['feature_ticketlib'] == 'on') ? 'y' : 'n';
	$tikilib->set_preference('feature_ticketlib', $b);
	$smarty->assign('feature_ticketlib', $b);

	$b = (isset($_REQUEST['feature_ticketlib2']) && $_REQUEST['feature_ticketlib2'] == 'on') ? 'y' : 'n';
	$tikilib->set_preference('feature_ticketlib2', $b);
	$smarty->assign('feature_ticketlib2', $b);

	$v = isset($_REQUEST['highlight_group']) ? $_REQUEST['highlight_group'] : '';
	$tikilib->set_preference('highlight_group', $v);
	$smarty->assign('highlight_group', $v);

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
    if (isset($_REQUEST["auth_create_user_tiki"]) && $_REQUEST["auth_create_user_tiki"] == "on") {
	$tikilib->set_preference("auth_create_user_tiki", 'y');

	$smarty->assign("auth_create_user_tiki", 'y');
    } else {
	$tikilib->set_preference("auth_create_user_tiki", 'n');

	$smarty->assign("auth_create_user_tiki", 'n');
    }

    if (isset($_REQUEST["auth_create_user_auth"]) && $_REQUEST["auth_create_user_auth"] == "on") {
	$tikilib->set_preference("auth_create_user_auth", 'y');

	$smarty->assign("auth_create_user_auth", 'y');
    } else {
	$tikilib->set_preference("auth_create_user_auth", 'n');

	$smarty->assign("auth_create_user_auth", 'n');
    }

    if (isset($_REQUEST["auth_skip_admin"]) && $_REQUEST["auth_skip_admin"] == "on") {
	$tikilib->set_preference("auth_skip_admin", 'y');

	$smarty->assign("auth_skip_admin", 'y');
    } else {
	$tikilib->set_preference("auth_skip_admin", 'n');

	$smarty->assign("auth_skip_admin", 'n');
    }

    if (isset($_REQUEST["auth_ldap_url"])) {
	$tikilib->set_preference("auth_ldap_url", $_REQUEST["auth_ldap_url"]);

	$smarty->assign('auth_ldap_url', $_REQUEST["auth_ldap_url"]);
    }

    if (isset($_REQUEST["auth_pear_host"])) {
	$tikilib->set_preference("auth_pear_host", $_REQUEST["auth_pear_host"]);

	$smarty->assign('auth_pear_host', $_REQUEST["auth_pear_host"]);
    }

    if (isset($_REQUEST["auth_pear_port"])) {
	$tikilib->set_preference("auth_pear_port", $_REQUEST["auth_pear_port"]);

	$smarty->assign('auth_pear_port', $_REQUEST["auth_pear_port"]);
    }

    if (isset($_REQUEST["auth_ldap_scope"])) {
	$tikilib->set_preference("auth_ldap_scope", $_REQUEST["auth_ldap_scope"]);

	$smarty->assign('auth_ldap_scope', $_REQUEST["auth_ldap_scope"]);
    }

    if (isset($_REQUEST["auth_ldap_basedn"])) {
	$tikilib->set_preference("auth_ldap_basedn", $_REQUEST["auth_ldap_basedn"]);

	$smarty->assign('auth_ldap_basedn', $_REQUEST["auth_ldap_basedn"]);
    }

    if (isset($_REQUEST["auth_ldap_userdn"])) {
	$tikilib->set_preference("auth_ldap_userdn", $_REQUEST["auth_ldap_userdn"]);

	$smarty->assign('auth_ldap_userdn', $_REQUEST["auth_ldap_userdn"]);
    }

    if (isset($_REQUEST["auth_ldap_userattr"])) {
	$tikilib->set_preference("auth_ldap_userattr", $_REQUEST["auth_ldap_userattr"]);

	$smarty->assign('auth_ldap_userattr', $_REQUEST["auth_ldap_userattr"]);
    }

    if (isset($_REQUEST["auth_ldap_useroc"])) {
	$tikilib->set_preference("auth_ldap_useroc", $_REQUEST["auth_ldap_useroc"]);

	$smarty->assign('auth_ldap_useroc', $_REQUEST["auth_ldap_useroc"]);
    }

    if (isset($_REQUEST["auth_ldap_groupdn"])) {
	$tikilib->set_preference("auth_ldap_groupdn", $_REQUEST["auth_ldap_groupdn"]);

	$smarty->assign('auth_ldap_groupdn', $_REQUEST["auth_ldap_groupdn"]);
    }

    if (isset($_REQUEST["auth_ldap_groupattr"])) {
	$tikilib->set_preference("auth_ldap_groupattr", $_REQUEST["auth_ldap_groupattr"]);

	$smarty->assign('auth_ldap_groupattr', $_REQUEST["auth_ldap_groupattr"]);
    }

    if (isset($_REQUEST["auth_ldap_groupoc"])) {
	$tikilib->set_preference("auth_ldap_groupoc", $_REQUEST["auth_ldap_groupoc"]);

	$smarty->assign('auth_ldap_groupoc', $_REQUEST["auth_ldap_groupoc"]);
    }

    if (isset($_REQUEST["auth_ldap_memberattr"])) {
	$tikilib->set_preference("auth_ldap_memberattr", $_REQUEST["auth_ldap_memberattr"]);

	$smarty->assign('auth_ldap_ldap_memberattr', $_REQUEST["auth_ldap_memberattr"]);
    }

    if (isset($_REQUEST["auth_ldap_memberisdn"]) && $_REQUEST["auth_ldap_memberisdn"] == "on") {
	$tikilib->set_preference("auth_ldap_memberisdn", 'y');

	$smarty->assign("auth_ldap_memberisdn", 'y');
    } else {
	$tikilib->set_preference("auth_ldap_memberisdn", 'n');

	$smarty->assign("auth_ldap_memberisdn", 'n');
    }

    if (isset($_REQUEST["auth_ldap_adminuser"])) {
	$tikilib->set_preference("auth_ldap_adminuser", $_REQUEST["auth_ldap_adminuser"]);

	$smarty->assign('auth_ldap_adminuser', $_REQUEST["auth_ldap_adminuser"]);
    }

    if (isset($_REQUEST["auth_ldap_adminpass"])) {
	$tikilib->set_preference("auth_ldap_adminpass", $_REQUEST["auth_ldap_adminpass"]);

	$smarty->assign('auth_ldap_adminpass', $_REQUEST["auth_ldap_adminpass"]);
    }
}

if (isset($_REQUEST["auth_pam"])) {
        check_ticket('admin-inc-login');
    if (isset($_REQUEST["pam_create_user_tiki"]) && $_REQUEST["pam_create_user_tiki"] ==  "on") {
        $tikilib->set_preference("pam_create_user_tiki", 'y');

        $smarty->assign("pam_create_user_tiki", 'y');
    } else {
        $tikilib->set_preference("pam_create_user_tiki", 'n');

        $smarty->assign("pam_create_user_tiki", 'n');
    }
    if (isset($_REQUEST["pam_skip_admin"]) && $_REQUEST["pam_skip_admin"] == "on") {
        $tikilib->set_preference("pam_skip_admin", 'y');

        $smarty->assign("pam_skip_admin", 'y');
    } else {
        $tikilib->set_preference("pam_skip_admin", 'n');

        $smarty->assign("pam_skip_admin", 'n');
    }
    if (isset($_REQUEST["pam_service"])) {
        $tikilib->set_preference("pam_service", $_REQUEST["pam_service"]);

        $smarty->assign('pam_service', $_REQUEST["pam_service"]);
    }
}

if (isset($_REQUEST['auth_cas'])) {
        check_ticket('admin-inc-login');
    if (isset($_REQUEST['cas_create_user_tiki']) && $_REQUEST['cas_create_user_tiki'] ==  'on') {
        $tikilib->set_preference('cas_create_user_tiki', 'y');

        $smarty->assign('cas_create_user_tiki', 'y');
    } else {
        $tikilib->set_preference('cas_create_user_tiki', 'n');

        $smarty->assign('cas_create_user_tiki', 'n');
    }
    if (isset($_REQUEST['cas_skip_admin']) && $_REQUEST['cas_skip_admin'] == 'on') {
        $tikilib->set_preference('cas_skip_admin', 'y');

        $smarty->assign('cas_skip_admin', 'y');
    } else {
        $tikilib->set_preference('cas_skip_admin', 'n');

        $smarty->assign('cas_skip_admin', 'n');
    }
	if (isset($_REQUEST['cas_version'])) {
		$tikilib->set_preference('cas_version', $_REQUEST['cas_version']);

		$smarty->assign('cas_version', $_REQUEST['cas_version']);
	}
	if (isset($_REQUEST['cas_hostname'])) {
		$tikilib->set_preference('cas_hostname', $_REQUEST['cas_hostname']);

		$smarty->assign('cas_hostname', $_REQUEST['cas_hostname']);
	}
	if (isset($_REQUEST['cas_port'])) {
		$tikilib->set_preference('cas_port', $_REQUEST['cas_port']);

		$smarty->assign('cas_port', $_REQUEST['cas_port']);
	}
	if (isset($_REQUEST['cas_path'])) {
		$tikilib->set_preference('cas_path', $_REQUEST['cas_path']);

		$smarty->assign('cas_path', $_REQUEST['cas_path']);
	}
}

if (isset($_REQUEST['auth_shib'])) {
        check_ticket('admin-inc-login');
    if (isset($_REQUEST['shib_create_user_tiki']) && $_REQUEST['shib_create_user_tiki'] ==  'on') {
        $tikilib->set_preference('shib_create_user_tiki', 'y');

        $smarty->assign('shib_create_user_tiki', 'y');
    } else {
        $tikilib->set_preference('shib_create_user_tiki', 'n');

        $smarty->assign('shib_create_user_tiki', 'n');
    }
    if (isset($_REQUEST['shib_skip_admin']) && $_REQUEST['shib_skip_admin'] == 'on') {
        $tikilib->set_preference('shib_skip_admin', 'y');

        $smarty->assign('shib_skip_admin', 'y');
    } else {
        $tikilib->set_preference('shib_skip_admin', 'n');

        $smarty->assign('shib_skip_admin', 'n');
    }
    if (isset($_REQUEST['shib_affiliation'])) {
        $tikilib->set_preference('shib_affiliation', $_REQUEST['shib_affiliation']);

		$smarty->assign('shib_affiliation', $_REQUEST['shib_affiliation']);
    }
	
    if (isset($_REQUEST['shib_usegroup']) && $_REQUEST['shib_usegroup'] == 'on') {
		$tikilib->set_preference('shib_usegroup', 'y');

		$smarty->assign('shib_usegroup', 'y');
		
		if (isset($_REQUEST['shib_group']) && $_REQUEST['shib_group'] != '') 
		{
			$tikilib->set_preference('shib_group', $_REQUEST['shib_group']);
		
			$smarty->assign('shib_group', $_REQUEST['shib_group']);
		}
		else
		{
			$tikilib->set_preference('shib_group', 'Shibboleth');
			$smarty->assign('shib_group', 'Shibboleth');
		}
	}
	else
	{
		$tikilib->set_preference('shib_usegroup', 'n');
		$smarty->assign('shib_usegroup', 'n');
		$tikilib->set_preference('shib_group', '');
		$smarty->assign('shib_group', '');

	}
}

// Users Defaults
if (isset($_REQUEST['users_defaults'])) {
	check_ticket('admin-inc-login');

	// numerical and text values
	$prefs = array(
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
	foreach($prefs as $pref) {
		if(isset($_REQUEST[$pref])) {
			$tikilib->set_preference($pref, $_REQUEST[$pref]);
			$smarty->assign($pref, $_REQUEST[$pref]);
		}
	}
	
	// boolean values
	$prefs = array(
		'users_prefs_show_mouseover_user_info',
		'users_prefs_allowMsgs',
		'users_prefs_mytiki_pages',
		'users_prefs_mytiki_blogs',
		'users_prefs_mytiki_gals',
		'users_prefs_mytiki_msgs',
		'users_prefs_mytiki_tasks',
		'users_prefs_mytiki_items',
		'users_prefs_mytiki_workflow',
		'users_prefs_mess_sendReadStatus'
	);
	foreach($prefs as $pref) {
		if(isset($_REQUEST[$pref])) {
			$tikilib->set_preference($pref, 'y');
			$smarty->assign($pref, 'y');
		}
		else {
			$tikilib->set_preference($pref, 'n');
			$smarty->assign($pref, 'n');
		}
	}
}

// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages(false,null,true);
$smarty->assign_by_ref('languages', $languages);

$smarty->assign("styles", $tikilib->list_styles());

$smarty->assign("available_languages", unserialize($tikilib->get_preference("available_languages")));
$smarty->assign("available_styles", unserialize($tikilib->get_preference("available_styles")));

$smarty->assign("userTracker", $tikilib->get_preference("userTracker", "n"));
$smarty->assign("groupTracker", $tikilib->get_preference("groupTracker", "n"));

$listTrackers = $tikilib->list_trackers(0,-1,"name_desc","");
$smarty->assign("listTrackers",$listTrackers['list']);

$smarty->assign("change_theme", $tikilib->get_preference("change_theme", "n"));
$smarty->assign("change_language", $tikilib->get_preference("change_language", "n"));
$smarty->assign("change_password", $tikilib->get_preference("change_password", "y"));
$smarty->assign("rememberme", $tikilib->get_preference("rememberme", "disabled"));
$smarty->assign("remembertime", $tikilib->get_preference("remembertime", 7200));
$smarty->assign("allowRegister", $tikilib->get_preference("allowRegister", 'n'));
$smarty->assign("eponymousGroups", $tikilib->get_preference("eponymousGroups", 'n'));
$smarty->assign("useRegisterPasscode", $tikilib->get_preference("useRegisterPasscode", 'n'));
$smarty->assign("registerPasscode", $tikilib->get_preference("registerPasscode", ''));
$smarty->assign("validateUsers", $tikilib->get_preference("validateUsers", 'n'));
$smarty->assign("validateEmail", $tikilib->get_preference("validateEmail", 'n'));
$smarty->assign("forgotPass", $tikilib->get_preference("forgotPass", 'n'));
$smarty->assign("highlight_group", $tikilib->get_preference("highlight_group", ''));
$listgroups = $userlib->get_groups(0, -1, 'groupName_desc', '', '', 'n');
$smarty->assign("listgroups", $listgroups['data']);


// Users Defaults
$mailCharsets = array('utf-8', 'iso-8859-1');
$smarty->assign_by_ref('mailCharsets', $mailCharsets);

ask_ticket('admin-inc-login');
?>
