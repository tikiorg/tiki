<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $prefs;

if ($prefs['feature_directory'] == 'y') {
	$ranking = $tikilib->dir_list_all_valid_sites2(0, $module_rows, 'hits_desc', '');

	$smarty->assign('modTopdirSites', $ranking["data"]);
}

?>
