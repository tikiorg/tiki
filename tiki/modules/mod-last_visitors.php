<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}
global $userlib;include_once('lib/userslib.php');

$last_visitors= $userlib->get_users(0,$module_rows,'currentLogin_desc');
$smarty->assign('modLastVisitors', $last_visitors['data']);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('showavatars', isset($module_params["showavatars"]) ? $module_params["showavatars"] : 'n');

?>
