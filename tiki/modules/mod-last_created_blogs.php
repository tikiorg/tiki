<?php

$ranking = $tikilib->list_blogs(0, $module_rows, 'created_desc', '');

$smarty->assign('modLastCreatedBlogs', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>