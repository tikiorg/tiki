<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if ($user && isset($feature_tasks) && $feature_tasks == 'y' && isset($tiki_p_tasks) && $tiki_p_tasks == 'y') {

	$ownurl =/*httpPrefix().*/ $_SERVER["REQUEST_URI"];
	$smarty->assign('ownurl', $ownurl);
	$tasks_useDates = $tikilib->get_user_preference($user, 'tasks_useDates');
	$user_groups = $tasklib->get_groups_to_user_with_permissions($user,'tiki_p_tasks_receive');

	if (isset($_REQUEST["modTasksSearch"])) {
          $user_group = $_REQUEST["user_group"];
	}
	else{
	  $user_group = '';
	}

        $smarty->assign('user_group', $user_group);

	if ($user_group == '') {
	  $public_tasks =  array('data'=>''); //$tasklib->list_tasks($user, '0', '10', 'priority_desc', '', 'n', date('U'),false,false,true); 
	}
        else{  
	  	$public_tasks =  $tasklib->list_tasks($user, '0', '10',NULL, 'priority_asc', false,false,false,false,true,$user_group); 
        }
	$smarty->assign('public_tasks', $public_tasks['data']);
	$smarty->assign('user_groups', $user_groups );
}

?>
