<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_subscribegroups_info()
{
	return array(
		'name' => tra('Subscribe Groups'),
		'documentation' => 'PluginSubscribeGroups',
		'description' => tra('Allow users to subscribe to a list of groups'),
		'prefs' => array( 'wikiplugin_subscribegroups' ),
		'iconname' => 'group',
		'introduced' => 2,
		'params' => array(
			'subscribe' => array(
				'required' => false,
				'name' => tra('Subscribe'),
				'description' => tr('Text shown in the dropdown box. Default: %0Subscribe to a group%1', '<code>',
					'</code>'),
				'since' => '2.0',
				'default' => '',
			),
			'showsubscribe' => array(
				'required' => false, 
				'name' => tra('Show Subscribe Box'),
				'description' => tra('Show the subscribe box (shown by default). Will not show if there are no other
					groups the user may register for.'),
				'since' => '4.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'subscribestyle' => array(
				'required' => false,
				'name' => tra('Subscribe Box Style'),
				'description' => tra('Style to show the subscribe box.'),
				'since' => '8.0',
				'filter' => 'alpha',
				'default' => 'dropdown',
				'options' => array(
					array('text' => tra('Dropdown'), 'value' => 'dropdown'),
					array('text' => tra('Table'), 'value' => 'table'),
				)
			),
			'showdefault' => array(
				'required' => false, 
				'name' => tra('Show Default'),
				'description' => tra('Shows which group is the user\'s default group (if any) and allows the user to
					change his or her default group.'),
				'since' => '4.0',
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
				'since' => '4.0',
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
				'since' => '2.0',
				'default' => '',
			),
			'including' => array(
				'required' => false,
				'name' => tra('Including Group'),
				'description' => tra('Only list groups that include the group being specified here'),
				'since' => '4.0',
				'default' => '',
			),
			'defaulturl' => array(
				'required' => false,
				'name' => tra('Default URL'),
				'description' => tra('Page user will be directed to after clicking on icon to change default group'),
				'since' => '4.0',
				'default' => '',
			),
			'leadergroupname' => array(
				'required' => false,
				'name' => tra('Leader Group Name'),
				'description' => tr('Name of group for leaders of the group, where %0 will be substituted by
					the group name', '<code>groupName</code>'),
				'since' => '8.0',
				'default' => '',
			),
			'pendinggroupname' => array(
				'required' => false,
				'name' => tra('Pending Users Group Name'),
				'description' => tr('Name of group for users that are waiting for approval to enter the group, where
					%0 will be substituted by the group name', '<code>groupName</code>'),
				'since' => '8.0',
				'default' => '',
			),
			'managementpagename' => array(
				'required' => false,
				'name' => tra('Group Management Page Name'),
				'description' => tr('Name of wiki page for group management by leaders, where %0 will be
					substituted by the group name', '<code>groupName</code>'),
				'since' => '8.0',
				'default' => '',
			), 
			'hidelink_including' => array(
				'required' => false,
				'name' => tra('Hide link for groups including'),
				'description' => tra('Hide link to group home page for groups that include the group being specified here'),
				'since' => '8.0',
				'default' => '',
			),
			'alwaysallowleave' => array(
				'required' => false,
				'name' => tra('Always allow leaving group'),
				'description' => tra('Always allow leaving group even if the group settings do not allow user choice.'),
				'since' => '8.0',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
                                        array('text' => '', 'value' => ''),
                                        array('text' => tra('Yes'), 'value' => 'y'),
                                        array('text' => tra('No'), 'value' => 'n')
                                )
			)
		)
	);
}

function wikiplugin_subscribegroups($data, $params)
{
	global $tiki_p_subscribe_groups, $user;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');
	if ($tiki_p_subscribe_groups != 'y' || empty($user)) {
		return tra('You do not have permission to subscribe to groups.');
	}
	extract($params, EXTR_SKIP);

	if (!empty($_REQUEST['assign'])) {
		$group = $_REQUEST['assign'];
	} elseif (!empty($_REQUEST['unassign'])) {
		$group = $_REQUEST['unassign'];
	} else {
		$group = '';
	}

	if (!empty($groups)) {
		$groups = explode(':', $groups);
	}
	if (!empty($including)) {
		$groups = $userlib->get_including_groups($including);
	}
	if (!empty($hidelink_including)) {
		$privategroups = $userlib->get_including_groups($hidelink_including);
		$smarty->assign('privategroups', $privategroups);
	} else {
		$smarty->assign('privategroups', array());
	}
	if ($group) {
		$garray = (array) $group;
		foreach ($garray as &$g) {
			if ($g == 'Anonymous' || $g == 'Registered') {
				return tra('Incorrect parameter');
			}
			if (!($info = $userlib->get_group_info($g))) {
				return tra('Incorrect parameter');
			}
			if (isset($alwaysallowleave) && $alwaysallowleave == 'y') {
				if ($info['userChoice'] != 'y' && !empty($_REQUEST['assign'])) {
					return tra('You do not have permission to subscribe to groups');
				}
				$smarty->assign('alwaysallowleave', 'y');
			} else {
				if ($info['userChoice'] != 'y') {
					return tra('You do not have permission to subscribe to groups');
				}
				$smarty->assign('alwaysallowleave', 'n');
			}
			if (!empty($groups) && !in_array($g, $groups)) {// limit the group to the groups params
				$g = '';
			}
		}
		unset($g);
	}

	$userGroups = $userlib->get_user_groups_inclusion($user);
	if (isset($garray)) {
		foreach ($garray as $g) {
			if (!empty($_REQUEST['assign']) && !isset($userGroups[$g])) {
				$userlib->assign_user_to_group($user, $g);
			}
			if (!empty($_REQUEST['unassign']) && isset($userGroups[$g])) {
				$userlib->remove_user_from_group($user, $group);
			}
		}
		$userGroups = $userlib->get_user_groups_inclusion($user); // refresh after update
	}
	if (!empty($_REQUEST['default']) && isset($userGroups[$_REQUEST['default']])) {
		$userlib->set_default_group($user, $_REQUEST['default']);
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

	$leadergroups = array();
	$managementpages = array();
	if (!empty($leadergroupname)) {
		$pattern = '/' . str_replace('groupName', '(.+)', preg_quote($leadergroupname)) . '/';
		foreach ($userGroups as $g=>$type) {
			if (preg_match($pattern, $g, $matches)) {
				// these are the groups where the user is a leader
				$leadergroups[] = $matches[1]; 
			} 
			if (!empty($managementpagename)) {
				$managementpages[$g] = str_replace('groupName', $g, $managementpagename);
			}
		}
	}
	$smarty->assign('managementpages', $managementpages);

	if (isset($groups)) {
		foreach ($userGroups as $g=>$type) {
			if (!in_array($g, $groups)) {
				unset($userGroups[$g]);
			}
			// set type as included if user is a leader even if user has real group because he should not leave
			if (in_array($g, $leadergroups)) {
				$userGroups[$g] = 'leader';
			}
		}
	}

	$allGroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');

	$possibleGroups = array();
	$basegroupnames = array();
	foreach ($allGroups['data'] as $gr) {
		// hide pending (needing approval) group of user if he is already in base group 
		if (!empty($pendinggroupname)) {
			$pattern = '/' . str_replace('groupName', '(.+)', preg_quote($pendinggroupname)) . '/';
			if (preg_match($pattern, $gr['groupName'], $matches)) {
				$basegroupnames[$gr['groupName']] = $matches[1];
				if (isset($userGroups[$matches[1]])) {
					continue;
				}
			}
		} 
		if ($gr['userChoice'] == 'y' && (empty($groups) || in_array($gr['groupName'], $groups)) && !isset($userGroups[$gr['groupName']]) && $gr['groupName'] != 'Registered' && $gr['groupName'] != 'Anonymous') {
			$possibleGroups[] = $gr['groupName'];
		}
	}
	$smarty->assign('basegroupnames', $basegroupnames);

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
	if (isset($subscribestyle)) {
		$smarty->assign('subscribestyle', $subscribestyle);
	} else {
		$smarty->assign('subscribestyle', 'dropdown');
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
