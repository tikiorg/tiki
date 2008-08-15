<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$logged_users = $tikilib->count_sessions();

$online_users = $tikilib->get_online_users();

if(isset($module_params["cluster"]) && $module_params["cluster"]==1) {
  $smarty->assign('cluster',true);
  $logged_cluster_users = $tikilib->count_cluster_sessions();
  $smarty->assign('logged_cluster_users', $logged_cluster_users);
} else {
  $smarty->assign('cluster',false);
}

$smarty->assign_by_ref('online_users', $online_users);
$smarty->assign('logged_users', $logged_users);

?>
