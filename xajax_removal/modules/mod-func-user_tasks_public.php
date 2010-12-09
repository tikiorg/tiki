<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_user_tasks_public_info() {
	return array(
		'name' => tra('Public tasks'),
		'description' => tra('Lists the public user tasks of a chosen group, with links to tasks.'),
		'prefs' => array("feature_tasks"),
		'params' => array()
	);
}

function module_user_tasks_public( $mod_reference, $module_params ) {
	global $user, $tikilib, $smarty, $tiki_p_tasks, $prefs;
	
	if ($user && isset($tiki_p_tasks) && $tiki_p_tasks == 'y') {
		global $tasklib; require_once "lib/tasks/tasklib.php";
		
		$smarty->assign('ownurl', $_SERVER["REQUEST_URI"]);
		$user_groups = $tasklib->get_groups_to_user_with_permissions($user,'tiki_p_tasks_receive');
		
		if (isset($_REQUEST["modTasksSearch"])) {
			check_ticket('user-prefs');
			$user_group = $_REQUEST["user_group"];
			$tikilib->set_user_preference($user, 'tasks_modLastSelectedGroup', $user_group);
		}
		else {
			$user_group = $tikilib->get_user_preference($user, 'tasks_modLastSelectedGroup', /*default*/'');
		}
		
		$smarty->assign('user_group', $user_group);
		
		if ($user_group == '') {
			$public_tasks =  array('data'=>''); 
		} else {
			$public_tasks =  $tasklib->list_tasks($user, '0', '10',NULL, 'priority_asc', false,false,false,false,true,$user_group); 
		}
		$smarty->assign('public_tasks', $public_tasks['data']);
		$smarty->assign('user_groups', $user_groups );
		$smarty->clear_assign('tpl_module_title');
	}
}
