<?php

$ranking = $tikilib->list_blogs(0, $module_rows, 'lastModif_desc', '');

$smarty->assign('modLastModifiedBlogs', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>