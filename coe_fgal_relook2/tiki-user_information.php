<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/messu/messulib.php');
include_once ('lib/userprefs/scrambleEmail.php');
include_once ('lib/registration/registrationlib.php');
include_once ('lib/trackers/trackerlib.php');
if (isset($_REQUEST['userId'])) {
	$userwatch = $tikilib->get_user_login($_REQUEST['userId']);
	if ($userwatch === NULL) {
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->assign('msg', tra("Unknown user"));
		$smarty->display("error.tpl");
		die;
	}
} elseif (isset($_REQUEST['view_user'])) {
	$userwatch = $_REQUEST['view_user'];
	if (!$userlib->user_exists($userwatch)) {
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->assign('msg', tra("Unknown user"));
		$smarty->display("error.tpl");
		die;
	}
} else {
	$access->check_user($user);
	$userwatch = $user;
}

$smarty->assign('userwatch', $userwatch);
// Custom fields
$customfields = array();
$customfields = $registrationlib->get_customfields($userwatch);
$smarty->assign_by_ref('customfields', $customfields);
if ($prefs['feature_friends'] == 'y') {
	$smarty->assign('friend', $tikilib->verify_friendship($userwatch, $user));
	$smarty->assign('friend_pending', $tikilib->verify_friendship_request($userwatch, $user));
	$smarty->assign('friend_waiting', $tikilib->verify_friendship_request($user, $userwatch));
}
$smarty->assign('infoPublic', 'y');
if ($tiki_p_admin != 'y') {
	$user_information = $tikilib->get_user_preference($userwatch, 'user_information', 'public');
	// If the user is trying to pull info on themselves, allow it.
	if ($user_information == 'private' && $userwatch != $user) {
		$smarty->assign('infoPublic', 'n');
	}
}
if ($user) {
	$smarty->assign('sent', 0);
	if (isset($_REQUEST['send'])) {
		check_ticket('user-information');
		$smarty->assign('sent', 1);
		$message = '';
		if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
			$smarty->assign('message', tra('ERROR: Either the subject or body must be non-empty'));
			$smarty->display("tiki.tpl");
			die;
		}
		$sent = $messulib->post_message($userwatch, $user, $_REQUEST['to'], '', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority'], '',
								isset($_REQUEST['replytome']) ? 'y' : '', isset($_REQUEST['bccme']) ? 'y' : '');
		if ($sent) {
			$message = tra('Message sent to') . ':' . $userlib->clean_user($userwatch) . '<br />';
		} else {
			$message = tra('An error occurred, please check your mail settings and try again');
		}
		$smarty->assign('message', $message);
	}
}
if ($prefs['feature_score'] == 'y' and isset($user) and $user != $userwatch) {
	$tikilib->score_event($user, 'profile_see');
	$tikilib->score_event($userwatch, 'profile_is_seen');
}
$smarty->assign('priority', 3);
if ($prefs['allowmsg_is_optional'] == 'y') {
	$allowMsgs = $tikilib->get_user_preference($userwatch, 'allowMsgs', 'y');
} else {
	$allowMsgs = 'y';
}
$smarty->assign('allowMsgs', $allowMsgs);
$smarty->assign_by_ref('user_prefs', $user_preferences[$userwatch]);
$user_style = $tikilib->get_user_preference($userwatch, 'theme', $prefs['site_style']);
$smarty->assign_by_ref('user_style', $user_style);
$user_language = $tikilib->get_language($userwatch);
$user_language_text = $tikilib->format_language_list(array($user_language));
$smarty->assign_by_ref('user_language', $user_language_text[0]['name']);
$realName = $tikilib->get_user_preference($userwatch, 'realName', '');
$gender = $tikilib->get_user_preference($userwatch, 'gender', '');
$country = $tikilib->get_user_preference($userwatch, 'country', 'Other');
$smarty->assign('country', $country);
$anonpref = $tikilib->get_preference('userbreadCrumb', 4);
$userbreadCrumb = $tikilib->get_user_preference($userwatch, 'userbreadCrumb', $anonpref);
$smarty->assign_by_ref('realName', $realName);
$smarty->assign_by_ref('gender', $gender);
$smarty->assign_by_ref('userbreadCrumb', $userbreadCrumb);
$homePage = $tikilib->get_user_preference($userwatch, 'homePage', '');
$smarty->assign_by_ref('homePage', $homePage);
$avatar = $tikilib->get_user_avatar($userwatch);
$smarty->assign('avatar', $avatar);
$user_information = $tikilib->get_user_preference($userwatch, 'user_information', 'public');
$smarty->assign('user_information', $user_information);
$userinfo = $userlib->get_user_info($userwatch);
$email_isPublic = $tikilib->get_user_preference($userwatch, 'email is public', 'n');
if ($email_isPublic != 'n') {
	$smarty->assign('scrambledEmail', scrambleEmail($userinfo['email'], $email_isPublic));
}
$smarty->assign_by_ref('userinfo', $userinfo);
$smarty->assign_by_ref('email_isPublic', $email_isPublic);
$userPage = $prefs['feature_wiki_userpage_prefix'] . $userinfo['login'];
$exist = $tikilib->page_exists($userPage);
$smarty->assign("userPage_exists", $exist);
if ($prefs['feature_display_my_to_others'] == 'y') {
	if ($prefs['feature_wiki'] == 'y') {
		include_once ('lib/wiki/wikilib.php');
		$user_pages = $wikilib->get_user_all_pages($userwatch, 'pageName_asc');
		$smarty->assign_by_ref('user_pages', $user_pages);
	}
	if ($prefs['feature_blogs'] == 'y') {
		require_once('lib/blogs/bloglib.php');
		$user_blogs = $bloglib->list_user_blogs($userwatch, false);
		$smarty->assign_by_ref('user_blogs', $user_blogs);
	}
	if ($prefs['feature_galleries'] == 'y') {
		$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
		$smarty->assign_by_ref('user_galleries', $user_galleries);
	}
	if ($prefs['feature_trackers'] == 'y') {
		$trklib = TikiLib::lib('trk');
		$user_items = $trklib->get_user_items($userwatch);
		$smarty->assign_by_ref('user_items', $user_items);
	}
	if ($prefs['feature_articles'] == 'y') {
		include_once ('lib/articles/artlib.php');
		$user_articles = $artlib->get_user_articles($userwatch, -1);
		$smarty->assign_by_ref('user_articles', $user_articles);
	}
	if ($prefs['feature_forums'] == 'y') {
		include_once ("lib/comments/commentslib.php");
		$commentslib = new Comments($dbTiki);
		$user_forum_comments = $commentslib->get_user_forum_comments($userwatch, -1);
		$smarty->assign_by_ref('user_forum_comments', $user_forum_comments);
		$user_forum_topics = $commentslib->get_user_forum_comments($userwatch, -1, 'topics');
		$smarty->assign_by_ref('user_forum_topics', $user_forum_topics);
	}
	if ($prefs['user_who_viewed_my_stuff'] == 'y') {
		$mystuff = array();
		if (isset($user_pages)) {
			$stuffType = 'wiki page';
			foreach ($user_pages as $obj) {
				$mystuff[] = array( 'object' => $obj["pageName"], 'objectType' => $stuffType, 'comment' => '' );
			}
		}
		if (isset($user_blogs)) {
			$stuffType = 'blog';
			foreach ($user_blogs as $obj) {
				$mystuff[] = array( 'object' => $obj["blogId"], 'objectType' => $stuffType, 'comment' => '' );
			}
		}
		if (isset($user_articles)) {
			$stuffType = 'article';
			foreach ($user_articles as $obj) {
				$mystuff[] = array( 'object' => $obj["articleId"], 'objectType' => $stuffType, 'comment' => '' );
			}
		}
		if (isset($user_forum_topics)) {
			$stuffType = 'forum';
			foreach ($user_forum_topics as $obj) {
				$forum_comment = 'comments_parentId=' . $obj["threadId"];
				$mystuff[] = array( 'object' => $obj["object"], 'objectType' => $stuffType, 'comment' => $forum_comment );
			}
		}
		global $logslib;
		if (!is_object($logslib)) {
			require_once("lib/logs/logslib.php");		
		}
		$whoviewed = $logslib->get_who_viewed($mystuff, false);
		$smarty->assign('whoviewed', $whoviewed);
	}
}
if ($prefs['user_tracker_infos']) {
	// arg passed 11,56,58,68=trackerId,fieldId...
	$trackerinfo = explode(',', $prefs['user_tracker_infos']);
	$userTrackerId = $trackerinfo[0];
	array_shift($trackerinfo);
	$fields = $trklib->list_tracker_fields($userTrackerId, 0, -1, 'position_asc', '', true, array('fieldId' => $trackerinfo));
	foreach($fields['data'] as $field) {
		$lll[$field['fieldId']] = $field;
	}
	$items = $trklib->list_items($userTrackerId, 0, 1, '', $lll, $trklib->get_field_id_from_type($userTrackerId, 'u', '1%'), '', '', '', $userwatch);
	$smarty->assign_by_ref('userItem', $items['data'][0]);
}
ask_ticket('user-information');
// Get full user picture if it is set
if ($prefs["user_store_file_gallery_picture"] == 'y') {
	require_once ('lib/userprefs/userprefslib.php');
	if ($user_picture_id = $userprefslib->get_user_picture_id($userwatch)) {	
		$smarty->assign('user_picture_id', $user_picture_id);
	}	
}
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-user_information.tpl');
$smarty->display("tiki.tpl");
