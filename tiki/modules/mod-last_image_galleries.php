<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $imagegallib;
if (!is_object($imagegallib)) {
	require_once('lib/imagegals/imagegallib.php');
}

$ranking = $imagegallib->list_visible_galleries(0, $module_rows, 'lastModif_desc', 'admin', '');

$smarty->assign('modLastGalleries', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
