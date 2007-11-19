<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_subscribegroup.php,v 1.1.2.3 2007-11-19 22:30:48 sylvieg Exp $
// Display wiki text if user is in one of listed groups
// Usage:
// {GROUP(groups=>Admins|Developers)}wiki text{GROUP}

function wikiplugin_subscribegroup_help() {
	$help = tra('Subscribe or unsubscribe to a group').":\n";
	$help.= "~np~<br />{SUBSCRIBEGROUP(group=, subscribe=text, unsubscribe=text) /}<br />~/np~";
	return $help;
}
function wikiplugin_subscribegroup($data, $params) {
	global $tiki_p_subscribe_groups, $userlib, $user, $smarty;
	if ($tiki_p_subscribe_groups != 'y' || empty($user)) {
		return '';
	}
	extract ($params, EXTR_SKIP);
	if (empty($group)) {
		if (!empty($_REQUEST['group'])) {
			$group = $_REQUEST['group'];
		} else {
			return tra('Missing parameter');
		}
	}
	echo $group;
	if ($group == 'Anonymous' || $group == 'Registered') {
		return tra('Incorrect param');
	}
	if (!($info = $userlib->get_group_info($group))) {
		return tra('Incorrect param');
	}
	if ($info['userChoice'] != 'y') {
		return tra('Permission denied');
	}

	$groups = $userlib->get_user_groups_inclusion($user);

	if (!empty($_REQUEST['subscribeGroup']) && $_REQUEST['group'] == $group) {
		if (isset($groups[$group])) {
			$userlib->remove_user_from_group($user, $group);
			unset($groups[$group]);
		} else {
			$userlib->assign_user_to_group($user, $group);
			$groups[$group] = 'real';
		}
	}

	if (isset($groups[$group])) {
		if ($groups[$group] == 'included') {
			return tra('Incorrect param');
		}
		$text = empty($unsubscribe)? 'Unsubscribe %s': $unsubscribe;
	} else {
		$text = empty($subscribe)? 'Subscribe to %s': $subscribe;
	}
	$smarty->assign('text', sprintf(tra($text), $group));
	$smarty->assign('subscribeGroup', $group);
	$data = $smarty->fetch('wiki-plugins/wikiplugin_subscribegroup.tpl');
	return $data;
}