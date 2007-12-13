<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-user_preferences.php,v 1.102.2.8 2007-12-13 23:24:45 nkoth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'mytiki';
require_once ('tiki-setup.php');
if ($prefs['feature_ajax'] == "y") {
require_once ('lib/ajax/ajaxlib.php');
}
include_once('lib/modules/modlib.php');
include_once ('lib/userprefs/scrambleEmail.php');
include_once ('lib/userprefs/userprefslib.php');

// User preferences screen
if ($prefs['feature_userPreferences'] != 'y' && $user != 'admin') {
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

if (isset($_REQUEST['userId']) || isset($_REQUEST['view_user'])) {
	if (empty($_REQUEST['view_user']))
		$userwatch = $tikilib->get_user_login($_REQUEST['userId']);
	else
		$userwatch = $_REQUEST['view_user'];
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
$customfields = $userprefslib->get_userprefs('CustomFields');
$smarty->assign_by_ref('customfields', $customfields);

$smarty->assign('userwatch', $userwatch);

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-user_preferences", "tiki-editpage", $foo["path"]);
$foo2 = str_replace("tiki-user_preferences", "tiki-index", $foo["path"]);
$smarty->assign('url_edit', $tikilib->httpPrefix(). $foo1);
$smarty->assign('url_visit', $tikilib->httpPrefix(). $foo2);

$smarty->assign('show_mouseover_user_info',
	isset($prefs['show_mouseover_user_info']) ? $prefs['show_mouseover_user_info'] : $prefs['feature_community_mouseover']
);

if (isset($_REQUEST["new_prefs"])) {
	check_ticket('user-prefs');
	// setting preferences
	if ($prefs['change_theme'] == 'y') {
		if (isset($_REQUEST["mystyle"])) {
			if ($user == $userwatch) {
				$t = $tikidomain? $tikidomain.'/':'';
                                if ($_REQUEST["mystyle"] == "") {
                                  //If mystyle is empty --> user has selected "Site Default" theme
                                  $sitestyle = $tikilib->getOne("select `value` from `tiki_preferences` where `name`=?", 'style');
		                  $headerlib->replace_cssfile('styles/'.$t.$prefs['style'], 'styles/'.$t.$sitestyle, 51);
                                }
                                else  {
				  $headerlib->replace_cssfile('styles/'.$t.$prefs['style'], 'styles/'.$t.$_REQUEST['mystyle'], 51);
                                }
			}
                        
                        if ($_REQUEST["mystyle"] == "") {
			  $tikilib->set_user_preference($userwatch, 'theme', "");
                        }
                        else {
                          $tikilib->set_user_preference($userwatch, 'theme', $_REQUEST["mystyle"]);
                        }
		}
	}

	if (isset($_REQUEST["userbreadCrumb"]))
		$tikilib->set_user_preference($userwatch, 'userbreadCrumb', $_REQUEST["userbreadCrumb"]);

	if (isset($_REQUEST["language"]) && preg_match("/^[a-zA-Z-_]*$/", $_REQUEST['language'])  && file_exists('lang/' . $_REQUEST['language'] . '/language.php')) {
		if ($tiki_p_admin || $prefs['change_language'] == 'y') {
			$tikilib->set_user_preference($userwatch, 'language', $_REQUEST["language"]);
		}
		if ($userwatch == $user) {
			include ('lang/' . $_REQUEST["language"] . '/language.php');
		}
	}

	if (isset($_REQUEST['display_timezone'])) {
		$tikilib->set_user_preference($userwatch, 'display_timezone', $_REQUEST['display_timezone']);
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

	if ($prefs['feature_community_mouseover'] == 'y') {
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
		if (isset($_REQUEST[$customfields[$custpref]['prefName']]))
			$tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
	}

	if (isset($_REQUEST["realName"]) && ($prefs['auth_ldap_nameattr'] == '' || $prefs['auth_method'] != 'auth'))
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

	$tikilib->set_user_preference($userwatch, 'mess_maxRecords', $_REQUEST['mess_maxRecords']);
	$tikilib->set_user_preference($userwatch, 'mess_archiveAfter', $_REQUEST['mess_archiveAfter']);

	if (isset($_REQUEST['mess_sendReadStatus']) && $_REQUEST['mess_sendReadStatus'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mess_sendReadStatus', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mess_sendReadStatus', 'n');
	}

	$tikilib->set_user_preference($userwatch, 'minPrio', $_REQUEST['minPrio']);

	if ($prefs['allowmsg_is_optional'] == 'y') {
		if (isset($_REQUEST['allowMsgs']) && $_REQUEST['allowMsgs'] == 'on') {
			$tikilib->set_user_preference($userwatch, 'allowMsgs', 'y');
		} else {
			$tikilib->set_user_preference($userwatch, 'allowMsgs', 'n');
		}
	}
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

	if (isset($_REQUEST['mytiki_forum_topics']) && $_REQUEST['mytiki_forum_topics'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_forum_topics', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_forum_topics', 'n');
	}
	
	if (isset($_REQUEST['mytiki_forum_replies']) && $_REQUEST['mytiki_forum_replies'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_forum_replies', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_forum_replies', 'n');
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
	$tikilib->set_user_preference($userwatch, 'tasks_maxRecords', $_REQUEST['tasks_maxRecords']);

}

if ($prefs['auth_method'] == 'auth' && $user == 'admin' && $prefs['auth_skip_admin'] == 'y') {
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

	if (!empty($_REQUEST['email']) && $prefs['login_is_email'] != 'y') {
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

		$polerr = $userlib->check_password_policy($_REQUEST["pass1"]);
		if ( strlen($polerr)>0 ) {
			$smarty->assign('msg',$polerr);
		    $smarty->display("error.tpl");
		    die;
		}

	    $userlib->change_user_password($userwatch, $_REQUEST["pass1"]);
	}

}

$tikilib->get_user_preference($userwatch, 'mytiki_pages', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_blogs', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_gals', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_items', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_msgs', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_tasks', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_workflow', 'y');
$tikilib->get_user_preference($userwatch, 'mylevel', '1');
$tikilib->get_user_preference($userwatch, 'tasks_maxRecords');
$tikilib->get_user_preference($userwatch, 'mess_maxRecords', 20);
$tikilib->get_user_preference($userwatch, 'mess_archiveAfter', 0);
$tikilib->get_user_preference($userwatch, 'mess_sendReadStatus', 0);
$tikilib->get_user_preference($userwatch, 'allowMsgs', 'y');
$tikilib->get_user_preference($userwatch, 'minPrio', 6);
$tikilib->get_user_preference($userwatch, 'theme', '');
$tikilib->get_user_preference($userwatch, 'language', '');
$tikilib->get_user_preference($userwatch, 'realName', '');
$tikilib->get_user_preference($userwatch, 'country', 'Other');
$tikilib->get_user_preference($userwatch, 'lat', '');
$tikilib->get_user_preference($userwatch, 'lon', '');
$tikilib->get_user_preference($userwatch, 'userbreadCrumb', $prefs['site_userbreadCrumb']);
$tikilib->get_user_preference($userwatch, 'homePage', '');
$tikilib->get_user_preference($userwatch, 'email is public', 'n');
$user_preferences[$userwatch]['email_isPublic'] = $user_preferences[$userwatch]['email is public'];
$tikilib->get_user_preference($userwatch, 'mailCharset', $prefs['default_mail_charset']);
$tikilib->get_user_preference($userwatch, 'user_dbl', 'y');

$userinfo = $userlib->get_user_info($userwatch);
$smarty->assign_by_ref('userinfo', $userinfo);

$llist = array();
$llist = $tikilib->list_styles();
$smarty->assign_by_ref('styles',$llist);

$languages = array();
$languages = $tikilib->list_languages();
$smarty->assign_by_ref('languages', $languages);

$user_pages = $tikilib->get_user_pages($userwatch, -1);
$smarty->assign_by_ref('user_pages', $user_pages);
$user_blogs = $tikilib->list_user_blogs($userwatch, false);
$smarty->assign_by_ref('user_blogs', $user_blogs);
$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
$smarty->assign_by_ref('user_galleries', $user_galleries);
$user_items = $tikilib->get_user_items($userwatch);
$smarty->assign_by_ref('user_items', $user_items);

$flags = $tikilib->get_flags();
$smarty->assign_by_ref('flags', $flags);

$scramblingMethods = array("n", "strtr", "unicode", "x"); // email_isPublic utilizes 'n'
$smarty->assign_by_ref('scramblingMethods', $scramblingMethods);
$scramblingEmails = array(tra("no"), scrambleEmail($userinfo['email'], 'strtr'), scrambleEmail($userinfo['email'], 'unicode')."-".tra("unicode"), scrambleEmail($userinfo['email'], 'x'));
$smarty->assign_by_ref('scramblingEmails', $scramblingEmails);
$avatar = $tikilib->get_user_avatar($userwatch);
$smarty->assign_by_ref('avatar', $avatar);
$mailCharsets = array('utf-8', 'iso-8859-1');
$smarty->assign_by_ref('mailCharsets', $mailCharsets);

$smarty->assign_by_ref('user_prefs', $user_preferences[$userwatch]);

$tikilib->get_user_preference($userwatch, 'user_information', 'public');
$tikilib->get_user_preference($userwatch, 'diff_versions', 'n');

$usertrackerId = false;
$useritemId= false;
if ($prefs['userTracker'] == 'y') {
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

if ($prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y') {
	$unread = $tikilib->user_unread_messages($userwatch);
	$smarty->assign('unread', $unread);
}

$smarty->assign_by_ref("timezones", $GLOBALS['_DATE_TIMEZONE_DATA']);

$smarty->assign('userPageExists', 'n');
if ($prefs['feature_wiki'] == 'y' and $prefs['feature_wiki_userpage'] == 'y') {
	if ($tikilib->page_exists($prefs['feature_wiki_userpage_prefix'].$user))
		$smarty->assign('userPageExists', 'y');
}

$smarty->assign_by_ref('tikifeedback', $tikifeedback);

include_once ('tiki-section_options.php');

ask_ticket('user-prefs');
if ($prefs['feature_ajax'] == "y") {
function user_preferences_ajax() {
    global $ajaxlib, $xajax;
    $ajaxlib->registerTemplate("tiki-user_preferences.tpl");
    $ajaxlib->registerFunction("loadComponent");
    $ajaxlib->processRequests();
}
user_preferences_ajax();
$smarty->assign("mootab",'y');
}
$smarty->assign('mid', 'tiki-user_preferences.tpl');
$smarty->display("tiki.tpl");

?>
