<?php
$ranking = $tikilib->forums_ranking_most_read_topics($module_rows);
$smarty->assign('modForumsMostReadTopics',$ranking["data"]);
?>