<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $imagegallib; include_once ("lib/imagegals/imagegallib.php");

if (isset($module_params["galleryId"])) {
	$galleryId = $module_params["galleryId"];
} else {
	$galleryId = -1;
}


$ranking = $imagegallib->get_random_image($galleryId);
$smarty->assign('img', $ranking);
?>
