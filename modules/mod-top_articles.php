<?php

$ranking = $tikilib->list_articles(0, $module_rows, 'reads_desc', '', '', $user);

$smarty->assign('modTopArticles', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>