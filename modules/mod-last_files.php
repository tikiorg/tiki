<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (!function_exists('mod_last_files_help')) {
	function mod_last_files_help() {
		return 'galleryId=id1:id2,nonums=y|n';
	}
}

if (isset($module_params["galleryId"])) {
	if (strstr($module_params['galleryId'], ':')) {
		$ranking = $tikilib->get_files(0, $module_rows, 'created_desc', '', explode(':',$module_params['galleryId']));
	} else {
		$ranking = $tikilib->get_files(0, $module_rows, 'created_desc', '', $module_params["galleryId"]);
	}
} else {
	$ranking = $tikilib->list_files(0, $module_rows, 'created_desc', '');
}

$smarty->assign('modLastFiles', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
