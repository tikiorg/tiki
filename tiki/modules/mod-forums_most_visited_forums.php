<?php
$ranking = $tikilib->forums_ranking_most_visited_forums($module_rows);
$smarty->assign('modForumsMostVisitedForums',$ranking["data"]);
?>