<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $tikilib, $smarty;
$pages=$tikilib->list_pages(0, $module_rows, "random", '', '', true, true);

$smarty->assign('modRandomPages', $pages["data"]);