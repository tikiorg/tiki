<?php
$logged_users = $tikilib->count_sessions();
$online_users = $tikilib->get_online_users();

$smarty->assign('online_users',$online_users);
$smarty->assign('logged_users',$logged_users);
?>
