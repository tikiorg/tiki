<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-user_preferences.php,v 1.86 2006-12-27 06:43:37 mose Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'mytiki';
require_once ('tiki-setup.php');
include_once('lib/modules/modlib.php');
include_once ('lib/userprefs/scrambleEmail.php');
include_once('lib/registration/registrationlib.php');

// User preferences screen
if ($feature_userPreferences != 'y' && $user != 'admin') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_userPreferences");

	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));
	$smarty->assign('errortype', '402');
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['userId'])) {
	$userwatch = $tikilib->get_user_login($_REQUEST['userId']);
	if ($userwatch != $user) {
		if ($userwatch === NULL) {
			$smarty->assign('msg', tra("Unknown user"));
			$smarty->display("error.tpl");
			die;
		} elseif ($tiki_p_admin != 'y' and $tiki_p_admin_users != 'y') {
			$smarty->assign('msg', tra("You do not have permission to view other users data"));
			$smarty->display("error.tpl");
			die;
		}
	}
} elseif (isset($_REQUEST["view_user"])) {
	if ($_REQUEST["view_user"] != $user) {
		if ($tiki_p_admin == 'y' or $tiki_p_admin_users == 'y') {
			$userwatch = $_REQUEST["view_user"];
			if (!$userlib->user_exists($userwatch)) {
				$smarty->assign('msg', tra("Unknown user"));
				$smarty->display("error.tpl");
				die;
			}
		} else {
			$smarty->assign('msg', tra("You do not have permission to view other users data"));
			$smarty->display("error.tpl");
			die;
		}
	} else {
		$userwatch = $user;
	}
} else {
	$userwatch = $user;
}

// Custom fields
$customfields = array();
$customfields = $registrationlib->get_customfields($userwatch);
$smarty->assign_by_ref('customfields', $customfields);

$smarty->assign('userwatch', $userwatch);

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-user_preferences", "tiki-editpage", $foo["path"]);
$foo2 = str_replace("tiki-user_preferences", "tiki-index", $foo["path"]);
$smarty->assign('url_edit', $tikilib->httpPrefix(). $foo1);
$smarty->assign('url_visit', $tikilib->httpPrefix(). $foo2);

$smarty->assign('show_mouseover_user_info',
		$userlib->get_user_preference($user, 'show_mouseover_user_info',$feature_community_mouseover));

$cookietab = 1;

if (isset($_REQUEST["prefs"])) {
	check_ticket('user-prefs');
	// setting preferences
	//  if (isset($_REQUEST["email"]))  $userlib->change_user_email($userwatch,$_REQUEST["email"]);
	if ($change_theme == 'y') {
		if (isset($_REQUEST["mystyle"])) {
			$tikilib->set_user_preference($userwatch, 'theme', $_REQUEST["mystyle"]);
		}
	}

	if (isset($_REQUEST["userbreadCrumb"]))
		$tikilib->set_user_preference($userwatch, 'userbreadCrumb', $_REQUEST["userbreadCrumb"]);

	if (isset($_REQUEST["language"]) && preg_match("/^[a-zA-Z-_]*$/", $_REQUEST['language'])  && file_exists('lang/' . $_REQUEST['language'] . '/language.php')) {
		if ($tiki_p_admin || $change_language == 'y') {
			$tikilib->set_user_preference($userwatch, 'language', $_REQUEST["language"]);
		}
		if ($userwatch == $user) {
			$smarty->assign('language', $_REQUEST["language"]);
			include ('lang/' . $_REQUEST["language"] . '/language.php');
		}
	}

	if (isset($_REQUEST['display_timezone'])) {
		$tikilib->set_user_preference($userwatch, 'display_timezone', $_REQUEST['display_timezone']);

		$smarty->assign_by_ref('display_timezone', $_REQUEST['display_timezone']);
	}

	$tikilib->set_user_preference($userwatch, 'user_information', $_REQUEST['user_information']);
	if (isset($_REQUEST['user_dbl']) && $_REQUEST['user_dbl'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'user_dbl', 'y');

		$smarty->assign('user_dbl', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'user_dbl', 'n');

		$smarty->assign('user_dbl', 'n');
	}

	if (isset($_REQUEST['diff_versions']) && $_REQUEST['diff_versions'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'diff_versions', 'y');

		$smarty->assign('diff_versions', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'diff_versions', 'n');

		$smarty->assign('diff_versions' ,'n');
	}

	if ($feature_community_mouseover == 'y') {
	    if (isset($_REQUEST['show_mouseover_user_info']) && $_REQUEST['show_mouseover_user_info'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'show_mouseover_user_info', 'y');
		$smarty->assign('show_mouseover_user_info','y');
	    } else {
		$tikilib->set_user_preference($userwatch, 'show_mouseover_user_info', 'n');
		$smarty->assign('show_mouseover_user_info','n');
	    }
	}

	$email_isPublic = isset($_REQUEST['email_isPublic']) ? $_REQUEST['email_isPublic']: 'n';
	$tikilib->set_user_preference($userwatch, 'email is public', $email_isPublic);

	$tikilib->set_user_preference($userwatch, 'mailCharset', $_REQUEST['mailCharset']);

	// Custom fields
	foreach ($customfields as $custpref=>$prefvalue ) {
		//print $customfields[$custpref]['prefName'];
		//print $_REQUEST[$customfields[$custpref]['prefName']];
		if (isset($_REQUEST[$customfields[$custpref]['prefName']]))
			$tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
	}

	header ("location: tiki-user_preferences.php?view_user=$userwatch");
	die;
}

