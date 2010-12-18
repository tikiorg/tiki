<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_grouplist_info() {
	return array(
		'name' => tra('Group List'),
		'documentation' => 'PluginGroupList',
		'description' => tra('List all groups or just groups that include a certain group'),
		'params' => array(
			'group' => array(
				'required' => false,
				'name' => tra('Group Name'),
				'description' => tra('If empty, all groups will be listed. Entering a group name will cause only groups that include this group to be listed.'),
				'filter' => 'groupname',
			),
			'linkhome' => array(
				'required' => false,
				'name' => tra('Group Home Page'),
				'description' => tra('Link the group name to the group home page, if there is one (not linked by default)'),
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
			'recur' => array(
				'required' => false,
				'name' => tra('Recursively List Groups'),
				'description' => tra('Recurse on the included groups (default is not to recurse)'),
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
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
