<?php

$ranking = $tikilib->list_visible_galleries(0, $module_rows, 'hits_desc', 'admin', '');

$smarty->assign('modTopGalleries', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>