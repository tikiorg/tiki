<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_groupstat_info()
{
	return array(
		'name' => tra('Group Stats'),
		'documentation' => 'PluginGroupStat',
		'description' => tra('Show the distribution of users among groups'),
		'body' => tra('Title'),
		'iconname' => 'group',
		'introduced' => 4,
		'params' => array(
			'groups' => array(
				'required' => false,
				'name' => tra('Groups'),
				'description' => tra('Groups, separated by ":". If empty, all groups will be listed.'),
				'since' => '4.0',
			),
			'percent_of' => array(
				'required' => false,
				'name' => tra('Percentage of'),
				'description' => tra('Show percentage out of all users in site, or just those specified in the groups
					parameter.'),
				'since' => '8.0',
				'default' => 'groups',
				'options' => array(
					array('text' => tra('Users in groups'), 'value' => 'groups'),
					array('text' => tra('Site users'), 'value' => 'site')
				)
			),
			'show_percent' => array(
				'required' => false,
				'name' => tra('Show Percentage'),
				'description' => tra('Show the percentage of total users that are members of each group (percentages
					are shown by default)'),
				'since' => '4.0',
				'default' => 'y',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'show_bar' => array(
				'required' => false,
				'name' => tra('Show Bar'),
				'description' => tra('Represent the percentage of total users that are members of each group in a bar
					graph (default is not to show the bar graph)'),
				'since' => '4.0',
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

function wikiplugin_groupstat($data, $params)
{
	global $prefs;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');

	if (isset($params['groups'])) {
		$groups = explode(':', $params['groups']);
		if (isset($params['percent_of']) && $params['percent_of'] == 'site') {
			$total = $userlib->nb_users_in_group();
		} else {
			$query = 'SELECT COUNT(DISTINCT `userId`) FROM `users_usergroups` WHERE `groupName` IN('.implode(',', array_fill(0, count($groups), '?')).')';
			$total = $tikilib->getOne($query, $groups);
		}
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
