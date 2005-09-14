<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_actionlog.php,v 1.1 2005-09-14 21:45:38 sylvieg Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once('lib/logs/logslib.php');
include_once('lib/userslib.php');
include_once('lib/commentslib.php');
$commentslib = new Comments($dbTiki);

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
if ($feature_actionlog != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_actionlog");
	$smarty->display("error.tpl");
	die;
}
$confs = $logslib->get_all_actionlog_conf();
if (isset($_REQUEST['setConf'])) { 
	for ($i = 0; $i < sizeof($confs); ++$i) {
		if (isset($_REQUEST[$confs[$i]['code']]) && $_REQUEST[$confs[$i]['code']] == 'on') {
			$logslib->set_actionlog_conf($confs[$i]['action'], $confs[$i]['objectType'], 'y');
			$confs[$i]['status'] = 'y';
		} else {
			$logslib->set_actionlog_conf($confs[$i]['action'], $confs[$i]['objectType'], 'n');
			$confs[$i]['status'] = 'n';
		}
	}
}
$smarty->assign_by_ref('actionlogConf', $confs);

$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);
$categories = $categlib->list_categs();
$smarty->assign_by_ref('categories', $categories);
foreach ($categories as $categ) {
	$categNames[$categ['categId']] = $categ['name'];
}
$smarty->assign_by_ref('categNames', $categNames);

if (isset($_REQUEST['list'])) {
	if (!isset($_REQUEST['user']))
		$_REQUEST['user'] = '';
	$smarty->assign('reportUser', $_REQUEST['user']);
	if (!isset($_REQUEST['categId']) || $_REQUEST['categId'] == 0)
		$_REQUEST['categId']='';
	else
		$smarty->assign('reportCateg', $categNames[$_REQUEST['categId']]);

	$showCateg = $logslib->action_must_be_logged('*', 'category');
	$smarty->assign('showCateg', $showCateg?'y':'n');
	$showLogin = $logslib->action_must_be_logged('*', 'login');
	$smarty->assign('showLogin', $showLogin?'y':'n');

	$startDate = mktime(0, 0, 0, $_REQUEST["startDate_Month"], $_REQUEST["startDate_Day"], $_REQUEST["startDate_Year"]);
	$smarty->assign('startDate', $startDate);
	$endDate = mktime(23, 59, 59, $_REQUEST["endDate_Month"], $_REQUEST["endDate_Day"], $_REQUEST["endDate_Year"]);
	$smarty->assign('endDate', $endDate);

	$actions = $logslib->list_actions('', '', $_REQUEST['user'], 0, -1, 'lastModif_desc', '', $startDate, $endDate, $_REQUEST['categId']);

	$statUser = $logslib->get_action_stat_user($actions);
	$smarty->assign_by_ref('statUser', $statUser);
	if ($showCateg) {
		$statCateg = $logslib->get_action_stat_categ($actions, $categNames);
		$smarty->assign_by_ref('statCateg', $statCateg);
		$statUserCateg = $logslib->get_action_stat_user_categ($actions, $categNames);
		$smarty->assign_by_ref('statUserCateg', $statUserCateg);
	}
	for ($i = 0; $i < sizeof($actions); ++$i) {
		if ($actions[$i]['categId'])
			$actions[$i]['categName'] = $categNames[$actions[$i]['categId']];
		switch ($actions[$i]['objectType']) {
		case 'wiki page':
			$actions[$i]['link'] = 'tiki-index.php?page='.$actions[$i]['object'];
			break;
		case 'category':
			$actions[$i]['link'] = 'tiki-browse_categories.php?parentId='.$actions[$i]['object'];
			$actions[$i]['object'] = $categNames[$actions[$i]['object']];
			break;
		case 'forum':
			$actions[$i]['link'] = 'tiki-view_forum_thread.php?forumId='.$actions[$i]['object'].'&'.$actions[$i]['comment'];
			if (!isset($forumNames)) {
				$objects = $commentslib->list_forums(0, -1, 'name_asc', '');
				$forumNames = array();
				foreach ($objects['data'] as $object) {
					$forumNames[$object['forumId']] = $object['name'];
				}
			}
			$actions[$i]['object'] = $forumNames[$actions[$i]['object']];
			break;
		case 'image gallery':
			if ($actions[$i]['action'] == 'Uploaded')
				$actions[$i]['link'] = 'tiki-browse_image.php?galleryId='.$actions[$i]['object'].'&'.$actions[$i]['comment'];
			else
				$actions[$i]['link'] = 'tiki-browse_gallery.php?galleryId='.$actions[$i]['object'];
			if (!isset($imageGalleryNames)) {
				include_once('lib/imagegals/imagegallib.php');
				$objects = $imagegallib->list_galleries(0, -1, 'name_asc', 'admin');
				foreach ($objects['data'] as $object) {
					$imageGalleryNames[$object['galleryId']] = $object['name'];
				}
			}
			$actions[$i]['object'] = $imageGalleryNames[$actions[$i]['object']];
			break;
		case 'file gallery':
			$actions[$i]['link'] = 'tiki-list_file_gallery.php?galleryId='.$actions[$i]['object'];
			if (!isset($fileGalleryNames)) {
				include_once('lib/filegals/filegallib.php');
				$objects = $filegallib->list_file_galleries(0, -1, 'name_asc', 'admin', '');
				foreach ($objects['data'] as $object) {
					$fileGalleryNames[$object['galleryId']] = $object['name'];
				}
			}
			$actions[$i]['object'] = $fileGalleryNames[$actions[$i]['object']];
			break;
		}
	}
	if ($showLogin) {
		$logins = $logslib->list_logs('login', $_REQUEST['user'], 0, -1, 'logtime_asc', '', $startDate, $endDate, $actions);
		$logTimes = $logslib->get_login_time($logins['data'], $startDate, $endDate, $actions);
		$smarty->assign_by_ref('logTimes', $logTimes);
		foreach ($logins['data'] as $log) { // merge logs table in action table
			if (strstr($log['logmessage'], "logged from"))
				$action = "Login";
			elseif (strstr($log['logmessage'], "logged out"))
				$action = "Logout";
			else
				$action = ucfirst($log['logmessage']);
			$actions[] = array('lastModif'=>$log['logtime'], 'user'=>$log['loguser'], 'action'=>$action, 'objectType'=>'login');
		}
		usort($actions, array($logslib, 'sort_by_date'));
	}
	$smarty->assign_by_ref('actionlogs', $actions);
}

// Display the template
$smarty->assign('mid', 'tiki-admin_actionlog.tpl');
$smarty->display("tiki.tpl");

?>