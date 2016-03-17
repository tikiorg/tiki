<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		'iconname' => 'group',
		'introduced' => 2,
		'tags' => array( 'basic' ),
		'params' => array(
			'group' => array(
				'required' => true,
				'name' => tra('Group Name'),
				'description' => tra('Group name to subscribe to or unsubscribe from'),
				'since' => '2.0',
				'filter' => 'groupname',
				'default' => ''
			),
			'subscribe' => array(
				'required' => false,
				'name' => tra('Subscribe Text'),
				'description' => tr('Subscribe text, containing %0 as the placeholder for the group name.',
					'<code>%s</code>'),
				'since' => '2.0',
				'default' => tra('Subscribe') . '%s',
			),
			'unsubscribe' => array(
				'required' => false,
				'name' => tra('Unsubscribe Text'),
				'description' => tr('Unsubscribe text, containing %0 as the placeholder for the group name.',
					'<code>%s</code>'),
				'since' => '2.0',
				'default' => tra('Unsubscribe') . '%s'
			),
			'subscribe_action' => array(
				'required' => false,
				'name' => tra('Subscribe Action'),
				'description' => tr('Subscribe button label, containing %0 as the placeholder for the group name.',
					'<code>%s</code>'),
				'since' => '2.0',
				'default' => tra('OK')
			),
			'unsubscribe_action' => array(
				'required' => false,
				'name' => tr('Unsubscribe Action'),
				'description' => tra('Unsubscribe button label, containing %0 as the placeholder for the group name.',
					'<code>%s</code>'),
				'since' => '2.0',
				'default' => tra('OK')
			),
			'postsubscribe_url' => array(
				'required' => false,
				'name' => tra('Post-subscribe URL'),
				'description' => tra('URL to send the user to after subscribing, if required.'),
				'since' => '8.0',
				'filter' => 'url',
				'default' => ''
			),
			'postunsubscribe_url' => array(
				'required' => false,
				'name' => tra('Post-unsubscribe URL'),
				'description' => tra('URL to send the user to after unsubscribing, if required.'),
				'since' => '8.0',
				'filter' => 'url',
				'default' => ''
			),
			'defgroup' => array(
				'required' => false,
				'name' => tra('Default Group'),
				'description' => tr('Make this the default group text, containing %0 as the placeholder for the group name.',
					'<code>%s</code>'),
				'since' => '9.1',
				'default' => tra('OK')
			),
			'undefgroup' => array(
				'required' => false,
				'name' => tra('Not Default Group'),
				'description' => tr('Stop this being default group text, containing %0 as the placeholder for the group name.',
					'<code>%s</code>'),
				'since' => '9.1',
				'default' => tra('OK')
			),
			'defgroup_action' => array(
				'required' => false,
				'name' => tra('Default Group Action'),
				'description' => tra('Default group button label. Will subscribe to the group first if not already a member.'),
				'since' => '9.1',
				'default' => tra('OK')
			),
			'undefgroup_action' => array(
				'required' => false,
				'name' => tra('Not Default Group Action'),
				'description' => tra('Stop this being default group button label. Does not unsubscribe from the group.'),
				'since' => '9.1',
				'default' => tra('OK')
			),
			'undefgroup_group' => array(
				'required' => false,
				'name' => tra('Second Default'),
				'description' => tra('Group name to set as default when user stops this group being it.'),
				'since' => '9.1',
				'filter' => 'groupname',
				'default' => 'Registered'
			),
			'defgroup_redirect_home' => array(
				'required' => false,
				'name' => tra('Redirect'),
				'description' => tra('Redirect to new home page after default group change. (default is to redirect)'),
				'since' => '9.1',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'allowLeaveNonUserChoice' => array(
				'required' => false,
				'name' => tra('Can Always Leave'),
				'description' => tra('Always allow leaving a group even if the group settings do not allow user choice.'),
				'since' => '14.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_subscribegroup($data, $params)
{
	global $tiki_p_subscribe_groups, $user;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');

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
	if (!isset($params['allowLeaveNonUserChoice']) || $params['allowLeaveNonUserChoice'] != 'y') {
		if ($info['userChoice'] != 'y') {
			return tra('Permission denied');
		}
	}

	$groups = $userlib->get_user_groups_inclusion($user);
	$current_defgroup = $userlib->get_user_default_group($user);

	if (!$groups[$group] && $params['allowLeaveNonUserChoice'] == 'y') {
		// Deny anyway if user is not in group even if allowLeaveNonUserChoice is y
		return tra('Permission denied');
	}

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
			if (!empty($params['defgroup_url']) && $params['defgroup_url'] === 'n') {
				ZendOpenId\OpenId::redirect(ZendOpenId\OpenId::selfUrl());
			} else {
				global $tikiroot;
				ZendOpenId\OpenId::redirect($tikiroot);
			}
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
		$smarty->assign('action', $unsubscribe_action);
	} else {
		$text = isset($subscribe)? $subscribe: tra('Subscribe') . '%s';
		if (!isset($subscribe_action)) {
			$subscribe_action = tra('OK');
		}
		$smarty->assign('action', $subscribe_action);
	}
	$smarty->assign('text', sprintf(tra($text), $group));
	$smarty->assign('subscribeGroup', $group);
	$smarty->assign('iSubscribeGroup', $iSubscribeGroup);
	$data = $data.$smarty->fetch('wiki-plugins/wikiplugin_subscribegroup.tpl');
	return $data;
}
