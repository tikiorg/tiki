<?php
$ranking = $tikilib->forums_ranking_last_topics($module_rows);
$smarty->assign('modForumsLastTopics',$ranking["data"]);
?>