<?php

$logged_users = $tikilib->count_sessions();

$smarty->assign('logged_users', $logged_users);

?>