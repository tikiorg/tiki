<?php

#$ranking = $tikilib->last_major_pages($module_rows);
$ranking = $tikilib->last_pages($module_rows);

$smarty->assign('modLastModif', $ranking);
$smarty->assign('maxlen', isset($module_params["maxlen"]) ? $module_params["maxlen"] : 0);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
