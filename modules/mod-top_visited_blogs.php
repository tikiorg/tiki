<?php

$ranking = $tikilib->list_blogs(0, $module_rows, 'hits_desc', '');

$smarty->assign('modTopVisitedBlogs', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>