if (isset($_REQUEST['info'])) {
	check_ticket('user-prefs');

	if (isset($_REQUEST["realName"]))
		$tikilib->set_user_preference($userwatch, 'realName', $_REQUEST["realName"]);

	/* this should be optional
	if (isset($_REQUEST["gender"]))
		$tikilib->set_user_preference($userwatch, 'gender', $_REQUEST["gender"]);
	*/

	if (isset($_REQUEST["homePage"]))
		$tikilib->set_user_preference($userwatch, 'homePage', $_REQUEST["homePage"]);

	if (isset($_REQUEST["lat"])) {
	  if (is_numeric($_REQUEST["lat"])) {
	  	$lat=floatval($_REQUEST["lat"]);
	  } else {
	  	$lat=NULL;
	  }
		$smarty->assign('lat', $lat);
		$tikilib->set_user_preference($userwatch, 'lat', $lat);
	}
	if (isset($_REQUEST["lon"])) {
		  if (is_numeric($_REQUEST["lon"])) {
	  	$lon=floatval($_REQUEST["lon"]);
	  } else {
	  	$lon=NULL;
	  }
		$smarty->assign('lon', $lon);
		$tikilib->set_user_preference($userwatch, 'lon', $lon);
	}

        // Custom fields
        foreach ($customfields as $custpref=>$prefvalue ) {
                // print $customfields[$custpref]['prefName'];
                // print $_REQUEST[$customfields[$custpref]['prefName']];
                $tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
        }

	$tikilib->set_user_preference($userwatch, 'country', $_REQUEST["country"]);

	$cookietab = 1;
}

if ($auth_method == 'auth' && $user == 'admin' && $auth_skip_admin == 'y') {
	$change_password = 'y';
	$smarty->assign('change_password', $change_password);
}

