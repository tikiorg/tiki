<?php

$ranking = $tikilib->get_user_galleries($user, $module_rows);

$smarty->assign('modUserG', $ranking);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>