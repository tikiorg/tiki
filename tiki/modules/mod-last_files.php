<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($module_params["galleryId"])) {
	$ranking = $tikilib->get_files(0, $module_rows, 'created_desc', '', $module_params["galleryId"]);
} else {
	$ranking = $tikilib->list_files(0, $module_rows, 'created_desc', '');
}

$smarty->assign('modLastFiles', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
