<?php

$ranking = $tikilib->get_user_pages($user, $module_rows);

$smarty->assign('modUserPages', $ranking);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>