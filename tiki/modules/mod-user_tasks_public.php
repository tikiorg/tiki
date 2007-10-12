<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $user;

if ($user && isset($prefs['feature_tasks']) && $prefs['feature_tasks'] == 'y' && isset($tiki_p_tasks) && $tiki_p_tasks == 'y') {
	global $tasklib;

	if (!is_object($tasklib)) {
		global $dbTiki;
		include "lib/tasks/tasklib.php";
	}
    $smarty->assign('ownurl', $tikilib->httpPrefix().$_SERVER["SCRIPT_NAME"].'?'.urlencode($_SERVER["QUERY_STRING"]));
	$user_groups = $tasklib->get_groups_to_user_with_permissions($user,'tiki_p_tasks_receive');

	if (isset($_REQUEST["modTasksSearch"])) {
		check_ticket('user-prefs');
        $user_group = $_REQUEST["user_group"];
		$tikilib->set_user_preference($user, 'tasks_modLastSelectedGroup', $user_group);
	}
	else{
	  $user_group = $tikilib->get_user_preference($user, 'tasks_modLastSelectedGroup', /*default*/'');
	}

        $smarty->assign('user_group', $user_group);

	if ($user_group == '') {
	  $public_tasks =  array('data'=>''); 
	}
        else{  
	  	$public_tasks =  $tasklib->list_tasks($user, '0', '10',NULL, 'priority_asc', false,false,false,false,true,$user_group); 
        }
	$smarty->assign('public_tasks', $public_tasks['data']);
	$smarty->assign('user_groups', $user_groups );
}

?>
