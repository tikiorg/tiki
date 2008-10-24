<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $imagegallib; include_once ("lib/imagegals/imagegallib.php");

$galleryId = -1;
if (isset($module_params["galleryId"])) {
	$galleryId = $module_params["galleryId"];
}

$ranking = $imagegallib->list_images(0, $module_rows, 'created_desc', '', $galleryId);
$smarty->assign('modLastImages', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
