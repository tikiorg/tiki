<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $tikilib, $smarty;
$ranking = $tikilib->list_faqs(0, $module_rows, 'hits_desc', '');

$smarty->assign('modTopVisitedFaqs', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');


