<?php
$ranking = $tikilib->list_quizzes(0,$module_rows,'created_desc','');
$ranking = $tikilib->list_quiz_sum_stats(0,$module_rows,'timesTaken_desc','');
$smarty->assign('modTopQuizzes',$ranking["data"]);
?>