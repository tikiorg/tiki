<?php
$ranking = $tikilib->forums_ranking_top_topics($module_rows);
$smarty->assign('modForumsTopTopics',$ranking["data"]);
?>