<?php
if($user && $feature_tasks == 'y') {
  if(isset($_SESSION['thedate'])) {
    $pdate = $_SESSION['thedate'];
  } else {
    $pdate = date("U");
  }
  if(isset($_REQUEST["modTasksDel"])) {

    foreach(array_keys($_REQUEST["modTasks"]) as $task) {      	
      $tikilib->remove_task($user, $task);
    }


  }
  if(isset($_REQUEST["modTasksCom"])) {

    foreach(array_keys($_REQUEST["modTasks"]) as $task) {      	
      $tikilib->complete_task($user, $task);
    }


  }
  $ownurl = httpPrefix().$_SERVER["REQUEST_URI"];
  $smarty->assign('ownurl',$ownurl);
  $tasks_useDates = $tikilib->get_user_preference($user,'tasks_useDates');
  $modTasks = $tikilib->list_tasks($user,0,-1,'priority_desc','',$tasks_useDates,$pdate);
  $smarty->assign('modTasks',$modTasks['data']);
}
?>