if (isset($_REQUEST['chgadmin'])) {
	check_ticket('user-prefs');

	if (isset($_REQUEST['pass'])) {
	    $pass = $_REQUEST['pass'];
	} else {
	    $pass = '';
	}

	// check user's password, admin doesn't need it to change other user's info
	if ($tiki_p_admin != 'y' || $user == $userwatch) {
	    list($ok, $userwatch, $error) = $userlib->validate_user($userwatch, $pass, '', '');
	    if (!$ok) {
		$smarty->assign('msg', tra("Invalid password.  Your current password is required to change administrative information"));

		$smarty->display("error.tpl");
		die;
	    }
	}

	if (!empty($_REQUEST['email'])) {
		$userlib->change_user_email($userwatch, $_REQUEST['email'], $pass);
		$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("Email is set to %s"),$_REQUEST['email']));
	}

	// If user has provided new password, let's try to change
	if (!empty($_REQUEST["pass1"])) {

	    if ($_REQUEST["pass1"] != $_REQUEST["pass2"]) {
		$smarty->assign('msg', tra("The passwords did not match"));
		$smarty->display("error.tpl");
		die;
	    }

	    //Validate password here
	    if (strlen($_REQUEST["pass1"]) < $min_pass_length) {
		$smarty->assign('msg', tra("Password should be at least"). ' ' . $min_pass_length . ' ' . tra("characters long"));

		$smarty->display("error.tpl");
		die;
	    }

	    // Check this code
	    if ($pass_chr_num == 'y') {
		if (!preg_match_all("/[0-9]+/", $_REQUEST["pass1"], $foo) || !preg_match_all("/[A-Za-z]+/", $_REQUEST["pass1"], $foo)) {
		    $smarty->assign('msg', tra("Password must contain both letters and numbers"));

		    $smarty->display("error.tpl");
		    die;
		}
	    }

	    $userlib->change_user_password($userwatch, $_REQUEST["pass1"]);
	}

	$cookietab = 2;
}

if (isset($_REQUEST['messprefs'])) {
	check_ticket('user-prefs');
	$tikilib->set_user_preference($userwatch, 'mess_maxRecords', $_REQUEST['mess_maxRecords']);
	$tikilib->set_user_preference($userwatch, 'mess_archiveAfter', $_REQUEST['mess_archiveAfter']);

	if (isset($_REQUEST['mess_sendReadStatus']) && $_REQUEST['mess_sendReadStatus'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mess_sendReadStatus', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mess_sendReadStatus', 'n');
	}

	$tikilib->set_user_preference($userwatch, 'minPrio', $_REQUEST['minPrio']);

	if ($allowmsg_is_optional == 'y') {
		if (isset($_REQUEST['allowMsgs']) && $_REQUEST['allowMsgs'] == 'on') {
			$tikilib->set_user_preference($userwatch, 'allowMsgs', 'y');
		} else {
			$tikilib->set_user_preference($userwatch, 'allowMsgs', 'n');
		}
	}

	$cookietab = 3;
}

if (isset($_REQUEST['mytikiprefs'])) {
	check_ticket('user-prefs');
	if (isset($_REQUEST['mytiki_pages']) && $_REQUEST['mytiki_pages'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_pages', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_pages', 'n');
	}

	if (isset($_REQUEST['mytiki_blogs']) && $_REQUEST['mytiki_blogs'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_blogs', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_blogs', 'n');
	}

	if (isset($_REQUEST['mytiki_gals']) && $_REQUEST['mytiki_gals'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_gals', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_gals', 'n');
	}

	if (isset($_REQUEST['mytiki_msgs']) && $_REQUEST['mytiki_msgs'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_msgs', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_msgs', 'n');
	}

	if (isset($_REQUEST['mytiki_tasks']) && $_REQUEST['mytiki_tasks'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_tasks', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_tasks', 'n');
	}

	if (isset($_REQUEST['mytiki_items']) && $_REQUEST['mytiki_items'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_items', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_items', 'n');
	}

	if (isset($_REQUEST['mytiki_workflow']) && $_REQUEST['mytiki_workflow'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_workflow', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_workflow', 'n');
	}

	$cookietab = 3;
}

