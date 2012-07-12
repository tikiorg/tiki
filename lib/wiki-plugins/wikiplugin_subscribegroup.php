<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_subscribegroup_info()
{
	return array(
		'name' => tra('Subscribe Group'),
		'documentation' => 'PluginSubscribeGroup',
		'description' => tra('Allow users to subscribe to a group'),
		'prefs' => array( 'wikiplugin_subscribegroup' ),
		'body' => tra('text displayed before the button'),
		'icon' => 'img/icons/group_add.png',
		'tags' => array( 'basic' ),
		'params' => array(
			'group' => array(
				'required' => true,
				'name' => tra('Group Name'),
				'description' => tra('Group name to subscribe to or unsubscribe from'),
				'filter' => 'groupname',
				'default' => ''
			),
			'subscribe' => array(
				'required' => false,
				'name' => tra('Subscribe Text'),
				'description' => tra('Subscribe text, containing %s as the placeholder for the group name.'),
				'default' => tra('Subscribe') . '%s',
			),
			'unsubscribe' => array(
				'required' => false,
				'name' => tra('Unsubscribe Text'),
				'description' => tra('Unsubscribe text, containing %s as the placeholder for the group name.'),
				'default' => tra('Unsubscribe') . '%s'
			),
			'subscribe_action' => array(
				'required' => false,
				'name' => tra('Subscribe Action'),
				'description' => tra('Subscribe button label, containing %s as the placeholder for the group name.'),
				'default' => tra('OK')
			),
			'unsubscribe_action' => array(
				'required' => false,
				'name' => tra('Unsubscribe Action'),
				'description' => tra('Unsubscribe button label, containing %s as the placeholder for the group name.'),
				'default' => tra('OK')
			),
			'postsubscribe_url' => array(
				'required' => false,
				'name' => tra('Postsubscribe URL'),
				'description' => tra('URL to send the user to after subscribing, if required.'),
				'filter' => 'url',
				'default' => ''
			),
			'postunsubscribe_url' => array(
				'required' => false,
				'name' => tra('Postunsubscribe URL'),
				'description' => tra('URL to send the user to after unsubscribing, if required.'),
				'filter' => 'url',
				'default' => ''
			),
			'defgroup' => array(
				'required' => false,
				'name' => tra('Default Group'),
				'description' => tra('Make this the default group text, containing %s as the placeholder for the group name..'),
				'default' => tra('OK')
			),
			'undefgroup' => array(
				'required' => false,
				'name' => tra('Not Default Group'),
				'description' => tra('Stop this being default group text, containing %s as the placeholder for the group name..'),
				'default' => tra('OK')
			),
			'defgroup_action' => array(
				'required' => false,
				'name' => tra('Default Group Action'),
				'description' => tra('Default group button label. Will subscribe to the group first if not already a member.'),
				'default' => tra('OK')
			),
			'undefgroup_action' => array(
				'required' => false,
				'name' => tra('Not Default Group Action'),
				'description' => tra('Stop this being default group button label. Does not unsubscribe from the group.'),
				'default' => tra('OK')
			),
			'undefgroup_group' => array(
				'required' => false,
				'name' => tra('Group To Set When Not Default'),
				'description' => tra('Group name to set as default when user stops this group being it.'),
				'filter' => 'groupname',
				'default' => 'Registered'
			),
		),
	);
}

function wikiplugin_subscribegroup($data, $params)
{
	global $tiki_p_subscribe_groups, $userlib, $user, $smarty;
	static $iSubscribeGroup = 0;
	++$iSubscribeGroup;
	if (empty($user)) {
		return '';
	}
	if ($tiki_p_subscribe_groups != 'y') {
		return tra('Permission denied');
	}
	extract($params, EXTR_SKIP);

	if (empty($group)) {
		if (!empty($_REQUEST['group'])) {
			$group = $_REQUEST['group'];
		} else {
			return tra('Missing parameter');
		}
	}
	if ($group == 'Anonymous' || $group == 'Registered') {
		return tra('Incorrect param');
	}
	if (!($info = $userlib->get_group_info($group)) || $info['groupName'] != $group) { // must have the right case
		return tra('Incorrect param');
	}
	if ($info['userChoice'] != 'y') {
		return tra('Permission denied');
	}

	$groups = $userlib->get_user_groups_inclusion($user);
	$current_defgroup = $userlib->get_user_default_group($user);

	if (!empty($_REQUEST['subscribeGroup']) && !empty($_REQUEST['iSubscribeGroup']) && $_REQUEST['iSubscribeGroup'] == $iSubscribeGroup && $_REQUEST['group'] == $group) {
		if (isset($defgroup) || isset($defgroup_action) || isset($undefgroup) || isset($undefgroup_action)) {
			if ($current_defgroup == $group) {
				$new_group = !empty($undefgroup_group) ? $undefgroup_group : 'Registered';
				$userlib->set_default_group($user, $new_group);
			} else {
				if (!isset($groups[$group])) {
					$userlib->assign_user_to_group($user, $group);
				}
				$userlib->set_default_group($user, $group);
			}
			include_once('lib/core/Zend/OpenId.php');	// contains useful redirect selfUrl functions
			Zend_OpenId::redirect(Zend_OpenId::selfUrl());
			die;
		} else if (isset($groups[$group])) {
			$userlib->remove_user_from_group($user, $group);
			unset($groups[$group]);
			if (!empty($postunsubscribe_url)) {
				header("Location: $postunsubscribe_url");
				die;
			}
		} else {
			$userlib->assign_user_to_group($user, $group);
			$groups[$group] = 'real';
			if (!empty($postsubscribe_url)) {
				header("Location: $postsubscribe_url");
				die;
			}
		}
	}

	if (isset($undefgroup) || isset($undefgroup_action)) {
		if ($current_defgroup == $group) {
			$text = isset($undefgroup) ? $undefgroup : '';
			if (!isset($undefgroup_action)) {
				$undefgroup_action = tra('OK');
			}
			$smarty->assign('action', $undefgroup_action);
		} else {
			$text = isset($defgroup) ? $defgroup : '';
			if (!isset($defgroup_action)) {
				$defgroup_action = tra('OK');
			}
			$smarty->assign('action', $defgroup_action);
		}
	} else if (isset($groups[$group])) {//user already in the group->
		if ($groups[$group] == 'included') {
			return tra('Incorrect param');
		}
		$text = isset($unsubscribe)? $unsubscribe: tra('Unsubscribe') . '%s';
		if (!isset($unsubscribe_action)) {
			$unsubscribe_action = tra('OK');
		}
	} else {
		$text = isset($subscribe)? $subscribe: tra('Subscribe') . '%s';
		if (!isset($subscribe_action)) {
			$subscribe_action = tra('OK');
		}
	}
	$smarty->assign('text', sprintf(tra($text), $group));
	$smarty->assign('subscribeGroup', $group);
	$smarty->assign('iSubscribeGroup', $iSubscribeGroup);
	$data = $data.$smarty->fetch('wiki-plugins/wikiplugin_subscribegroup.tpl');
	return $data;
}
