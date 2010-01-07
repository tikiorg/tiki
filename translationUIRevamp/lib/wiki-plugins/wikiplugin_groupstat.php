<?php

// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerstat.php,v 1.14.2.5 2007-12-18 23:03:15 sylvieg Exp $

function wikiplugin_groupstat_info() {
	return array(
		'name' => tra('Group Stats'),
		'documentation' => 'PluginGroupStat',
		'description' => tra("Displays some stat about group belonging"),
		'body' => tra('Title'),
		'params' => array(
			'groups' => array(
				'required' => false,
				'name' => tra('groups'),
				'description' => tra('Groups separated by :'),
			),
			'show_percent' => array(
				'required' => false,
				'name' => tra('Show Percentage'),
				'description' => 'y|n',
			),
			'show_bar' => array(
				'required' => false,
				'name' => tra('Show Bar'),
				'description' => 'y|n',
			),
		),
	);
}

function wikiplugin_groupstat($data, $params) {
	global $smarty, $prefs, $userlib, $tikilib;

	if (isset($params['groups'])) {
		$groups = split(':', $params['groups']);
		$query = 'SELECT COUNT(DISTINCT(*)) FROM `users_usergroups` WHERE `groupName` IN('.implode(',', array_fill(0,count($groups),'?')).')';
		$total = $tikilib->getOne($query, $groups);
	} else {
		$groups = $userlib->list_all_groups();
		$total = $userlib->nb_users_in_group();
	}
	$stats = array();
	foreach ($groups as $group) {
		$nb = $userlib->nb_users_in_group($group);
		$stats[] = array('group' => $group, 'nb' => $nb);
	}
	foreach ($stats as $i=>$stat) {
		$stats[$i]['percent'] = ($stat['nb'] * 100) / $total;
	}
	$smarty->assign_by_ref('params', $params);
	$smarty->assign_by_ref('stats', $stats);
	return "~np~".$smarty->fetch('wiki-plugins/wikiplugin_groupstat.tpl')."~/np~";
}
