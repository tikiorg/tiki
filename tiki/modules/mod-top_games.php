<?php

$ranking = $tikilib->list_games(0, $module_rows, 'hits_desc', '');

$smarty->assign('modTopGames', $ranking["data"]);

?>