<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_subscribegroups_help() {
	$help = tra('Subscribe or unsubscribe to a group').":\n";
	$help.= "~np~<br />{SUBSCRIBEGROUPS(subscribe=text,groups=g1:g2) /}<br />~/np~";
	return $help;
}

function wikiplugin_subscribegroups_info() {
	return array(
		'name' => tra('Subscribe Groups'),
		'documentation' => 'PluginSubscribeGroups',		
		'description' => tra('Subscribe or unsubscribe to a group'),
		'prefs' => array( 'wikiplugin_subscribegroups' ),
		'params' => array(
			'subscribe' => array(
				'required' => false,
				'name' => tra('Subscribe'),
				'description' => tra('text'),
			),
			'showsubscribe' => array(
				'required' => false, 
				'name' => tra('Show subscribe box'),
				'description' => 'y|n',
			),
			'showdefault' => array(
				'required' => false, 
				'name' => tra('Show default setting and buttons'),
				'description' => 'y|n',
			),
			'showgroupdescription' => array(
				'required' => false, 
				'name' => tra('Show group description'),
				'description' => 'y|n',
			),
			'groups' => array(
				'required' => false,
				'name' => tra('Groups'),
				'description' => tra('Colon separated list of groups.'),
			),
			'including' => array(
				'required' => false,
				'name' => tra('Including group'),
				'description' => tra('Group'),
			),
			'defaulturl' => array(
				'required' => false,
				'name' => tra('Url'),
				'description' => tra('After changing default'),
			),
		),
	);
}

function wikiplugin_subscribegroups($data, $params) {
	global $tiki_p_subscribe_groups, $userlib, $user, $smarty;
	if ($tiki_p_subscribe_groups != 'y' || empty($user)) {
		return tra('Permission denied');
	}
	extract ($params, EXTR_SKIP);

	if (!empty($_REQUEST['assign'])) {
		$group = $_REQUEST['assign'];
	} elseif (!empty($_REQUEST['unassign'])) {
		$group = $_REQUEST['unassign'];
	} else {
		$group = '';
	}

	if (!empty($groups)) {
		$groups = explode(':',$groups);
	}
	if (!empty($including)) {
		$groups = $userlib->get_including_groups($including);
	}
	if ($group) {
		if ($group == 'Anonymous' || $group == 'Registered') {
			return tra('Incorrect param');
		}
		if (!($info = $userlib->get_group_info($group))) {
			return tra('Incorrect param');
		}
		if ($info['userChoice'] != 'y') { // limit to userchoice
			return tra('Permission denied');
		}
		if (!empty($groups) && !in_array($group, $groups)) {// limit the group to the groups params
			$group = '';
		}
	}

	if (!empty($_REQUEST['assign']) && !isset($userGroups[$group])) {
		$userlib->assign_user_to_group($user, $group);
	}
	if (!empty($_REQUEST['unassign']) && isset($userGroups[$group])) {
		$userlib->remove_user_from_group($user, $group);
	}
	$userGroups = $userlib->get_user_groups_inclusion($user);
	if (!empty($_REQUEST['default']) && isset($userGroups[$_REQUEST['default']])) {
		$userlib->set_default_group($user, $_REQUEST['default']);
		global $group;
		$group = $_REQUEST['default'];
		if (isset($defaulturl)) {
			header("Location: $defaulturl");
			die;
		}
	}
	if (isset($userGroups['Anonymous'])) {
		unset($userGroups['Anonymous']);
	}
	if (isset($userGroups['Registered'])) {
		unset($userGroups['Registered']);
	}
	if (isset($groups)) {
		foreach ($userGroups as $g=>$type) {
			if (!in_array($g, $groups)) {
				unset($userGroups[$g]);
			}
		}
	}

	$allGroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n', '', 'y');
	$possibleGroups = array();
	foreach ($allGroups['data'] as $gr) {
		if ($gr['userChoice'] == 'y' && (empty($groups) || in_array($gr['groupName'], $groups)) && !isset($userGroups[$gr['groupName']]) && $gr['groupName'] != 'Registered' && $gr['groupName'] != 'Anonymous') {
			$possibleGroups[] = $gr['groupName'];
		}
	}
	if (isset($subscribe)) {
		$smarty->assign_by_ref('subscribe', $subscribe);
	} else {
		$smarty->assign('subscribe', '');
	}
	if (isset($showsubscribe) && $showsubscribe == 'n') {
		$smarty->assign('showsubscribe', 'n');
	} else {
		$smarty->assign('showsubscribe', 'y');
	}
	if (isset($showdefault) && $showdefault == 'y') {
		$smarty->assign('showdefault', 'y');
	} else {
		$smarty->assign('showdefault', 'n');
	}
	if (isset($showgroupdescription) && $showgroupdescription == 'y') {
		$smarty->assign_by_ref('groupDescs', $groupDescs);
		$smarty->assign('showgroupdescription', 'y');
	} else {
		$smarty->assign('showgroupdescription', 'n');
	}
	if (!empty($defaulturl)) {
		$smarty->assign('defaulturl', $defaulturl);
	}   
	$all = array();
	foreach ($allGroups['data'] as $gr) {
		if (isset($userGroups[$gr['groupName']]) || in_array($gr['groupName'], $possibleGroups))
			$all[$gr['groupName']] = $gr;
	}
	$smarty->assign_by_ref('userGroups', $userGroups);
	$smarty->assign_by_ref('possibleGroups', $possibleGroups);
	$smarty->assign_by_ref('allGroups', $all);
	$data = $smarty->fetch('wiki-plugins/wikiplugin_subscribegroups.tpl');
	return $data;
}
