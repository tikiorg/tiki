<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_grouplist_info()
{
	return array(
		'name' => tra('Group List'),
		'documentation' => 'PluginGroupList',
		'description' => tra('Create a complete or filtered list of groups'),
		'iconname' => 'group',
		'introduced' => 5,
		'params' => array(
			'group' => array(
				'required' => false,
				'name' => tra('Group Name'),
				'description' => tra('If empty, all groups will be listed. Entering a group name will cause only groups
					that include this group to be listed.'),
				'since' => '5.0',
				'filter' => 'groupname',
			),
			'linkhome' => array(
				'required' => false,
				'name' => tra('Group Homepage'),
				'description' => tra('Link the group name to the group homepage, if there is one (not linked to by default)'),
				'since' => '5.0',
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
				'description' => tra('Recursively list the included groups (the default is to not list them repeatedly)'),
				'since' => '5.0',
				'default' => 'n',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				)
			),
            'own' => array(
                'required' => false,
                'name' => tra('List only your own'),
                'description' => tra('Filter the list of groups to include only the groups from the user viewing the
                    page (default is not to filter)'),
	            'since' => '14.0',
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

function wikiplugin_grouplist( $data, $params )
{
	global $user;
	$userlib = TikiLib::lib('user');
	$smarty = TikiLib::lib('smarty');
	$access = TikiLib::lib('access');

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
    if (!empty($params['own']) && $params['own'] == 'y') {
        $user_details = $userlib->get_user_details($user);
        $groups_user = $user_details['groups'];
        $groups = array_intersect_key($groups_user, $groups);
    }
	$groups = $userlib->get_group_info($groups);
	$smarty->assign_by_ref('groups', $groups);
	$smarty->assign_by_ref('params', $params);
	return '~np~' . $smarty->fetch('wiki-plugins/wikiplugin_grouplist.tpl') . '~/np~';
}
