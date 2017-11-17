<?php

require_once('tiki-setup.php');

$access->check_feature('login_autologin');

if (empty($prefs['login_autologin_user'])) {
	$access->display_error('', tra('Remote system user needs to be configured'), "500");
	die;
}

if (empty($prefs['login_autologin_group'])) {
	$access->display_error('', tra('Remote system group for autologin need to be configured'), "500");
	die;
}

if (! empty($_REQUEST['uname'])) {
	$uname = $_REQUEST['uname'];
} else {
	$access->display_error('', tra('User name needs to be specified'), "400");
	die;
}

if (! empty($_REQUEST['email'])) {
	$email = $_REQUEST['email'];
} else {
	$email = '';
}

if (! empty($_REQUEST['realName'])) {
	$realName = $_REQUEST['realName'];
} else {
	$realName = '';
}

if (! empty($_REQUEST['groups'])) {
	$groups = $_REQUEST['groups'];
} else {
	$groups = [];
}

if (! empty($_REQUEST['page'])) {
	$page = $_REQUEST['page'];
} else {
	$page = '';
}

if (! empty($_REQUEST['base_url'])) {
	$autologin_base_url = $_REQUEST['base_url'];
} else {
	$access->display_error('', tra('Base URL not received from remote system'), "500");
	die;
}

if ($user == $prefs['login_autologin_user']) {
	// Attempted server-side login
	if (! empty($prefs['login_autologin_allowedgroups'])) {
		$allowedgroups = array_map('trim', explode(',', $prefs['login_autologin_allowedgroups']));
		if (! array_intersect($allowedgroups, $groups)) {
			$access->display_error('', tra('Permission denied'), "401");
			die;
		}
	}
	if ($prefs['login_autologin_createnew'] == 'y' && ! TikiLib::lib('user')->user_exists($uname)) {
		$randompass = TikiLib::lib('user')->genPass();
		if (empty($email)) {
			$access->display_error('', tra('Email needs to be specified'), "400");
			die;
		}
		TikiLib::lib('user')->add_user($uname, $randompass, $email);
	} elseif (! TikiLib::lib('user')->user_exists($uname)) {
		$access->display_error('', tra('Permission denied'), "401");
		die;
	} elseif (! empty($email) && ($prefs['user_unique_email'] != 'y' || ! TikiLib::lib('user')->other_user_has_email($uname, $email))) {
		TikiLib::lib('user')->change_user_email($uname, $email);
	}
	if (! empty($realName)) {
		TikiLib::lib('tiki')->set_user_preference($uname, 'realName', $realName);
	}
	if (! empty($prefs['login_autologin_syncgroups']) && ! empty($groups)) {
		$syncgroups = array_map('trim', explode(',', $prefs['login_autologin_syncgroups']));
		foreach ($syncgroups as $g) {
			if (! in_array($g, $groups) && TikiLib::lib('user')->group_exists($g)) {
				TikiLib::lib('user')->remove_user_from_group($uname, $g);
			}
		}
		foreach ($groups as $g) {
			if (in_array($g, $syncgroups) && TikiLib::lib('user')->group_exists($g)) {
				TikiLib::lib('user')->assign_user_to_group($uname, $g);
			}
		}
	}
	// Generate token url to log the user in for real
	require_once 'lib/auth/tokens.php';
	$tokenlib = AuthTokens::build($prefs);
	$params['uname'] = $uname;
	$params['page'] = $page;
	$params['base_url'] = $autologin_base_url;
	$url = $base_url . 'tiki-autologin.php' . '?' . http_build_query($params, '', '&');
	$url = $tokenlib->includeToken($url, [$prefs['login_autologin_group']], '', 30, 1);
	echo $url;
} else {
	// Actual user attempt via token
	if (! in_array($prefs['login_autologin_group'], Perms::get()->getGroups())) {
		$access->display_error('', tra('Permission denied'), "401");
		die;
	}
	if ($user || TikiLib::lib('user')->autologin_user($uname)) {
		if (! empty($autologin_base_url)) {
			$_SESSION['autologin_base_url'] = $autologin_base_url;
		}
		if (! empty($_SESSION['loginfrom'])) {
			TikiLib::lib('access')->redirect($_SESSION['loginfrom']);
		} elseif (! empty($page)) {
			$sefurl = TikiLib::lib('wiki')->sefurl($page);
			TikiLib::lib('access')->redirect($sefurl);
		} else {
			TikiLib::lib('access')->redirect("tiki-index.php");
		}
	} else {
		$access->display_error('', tra('Permission denied'), "401");
		die;
	}
}
