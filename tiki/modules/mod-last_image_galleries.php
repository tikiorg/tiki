<?php
// nonums = no numbering
//{MODULE(nonums=y|n /}

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (empty($imagegallib) or !is_object($imagegallib)) include_once 'lib/imagegals/imagegallib.php';
$ranking = $imagegallib->list_visible_galleries(0, $module_rows, 'lastModif_desc', $user, '');

$smarty->assign('modLastGalleries', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
