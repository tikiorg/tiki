<?php
$ranking = $tikilib->forums_ranking_most_commented_forum($module_rows);
$smarty->assign('modForumsMostCommentedForums',$ranking["data"]);
?>