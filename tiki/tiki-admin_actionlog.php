<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_actionlog.php,v 1.9 2005-11-04 16:47:53 sylvieg Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once('lib/logs/logslib.php');
include_once('lib/userslib.php');
include_once('lib/commentslib.php');
include_once('lib/categories/categlib.php');
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
		if (isset($_REQUEST['view_'.$confs[$i]['code']]) && $_REQUEST['view_'.$confs[$i]['code']] == 'on') {//viewed and reported
			$logslib->set_actionlog_conf($confs[$i]['action'], $confs[$i]['objectType'], 'v');
			$confs[$i]['status'] = 'v';
		} elseif (isset($_REQUEST[$confs[$i]['code']]) && $_REQUEST[$confs[$i]['code']] == 'on') {
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
$groups = $userlib->list_all_groups();
$smarty->assign_by_ref('groups', $groups);
$categories = $categlib->list_categs();
$smarty->assign_by_ref('categories', $categories);
foreach ($categories as $categ) {
	$categNames[$categ['categId']] = $categ['name'];
}
$smarty->assign_by_ref('categNames', $categNames);

if (isset($_REQUEST['list'])) {
	$selectedUsers = array();
	if (isset($_REQUEST['selectedUsers'])) {
		foreach ($users as $key=>$user) {
			if (in_array($user, $_REQUEST['selectedUsers']))
				$selectedUsers[$key] = 'y';
			else
				$selectedUsers[$key] = 'n';
		}
	}
	$smarty->assign('selectedUsers', $selectedUsers);
	if (isset($_REQUEST['selectedGroups']) && !(sizeof($_REQUEST['selectedGroups']) == 1 && $_REQUEST['selectedGroups'][0] == '')) {
		$selectedGroups = array();
		foreach ($groups as $key=>$group) {
			if (in_array($group, $_REQUEST['selectedGroups'])) {
				$selectedGroups[$key] = 'y';
				$members = $userlib->get_group_users($group);
				foreach ($members as $m)
					$_REQUEST['selectedUsers'][] = $m;
			} else {
				$selectedGroups[$key] = 'n';
			}
		}
		$smarty->assign_by_ref('selectedGroups', $selectedGroups);
	}
	if (!isset($_REQUEST['selectedUsers']) || (sizeof($_REQUEST['selectedUsers']) == 1 && $_REQUEST['selectedUsers'][0] == ''))
		$_REQUEST['selectedUsers'] = '';
	if (!isset($_REQUEST['categId']) || $_REQUEST['categId'] == 0)
		$_REQUEST['categId']='';
	else
		$smarty->assign('reportCateg', $categNames[$_REQUEST['categId']]);

	$showCateg = $logslib->action_is_viewed('*', 'category');
	$smarty->assign('showCateg', $showCateg?'y':'n');
	$showLogin = $logslib->action_is_viewed('*', 'login');
	$smarty->assign('showLogin', $showLogin?'y':'n');

	$startDate = mktime(0, 0, 0, $_REQUEST["startDate_Month"], $_REQUEST["startDate_Day"], $_REQUEST["startDate_Year"]);
	$smarty->assign('startDate', $startDate);
	$endDate = mktime(23, 59, 59, $_REQUEST["endDate_Month"], $_REQUEST["endDate_Day"], $_REQUEST["endDate_Year"]);
	$smarty->assign('endDate', $endDate);

	$actions = $logslib->list_actions('', '', $_REQUEST['selectedUsers'], 0, -1, 'lastModif_desc', '', $startDate, $endDate, $_REQUEST['categId']);

	$statUser = $logslib->get_action_stat_user($actions);
	$smarty->assign_by_ref('statUser', $statUser);
	if ($showCateg) {
		$statCateg = $logslib->get_action_stat_categ($actions, $categNames);
		$smarty->assign_by_ref('statCateg', $statCateg);
		$volCateg = $logslib->get_action_vol_categ($actions, $categNames);
		if (isset($_REQUEST['unit']) && $_REQUEST['unit'] == 'kb')
			$volCateg = $logslib->in_kb($volCateg);
		$smarty->assign_by_ref('volCateg', $volCateg);
		$volUserCateg = $logslib->get_action_vol_user_categ($actions, $categNames);
		if (isset($_REQUEST['unit']) && $_REQUEST['unit'] == 'kb')
			$volUserCateg = $logslib->in_kb($volUserCateg);
		$smarty->assign_by_ref('volUserCateg', $volUserCateg);
		$typeVol = $logslib->get_action_vol_type($volCateg);
		$smarty->assign_by_ref('typeVol', $typeVol);
		$statUserCateg = $logslib->get_action_stat_user_categ($actions, $categNames);
		$smarty->assign_by_ref('statUserCateg', $statUserCateg);
	}
	for ($i = 0; $i < sizeof($actions); ++$i) {
		if ($actions[$i]['categId'])
			$actions[$i]['categName'] = $categNames[$actions[$i]['categId']];
		if ($bytes = $logslib->get_volume_action($actions[$i])) {
			if (isset($bytes['add'] ))
				$actions[$i]['add'] = $bytes['add'];
			if (isset($bytes['del']))
				$actions[$i]['del'] = $bytes['del'];
		}
		switch ($actions[$i]['objectType']) {
		case 'wiki page':
			$actions[$i]['link'] = 'tiki-index.php?page='.$actions[$i]['object'];
			break;
		case 'category':
			$actions[$i]['link'] = 'tiki-browse_categories.php?parentId='.$actions[$i]['object'];
			$actions[$i]['object'] = $categNames[$actions[$i]['object']];
			break;
		case 'forum':
			if ($actions[$i]['action'] == 'Removed')
				$actions[$i]['link'] = 'tiki-view_forum.php?forumId='.$actions[$i]['object'].'&'.$actions[$i]['comment'];// threadId dded for debug info
			else
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
			if ($actions[$i]['action'] == 'Uploaded' || $actions[$i]['action'] == 'Downloaded')
				$actions[$i]['link'] = 'tiki-upload_file.php?galleryId='.$actions[$i]['object'].'&'.$actions[$i]['comment'];
			else
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
		$logins = $logslib->list_logs('login', $_REQUEST['selectedUsers'], 0, -1, 'logtime_asc', '', $startDate, $endDate, $actions);
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
	if (isset($_REQUEST['unit']) && $_REQUEST['unit'] == 'kb') {
		for ($i = count($actions) -1; $i >= 0; --$i) {
			if (isset($actions[$i]['add']))
				$actions[$i]['add'] = round($actions[$i]['add']/1024);
			if (isset($actions[$i]['del']))
				$actions[$i]['del'] = round($actions[$i]['del']/1024);
		}
	}
	$smarty->assign_by_ref('actionlogs', $actions);
}
if (isset($_REQUEST['unit']))
	$smarty->assign('unit', $_REQUEST['unit']);
// Display the template
$smarty->assign('mid', 'tiki-admin_actionlog.tpl');
$smarty->display("tiki.tpl");

?>