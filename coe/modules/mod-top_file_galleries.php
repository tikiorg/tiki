<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $tikilib, $smarty, $prefs;
$ranking = $tikilib->get_files(0, $module_rows, 'hits_desc', null, $prefs['fgal_root_id'], false, true, false, false, false, false, false);

$smarty->assign('modTopFileGalleries', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');


