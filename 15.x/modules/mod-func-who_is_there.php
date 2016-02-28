<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @return array
 */
function module_who_is_there_info()
{
	return array(
		'name' => tra('Online Users'),
		'description' => tra('Display information about users currently logged in.'),
		'prefs' => array(),
		'documentation' => 'Module who_is_there',
		'params' => array(
			'content' => array(
				'name' => tra('List Type'),
				'description' => tra('Display the number of users logged in, the list of users logged in, or both.') . " " . tr('Possible values: "count", "list" or "both". Default value: "both"')
			),
			'cluster' => array(
				'name' => tra('Cluster Mode'),
				'description' => tra('If set to "1", separate users based on which host/server they logged on.')
			),
			'silent' => array(
				'name' => tra('Silent Mode'),
				'description' => tra('If set to "1" hides the module, which allows another "who is there" module to include users that should not see it.')
			),
		)
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_who_is_there($mod_reference, $module_params)
{
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	$count = !isset($module_params['content']) || $module_params['content'] != 'list';
	$list = !isset($module_params['content']) || $module_params['content'] != 'count';
	$smarty->assign('count', $count);
	$smarty->assign('list', $list);

	if ($count) {
		$logged_users = $tikilib->count_sessions();
		$smarty->assign('logged_users', $logged_users);
	}

	if ($list) {
		$online_users = $tikilib->get_online_users();
		$smarty->assign_by_ref('online_users', $online_users);
	}

	if (isset($module_params['cluster']) && $module_params['cluster']==1) {
		$smarty->assign('cluster', true);
		if ($count) {
			$logged_cluster_users = $tikilib->count_cluster_sessions();
			$smarty->assign('logged_cluster_users', $logged_cluster_users);
		}
	} else {
		$smarty->assign('cluster', false);
	}

}
