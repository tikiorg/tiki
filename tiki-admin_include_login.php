<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_login.php,v 1.10 2004-01-15 06:32:47 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

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
		if (isset($_REQUEST["eligibleUserTrackers"])) {
			$tikilib->set_preference("eligibleUserTrackers", implode(',',$_REQUEST["eligibleUserTrackers"]));
		} else {
			$tikilib->set_preference("eligibleUserTrackers", '');
		}
	} else {
		$tikilib->set_preference("userTracker", 'n');
		$tikilib->set_preference("eligibleUserTrackers", '');
	}

	if (isset($_REQUEST["groupTracker"]) && $_REQUEST["groupTracker"] == "on") {
		$tikilib->set_preference("groupTracker", 'y');
		if (isset($_REQUEST["eligibleGroupTrackers"])) {
			$tikilib->set_preference("eligibleGroupTrackers", implode(',',$_REQUEST["eligibleGroupTrackers"]));
		} else {
			$tikilib->set_preference("eligibleGroupTrackers", '');
		}
	} else {
		$tikilib->set_preference("groupTracker", 'n');
		$tikilib->set_preference("eligibleGroupTrackers", '');
	}

    if (isset($_REQUEST["allowRegister"]) && $_REQUEST["allowRegister"] == "on") {
	$tikilib->set_preference("allowRegister", 'y');

	$smarty->assign('allowRegister', 'y');
    } else {
	$tikilib->set_preference("allowRegister", 'n');

	$smarty->assign('allowRegister', 'n');
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

    if (isset($_REQUEST["auth_ldap_host"])) {
	$tikilib->set_preference("auth_ldap_host", $_REQUEST["auth_ldap_host"]);

	$smarty->assign('auth_ldap_host', $_REQUEST["auth_ldap_host"]);
    }

    if (isset($_REQUEST["auth_ldap_port"])) {
	$tikilib->set_preference("auth_ldap_port", $_REQUEST["auth_ldap_port"]);

	$smarty->assign('auth_ldap_port', $_REQUEST["auth_ldap_port"]);
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

$smarty->assign("userTracker", $tikilib->get_preference("userTracker", "n"));
$smarty->assign("eligibleUserTrackers", array_flip(split(',',','.$tikilib->get_preference("eligibleUserTrackers", ""))));
$smarty->assign("groupTracker", $tikilib->get_preference("groupTracker", "n"));
$smarty->assign("eligibleGroupTrackers", array_flip(split(',',','.$tikilib->get_preference("eligibleGroupTrackers", ""))));

$listTrackers = $tikilib->list_trackers(0,-1,"name_desc","");
$smarty->assign("listTrackers",$listTrackers['list']);

$smarty->assign("change_theme", $tikilib->get_preference("change_theme", "n"));
$smarty->assign("change_language", $tikilib->get_preference("change_language", "n"));
$smarty->assign("rememberme", $tikilib->get_preference("rememberme", "disabled"));
$smarty->assign("remembertime", $tikilib->get_preference("remembertime", 7200));
$smarty->assign("allowRegister", $tikilib->get_preference("allowRegister", 'n'));
$smarty->assign("eponymousGroups", $tikilib->get_preference("eponymousGroups", 'n'));
$smarty->assign("useRegisterPasscode", $tikilib->get_preference("useRegisterPasscode", 'n'));
$smarty->assign("registerPasscode", $tikilib->get_preference("registerPasscode", ''));
$smarty->assign("validateUsers", $tikilib->get_preference("validateUsers", 'n'));
$smarty->assign("validateEmail", $tikilib->get_preference("validateEmail", 'n'));
$smarty->assign("forgotPass", $tikilib->get_preference("forgotPass", 'n'));
ask_ticket('admin-inc-login');
?>
