<?php

include_once ('lib/rankings/ranklib.php');

$ranking = $ranklib->forums_ranking_top_topics($module_rows);
$smarty->assign('modForumsTopTopics', $ranking["data"]);

?>