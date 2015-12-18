<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');
$userprefslib = TikiLib::lib('userprefs');
$imagegallib = TikiLib::lib('imagegal');
$access->check_feature('feature_userPreferences');
$access->check_user($user);
$auto_query_args = array('view_user');
if (!isset($_REQUEST["showall"])) $_REQUEST["showall"] = 'n';
$smarty->assign('showall', $_REQUEST["showall"]);
$userwatch = $user;
if (isset($_REQUEST["view_user"])) {
	if ($_REQUEST["view_user"] <> $user) {
		if ($tiki_p_admin == 'y') {
			$userwatch = $_REQUEST["view_user"];
		} else {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to view other users data"));
			$smarty->display("error.tpl");
			die;
		}
	} else {
		$userwatch = $user;
	}
}
$smarty->assign('userwatch', $userwatch);
// Upload avatar is processed here
if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	check_ticket('pick-avatar');
	$name = $_FILES['userfile1']['name'];

	$avatarlib = TikiLib::lib('avatar');
	$avatarlib->set_avatar_from_url($_FILES['userfile1']['tmp_name'], $userwatch, $name);

	/* redirect to prevent re-submit on page reload */
	if ($tiki_p_admin == 'y' && $user !== $userwatch) {
		header('Location: tiki-pick_avatar.php?view_user=' . $userwatch);
	} else {
		header('Location: tiki-pick_avatar.php');
	}
	exit;
}

if (isset($_REQUEST["uselib"])) {
	check_ticket('pick-avatar');
	$userprefslib->set_user_avatar($userwatch, 'l', $_REQUEST["avatar"], '', '', '', '');
}
if (isset($_REQUEST["reset"])) {
	check_ticket('pick-avatar');
	$userprefslib->set_user_avatar($userwatch, '0', '', '', '', '', '');
	$userprefslib->remove_file_gallery_image($userwatch);
}
$avatars = array();
$h = opendir("img/avatars/");
while ($file = readdir($h)) {
	if ($file != '.' && $file != '..' && $file != 'index.php' && substr($file, 0, 1) != "." && $file != "CVS" && $file != "README") {
		$avatars[] = 'img/avatars/' . $file;
	}
}
closedir($h);
$smarty->assign_by_ref('avatars', $avatars);
$smarty->assign('numav', count($avatars));
$smarty->assign('yours', rand(0, count($avatars)));

$avatar = $tikilib->get_user_avatar($userwatch);
$smarty->assign('avatar', $avatar);

// Get full user picture if it is set
if ($prefs["user_store_file_gallery_picture"] == 'y' && $user_picture_id = $userprefslib->get_user_picture_id($userwatch) ) {
	$smarty->assign('user_picture_id', $user_picture_id);	
}

ask_ticket('pick-avatar');
include_once ('tiki-mytiki_shared.php');
$smarty->assign('mid', 'tiki-pick_avatar.tpl');
$smarty->display("tiki.tpl");