$smarty->assign('mytiki_pages', $tikilib->get_user_preference($userwatch, 'mytiki_pages'), 'y');
$smarty->assign('mytiki_blogs', $tikilib->get_user_preference($userwatch, 'mytiki_blogs'), 'y');
$smarty->assign('mytiki_gals', $tikilib->get_user_preference($userwatch, 'mytiki_gals'), 'y');
$smarty->assign('mytiki_items', $tikilib->get_user_preference($userwatch, 'mytiki_items'), 'y');
$smarty->assign('mytiki_msgs', $tikilib->get_user_preference($userwatch, 'mytiki_msgs'), 'y');
$smarty->assign('mytiki_tasks', $tikilib->get_user_preference($userwatch, 'mytiki_tasks'), 'y');
$smarty->assign('mytiki_workflow', $tikilib->get_user_preference($userwatch, 'mytiki_workflow'), 'y');

if (isset($_REQUEST['tasksprefs'])) {
	check_ticket('user-prefs');
	$tikilib->set_user_preference($userwatch, 'tasks_maxRecords', $_REQUEST['tasks_maxRecords']);

	$cookietab = 3;
}

$tasks_maxRecords = $tikilib->get_user_preference($userwatch, 'tasks_maxRecords');
$smarty->assign('tasks_maxRecords', $tasks_maxRecords);

$mess_maxRecords = $tikilib->get_user_preference($userwatch, 'mess_maxRecords', 20);
$smarty->assign('mess_maxRecords', $mess_maxRecords);

$mess_archiveAfter = $tikilib->get_user_preference($userwatch, 'mess_archiveAfter', 0);
$smarty->assign('mess_archiveAfter', $mess_archiveAfter);

$mess_sendReadStatus = $tikilib->get_user_preference($userwatch, 'mess_sendReadStatus', 0);
$smarty->assign('mess_sendReadStatus', $mess_sendReadStatus);

$allowMsgs = $tikilib->get_user_preference($userwatch, 'allowMsgs', 'y');
$smarty->assign('allowMsgs', $allowMsgs);
$minPrio = $tikilib->get_user_preference($userwatch, 'minPrio', 6);
$smarty->assign('minPrio', $minPrio);

$userinfo = $userlib->get_user_info($userwatch);
$smarty->assign_by_ref('userinfo', $userinfo);

$llist = array();
$llist = $tikilib->list_styles();
$smarty->assign_by_ref('styles',$llist);
$smarty->assign("available_styles", unserialize($tikilib->get_preference("available_styles")));

$languages = array();
$languages = $tikilib->list_languages();
$smarty->assign_by_ref('languages', $languages);
$smarty->assign("available_languages", unserialize($tikilib->get_preference("available_languages")));

// Get user pages
$user_pages = $tikilib->get_user_pages($userwatch, -1);
$user_blogs = $tikilib->list_user_blogs($userwatch, false);
$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
$smarty->assign_by_ref('user_pages', $user_pages);
$smarty->assign_by_ref('user_blogs', $user_blogs);
$smarty->assign_by_ref('user_galleries', $user_galleries);

$user_items = $tikilib->get_user_items($userwatch);
$smarty->assign_by_ref('user_items', $user_items);

// Get flags here
$flags = array();
$h = opendir("img/flags/");

while ($file = readdir($h)) {
	if (strstr($file, ".gif")) {
		$parts = explode('.', $file);

		$flags[] = $parts[0];
	}
}

closedir ($h);
sort ($flags);
$smarty->assign('flags', $flags);

// Get preferences
if ($change_theme == 'y')
	$style = $tikilib->get_user_preference($userwatch, 'theme', $style);
