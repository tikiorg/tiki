<?php

$online_users = $tikilib->get_online_users();

$smarty->assign('online_users', $online_users);

?>