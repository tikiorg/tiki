<?php

$ranking = $tikilib->list_games(0, $module_rows, 'hits_desc', '');

$smarty->assign('modTopGames', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>