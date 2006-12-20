<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_actionlog.php,v 1.22 2006-12-20 18:59:52 sylvieg Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once('lib/logs/logslib.php');
include_once('lib/userslib.php');
include_once('lib/commentslib.php');
include_once('lib/categories/categlib.php');

include_once('lib/contribution/contributionlib.php');
$commentslib = new Comments($dbTiki);

if ($feature_actionlog != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_actionlog");
	$smarty->display("error.tpl");
	die;
}
if (empty($user)) {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_admin == 'y') {
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
}

if (!empty($_REQUEST['actionId'])) {
	$action = $logslib->get_info_action($_REQUEST['actionId']);
	if (empty($action)) {
		$smarty->assign('msg', tra('Must specify actionId'));
		$smarty->display("error.tpl");
		die;
	}
	$smarty->assign_by_ref('action', $action);
	if (isset($_REQUEST['saveAction']) && $feature_contribution == 'y') {
		if ($contributionlib->update($action, empty($_REQUEST['contributions']) ? '': $_REQUEST['contributions'])) {
			$logslib->delete_params($_REQUEST['actionId']);
			if (isset($_REQUEST['contributions'])) {
				$logslib->insert_params($_REQUEST['actionId'], 'contribution', $_REQUEST['contributions']);
			}
		} else {
			$smarty->assign('error', 'found more than one object that can correspond');
		}
	} else {
		if  ($action['objectType'] == 'wiki page') {
			$contributions = $logslib->get_action_contributions($action['actionId']);
		} elseif ($id = $logslib->get_comment_action($action)) {
			$contributions = $logslib->get_action_contributions($action['actionId']);
		} else {
			$contributions = $contributionlib->get_assigned_contributions($action['object'], $action['objectType']); // todo: do a left join
		}
		$cont = array();
		foreach ($contributions as $contribution) {
			$cont[] = $contribution['contributionId'];
		}
		$section = $action['objectType'];
		$_REQUEST['contributions'] = $cont;
		include('contribution.php');
		$contributions['data'][] = array('contributionId'=>0, 'name'=>'');
		if (!empty($_REQUEST['startDate']))
			$smarty->assign('startDate', $_REQUEST['startDate']);
		if (!empty($_REQUEST['endDate']))
		$smarty->assign('endDate', $_REQUEST['endDate']);
	}
}

if ($tiki_p_admin == 'y') {
	$users = $userlib->list_all_users();
	$groups = $userlib->list_all_groups();
} else {
	$users = array($userlib->get_user_id($user) => $user);
 	$groups = $tikilib->get_user_groups($user);
}
$smarty->assign_by_ref('users', $users);
$smarty->assign_by_ref('groups', $groups);
$categories = $categlib->list_categs();
$smarty->assign_by_ref('categories', $categories);
foreach ($categories as $categ) {
	$categNames[$categ['categId']] = $categ['name'];
}
$smarty->assign_by_ref('categNames', $categNames);

