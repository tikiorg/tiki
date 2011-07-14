<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_subscribegroup.php,v 1.1.2.8 2007-12-25 14:42:01 sylvieg Exp $
// Display wiki text if user is in one of listed groups
// Usage:
// {GROUP(groups=>Admins|Developers)}wiki text{GROUP}

function wikiplugin_subscribegroup_help() {
	$help = tra('Subscribe or unsubscribe to a group').":\n";
	$help.= "~np~<br />{SUBSCRIBEGROUP(group=, subscribe=text, unsubscribe=text, subscribe_action=Name of subscribe submit button, unsubscribe_action=Name of unsubscribe submit button) /}<br />~/np~";
	return $help;
}

function wikiplugin_subscribegroup_info() {
	return array(
		'name' => tra('Subscribe Group'),
		'documentation' => 'PluginSubscribeGroup',		
		'description' => tra('Subscribe or unsubscribe to a group'),
		'prefs' => array( 'wikiplugin_subscribegroup' ),
		'params' => array(
			'group' => array(
				'required' => true,
				'name' => tra('Group Name'),
				'description' => tra('As known in Tikiwiki'),
			),
			'subscribe' => array(
				'required' => false,
				'name' => tra('Subscribe Text'),
				'description' => tra('Subscribe text, containing %s as the placeholder for the group name.'),
			),
			'unsubscribe' => array(
				'required' => false,
				'name' => tra('Unsubscribe Text'),
				'description' => tra('Unsubscribe text, containing %s as the placeholder for the group name.'),
			),
			'subscribe_action' => array(
				'required' => false,
				'name' => tra('Subscribe Action'),
				'description' => tra('Subscribe button label, containing %s as the placeholder for the group name.'),
			),
			'unsubscribe_action' => array(
				'required' => false,
				'name' => tra('Unsubscribe Action'),
				'description' => tra('Unsubscribe button label, containing %s as the placeholder for the group name.'),
			),
		),
	);
}

function wikiplugin_subscribegroup($data, $params) {
	global $tiki_p_subscribe_groups, $userlib, $user, $smarty;
	if (empty($user)) {
		return '';
	}
	if ($tiki_p_subscribe_groups != 'y') {
		return tra('Permission denied');
	}
	extract ($params, EXTR_SKIP);

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

	if (!empty($_REQUEST['subscribeGroup']) && $_REQUEST['group'] == $group) {
		if (isset($groups[$group]) and $groups[$group] != 'included') {
			$userlib->remove_user_from_group($user, $group);
			unset($groups[$group]);
		} else {
			$userlib->assign_user_to_group($user, $group);
			$groups[$group] = 'real';
		}
	}

	if (isset($groups[$group]) and $groups[$group] != 'included') {//user already in the group->
		$text = isset($unsubscribe)? $unsubscribe: 'Unsubscribe %s';
		if (!isset($unsubscribe_action)) {
			$unsubscribe_action = 'OK';
		}
		$smarty->assign('action', $unsubscribe_action);
	} else {
		$text = isset($subscribe)? $subscribe: 'Subscribe %s';
		if (!isset($subscribe_action)) {
			$subscribe_action = 'OK';
		}
		$smarty->assign('action', $subscribe_action);
	}
	$smarty->assign('text', sprintf(tra($text), $group));
	$smarty->assign('subscribeGroup', $group);
	$data = $smarty->fetch('wiki-plugins/wikiplugin_subscribegroup.tpl');
	return $data;
}
