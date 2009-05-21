<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_subscribegroups.php,v 1.1.2.1 2007-12-04 20:33:16 sylvieg Exp $

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
		if ($info['userChoice'] != 'y') {
			return tra('Permission denied');
		}
		if (!empty($groups) && !in_array($group, $groups)) {// limit the group to the groups params
			$group = '';
		}
	}

	$userGroups = $userlib->get_user_groups_inclusion($user);

	if (!empty($_REQUEST['assign']) && !isset($userGroups[$group])) {
		$userlib->assign_user_to_group($user, $group);
	}
	if (!empty($_REQUEST['unassign']) && isset($userGroups[$group])) {
		$userlib->remove_user_from_group($user, $group);
	}
	if (!empty($_REQUEST['default']) && isset($userGroups[$_REQUEST['default']])) {
		$userlib->set_default_group($user, $_REQUEST['default']);
		global $group;
		$group = $_REQUEST['default'];
	}
	$userGroups = $userlib->get_user_groups_inclusion($user);
	if (isset($userGroups['Anonymous'])) {
		unset($userGroups['Anonymous']);
	}
	if (isset($userGroups['Registered'])) {
		unset($userGroups['Registered']);
	}
	if (!empty($groups)) {
		foreach ($userGroups as $g=>$type) {
			if (!in_array($g, $groups)) {
				unset($userGroups[$g]);
			}
		}
	}

	$possiblegroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n', '', 'y');
	$groupDescs = array();
	foreach ($possiblegroups['data'] as $key=>$g) {
		if (isset($showgroupdescription) && $showgroupdescription == 'y') {
			$groupDescs[$g['groupName']] = $g['groupDesc'];
		}
		if (!empty($userGroups[$g['groupName']]) || (!empty($groups) && !in_array($g['groupName'], $groups))) {
			unset($possiblegroups['data'][$key]);
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
	$smarty->assign_by_ref('userGroups', $userGroups);
	$smarty->assign_by_ref('possiblegroups', $possiblegroups['data']);
	$data = $smarty->fetch('wiki-plugins/wikiplugin_subscribegroups.tpl');
	return $data;
}
