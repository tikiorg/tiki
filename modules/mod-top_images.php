<?php

include_once ("lib/imagegals/imagegallib.php");

$ranking = $imagegallib->list_images(0, $module_rows, 'hits_desc', '');
$smarty->assign('modTopImages', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>