<?php

include_once ('lib/rankings/ranklib.php');

$ranking = $ranklib->forums_ranking_last_topics($module_rows);
$smarty->assign('modForumsLastTopics', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>