$langUser = $tikilib->get_user_preference($userwatch, 'language', $language);
$smarty->assign('langUser', $langUser);
$smarty->assign_by_ref('style', $style);
$realName = $tikilib->get_user_preference($userwatch, 'realName', '');
$country = $tikilib->get_user_preference($userwatch, 'country', 'Other');
$smarty->assign('country', $country);
$lat = $tikilib->get_user_preference($userwatch, 'lat', '');
$smarty->assign('lat', $lat);
$lon = $tikilib->get_user_preference($userwatch, 'lon', '');
$smarty->assign('lon', $lon);
$anonpref = $tikilib->get_preference('userbreadCrumb', 4);
$userbreadCrumb = $tikilib->get_user_preference($userwatch, 'userbreadCrumb', $anonpref);
$smarty->assign_by_ref('realName', $realName);
$smarty->assign_by_ref('userbreadCrumb', $userbreadCrumb);
$homePage = $tikilib->get_user_preference($userwatch, 'homePage', '');
$smarty->assign_by_ref('homePage', $homePage);
$smarty->assign('email_isPublic', $tikilib->get_user_preference($userwatch, 'email is public', 'n'));
$scramblingMethods = array("n", "strtr", "unicode", "x"); // email_isPublic utilizes 'n'
$smarty->assign_by_ref('scramblingMethods', $scramblingMethods);
$scramblingEmails = array(tra("no"), scrambleEmail($userinfo['email'], 'strtr'), scrambleEmail($userinfo['email'], 'unicode')."-".tra("unicode"), scrambleEmail($userinfo['email'], 'x'));
$smarty->assign_by_ref('scramblingEmails', $scramblingEmails);
$avatar = $tikilib->get_user_avatar($userwatch);
$smarty->assign('avatar', $avatar);
$smarty->assign('mailCharset', $tikilib->get_user_preference($userwatch, 'mailCharset', $tikilib->get_preference('default_mail_charset', 'utf-8')));
$mailCharsets = array('utf-8', 'iso-8859-1');
$smarty->assign_by_ref('mailCharsets', $mailCharsets);
$user_dbl = $tikilib->get_user_preference($userwatch, 'user_dbl', 'y');
$smarty->assign_by_ref('user_dbl', $user_dbl);

$user_information = $tikilib->get_user_preference($userwatch, 'user_information', 'public');
$smarty->assign('user_information', $user_information);
$diff_versions = $tikilib->get_user_preference($userwatch, 'diff_versions', 'n');
$smarty->assign('diff_versions', $diff_versions);

$usertrackerId = false;
$useritemId= false;
if ($userTracker == 'y') {
	$re = $userlib->get_usertracker($userinfo["userId"]);
	if (isset($re['usersTrackerId']) and $re['usersTrackerId']) {
		include_once('lib/trackers/trackerlib.php');
		$info = $trklib->get_item_id($re['usersTrackerId'],$trklib->get_field_id($re['usersTrackerId'],'Login'),$userwatch);
		$usertrackerId = $re['usersTrackerId'];
		$useritemId = $info;
	}
}
$smarty->assign('usertrackerId', $usertrackerId);
$smarty->assign('useritemId', $useritemId);

// Custom fields
foreach ($customfields as $custpref=>$prefvalue ) {
	$customfields[$custpref]['value'] = $tikilib->get_user_preference($userwatch, $customfields[$custpref]['prefName'], $customfields[$custpref]['value']);
	$smarty->assign($customfields[$custpref]['prefName'], $customfields[$custpref]['value']);
}

if ($feature_messages == 'y' && $tiki_p_messages == 'y') {
	$unread = $tikilib->user_unread_messages($userwatch);

	$smarty->assign('unread', $unread);
}

//$timezone_options = $tikilib->get_timezone_list(true);
//$smarty->assign_by_ref('timezone_options',$timezone_options);
//$server_time = new Date();
$display_timezone = $tikilib->get_display_timezone($userwatch);

if ($display_timezone != "UTC")
	$display_timezone = "Local";

$smarty->assign('userPageExists', 'n');
if ($feature_wiki == 'y' and $feature_wiki_userpage == 'y') {
	if ($tikilib->page_exists($feature_wiki_userpage_prefix.$user))
		$smarty->assign('userPageExists', 'y');
}

$smarty->assign_by_ref('display_timezone', $display_timezone);
$smarty->assign_by_ref('tikifeedback', $tikifeedback);

setcookie('tab',$cookietab);
$smarty->assign_by_ref('cookietab',$cookietab);
include_once ('tiki-section_options.php');

ask_ticket('user-prefs');

$smarty->assign('mid', 'tiki-user_preferences.tpl');
$smarty->display("tiki.tpl");

?>
