<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_subscribegroups.php,v 1.1.2.1 2007-12-04 20:33:16 sylvieg Exp $
// Display wiki text if user is in one of listed groups
// Usage:
// {GROUP(groups=>Admins|Developers)}wiki text{GROUP}

function wikiplugin_subscribegroups_help() {
	$help = tra('Subscribe or unsubscribe to a group').":\n";
	$help.= "~np~<br />{SUBSCRIBEGROUPS(subscribe=text,groups=g1,g2) /}<br />~/np~";
	return $help;
}
function wikiplugin_subscribegroups($data, $params) {
	global $tiki_p_subscribe_groups, $userlib, $user, $smarty;
	if ($tiki_p_subscribe_groups != 'y' || empty($user)) {
		return '';
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
	foreach ($possiblegroups['data'] as $key=>$g) {
		if (!empty($userGroups[$g['groupName']]) || (!empty($groups) && !in_array($g['groupName'], $groups))) {
			unset($possiblegroups['data'][$key]);
		}
	}

	if (isset($subscribe)){
		$smarty->assign_by_ref('subscribe', $subscribe);
	} else {
		$smarty->assign('subscribe', '');
	}
	$smarty->assign_by_ref('userGroups', $userGroups);
	$smarty->assign_by_ref('possiblegroups', $possiblegroups['data']);
	$data = $smarty->fetch('wiki-plugins/wikiplugin_subscribegroups.tpl');
	return $data;
}