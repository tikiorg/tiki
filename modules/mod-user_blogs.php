<?php

$ranking = $tikilib->list_user_blogs($user, false);

$smarty->assign('modUserBlogs', $ranking);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>