<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

$online_users = $tikilib->get_online_users();

$smarty->assign('online_users', $online_users);

?>