if (isset($_REQUEST['list']) || isset($_REQUEST['export'])) {
	$url = '';
	$selectedUsers = array();
	if (isset($_REQUEST['selectedUsers'])) {
		foreach ($users as $key=>$u) {
			if (in_array($u, $_REQUEST['selectedUsers'])) {
				$url .= "&amp;selectedUsers[]=$u";
				$selectedUsers[$key] = 'y';
			} else
				$selectedUsers[$key] = 'n';
		}
	}
	$smarty->assign('selectedUsers', $selectedUsers);
	if (isset($_REQUEST['selectedGroups']) && !(sizeof($_REQUEST['selectedGroups']) == 1 && $_REQUEST['selectedGroups'][0] == '')) {
		$selectedGroups = array();
		foreach ($groups as $key=>$g) {
			if (in_array($g, $_REQUEST['selectedGroups'])) {
				$url .= "&amp;selectedGroups[]=$g";
				$selectedGroups[$key] = 'y';
				if ($tiki_p_admin == 'y') {
					$members = $userlib->get_group_users($g);
					foreach ($members as $m)
						$_REQUEST['selectedUsers'][] = $m;
				}
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
	else {
		$url .= '&amp;categId='. $_REQUEST['categId'];
		$smarty->assign('reportCateg', $categNames[$_REQUEST['categId']]);
	}

	$showCateg = $logslib->action_is_viewed('*', 'category');
	$smarty->assign('showCateg', $showCateg?'y':'n');
	$showLogin = $logslib->action_is_viewed('*', 'login');
	$smarty->assign('showLogin', $showLogin?'y':'n');

	if (isset($_REQUEST['startDate_Month'])) {
		$startDate = mktime(0, 0, 0, $_REQUEST['startDate_Month'], $_REQUEST['startDate_Day'], $_REQUEST['startDate_Year']);
		$url .= "&amp;start=$startDate";
	} elseif (isset($_REQUEST['startDate'])) {
		$startDate = $_REQUEST['startDate'];
	} else
		$startDate = mktime(0, 0, 0, date('n'), date('d'), date('Y'));
	$smarty->assign('startDate', $startDate);
	if (isset($_REQUEST['endDate_Month'])) {
		$endDate = mktime(23, 59, 59, $_REQUEST['endDate_Month'], $_REQUEST['endDate_Day'], $_REQUEST['endDate_Year']);
		$url .= "&amp;end=$endDate";
	} elseif (isset($_REQUEST['endDate'])) {
		$endDate = $_REQUEST['endDate'];
	} else
		$endDate = mktime(23, 59, 59, date('n'), date('d'), date('Y'));
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
			if (preg_match("/old=(.*)/", $actions[$i]['comment'], $matches))
				$actions[$i]['link'] = 'tiki-index.php?page='.$actions[$i]['object'].'&amp;old='.$matches[1];
			else
				$actions[$i]['link'] = 'tiki-index.php?page='.$actions[$i]['object'];
			break;
		case 'category':
			$actions[$i]['link'] = 'tiki-browse_categories.php?parentId='.$actions[$i]['object'];
			if (!empty($categNames[$actions[$i]['object']]))
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
			if (!empty($forumNames[$actions[$i]['object']]))
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
			if (!empty($imageGalleryNames[$actions[$i]['object']]))
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
			if (!empty($fileGalleryNames[$actions[$i]['object']]))
				$actions[$i]['object'] = $fileGalleryNames[$actions[$i]['object']];
			break;
		case 'comment':
			preg_match('/type=([^&]*)(&.*)/', $actions[$i]['comment'], $matches);
			switch ($matches[1]) {
			case 'wiki page': case 'wiki+page': case 'wiki%20page':
				$actions[$i]['link'] = 'tiki-index.php?page='.$actions[$i]['object'];
				if (preg_match("/old=(.*)&amp;/", $actions[$i]['comment'], $ms))
					$actions[$i]['link'] .= '&amp;old='.$ms[1];
				$actions[$i]['link'] .= $matches[2];
				break;
			case 'file gallery':
				$actions[$i]['link'] = 'tiki-list_file_gallery.php?galleryId='.$actions[$i]['object'].$matches[2];
				break;
			case 'image gallery':
				$actions[$i]['link'] = 'tiki-browse_gallery.php?galleryId='.$actions[$i]['object'].$matches[2];
				break;
			}
			break;
		case 'sheet':
			if (!isset($sheetNames)) {
				global $sheetlib; include_once('lib/sheet/grid.php');
				$objects = $sheetlib->list_sheets();
				foreach ($objects['data'] as $object) {
					$sheetNames[$object['sheetId']] = $object['title'];
				}
			}
			if (!empty($sheetNames[$actions[$i]['object']]))
				$actions[$i]['object'] = $sheetNames[$actions[$i]['object']];
			$actions[$i]['link'] = 'tiki-view_sheets.php?sheetId='.$actions[$i]['object'];
			break;
		case 'blog':

			if (!isset($blogNames)) {
				$objects = $tikilib->list_blogs();
				foreach ($objects['data'] as $object) {
					$blogNames[$object['blogId']] = $object['title'];
				}
		}
			$actions[$i]['link'] = 'tiki-view_blog.php?'.$actions[$i]['comment'];
			if (!empty($blogNames[$actions[$i]['object']]))
				$actions[$i]['object'] = $blogNames[$actions[$i]['object']];
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
	if (isset($_REQUEST['sort_mode'])) {
		list($col, $order) = split('_', $_REQUEST['sort_mode']);
		$sort = array();
		foreach ($actions as $a) {
			$sort[] = isset($a[$col]) ? $a[$col]: '';
		}
		array_multisort($sort, ($order == 'desc')?SORT_DESC: SORT_ASC, $actions);
		$smarty->assign('sort_mode', $_REQUEST['sort_mode']);
	}
	$smarty->assign_by_ref('actionlogs', $actions);
	if (isset($_REQUEST['unit']))
		$url .= '&amp;unit='. $_REQUEST['unit'];
	$smarty->assign('url', "&amp;list=y$url#Report");
	if (isset($_REQUEST['export'])) {
		$csv = $logslib->export($actions);
		$smarty->assign('csv', $csv);
	}
	if ($feature_contribution == 'y') {
		if (empty($_REQUEST['contribTime']))
			$_REQUEST['contribTime'] = 'w';
		$contributionStat = $logslib->get_stat_contribution($actions, $startDate, $endDate, $_REQUEST['contribTime']);
		$smarty->assign_by_ref('contributionStat', $contributionStat['data']);
		$smarty->assign_by_ref('contributionNbCols', $contributionStat['nbCols']);
		$smarty->assign_by_ref('contribTime', $_REQUEST['contribTime']);
	}
}

if (isset($_REQUEST['time']))
	$smarty->assign('time', $_REQUEST['time']);
if (isset($_REQUEST['unit']))
	$smarty->assign('unit', $_REQUEST['unit']);
// Display the template
$smarty->assign('mid', 'tiki-admin_actionlog.tpl');
$smarty->display("tiki.tpl");

?>
