<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_group_info()
{
	return array(
		'name' => tra('Group'),
		'documentation' => 'PluginGroup',
		'description' => tra('Display content based on the user\'s groups or friends'),
		'body' => tr('Wiki text to display if conditions are met. The body may contain %0. Text after the marker
			will be displayed to users not matching the conditions.', '<code>{ELSE}</code>'),
		'prefs' => array('wikiplugin_group'),
		'iconname' => 'group',
		'filter' => 'wikicontent',
		'introduced' => 1,
		'tags' => array( 'basic' ),
		'params' => array(
			'friends' => array(
				'required' => false,
				'name' => tra('Allowed User Friends'),
				'description' => tra('Pipe separated list of users whose friends are allowed to view the block.
					Example:') . ' <code>admin|johndoe|foo</code>',
				'since' => '4.0',
				'filter' => 'username',
				'default' => ''
			),
			'groups' => array(
				'required' => false,
				'name' => tra('Allowed Groups'),
				'description' => tra('Pipe separated list of groups allowed to view the block.
					Example:') . ' <code>Admins|Developers</code>',
				'since' => '1',
				'filter' => 'groupname',
				'default' => ''
			),
			'notgroups' => array(
				'required' => false,
				'name' => tra('Denied Groups'),
				'description' => tra('Pipe separated list of groups denied from viewing the block.'),
				'since' => '1',
				'filter' => 'groupname',
				'default' => ''
			),
			'pending' => array(
				'required' => false,
				'name' => tra('Allowed Groups Pending Membership'),
				'description' => tra('User allowed to view block if membership payment to join group (or pipe-separated
					list of groups) is outstanding.'),
				'since' => '13.0',
				'filter' => 'groupname',
				'default' => ''
			),
			'notpending' => array(
				'required' => false,
				'name' => tra('Allowed Groups Full Membership'),
				'description' => tra('User allowed to view block if membership in the group (or pipe-separated list of
					groups) is not pending.'),
				'since' => '13.0',
				'filter' => 'groupname',
				'default' => ''
			),
		),
	);
}

function wikiplugin_group($data, $params)
{
	// TODO : Re-implement friend filter
	global $user, $groupPluginReturnAll;
	$tikilib = TikiLib::lib('tiki');
	$dataelse = '';
	if (strrpos($data, '{ELSE}')) {
		$dataelse = substr($data, strrpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strrpos($data, '{ELSE}'));
	}

	if (isset($groupPluginReturnAll) && $groupPluginReturnAll == true) {
		return $data.$dataelse;
	}

	if (!empty($params['groups'])) {
		$groups = explode('|', $params['groups']);
	}
	if (!empty($params['notgroups'])) {
		$notgroups = explode('|', $params['notgroups']);
	}
	$userPending = array();
	if (!empty($params['pending']) || !empty($params['notpending'])) {
		$attributelib = TikiLib::lib('attribute');
		$attributes = $attributelib->get_attributes('user', $user);
		$userlib = TikiLib::lib('user');
		if (!empty($params['pending'])) {
			$pending = explode('|', $params['pending']);
			foreach ($pending as $pgrp) {
				$grpinfo = $userlib->get_group_info($pgrp);
				$attname = 'tiki.memberextend.' . $grpinfo['id'];
				if (isset($attributes[$attname])) {
					$userPending[] = $pgrp;
				}
			}
		}
		if (!empty($params['notpending'])) {
			$notpending = explode('|', $params['notpending']);
			foreach ($notpending as $npgrp) {
				$grpinfo = $userlib->get_group_info($npgrp);
				$attname = 'tiki.memberextend.' . $grpinfo['id'];
				if (!isset($attributes[$attname])) {
					$userNotPending[] = $npgrp;
				}
			}
		}
	}

	if (empty($groups) && empty($notgroups) && empty($pending) && empty($notpending)) {
		return '';
	}

	$userGroups = $tikilib->get_user_groups($user);
	$smarty = TikiLib::lib('smarty');
	if (count($userGroups) > 1) { //take away the anonymous as everybody who is registered is anonymous
		foreach ($userGroups as $key=>$grp) {
			if ($grp == 'Anonymous') {
				$userGroups[$key] = '';
				break;
			}
		}
	}
	if (!empty($groups) || !empty($pending)) {
		$ok = false;
		if (!empty($groups)) {
			foreach ($userGroups as $grp) {
				if (in_array($grp, $groups)) {
					$ok = true;
					$smarty->assign('groupValid', 'y');
					break;
				}
				$smarty->assign('groupValid', 'n');
			}
		}
		if (count($userPending) > 0) {
			$ok = true;
		}
		if (!$ok)
			return $dataelse;
	}

	if (!empty($notgroups) || !empty($notpending)) {
		$ok = true;
		if (!empty($notgroups)) {
			foreach ($userGroups as $grp) {
				if (in_array($grp, $notgroups)) {
					$ok = false;
					$smarty->assign('notgroupValid', 'y');
					break;
				}
				$smarty->assign('notgroupValid', 'n');
			}
		}
		if (isset($userNotPending) && (count($userNotPending) < count($notpending))) {
			$ok = false;
		}
		if (!$ok)
			return $dataelse;
	}
		
	
	return $data;
}
