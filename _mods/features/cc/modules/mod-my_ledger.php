<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


global $cclib,$user;
if (!is_object($cclib)) {
	include "lib/cc/cclib.php";
}
if ($user) {
	$myinfo = $cclib->user_infos($user,'y');
}
$smarty->assign('myinfo', $myinfo['registered_cc']);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
