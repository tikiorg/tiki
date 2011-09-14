<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_who_is_there_info() {
	return array(
		'name' => tra('Online users'),
		'description' => tra('Displays information about users currently logged in.'),
		'prefs' => array(),
		'documentation' => 'Module who_is_there',
		'params' => array(
			'content' => array(
				'name' => tra('Content to display'),
				'description' => tra('Display the number of users logged in, the list of users logged in, or both.') . " " . tr('Possible values: "count", "list" or "both". Default value: "both"')
			),
			'cluster' => array(
				'name' => tra('Cluster mode'),
				'description' => tra('If set to "1", separate users based on which host/server they logged on.')
			)
		)
	);
}

function module_who_is_there( $mod_reference, $module_params ) {
	global $tikilib, $smarty;

	$count = !isset($module_params["content"]) || $module_params["content"] != "list";
	$list = !isset($module_params["content"]) || $module_params["content"] != "count";
	$smarty->assign("count", $count);
	$smarty->assign("list", $list);

	if ($count) {
		$logged_users = $tikilib->count_sessions();
		$smarty->assign('logged_users', $logged_users);
	}
	
	if ($list) {
		$online_users = $tikilib->get_online_users();
		$smarty->assign_by_ref('online_users', $online_users);
	}

	if(isset($module_params["cluster"]) && $module_params["cluster"]==1) {
		$smarty->assign('cluster',true);
		if ($count) {
			$logged_cluster_users = $tikilib->count_cluster_sessions();
			$smarty->assign('logged_cluster_users', $logged_cluster_users);
		}
	} else {
		$smarty->assign('cluster',false);
	}
	
}
