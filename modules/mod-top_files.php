<?php

$ranking = $tikilib->list_files(0, $module_rows, 'downloads_desc', '');

$smarty->assign('modTopFiles', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>