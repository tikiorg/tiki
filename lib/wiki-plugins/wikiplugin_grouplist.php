<?php

function wikiplugin_grouplist_info() {
	return array(
		'name' => tra('Group List'),
		'description' => tra('List of groups including a group'),
		'params' => array(
			'group' => array(
				'required' => false,
				'name' => tra('Group Name'),
				'description' => tra('Group Name'),
				'filter' => 'groupname',
			),
			'linkhome' => array(
				'required' => false,
				'name' => tra('Link to the group home page'),
				'description' => 'y | n',
				'default' => 'n',
				'filter' => 'alpha',
			),
			'recur' => array(
				'required' => false,
				'name' => tra('Recurse on the included groups'),
				'description' => 'y | n',
				'default' => 'n',
				'filter' => 'alpha',
			),
		),
	);
}

function wikiplugin_grouplist( $data, $params ) {
	global $smarty, $userlib, $access;
	$default = array('recur' => 'n', 'linkhome' => 'n');
	$params = array_merge($default, $params);
	if ($params['linkhome'] == 'y') {
		$access->check_feature('useGroupHome');
	}
	if (empty($params['group'])) {
		$groups = $userlib->list_all_groups();
	} else {
		$groups = $userlib->get_including_groups($params['group'], $params['recur']);
	}
	$groups = $userlib->get_group_info($groups);
	$smarty->assign_by_ref('groups', $groups);
	$smarty->assign_by_ref('params', $params);
	return '~np~' . $smarty->fetch( 'wiki-plugins/wikiplugin_grouplist.tpl' ) . '~/np~';
}