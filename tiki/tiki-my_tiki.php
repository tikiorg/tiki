<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-my_tiki.php,v 1.5 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/messu/messulib.php');
include_once ('lib/tasks/tasklib.php');

// User preferences screen
if ($feature_userPreferences != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

$userwatch = $user;

if (isset($_REQUEST["view_user"])) {
	if ($_REQUEST["view_user"] <> $user) {
		if ($tiki_p_admin == 'y') {
			$userwatch = $_REQUEST["view_user"];
		} else {
			$smarty->assign('msg', tra("You dont have permission to view other users data"));

			$smarty->display("styles/$style_base/error.tpl");
			die;
		}
	} else {
		$userwatch = $user;
	}
}

$smarty->assign('userwatch', $userwatch);

$smarty->assign('mytiki_wiki', 'display:none;');
$smarty->assign('mytiki_wiki_open', 'n');

if (isset($_COOKIE["mytiki_wiki"])) {
	if ($_COOKIE["mytiki_wiki"] == 'o') {
		$smarty->assign('mytiki_wiki', 'display:block;');

		$smarty->assign('mytiki_wiki_open', 'y');
	}
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-user_preferences", "tiki-editpage", $foo["path"]);
$foo2 = str_replace("tiki-user_preferences", "tiki-index", $foo["path"]);
$smarty->assign('url_edit', httpPrefix(). $foo1);
$smarty->assign('url_visit', httpPrefix(). $foo2);

/*
// This code should be in tiki-user_preferences.php; remove it if you are sure there's no danger
if(isset($_REQUEST["prefs"])) {
  // setting preferences
  if (isset($_REQUEST["email"]))  $userlib->change_user_email($userwatch,$_REQUEST["email"]);
  if($change_theme == 'y') {
  if (isset($_REQUEST["style"])) $tikilib->set_user_preference($userwatch,'theme',$_REQUEST["style"]);
  }
  if (isset($_REQUEST["realName"])) $tikilib->set_user_preference($userwatch,'realName',$_REQUEST["realName"]);
  if (isset($_REQUEST["userbreadCrumb"])) $tikilib->set_user_preference($userwatch,'userbreadCrumb',$_REQUEST["userbreadCrumb"]);
  if (isset($_REQUEST["homePage"])) $tikilib->set_user_preference($userwatch,'homePage',$_REQUEST["homePage"]);
  if($change_language == 'y') {
	if (isset($_REQUEST["language"])) {
	  $tikilib->set_user_preference($userwatch,'language',$_REQUEST["language"]);
	  $smarty->assign('language',$_REQUEST["language"]);
	  include('lang/'.$_REQUEST["language"].'/language.php');
	}
  }
  if (isset($_REQUEST["style"])) $smarty->assign('style',$_REQUEST["style"]);
  if (isset($_REQUEST["language"]))$smarty->assign('language',$_REQUEST["language"]);
	
  if(isset($_REQUEST['display_timezone'])) {
	$tikilib->set_user_preference($userwatch,'display_timezone',$_REQUEST['display_timezone']); 
	$smarty->assign_by_ref('display_timezone',$_REQUEST['display_timezone']);
  }
  $tikilib->set_user_preference($userwatch,'country',$_REQUEST["country"]);
  $tikilib->set_user_preference($userwatch,'user_information',$_REQUEST['user_information']);
  header("location: tiki-user_preferences.php?view_user=$userwatch");
  die;
}

if(isset($_REQUEST["chgpswd"])) {
  if($_REQUEST["pass1"]!=$_REQUEST["pass2"]) {
	$smarty->assign('msg',tra("The passwords didn't match"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
  }
  
  if(!$userlib->validate_user($userwatch,$_REQUEST["old"],'','')) {
	$smarty->assign('msg',tra("Invalid old password"));
	$smarty->display("styles/$style_base/error.tpl");
	die;
  }
  
  //Validate password here
  if(strlen($_REQUEST["pass1"])<$min_pass_length) {
	$smarty->assign('msg',tra("Password should be at least").' '.$min_pass_length.' '.tra("characters long"));
	$smarty->display("styles/$style_base/error.tpl");
	die; 	
  }
  
  // Check this code
  if($pass_chr_num == 'y') {
	if(!preg_match_all("[0-9]+",$_REQUEST["pass1"],$foo) || !preg_match_all("[A-Za-z]+",$_REQUEST["pass1"],$foo)) {
	  $smarty->assign('msg',tra("Password must contain both letters and numbers"));
	  $smarty->display("styles/$style_base/error.tpl");
	  die; 	
	}
  }

  
  $userlib->change_user_password($userwatch,$_REQUEST["pass1"]);
}
*/
if (isset($_REQUEST['messprefs'])) {
	$tikilib->set_user_preference($userwatch, 'mess_maxRecords', $_REQUEST['mess_maxRecords']);

	$tikilib->set_user_preference($userwatch, 'minPrio', $_REQUEST['minPrio']);

	if (isset($_REQUEST['allowMsgs']) && $_REQUEST['allowMsgs'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'allowMsgs', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'allowMsgs', 'n');
	}
}

if (isset($_REQUEST['tasksprefs'])) {
	$tikilib->set_user_preference($userwatch, 'tasks_maxRecords', $_REQUEST['tasks_maxRecords']);

	if (isset($_REQUEST['tasks_useDates']) && $_REQUEST['tasks_useDates'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'tasks_useDates', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'tasks_useDates', 'n');
	}
}

$tasks_maxRecords = $tikilib->get_user_preference($userwatch, 'tasks_maxRecords');
$tasks_useDates = $tikilib->get_user_preference($userwatch, 'tasks_useDates');
$smarty->assign('tasks_maxRecords', $tasks_maxRecords);
$smarty->assign('tasks_useDates', $tasks_useDates);

$mess_maxRecords = $tikilib->get_user_preference($userwatch, 'mess_maxRecords', 20);
$smarty->assign('mess_maxRecords', $mess_maxRecords);
$allowMsgs = $tikilib->get_user_preference($userwatch, 'allowMsgs', 'y');
$smarty->assign('allowMsgs', $allowMsgs);
$minPrio = $tikilib->get_user_preference($userwatch, 'minPrio', 6);
$smarty->assign('minPrio', $minPrio);

$userinfo = $userlib->get_user_info($userwatch);
$smarty->assign_by_ref('userinfo', $userinfo);

$styles = array();
$h = opendir("styles/");

while ($file = readdir($h)) {
	if (strstr($file, "css")) {
		$styles[] = $file;
	}
}

closedir ($h);
$smarty->assign_by_ref('styles', $styles);

$languages = array();
$h = opendir("lang/");

while ($file = readdir($h)) {
	if ($file != '.' && $file != '..' && is_dir('lang/' . $file) && strlen($file) == 2) {
		$languages[] = $file;
	}
}

closedir ($h);
$smarty->assign_by_ref('languages', $languages);

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
$smarty->assign('flags', $flags);

// Get preferences
$style = $tikilib->get_user_preference($userwatch, 'theme', $style);
$language = $tikilib->get_user_preference($userwatch, 'language', $language);
$smarty->assign_by_ref('style', $style);
$realName = $tikilib->get_user_preference($userwatch, 'realName', '');
$country = $tikilib->get_user_preference($userwatch, 'country', 'Other');
$smarty->assign('country', $country);
$anonpref = $tikilib->get_preference('userbreadCrumb', 4);
$userbreadCrumb = $tikilib->get_user_preference($userwatch, 'userbreadCrumb', $anonpref);
$smarty->assign_by_ref('realName', $realName);
$smarty->assign_by_ref('userbreadCrumb', $userbreadCrumb);
$homePage = $tikilib->get_user_preference($userwatch, 'homePage', '');
$smarty->assign_by_ref('homePage', $homePage);

$avatar = $tikilib->get_user_avatar($userwatch);
$smarty->assign('avatar', $avatar);

//Get messages
$msgs = $messulib->list_user_messages($user, 0, -1, 'date_desc', '', 'isRead', 'n');
$smarty->assign('msgs', $msgs['data']);

//Get tasks
if (isset($_SESSION['thedate'])) {
	$pdate = $_SESSION['thedate'];
} else {
	$pdate = date("U");
}

$tasks_useDates = $tikilib->get_user_preference($user, 'tasks_useDates');
$tasks = $tasklib->list_tasks($user, 0, -1, 'priority_desc', '', $tasks_useDates, $pdate);
$smarty->assign('tasks', $tasks['data']);

$user_information = $tikilib->get_user_preference($userwatch, 'user_information', 'public');
$smarty->assign('user_information', $user_information);

if ($feature_messages == 'y' && $tiki_p_messages == 'y') {
	$unread = $tikilib->user_unread_messages($userwatch);

	$smarty->assign('unread', $unread);
}

$timezone_options = $tikilib->get_timezone_list(true);
$smarty->assign_by_ref('timezone_options', $timezone_options);
$server_time = new Date();
$display_timezone = $tikilib->get_user_preference($userwatch, 'display_timezone', $server_time->tz->getID());
$smarty->assign_by_ref('display_timezone', $display_timezone);

$smarty->assign('mytiki_pages', $tikilib->get_user_preference($user, 'mytiki_pages'), 'y');
$smarty->assign('mytiki_blogs', $tikilib->get_user_preference($user, 'mytiki_blogs'), 'y');
$smarty->assign('mytiki_gals', $tikilib->get_user_preference($user, 'mytiki_gals'), 'y');
$smarty->assign('mytiki_items', $tikilib->get_user_preference($user, 'mytiki_items'), 'y');
$smarty->assign('mytiki_msgs', $tikilib->get_user_preference($user, 'mytiki_msgs'), 'y');
$smarty->assign('mytiki_tasks', $tikilib->get_user_preference($user, 'mytiki_tasks'), 'y');

$section = 'mytiki';
include_once ('tiki-section_options.php');

$smarty->assign('uses_tabs', 'y');
$smarty->assign('mid', 'tiki-my_tiki.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>