<?php

$ranking = $tikilib->list_visible_galleries(0, $module_rows, 'lastModif_desc', 'admin', '');

$smarty->assign('modLastGalleries', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>