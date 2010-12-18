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
		'description' => tra('Allow users to subscribe to a list of groups'),
		'prefs' => array( 'wikiplugin_subscribegroups' ),
		'params' => array(
			'subscribe' => array(
				'required' => false,
				'name' => tra('Subscribe'),
				'description' => tra('Text shown in the dropdown box. Default: "Subscribe to a group"'),
				'default' => '',
			),
			'showsubscribe' => array(
				'required' => false, 
				'name' => tra('Show Subscribe Box'),
				'description' => tra('Show the subscribe drop down box (shown by default). Will not show if there are no other groups the user may register for.'),
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showdefault' => array(
				'required' => false, 
				'name' => tra('Show Default'),
				'description' => tra('Shows which group is the user\'s default group (if any) and allows the user to change his default group.'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'showgroupdescription' => array(
				'required' => false, 
				'name' => tra('Group Description'),
				'description' => tra('Show the description of the group (not shown by default)'),
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'groups' => array(
				'required' => false,
				'name' => tra('Groups'),
				'description' => tra('Colon separated list of groups. By default the list of groups available to the user.'),
				'default' => '',
			),
			'including' => array(
				'required' => false,
				'name' => tra('Including Group'),
				'description' => tra('All groups including this group will be listed'),
				'default' => '',
			),
			'defaulturl' => array(
				'required' => false,
				'name' => tra('Default URL'),
				'description' => tra('Page user will be directed to after clicking on icon to change default group'),
				'default' => '',
			)
		)
	);
}

function wikiplugin_subscribegroups($data, $params) {
	global $tiki_p_subscribe_groups, $userlib, $user, $smarty;
	if ($tiki_p_subscribe_groups != 'y' || empty($user)) {
		return tra('You do not have permission to subscribe to groups.');
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
			return tra('Incorrect parameter');
		}
		if (!($info = $userlib->get_group_info($group))) {
			return tra('Incorrect parameter');
		}
		if ($info['userChoice'] != 'y') { // limit to userchoice
			return tra('You do not have permission to subscribe to groups');
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
