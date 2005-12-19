<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $lslib; include_once ('lib/live_support/lslib.php');

$smarty->assign('modsupport', $lslib->operators_online());

?>
