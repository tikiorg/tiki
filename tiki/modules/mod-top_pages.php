<?php

$ranking = $tikilib->get_top_pages($module_rows);

$smarty->assign('modTopPages', $ranking);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>