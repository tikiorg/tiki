<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

$logged_users = $tikilib->count_sessions();

$online_users = $tikilib->get_online_users();

$smarty->assign('online_users', $online_users);
$smarty->assign('logged_users', $logged_users);

?>
